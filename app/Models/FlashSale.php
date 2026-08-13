<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashSale extends Model
{
    protected $fillable = ['title', 'starts_at', 'ends_at', 'is_active'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(FlashSaleProduct::class);
    }

    public function scopeLive(Builder $query): Builder
    {
        $now = now();

        return $query->where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now);
    }

    public function statusLabel(): string
    {
        if (! $this->is_active) {
            return 'Inactive';
        }
        $now = now();
        if ($now->lt($this->starts_at)) {
            return 'Upcoming';
        }
        if ($now->gt($this->ends_at)) {
            return 'Expired';
        }

        return 'Live';
    }
}
