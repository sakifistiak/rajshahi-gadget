<?php

namespace App\Support;

use App\Models\Order;
use App\Models\SiteSetting;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Thin client for the SSLCommerz v4 hosted checkout. The order number is the
 * transaction id, so every callback maps straight back to one order.
 */
class SslCommerz
{
    /**
     * Credentials come from Admin > Settings > Payment Gateway. The .env keys
     * are only a fallback for when nothing has been saved there.
     *
     * @return array{store_id: ?string, store_password: ?string, sandbox: bool}
     */
    public static function credentials(): array
    {
        $storeId = SiteSetting::getValue('sslcommerz_store_id');
        if ($storeId === null) {
            return [
                'store_id' => config('services.sslcommerz.store_id'),
                'store_password' => config('services.sslcommerz.store_password'),
                'sandbox' => (bool) config('services.sslcommerz.sandbox'),
            ];
        }

        return [
            'store_id' => $storeId,
            'store_password' => self::decryptPassword(SiteSetting::getValue('sslcommerz_store_password')),
            'sandbox' => SiteSetting::getValue('sslcommerz_sandbox', '1') === '1',
        ];
    }

    /** The store password is kept encrypted with APP_KEY in site_settings. */
    public static function encryptPassword(string $password): string
    {
        return Crypt::encryptString($password);
    }

    private static function decryptPassword(?string $encrypted): ?string
    {
        if ($encrypted === null) {
            return null;
        }

        try {
            return Crypt::decryptString($encrypted);
        } catch (DecryptException $e) {
            // APP_KEY changed since it was saved: treat it as missing.
            return null;
        }
    }

    public static function enabled(): bool
    {
        $credentials = self::credentials();

        return SiteSetting::getValue('sslcommerz_enabled', '1') === '1'
            && filled($credentials['store_id'])
            && filled($credentials['store_password']);
    }

    /** EMI tenures (months) SSLCommerz accepts for emi_max_inst_option. */
    public const EMI_TENURES = [3, 6, 9, 12, 18, 24, 36];

    /**
     * EMI is a second checkout option on the same store. The bank EMI page
     * only shows if SSLCommerz has also switched EMI on for the store.
     */
    public static function emiEnabled(): bool
    {
        return self::enabled() && SiteSetting::getValue('sslcommerz_emi_enabled', '1') === '1';
    }

    /** Smallest order total (BDT) that may be paid by EMI. */
    public static function emiMinAmount(): int
    {
        return max(0, (int) SiteSetting::getValue('sslcommerz_emi_min_amount', '5000'));
    }

    public static function emiMaxInstalment(): int
    {
        $months = (int) SiteSetting::getValue('sslcommerz_emi_max_instalment', '36');

        return in_array($months, self::EMI_TENURES, true) ? $months : 36;
    }

    public static function emiAvailableFor(int|float $total): bool
    {
        return self::emiEnabled() && $total >= self::emiMinAmount();
    }

    private static function baseUrl(): string
    {
        return self::credentials()['sandbox']
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }

    /**
     * Opens a real session for a 10 BDT test order that is never saved, to
     * check the credentials. Returns the payment page URL.
     */
    public static function smokeTest(): string
    {
        $order = (new Order)->forceFill([
            'order_number' => 'SMOKE-'.now()->format('ymdHis'),
            'customer_name' => 'Smoke Test',
            'phone' => '01700000000',
            'address' => 'Smoke test address',
            'delivery_area' => 'inside_dhaka',
            'total' => 10,
        ]);
        $order->setRelation('items', collect([(object) ['product_name' => 'Smoke test item', 'quantity' => 1]]));
        $order->setRelation('storeLocation', null);

        return self::initiate($order);
    }

    /**
     * Opens a payment session and returns the hosted payment page URL.
     * An EMI order (payment_method sslcommerz_emi) opens the EMI page.
     */
    public static function initiate(Order $order): string
    {
        $order->loadMissing('items', 'storeLocation');

        $response = Http::asForm()->timeout(30)->post(self::baseUrl().'/gwprocess/v4/api.php', [
            'store_id' => self::credentials()['store_id'],
            'store_passwd' => self::credentials()['store_password'],
            'total_amount' => $order->total,
            'currency' => 'BDT',
            'tran_id' => $order->order_number,
            'success_url' => route('payment.sslcommerz.success'),
            'fail_url' => route('payment.sslcommerz.fail'),
            'cancel_url' => route('payment.sslcommerz.cancel'),
            'ipn_url' => route('payment.sslcommerz.ipn'),
            'cus_name' => $order->customer_name,
            'cus_email' => $order->email ?: SiteSetting::getValue('site_email', 'khangadget.bd@gmail.com'),
            'cus_phone' => $order->phone,
            'cus_add1' => $order->address ?: ($order->storeLocation->name ?? 'Store pickup'),
            'cus_city' => $order->delivery_area === 'outside_dhaka' ? 'Outside Dhaka' : 'Dhaka',
            'cus_country' => 'Bangladesh',
            'shipping_method' => 'NO',
            'num_of_item' => $order->items->sum('quantity'),
            'product_name' => str($order->items->pluck('product_name')->implode(', '))->limit(250)->toString(),
            'product_category' => 'Electronics',
            'product_profile' => 'physical-goods',
            // EMI orders open straight on the bank EMI page (cards only, no
            // full-payment methods); a normal online payment hides EMI.
            ...($order->isEmi() ? [
                'emi_option' => 1,
                'emi_max_inst_option' => self::emiMaxInstalment(),
                'emi_allow_only' => 1,
            ] : [
                'emi_option' => 0,
            ]),
        ]);

        $url = $response->json('GatewayPageURL');
        if (! $response->successful() || $response->json('status') !== 'SUCCESS' || ! $url) {
            Log::warning('SSLCommerz session init failed', [
                'order' => $order->order_number,
                'http' => $response->status(),
                'reason' => $response->json('failedreason'),
            ]);

            throw new RuntimeException((string) ($response->json('failedreason') ?: 'Online payment is unavailable right now.'));
        }

        return $url;
    }

    /**
     * Asks SSLCommerz to confirm a val_id. Never trust the browser-posted
     * callback fields on their own: they are only used to find the order.
     *
     * @return array<string, mixed>|null the validated transaction, or null
     */
    public static function validate(string $valId): ?array
    {
        $response = Http::timeout(30)->get(self::baseUrl().'/validator/api/validationserverAPI.php', [
            'val_id' => $valId,
            'store_id' => self::credentials()['store_id'],
            'store_passwd' => self::credentials()['store_password'],
            'format' => 'json',
            'v' => 1,
        ]);

        if (! $response->successful() || ! in_array($response->json('status'), ['VALID', 'VALIDATED'], true)) {
            Log::warning('SSLCommerz validation rejected', ['val_id' => $valId, 'status' => $response->json('status')]);

            return null;
        }

        return $response->json();
    }
}
