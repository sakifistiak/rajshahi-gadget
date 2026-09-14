<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Link existing laptop sub-categories to Laptops without changing any
     * product category_id values. Safe to run against an existing database:
     * only matching categories with no current parent are updated.
     */
    public function up(): void
    {
        $categories = DB::table('categories')
            ->get(['id', 'slug', 'name', 'parent_id']);

        $find = function (array $slugs, array $names) use ($categories) {
            return $categories->first(function ($category) use ($slugs, $names) {
                $slug = Str::slug((string) $category->slug);
                $name = Str::slug((string) $category->name);

                return in_array($slug, $slugs, true) || in_array($name, $names, true);
            });
        };

        $parent = $find(['laptops', 'laptop'], ['laptops', 'laptop']);
        if (! $parent) {
            return;
        }

        $children = [
            ['gaming-series', 'gaming-laptops', 'gaming-laptop'],
            ['mobile-workstation', 'mobile-workstations'],
            ['macbook-series', 'macbook', 'macbooks'],
            ['premium-ultrabook', 'premium-ultrabooks'],
        ];

        foreach ($children as $slugs) {
            $child = $find($slugs, $slugs);
            if ($child && (int) $child->id !== (int) $parent->id && $child->parent_id === null) {
                DB::table('categories')
                    ->where('id', $child->id)
                    ->update(['parent_id' => $parent->id]);
            }
        }
    }

    public function down(): void
    {
        $categories = DB::table('categories')->get(['id', 'slug', 'name']);
        $parent = $categories->first(function ($category) {
            return in_array(Str::slug((string) $category->slug), ['laptops', 'laptop'], true)
                || in_array(Str::slug((string) $category->name), ['laptops', 'laptop'], true);
        });

        if (! $parent) {
            return;
        }

        $childSlugs = [
            'gaming-series', 'gaming-laptops', 'gaming-laptop',
            'mobile-workstation', 'mobile-workstations',
            'macbook-series', 'macbook', 'macbooks',
            'premium-ultrabook', 'premium-ultrabooks',
        ];

        DB::table('categories')
            ->where('parent_id', $parent->id)
            ->where(function ($query) use ($childSlugs) {
                foreach ($childSlugs as $index => $slug) {
                    $method = $index === 0 ? 'where' : 'orWhere';
                    $query->{$method}('slug', $slug);
                }
            })
            ->update(['parent_id' => null]);
    }
};
