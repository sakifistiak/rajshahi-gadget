<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['slug', 'name', 'tagline', 'image', 'item_count', 'parent_id', 'sort_order'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * This category's id followed by its parent's, grandparent's, ... A product
     * saved under a sub category also uses its main category's filters.
     *
     * @return array<int, int>
     */
    public function selfAndAncestorIds(): array
    {
        $ids = [];
        $category = $this;
        while ($category && ! in_array($category->id, $ids, true) && count($ids) < 10) {
            $ids[] = $category->id;
            $category = $category->parent;
        }

        return $ids;
    }

    public function filterAttributes(): HasMany
    {
        return $this->hasMany(FilterAttribute::class)->orderBy('sort_order');
    }
}
