<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AbandonedCart extends Model
{
    public const STATUS_ACTIVE = 'active';

    public const STATUS_ABANDONED = 'abandoned';

    public const STATUS_REMINDED = 'reminded';

    public const STATUS_RECOVERED = 'recovered';

    protected $fillable = [
        'cart_token',
        'customer_name',
        'phone',
        'email',
        'items',
        'cart_value',
        'status',
        'contacted_at',
        'reminded_at',
        'recovered_at',
        'order_id',
        'last_activity_at',
    ];

    protected function casts(): array
    {
        return [
            'items' => 'array',
            'contacted_at' => 'datetime',
            'reminded_at' => 'datetime',
            'recovered_at' => 'datetime',
            'last_activity_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
