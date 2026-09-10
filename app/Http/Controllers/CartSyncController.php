<?php

namespace App\Http\Controllers;

use App\Http\Middleware\EnsureCartToken;
use App\Models\AbandonedCart;
use App\Models\CartAddEvent;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartSyncController extends Controller
{
    /**
     * Called (debounced) from the storefront whenever the cart changes or the
     * checkout form's name/phone/email fields are typed into — lets the admin
     * panel's Abandoned Carts screen see and follow up on carts that never
     * reach checkout. Never touches order pricing/creation.
     */
    public function sync(Request $request): JsonResponse
    {
        $data = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:120'],
            'items' => ['present', 'array', 'max:20'],
            'items.*.slug' => ['required_with:items', 'string', 'max:255'],
            'items.*.quantity' => ['required_with:items', 'integer', 'min:1', 'max:99'],
            'items.*.name' => ['nullable', 'string', 'max:255'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
            'items.*.image' => ['nullable', 'string', 'max:2048'],
            'added_slug' => ['nullable', 'string', 'max:255'],
        ]);

        $token = $request->cookie(EnsureCartToken::COOKIE_NAME);
        if (! $token) {
            return response()->json(['ok' => false], 422);
        }

        if (! empty($data['added_slug'])) {
            $product = Product::where('slug', $data['added_slug'])->first(['id', 'slug', 'name']);
            if ($product) {
                CartAddEvent::create([
                    'product_id' => $product->id,
                    'product_slug' => $product->slug,
                    'product_name' => $product->name,
                    'cart_token' => $token,
                    'created_at' => now(),
                ]);
            }
        }

        $hasItems = ! empty($data['items']);
        $hasContact = ! empty($data['customer_name']) || ! empty($data['phone']) || ! empty($data['email']);

        $cart = AbandonedCart::where('cart_token', $token)->first();

        if (! $hasItems && ! $hasContact) {
            // Nothing left to track (cart emptied before any contact info was
            // captured) — drop the row instead of keeping an empty one around.
            $cart?->delete();

            return response()->json(['ok' => true]);
        }

        $cart ??= new AbandonedCart(['cart_token' => $token, 'status' => AbandonedCart::STATUS_ACTIVE]);

        if ($hasItems) {
            $cart->items = $data['items'];
            $cart->cart_value = (int) collect($data['items'])->sum(fn ($item) => ($item['price'] ?? 0) * $item['quantity']);
        }

        foreach (['customer_name', 'phone', 'email'] as $field) {
            if (! empty($data[$field])) {
                $cart->{$field} = $data[$field];
            }
        }

        // A visitor who's still actively shopping shouldn't stay flagged as
        // abandoned/reminded; a recovered cart is terminal, leave it alone.
        if (in_array($cart->status, [AbandonedCart::STATUS_ABANDONED, AbandonedCart::STATUS_REMINDED], true)) {
            $cart->status = AbandonedCart::STATUS_ACTIVE;
        }

        $cart->last_activity_at = now();
        $cart->save();

        return response()->json(['ok' => true]);
    }
}
