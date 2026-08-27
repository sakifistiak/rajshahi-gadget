<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class StoreLocation extends Model
{
    protected const ACTIVE_CACHE_KEY = 'store_locations.active';

    protected static function booted(): void
    {
        static::saved(fn () => static::flushActiveCache());
        static::deleted(fn () => static::flushActiveCache());
    }

    public static function activeOrdered()
    {
        try {
            return Cache::rememberForever(
                static::ACTIVE_CACHE_KEY,
                fn () => static::where('is_active', true)->orderBy('sort_order', 'asc')->get()
            );
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public static function flushActiveCache(): void
    {
        try {
            Cache::forget(static::ACTIVE_CACHE_KEY);
        } catch (\Throwable $e) {
        }
    }

    protected $fillable = [
        'name',
        'address',
        'phone',
        'phone_link_type',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * The address is rich-text HTML from the admin editor. Stripping tags
     * directly runs adjacent block elements together (e.g. "</p><p>" leaves
     * no space), so block-level boundaries are turned into a space first.
     */
    public function plainAddress(): string
    {
        $withBreaks = preg_replace('/<(p|div|br|li)[^>]*>/i', ' ', $this->address ?? '');
        $withBreaks = preg_replace('/<\/(p|div|li)>/i', ' ', $withBreaks);

        return trim(preg_replace('/\s+/', ' ', strip_tags($withBreaks)));
    }
}
