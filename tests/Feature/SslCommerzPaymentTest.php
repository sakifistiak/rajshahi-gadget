<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Smoke test for the SSLCommerz flow with the gateway faked: checkout opens a
 * session, and only a validator-confirmed payment of the right amount marks
 * the order paid.
 */
class SslCommerzPaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        SiteSetting::flushCache();

        config([
            'services.sslcommerz.store_id' => 'teststore',
            'services.sslcommerz.store_password' => 'secret',
            'services.sslcommerz.sandbox' => true,
        ]);

        Product::forceCreate([
            'slug' => 'macbook-air',
            'name' => 'MacBook Air',
            'brand_id' => Brand::create(['slug' => 'apple', 'name' => 'Apple'])->id,
            'category_id' => Category::forceCreate(['slug' => 'laptops', 'name' => 'Laptops'])->id,
            'condition_id' => Condition::create(['slug' => 'intact', 'label' => 'Intact', 'short' => 'Intact', 'tagline' => 'Sealed'])->id,
            'price' => 100000,
            'description' => 'A laptop.',
        ]);
    }

    private function fakeGateway(array $validation = []): void
    {
        Http::fake([
            'sandbox.sslcommerz.com/gwprocess/*' => Http::response([
                'status' => 'SUCCESS',
                'GatewayPageURL' => 'https://sandbox.sslcommerz.com/EasyCheckOut/testkey',
            ]),
            'sandbox.sslcommerz.com/validator/*' => function (HttpRequest $request) use ($validation) {
                $order = Order::first();

                return Http::response([
                    'status' => 'VALID',
                    'val_id' => $request['val_id'],
                    'tran_id' => $order->order_number,
                    'amount' => $order->total.'.00',
                    'currency_type' => 'BDT',
                    'currency_amount' => $order->total.'.00',
                    'bank_tran_id' => 'BANK123',
                    'card_type' => 'BKASH-BKash',
                    ...$validation,
                ]);
            },
        ]);
    }

    private function checkout(string $paymentMethod = 'sslcommerz', array $overrides = [])
    {
        return $this->postJson('/orders', [
            'accept_terms' => '1',
            'customer_name' => 'Rahim',
            'phone' => '01700000000',
            'delivery_method' => 'home_delivery',
            'delivery_area' => 'inside_dhaka',
            'address' => 'Dhanmondi, Dhaka',
            'payment_method' => $paymentMethod,
            'items' => [['slug' => 'macbook-air', 'quantity' => 1]],
            ...$overrides,
        ]);
    }

    public function test_checkout_opens_a_payment_session_and_returns_the_gateway_url(): void
    {
        $this->fakeGateway();
        $this->get('/checkout')->assertOk()->assertSee('value="sslcommerz"', false);

        $this->checkout()->assertOk()->assertJsonPath('redirect_url', 'https://sandbox.sslcommerz.com/EasyCheckOut/testkey');

        $order = Order::firstOrFail();
        $this->assertSame('unpaid', $order->payment_status);
        Http::assertSent(fn (HttpRequest $r) => str_contains($r->url(), '/gwprocess/v4/api.php')
            && $r['tran_id'] === $order->order_number
            && (int) $r['total_amount'] === $order->total
            && $r['store_id'] === 'teststore'
            && str_ends_with($r['ipn_url'], '/payment/sslcommerz/ipn'));
    }

    public function test_a_validated_success_callback_marks_the_order_paid(): void
    {
        $this->fakeGateway();
        $this->checkout();
        $order = Order::firstOrFail();

        // No CSRF token: SSLCommerz posts from its own domain.
        $this->post('/payment/sslcommerz/success', ['tran_id' => $order->order_number, 'val_id' => 'VAL1', 'status' => 'VALID'])
            ->assertRedirect(route('thank-you', ['order' => $order->order_number]));

        $order->refresh();
        $this->assertTrue($order->isPaid());
        $this->assertSame('BANK123', $order->payment_bank_tran_id);
        $this->assertNotNull($order->paid_at);

        $this->get('/thank-you?order='.$order->order_number)->assertSee('Payment received');
    }

    public function test_the_ipn_is_idempotent(): void
    {
        $this->fakeGateway();
        $this->checkout();
        $order = Order::firstOrFail();

        $this->post('/payment/sslcommerz/ipn', ['tran_id' => $order->order_number, 'val_id' => 'VAL1'])->assertOk()->assertSee('OK');
        $this->post('/payment/sslcommerz/ipn', ['tran_id' => $order->order_number, 'val_id' => 'VAL1'])->assertOk()->assertSee('OK');

        $this->assertTrue($order->refresh()->isPaid());
        Http::assertSentCount(2); // session init + one validation; the repeat IPN is not re-validated
    }

    public function test_a_wrong_amount_is_never_marked_paid(): void
    {
        $this->fakeGateway(['currency_amount' => '10.00', 'amount' => '10.00']);
        $this->checkout();
        $order = Order::firstOrFail();

        $this->post('/payment/sslcommerz/success', ['tran_id' => $order->order_number, 'val_id' => 'VAL1']);
        $this->post('/payment/sslcommerz/ipn', ['tran_id' => $order->order_number, 'val_id' => 'VAL1'])->assertSee('IGNORED');

        $this->assertFalse($order->refresh()->isPaid());
    }

    public function test_an_invalid_val_id_is_never_marked_paid(): void
    {
        $this->fakeGateway(['status' => 'INVALID_TRANSACTION']);
        $this->checkout();
        $order = Order::firstOrFail();

        $this->post('/payment/sslcommerz/ipn', ['tran_id' => $order->order_number, 'val_id' => 'FORGED'])->assertSee('IGNORED');
        $this->get('/thank-you?order='.$order->order_number)->assertDontSee('Payment received');

        $this->assertFalse($order->refresh()->isPaid());
    }

    public function test_fail_and_cancel_callbacks_cancel_the_unpaid_order(): void
    {
        $this->fakeGateway();
        $this->checkout();
        $order = Order::firstOrFail();

        $this->post('/payment/sslcommerz/cancel', ['tran_id' => $order->order_number])
            ->assertRedirect('/checkout?payment=cancelled');

        $order->refresh();
        $this->assertSame('cancelled', $order->payment_status);
        $this->assertSame('cancelled', $order->status);
    }

    public function test_a_failed_session_init_returns_an_error_and_cancels_the_order(): void
    {
        Http::fake(['sandbox.sslcommerz.com/*' => Http::response(['status' => 'FAILED', 'failedreason' => 'Store Credential Error'])]);

        $this->checkout()->assertStatus(502);

        $this->assertSame('failed', Order::firstOrFail()->payment_status);
    }

    public function test_online_payment_is_rejected_and_hidden_until_credentials_are_set(): void
    {
        config(['services.sslcommerz.store_id' => null]);
        Http::fake();

        $this->checkout()->assertUnprocessable()->assertJsonValidationErrors('payment_method');
        $this->get('/checkout')->assertOk()->assertDontSee('value="sslcommerz"', false);
        Http::assertNothingSent();
    }

    public function test_cash_on_delivery_is_unchanged(): void
    {
        Http::fake();

        $this->checkout('cod')->assertOk()->assertJsonMissingPath('redirect_url');

        $this->assertSame('unpaid', Order::firstOrFail()->payment_status);
        Http::assertNothingSent();
    }

    public function test_an_emi_order_opens_the_emi_only_payment_page(): void
    {
        $this->fakeGateway(['emi_instalment' => '6', 'card_type' => 'VISA-City Bank']);
        $this->get('/checkout')->assertOk()->assertSee('value="sslcommerz_emi"', false);

        $this->checkout('sslcommerz_emi')->assertOk()->assertJsonPath('redirect_url', 'https://sandbox.sslcommerz.com/EasyCheckOut/testkey');
        $order = Order::firstOrFail();
        Http::assertSent(fn (HttpRequest $r) => str_contains($r->url(), '/gwprocess/v4/api.php')
            && (int) $r['emi_option'] === 1
            && (int) $r['emi_allow_only'] === 1
            && (int) $r['emi_max_inst_option'] === 36);

        $this->post('/payment/sslcommerz/success', ['tran_id' => $order->order_number, 'val_id' => 'VAL1'])
            ->assertRedirect(route('thank-you', ['order' => $order->order_number]));

        $order->refresh();
        $this->assertTrue($order->isPaid());
        $this->assertSame(6, $order->payment_emi_instalment);
        $this->get('/thank-you?order='.$order->order_number)->assertSee('EMI, 6 months');
    }

    public function test_a_normal_online_payment_hides_emi(): void
    {
        $this->fakeGateway();
        $this->checkout();

        Http::assertSent(fn (HttpRequest $r) => str_contains($r->url(), '/gwprocess/v4/api.php')
            && (int) $r['emi_option'] === 0
            && ! isset($r['emi_allow_only']));
    }

    public function test_emi_is_refused_below_the_minimum_total(): void
    {
        SiteSetting::setValue('sslcommerz_emi_min_amount', '200000');
        Http::fake();

        $this->checkout('sslcommerz_emi')->assertUnprocessable()->assertJsonValidationErrors('payment_method');

        $this->assertSame(0, Order::count());
        Http::assertNothingSent();
    }

    public function test_emi_can_be_switched_off_on_its_own(): void
    {
        SiteSetting::setValue('sslcommerz_emi_enabled', '0');
        Http::fake();

        $this->get('/checkout')->assertOk()->assertSee('value="sslcommerz"', false)->assertDontSee('value="sslcommerz_emi"', false);
        $this->checkout('sslcommerz_emi')->assertUnprocessable()->assertJsonValidationErrors('payment_method');
    }

    public function test_a_risky_payment_is_held_for_verification(): void
    {
        $this->fakeGateway(['risk_level' => '1', 'risk_title' => 'Safe card but suspicious']);
        $this->checkout();
        $order = Order::firstOrFail();

        $this->post('/payment/sslcommerz/ipn', ['tran_id' => $order->order_number, 'val_id' => 'VAL1'])->assertSee('OK');

        $order->refresh();
        $this->assertTrue($order->isPaid());
        $this->assertTrue($order->isPaymentOnHold());
        $this->assertSame('Safe card but suspicious', $order->payment_risk_title);
        $this->get('/thank-you?order='.$order->order_number)->assertSee('verify this payment');
    }

    public function test_a_normal_payment_is_not_held(): void
    {
        $this->fakeGateway(['risk_level' => '0', 'risk_title' => 'Safe']);
        $this->checkout();
        $order = Order::firstOrFail();

        $this->post('/payment/sslcommerz/ipn', ['tran_id' => $order->order_number, 'val_id' => 'VAL1']);

        $this->assertFalse($order->refresh()->isPaymentOnHold());
    }

    public function test_the_terms_box_must_be_ticked(): void
    {
        Http::fake();

        $this->checkout('cod', ['accept_terms' => null])->assertUnprocessable()->assertJsonValidationErrors('accept_terms');
        $this->get('/checkout')->assertSee('name="accept_terms"', false)->assertSee('/page/return-refund-policy', false);
    }

    public function test_an_unknown_payment_method_is_rejected(): void
    {
        Http::fake();

        $this->checkout('bitcoin')->assertUnprocessable()->assertJsonValidationErrors('payment_method');
    }
}
