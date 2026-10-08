<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_name', 'phone', 'email', 'address',
        'delivery_area', 'note', 'payment_method', 'subtotal', 'shipping_fee',
        'total', 'status', 'delivery_method', 'store_location_id',
    ];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime'];
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /** Paid (or to be paid) through SSLCommerz, in full or by EMI. */
    public function isOnlinePayment(): bool
    {
        return in_array($this->payment_method, ['sslcommerz', 'sslcommerz_emi'], true);
    }

    public function isEmi(): bool
    {
        return $this->payment_method === 'sslcommerz_emi';
    }

    /** SSLCommerz flagged the payment (risk_level 1): verify the customer before delivering. */
    public function isPaymentOnHold(): bool
    {
        return $this->isPaid() && $this->payment_risk_title !== null;
    }

    public function paymentMethodLabel(): string
    {
        return match ($this->payment_method) {
            'sslcommerz' => 'Online Payment',
            'sslcommerz_emi' => 'EMI (Credit Card)',
            'cod' => 'Cash on Delivery',
            default => (string) $this->payment_method,
        };
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function storeLocation(): BelongsTo
    {
        return $this->belongsTo(StoreLocation::class);
    }
}
