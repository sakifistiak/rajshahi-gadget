<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Support\SslCommerz;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class PaymentGatewaySettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'sslcommerz_enabled' => SiteSetting::getValue('sslcommerz_enabled', '1'),
            'sslcommerz_store_id' => SiteSetting::getValue('sslcommerz_store_id', ''),
            'sslcommerz_sandbox' => SiteSetting::getValue('sslcommerz_sandbox', '1'),
            // Never sent back to the browser: the form only shows whether one is saved.
            'has_password' => SiteSetting::getValue('sslcommerz_store_password') !== null,
            'sslcommerz_emi_enabled' => SiteSetting::getValue('sslcommerz_emi_enabled', '1'),
            'sslcommerz_emi_min_amount' => SslCommerz::emiMinAmount(),
            'sslcommerz_emi_max_instalment' => SslCommerz::emiMaxInstalment(),
        ];
        $active = SslCommerz::enabled();

        return view('admin.payment-gateway.index', compact('settings', 'active'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'sslcommerz_store_id' => 'nullable|string|max:100',
            'sslcommerz_store_password' => 'nullable|string|max:255',
            'sslcommerz_emi_min_amount' => 'nullable|integer|min:0|max:10000000',
            'sslcommerz_emi_max_instalment' => 'nullable|in:'.implode(',', SslCommerz::EMI_TENURES),
        ]);

        SiteSetting::setValue('sslcommerz_store_id', trim((string) $request->input('sslcommerz_store_id')) ?: null);
        SiteSetting::setValue('sslcommerz_enabled', $request->has('sslcommerz_enabled') ? '1' : '0');
        SiteSetting::setValue('sslcommerz_sandbox', $request->has('sslcommerz_sandbox') ? '1' : '0');
        SiteSetting::setValue('sslcommerz_emi_enabled', $request->has('sslcommerz_emi_enabled') ? '1' : '0');
        foreach (['sslcommerz_emi_min_amount', 'sslcommerz_emi_max_instalment'] as $key) {
            if ($request->filled($key)) {
                SiteSetting::setValue($key, (string) $request->integer($key));
            }
        }

        // A blank password field keeps the saved one; the remove box clears it.
        if ($request->boolean('remove_store_password')) {
            SiteSetting::setValue('sslcommerz_store_password', null);
        } elseif (filled($request->input('sslcommerz_store_password'))) {
            SiteSetting::setValue('sslcommerz_store_password', SslCommerz::encryptPassword(trim($request->input('sslcommerz_store_password'))));
        }

        return redirect()->back()->with('success', 'Payment gateway settings updated successfully!');
    }

    public function test(): RedirectResponse
    {
        if (! SslCommerz::enabled()) {
            return redirect()->back()->with('error', 'Turn SSLCommerz on and save a Store ID and Store Password first.');
        }

        try {
            $url = SslCommerz::smokeTest();
        } catch (RuntimeException $e) {
            return redirect()->back()->with('error', 'Connection failed: '.$e->getMessage());
        }

        return redirect()->back()
            ->with('success', 'Connection OK. SSLCommerz opened a 10 BDT test payment session (no order was created).')
            ->with('test_url', $url);
    }
}
