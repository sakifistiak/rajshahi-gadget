<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Makes "Laptops" a main category and moves every laptop-type main category
 * (MacBook, Mobile Workstation, Gaming Series, Premium Ultrabook, ...) under
 * it as a sub category. Accessory categories (chargers, bags, mouse, RAM, ...)
 * stay main categories. Product category_id values are not touched: a product
 * in "Gaming Series" is now automatically under Laptops through its parent.
 * Anything the name rules get wrong can be fixed in Admin > Categories.
 */
return new class extends Migration
{
    private const LAPTOP_WORDS = [
        'laptop', 'macbook', 'workstation', 'gaming', 'ultrabook', 'notebook', 'chromebook', 'convertible',
        'thinkpad', 'thinkbook', 'ideapad', 'legion', 'zenbook', 'vivobook', 'rog', 'tuf', 'elitebook', 'probook',
        'pavilion', 'omen', 'envy', 'spectre', 'latitude', 'inspiron', 'vostro', 'xps', 'alienware', 'predator',
        'nitro', 'aspire', 'swift', 'surface', 'yoga',
    ];

    private const ACCESSORY_WORDS = [
        'bag', 'backpack', 'sleeve', 'charger', 'adapter', 'battery', 'batteries', 'stand', 'cooling', 'cooler', 'pad',
        'mousepad', 'mouse', 'keyboard', 'ram', 'memory', 'ssd', 'hdd', 'storage', 'hub', 'dock', 'webcam', 'camera',
        'headphone', 'headset', 'earphone', 'speaker', 'monitor', 'screen', 'protector', 'skin', 'cover', 'cable',
        'accessory', 'accessories', 'part', 'spare', 'stylus', 'pen',
    ];

    public function up(): void
    {
        $categories = DB::table('categories')->get(['id', 'slug', 'name', 'parent_id']);

        $laptops = $categories->first(fn ($c) => in_array(Str::slug($c->slug), ['laptops', 'laptop'], true)
            || in_array(Str::slug($c->name), ['laptops', 'laptop'], true));

        $parentIds = $categories->pluck('parent_id')->filter()->unique();
        $toMove = $categories->filter(fn ($c) => (int) $c->id !== (int) $laptops?->id
            && $c->parent_id === null
            // A main category that already has its own sub categories stays a main category.
            && ! $parentIds->contains($c->id)
            && $this->isLaptopType($c));

        if ($toMove->isEmpty()) {
            return; // nothing to nest (e.g. a fresh install): never create an empty Laptops
        }

        if (! $laptops) {
            $laptopsId = DB::table('categories')->insertGetId([
                'slug' => $categories->contains('slug', 'laptops') ? 'laptops-'.Str::lower(Str::random(4)) : 'laptops',
                'name' => 'Laptops',
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $laptopsId = $laptops->id;
            // Laptops itself must be a main category.
            DB::table('categories')->where('id', $laptopsId)->update(['parent_id' => null]);
        }

        DB::table('categories')->whereIn('id', $toMove->pluck('id'))->update(['parent_id' => $laptopsId, 'updated_at' => now()]);

        Cache::forget('nav.category_brands');
    }

    private function isLaptopType(object $category): bool
    {
        $words = collect(explode('-', Str::slug($category->slug.' '.$category->name)))
            ->filter()
            ->map(fn ($word) => rtrim($word, 's'));

        $matches = fn (array $list) => $words->intersect(array_map(fn ($w) => rtrim($w, 's'), $list))->isNotEmpty();

        return $matches(self::LAPTOP_WORDS) && ! $matches(self::ACCESSORY_WORDS);
    }

    public function down(): void
    {
        // Not reversed automatically: categories can be moved back from Admin > Categories.
    }
};
