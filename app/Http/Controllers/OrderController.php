<?php

namespace App\Http\Controllers;

use App\Models\FlashSaleProduct;
use App\Models\Order;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'delivery_method' => ['required', 'in:home_delivery,store_pickup'],
            'delivery_area' => ['required_if:delivery_method,home_delivery', 'nullable', 'in:inside_dhaka,outside_dhaka'],
            'address' => ['required_if:delivery_method,home_delivery', 'nullable', 'string', 'max:1000'],
            'store_location_id' => ['required_if:delivery_method,store_pickup', 'nullable', 'exists:store_locations,id'],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.slug' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        if (empty($data['payment_method'])) {
            $data['payment_method'] = ($data['delivery_method'] === 'store_pickup') ? 'Store Pickup' : 'cod';
        }

        $products = Product::whereIn('slug', collect($data['items'])->pluck('slug'))
            ->where('in_stock', true)
            ->get()->keyBy('slug');

        if ($products->count() !== count(collect($data['items'])->pluck('slug')->unique())) {
            throw ValidationException::withMessages(['items' => 'One or more selected products are unavailable.']);
        }

        $order = DB::transaction(function () use ($data, $products) {
            $subtotal = 0;
            $lines = [];
            foreach ($data['items'] as $item) {
                $product = $products[$item['slug']];
                $quantity = $item['quantity'];

                // Re-price from any live flash sale server-side — never trust a
                // price the client submits. Lock the row so concurrent orders
                // can't both claim the last few units of a limited stock deal.
                $flashSaleItem = FlashSaleProduct::where('product_id', $product->id)
                    ->whereHas('flashSale', fn ($q) => $q->live())
                    ->lockForUpdate()
                    ->first();

                $unitPrice = $product->price;
                if ($flashSaleItem) {
                    $hasStock = $flashSaleItem->stock_limit === null
                        || ($flashSaleItem->stock_limit - $flashSaleItem->sold_count) >= $quantity;

                    // If the flash stock ran out between page load and checkout,
                    // fall back to the regular price rather than blocking the
                    // order outright — the customer still gets the product.
                    if ($hasStock) {
                        $unitPrice = $flashSaleItem->priceFor($product->price);
                        $flashSaleItem->increment('sold_count', $quantity);
                    }
                }

                $lineTotal = $unitPrice * $quantity;
                $subtotal += $lineTotal;
                $lines[] = compact('product', 'item', 'lineTotal', 'unitPrice', 'quantity');
            }
            // Store pickup is always free. Home delivery is priced server-side
            // from the admin-configured settings, keyed on the customer's
            // selected delivery area — never trust a fee the client might submit.
            $shippingFee = 0;
            if ($data['delivery_method'] === 'home_delivery') {
                $shippingFee = ($data['delivery_area'] ?? null) === 'inside_dhaka'
                    ? (int) SiteSetting::getValue('shipping_fee_inside_dhaka', 70)
                    : (int) SiteSetting::getValue('shipping_fee_outside_dhaka', 130);
            }

            $order = Order::create([
                ...collect($data)->except('items')->all(),
                'order_number' => 'KG-'.now()->format('ymd').'-'.strtoupper(str()->random(6)),
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $subtotal + $shippingFee,
                'status' => 'pending',
            ]);
            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'product_slug' => $line['product']->slug,
                    'unit_price' => $line['unitPrice'],
                    'quantity' => $line['quantity'],
                    'line_total' => $line['lineTotal'],
                ]);
            }

            return $order;
        });

        return response()->json(['order_number' => $order->order_number]);
    }

    /**
     * Public, customer-facing invoice for an order — reachable straight from the
     * thank-you page by order number (the random suffix makes it unguessable).
     * Renders the same template the admin uses, in "public" mode so the internal
     * navigation is swapped for customer-appropriate actions.
     */
    public function invoice(Order $order, Request $request)
    {
        $order->load(['items', 'storeLocation']);

        $company = [
            'name' => SiteSetting::getValue('site_name', 'Khan Gadget'),
            'slogan' => SiteSetting::getValue('site_slogan', 'Brand NEW Intact BOX, Without BOX & Pre-Owned'),
            'logo' => SiteSetting::getValue('logo_light', '/media/b3ca13-kg-lockup-v2.png'),
            'phone' => SiteSetting::getValue('site_phone', '+8801700000000'),
            'whatsapp' => SiteSetting::getValue('whatsapp_number', '8801700000001'),
            'email' => SiteSetting::getValue('site_email', 'khangadget.bd@gmail.com'),
            'address' => SiteSetting::getValue('site_address', 'Level 4, House 12, Road 5, Dhanmondi, Dhaka 1205, Bangladesh'),
            'business_hours' => SiteSetting::getValue('site_business_hours', 'Sat – Thu · 10:00 AM – 9:00 PM'),
        ];

        return view('admin.orders.invoice', [
            'order' => $order,
            'company' => $company,
            'public' => true,
            'autoPrint' => $request->boolean('print'),
        ]);
    }
}
