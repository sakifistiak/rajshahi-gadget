<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'customer_name', 'phone', 'email', 'address',
        'district', 'note', 'payment_method', 'subtotal', 'shipping_fee',
        'total', 'status',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
