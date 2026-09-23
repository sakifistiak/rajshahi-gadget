<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * Builds absolute, canonical-host URLs for canonical tags and the sitemap.
 *
 * Deliberately does NOT use url()/asset()/$request->url(): on hosts where the
 * document root is the project folder, Laravel's base URL becomes "/public"
 * for direct hits on /public/..., and url() would leak that into the output.
 */
class Seo
{
    /** Configured canonical origin ("https://www.khangadget.com"), or null when unset/invalid. */
    public static function configuredOrigin(): ?string
    {
        $value = trim((string) config('app.canonical_url'));
        if ($value === '') {
            return null;
        }

        $parts = parse_url($value);
        if (! is_array($parts) || empty($parts['scheme']) || empty($parts['host'])) {
            return null;
        }

        return strtolower($parts['scheme']).'://'.strtolower($parts['host'])
            .(isset($parts['port']) ? ':'.$parts['port'] : '');
    }

    /** Canonical origin, falling back to the current request's own origin (local dev, tests). */
    public static function origin(?Request $request = null): string
    {
        return static::configuredOrigin() ?? ($request ?? request())->getSchemeAndHttpHost();
    }

    /** Absolute canonical URL for a path such as "/shop" (query string never included). */
    public static function url(string $path = '/', ?Request $request = null): string
    {
        $path = '/'.ltrim($path, '/');

        return static::origin($request).($path === '/' ? '/' : rtrim($path, '/'));
    }

    /** Builds a path from raw segments, percent-encoding each one: path('product', $slug). */
    public static function path(string ...$segments): string
    {
        return '/'.implode('/', array_map('rawurlencode', $segments));
    }

    /**
     * Canonical URL of the page being rendered: the current path without query string,
     * unless the caller passes a path (used for slug-keyed pages, so /product/FOO and
     * /product/foo don't each declare themselves canonical).
     */
    public static function canonicalUrl(?string $path = null, ?Request $request = null): string
    {
        $request ??= request();

        return static::url($path ?? '/'.$request->path(), $request);
    }
}
