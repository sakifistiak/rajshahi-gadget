<?php

namespace App\Http\Controllers;

use App\Models\FlashSaleProduct;
use App\Models\Order;
use App\Models\Product;
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
            'division' => ['required_if:delivery_method,home_delivery', 'nullable', 'string', 'max:100'],
            'district' => ['required_if:delivery_method,home_delivery', 'nullable', 'string', 'max:100'],
            'upazila' => ['required_if:delivery_method,home_delivery', 'nullable', 'string', 'max:100'],
            'union_area' => ['nullable', 'string', 'max:150'],
            'address' => ['required_if:delivery_method,home_delivery', 'nullable', 'string', 'max:1000'],
            'store_location_id' => ['required_if:delivery_method,store_pickup', 'nullable', 'exists:store_locations,id'],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.slug' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $products = Product::whereIn('slug', collect($data['items'])->pluck('slug'))
            ->where(function ($q) {
                $q->where('in_stock', true)->orWhere('is_preorder', true);
            })
            ->get()->keyBy('slug');

        if ($products->count() !== count(collect($data['items'])->pluck('slug')->unique())) {
            throw ValidationException::withMessages(['items' => 'One or more selected products are unavailable.']);
        }

        $order = DB::transaction(function () use ($data, $products) {
            $subtotal = 0;
            $lines = [];
            $isPreorder = false;
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
                if ($product->is_preorder) {
                    $isPreorder = true;
                }
            }
            $shippingFee = 0;
            $order = Order::create([
                ...collect($data)->except('items')->all(),
                'order_number' => 'KG-' . now()->format('ymd') . '-' . strtoupper(str()->random(6)),
                'subtotal' => $subtotal,
                'shipping_fee' => $shippingFee,
                'total' => $subtotal + $shippingFee,
                'status' => 'pending',
                'is_preorder' => $isPreorder,
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
}
