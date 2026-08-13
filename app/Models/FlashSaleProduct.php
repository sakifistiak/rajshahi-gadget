<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleProduct extends Model
{
    protected $fillable = [
        'flash_sale_id', 'product_id', 'discount_type', 'sale_price',
        'percent_off', 'stock_limit', 'sold_count', 'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sale_price' => 'integer',
            'percent_off' => 'integer',
            'stock_limit' => 'integer',
            'sold_count' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function priceFor(int $originalPrice): int
    {
        if ($this->discount_type === 'percent_off') {
            return (int) round($originalPrice * (100 - $this->percent_off) / 100);
        }

        return (int) $this->sale_price;
    }

    public function remainingStock(): ?int
    {
        if ($this->stock_limit === null) {
            return null;
        }

        return max(0, $this->stock_limit - $this->sold_count);
    }

    public function isSoldOut(): bool
    {
        return $this->stock_limit !== null && $this->remainingStock() <= 0;
    }
}
