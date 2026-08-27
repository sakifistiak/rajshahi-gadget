<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    protected const CACHE_KEY = 'site_settings.map';

    protected static ?array $map = null;

    protected static function booted(): void
    {
        static::saved(fn () => static::flushCache());
        static::deleted(fn () => static::flushCache());
    }

    protected static function map(): array
    {
        if (static::$map !== null) {
            return static::$map;
        }

        try {
            static::$map = Cache::rememberForever(
                static::CACHE_KEY,
                fn () => static::query()->pluck('value', 'key')->all()
            );
        } catch (\Throwable $e) {
            static::$map = [];
        }

        return static::$map;
    }

    public static function flushCache(): void
    {
        static::$map = null;

        try {
            Cache::forget(static::CACHE_KEY);
        } catch (\Throwable $e) {
        }
    }

    public static function getValue(string $key, $default = null): ?string
    {
        $val = static::map()[$key] ?? null;

        return ($val !== null && $val !== '') ? $val : $default;
    }

    public static function setValue(string $key, ?string $value): self
    {
        return static::updateOrCreate(['key' => $key], ['value' => $value]);
    }
}
