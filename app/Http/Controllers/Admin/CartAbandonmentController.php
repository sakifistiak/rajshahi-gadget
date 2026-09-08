<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbandonedCart;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class CartAbandonmentController extends Controller
{
    public function index(Request $request)
    {
        // Lazily auto-detect any idle active carts that have crossed the threshold
        AbandonedCart::autoDetectAbandoned();

        $query = AbandonedCart::query();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $carts = $query->latest('last_activity_at')->paginate(15)->withQueryString();

        $groupedCounts = AbandonedCart::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $statusCounts = [
            'all' => array_sum($groupedCounts),
            'active' => $groupedCounts[AbandonedCart::STATUS_ACTIVE] ?? 0,
            'abandoned' => $groupedCounts[AbandonedCart::STATUS_ABANDONED] ?? 0,
            'reminded' => $groupedCounts[AbandonedCart::STATUS_REMINDED] ?? 0,
            'recovered' => $groupedCounts[AbandonedCart::STATUS_RECOVERED] ?? 0,
        ];

        $settingsKeys = [
            'cart_abandonment_enabled' => '1',
            'cart_abandonment_threshold_minutes' => '60',
            'cart_abandonment_resend_cooldown_hours' => '24',
            'cart_abandonment_sms_template' => "Hi {name}, you left {items} (৳{value}) in your cart at Khan Gadget. Complete your order before it's gone!",
        ];

        $settings = [];
        foreach ($settingsKeys as $key => $default) {
            $settings[$key] = SiteSetting::getValue($key, $default);
        }

        return view('admin.cart-abandonment.index', compact('carts', 'statusCounts', 'settings'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'cart_abandonment_threshold_minutes' => ['required', 'integer', 'min:5'],
            'cart_abandonment_resend_cooldown_hours' => ['required', 'integer', 'min:1'],
            'cart_abandonment_sms_template' => ['required', 'string', 'max:500'],
        ]);

        SiteSetting::setValue('cart_abandonment_enabled', $request->has('cart_abandonment_enabled') ? '1' : '0');
        SiteSetting::setValue('cart_abandonment_threshold_minutes', $request->input('cart_abandonment_threshold_minutes'));
        SiteSetting::setValue('cart_abandonment_resend_cooldown_hours', $request->input('cart_abandonment_resend_cooldown_hours'));
        SiteSetting::setValue('cart_abandonment_sms_template', $request->input('cart_abandonment_sms_template'));

        return redirect()->route('admin.cart-abandonment.index')->with('success', 'Abandoned cart settings updated successfully.');
    }

    public function show(AbandonedCart $abandonedCart)
    {
        $abandonedCart->load('order');

        return view('admin.cart-abandonment.show', ['cart' => $abandonedCart]);
    }

    public function markContacted(AbandonedCart $abandonedCart)
    {
        $abandonedCart->update(['contacted_at' => now()]);

        return redirect()->back()->with('success', 'Marked as contacted.');
    }

    public function destroy(AbandonedCart $abandonedCart)
    {
        $abandonedCart->delete();

        return redirect()->route('admin.cart-abandonment.index')->with('success', 'Abandoned cart record deleted.');
    }
}
