<?php

namespace App\Http\Controllers;

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
            'address' => ['required', 'string', 'max:1000'],
            'district' => ['required', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cod'],
            'items' => ['required', 'array', 'min:1', 'max:20'],
            'items.*.slug' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $products = Product::whereIn('slug', collect($data['items'])->pluck('slug'))
            ->where('in_stock', true)->get()->keyBy('slug');

        if ($products->count() !== count(collect($data['items'])->pluck('slug')->unique())) {
            throw ValidationException::withMessages(['items' => 'One or more selected products are unavailable.']);
        }

        $order = DB::transaction(function () use ($data, $products) {
            $subtotal = 0;
            $lines = [];
            foreach ($data['items'] as $item) {
                $product = $products[$item['slug']];
                $lineTotal = $product->price * $item['quantity'];
                $subtotal += $lineTotal;
                $lines[] = compact('product', 'item', 'lineTotal');
            }
            $shippingFee = 0;
            $order = Order::create([
                ...collect($data)->except('items')->all(),
                'order_number' => 'KG-' . now()->format('ymd') . '-' . strtoupper(str()->random(6)),
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
                    'unit_price' => $line['product']->price,
                    'quantity' => $line['item']['quantity'],
                    'line_total' => $line['lineTotal'],
                ]);
            }
            return $order;
        });

        return response()->json(['order_number' => $order->order_number]);
    }
}
