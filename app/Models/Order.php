<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_name', 'phone', 'email', 'address',
        'district', 'note', 'payment_method', 'subtotal', 'shipping_fee',
        'total', 'status', 'is_preorder',
    ];

    protected function casts(): array
    {
        return [
            'is_preorder' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
