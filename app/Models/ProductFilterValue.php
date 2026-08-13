<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductFilterValue extends Model
{
    protected $fillable = ['product_id', 'filter_attribute_id', 'numeric_value', 'text_value'];

    protected function casts(): array
    {
        return [
            'numeric_value' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function filterAttribute(): BelongsTo
    {
        return $this->belongsTo(FilterAttribute::class);
    }
}
