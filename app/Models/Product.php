<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'slug', 'name', 'brand_id', 'category_id', 'condition_id',
        'price', 'compare_at_price', 'rating', 'reviews_count',
        'badge', 'description', 'in_stock', 'warranty',
        'is_new_arrival', 'price_is_tba',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'compare_at_price' => 'integer',
            'rating' => 'decimal:1',
            'reviews_count' => 'integer',
            'in_stock' => 'boolean',
            'is_new_arrival' => 'boolean',
            'price_is_tba' => 'boolean',
        ];
    }

    public function scopeNewArrival(Builder $query): Builder
    {
        return $query->where('is_new_arrival', true);
    }

    /**
     * New Arrival products are sourced on demand rather than kept in stock —
     * the storefront shows "TBA" instead of In Stock/Stock Out and hides the
     * Buy Now flow in favor of a WhatsApp-only order path.
     */
    public function stockStatusLabel(): string
    {
        if ($this->is_new_arrival) {
            return 'TBA';
        }

        return $this->in_stock ? 'In Stock' : 'Stock Out';
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function condition(): BelongsTo
    {
        return $this->belongsTo(Condition::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function highlights(): HasMany
    {
        return $this->hasMany(ProductHighlight::class)->orderBy('sort_order');
    }

    public function specs(): HasMany
    {
        return $this->hasMany(ProductSpec::class)->orderBy('sort_order');
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class);
    }

    public function flashSaleProducts(): HasMany
    {
        return $this->hasMany(FlashSaleProduct::class);
    }

    public function filterValues(): HasMany
    {
        return $this->hasMany(ProductFilterValue::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function primaryImage(): string
    {
        return $this->images()->where('is_primary', true)->first()?->image_path
            ?? $this->images()->first()?->image_path
            ?? '/assets/no-image-placeholder.svg';
    }

    public function discount(): ?int
    {
        if (! $this->compare_at_price || $this->compare_at_price <= $this->price) {
            return null;
        }

        return $this->compare_at_price - $this->price;
    }
}
