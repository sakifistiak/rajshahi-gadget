<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\SslCommerz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * SSLCommerz sends the customer's browser back here (success / fail / cancel)
 * and also calls the IPN url server-to-server. These are cross-site POSTs, so
 * they are CSRF-exempt (bootstrap/app.php): an order is only ever marked paid
 * after the validator API confirms the val_id, order number and amount.
 */
class SslCommerzController extends Controller
{
    public function success(Request $request)
    {
        $order = $this->confirm($request);
        if (! $order) {
            return redirect('/checkout?payment=failed');
        }

        return redirect()->route('thank-you', ['order' => $order->order_number]);
    }

    public function fail(Request $request)
    {
        return $this->abandon($request, 'failed');
    }

    public function cancel(Request $request)
    {
        return $this->abandon($request, 'cancelled');
    }

    public function ipn(Request $request)
    {
        $order = $this->confirm($request);

        return response($order?->isPaid() ? 'OK' : 'IGNORED', 200)->header('Content-Type', 'text/plain');
    }

    private function findOrder(Request $request): ?Order
    {
        return Order::where('order_number', (string) $request->input('tran_id'))
            ->where('payment_method', 'sslcommerz')
            ->first();
    }

    /**
     * Validates the transaction with SSLCommerz and marks the order paid.
     * Safe to run twice (browser redirect and IPN both arrive).
     */
    private function confirm(Request $request): ?Order
    {
        $order = $this->findOrder($request);
        if (! $order || $order->isPaid()) {
            return $order;
        }

        $valId = (string) $request->input('val_id');
        $txn = $valId !== '' ? SslCommerz::validate($valId) : null;

        $currency = $txn['currency_type'] ?? $txn['currency'] ?? null;
        $amount = (float) ($txn['currency_amount'] ?? $txn['amount'] ?? 0);
        $matches = $txn
            && ($txn['tran_id'] ?? null) === $order->order_number
            && $currency === 'BDT'
            && abs($amount - $order->total) < 0.01;

        if (! $matches) {
            return $order;
        }

        return DB::transaction(function () use ($order, $txn) {
            $order = Order::whereKey($order->id)->lockForUpdate()->first();
            if (! $order->isPaid()) {
                $order->forceFill([
                    'payment_status' => 'paid',
                    'payment_val_id' => $txn['val_id'] ?? null,
                    'payment_bank_tran_id' => $txn['bank_tran_id'] ?? null,
                    'payment_card_type' => $txn['card_type'] ?? null,
                    'paid_at' => now(),
                    // A late success beats an earlier fail/cancel callback.
                    'status' => $order->status === 'cancelled' ? 'pending' : $order->status,
                ])->save();
            }

            return $order;
        });
    }

    private function abandon(Request $request, string $paymentStatus)
    {
        $order = $this->findOrder($request);
        if ($order && ! $order->isPaid()) {
            $order->forceFill(['payment_status' => $paymentStatus, 'status' => 'cancelled'])->save();
        }

        return redirect('/checkout?payment='.$paymentStatus);
    }
}
