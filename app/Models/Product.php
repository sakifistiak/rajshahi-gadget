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
        'is_preorder', 'preorder_release_date', 'preorder_note',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'compare_at_price' => 'integer',
            'rating' => 'decimal:1',
            'reviews_count' => 'integer',
            'in_stock' => 'boolean',
            'is_preorder' => 'boolean',
            'preorder_release_date' => 'date',
        ];
    }

    public function scopePreorder(Builder $query): Builder
    {
        return $query->where('is_preorder', true);
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

    public function primaryImage(): string
    {
        return $this->images()->where('is_primary', true)->first()?->image_path
            ?? $this->images()->first()?->image_path
            ?? '/assets/laptop-ultrabook-C5nU_6_f.jpg';
    }

    public function discount(): ?int
    {
        if (!$this->compare_at_price || $this->compare_at_price <= $this->price) {
            return null;
        }
        return $this->compare_at_price - $this->price;
    }
}
