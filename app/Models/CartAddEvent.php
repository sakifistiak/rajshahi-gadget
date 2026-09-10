<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CartAddEvent extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'product_id',
        'product_slug',
        'product_name',
        'cart_token',
        'created_at',
    ];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function scopePeriod(Builder $query, string $period = '7d'): Builder
    {
        return match ($period) {
            'today' => $query->whereDate('created_at', today()),
            'yesterday' => $query->whereDate('created_at', today()->subDay()),
            '7d' => $query->where('created_at', '>=', now()->subDays(7)),
            '30d' => $query->where('created_at', '>=', now()->subDays(30)),
            'month' => $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
            'all' => $query,
            default => $query->where('created_at', '>=', now()->subDays(7)),
        };
    }
}
