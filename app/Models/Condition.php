<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

class Condition extends Model
{
    protected $fillable = ['slug', 'label', 'short', 'tagline', 'badge_active', 'badge_text', 'badge_color'];

    protected const BADGE_CACHE_KEY = 'conditions.badges';

    protected static ?array $badges = null;

    protected function casts(): array
    {
        return [
            'badge_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::flushBadgeCache());
        static::deleted(fn () => static::flushBadgeCache());
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Active product-card badges keyed by condition id. Cached like
     * SiteSetting so a grid of cards costs no extra queries.
     */
    public static function badgeMap(): array
    {
        if (static::$badges !== null) {
            return static::$badges;
        }

        try {
            static::$badges = Cache::rememberForever(static::BADGE_CACHE_KEY, fn () => static::query()
                ->where('badge_active', true)
                ->whereNotNull('badge_text')
                ->where('badge_text', '!=', '')
                ->get(['id', 'badge_text', 'badge_color'])
                ->mapWithKeys(fn ($c) => [$c->id => ['text' => $c->badge_text, 'color' => $c->badge_color]])
                ->all());
        } catch (\Throwable $e) {
            static::$badges = [];
        }

        return static::$badges;
    }

    public static function flushBadgeCache(): void
    {
        static::$badges = null;

        try {
            Cache::forget(static::BADGE_CACHE_KEY);
        } catch (\Throwable $e) {
        }
    }

    /**
     * Black or white text, whichever reads better on the badge colour.
     */
    public static function badgeTextColor(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) {
            return '#ffffff';
        }

        [$r, $g, $b] = array_map('hexdec', str_split($hex, 2));

        return (0.299 * $r + 0.587 * $g + 0.114 * $b) > 160 ? '#0f172a' : '#ffffff';
    }
}
