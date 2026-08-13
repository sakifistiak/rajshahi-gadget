<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FilterAttribute extends Model
{
    protected $fillable = ['category_id', 'key', 'match_labels', 'label', 'unit', 'type', 'options', 'sort_order'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(ProductFilterValue::class);
    }

    public function optionList(): array
    {
        return $this->splitCommaList($this->options);
    }

    public function matchLabelList(): array
    {
        $labels = $this->splitCommaList($this->match_labels);

        return $labels ?: [$this->label];
    }

    private function splitCommaList(?string $value): array
    {
        if (! $value) {
            return [];
        }

        return array_values(array_filter(array_map('trim', explode(',', $value))));
    }
}
