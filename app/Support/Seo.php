<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Throwable;

/**
 * Builds absolute, canonical-host URLs for canonical tags and the sitemap (SEO phase 1),
 * and the JSON-LD structured data blocks that sit next to the canonical tag (SEO phase 2).
 *
 * Deliberately does NOT use url()/asset()/$request->url(): on hosts where the
 * document root is the project folder, Laravel's base URL becomes "/public"
 * for direct hits on /public/..., and url() would leak that into the output.
 */
class Seo
{
    private const SCHEMA_CONTEXT = 'https://schema.org';

    /**
     * Condition slug => schema.org itemCondition. "Brand New Without Box" is NewCondition: the
     * store owner confirmed those units are unused (only the original box is missing).
     */
    private const ITEM_CONDITIONS = [
        'intact' => 'NewCondition',
        'without-box' => 'NewCondition',
        'pre-owned' => 'UsedCondition',
    ];

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

    /*
    |--------------------------------------------------------------------------
    | Structured data (JSON-LD)
    |--------------------------------------------------------------------------
    | Each builder returns one schema.org entity as an array. partials/canonical.blade.php
    | renders them through jsonLd(). Builders never take a page down: on any error they
    | report it and return no markup.
    */

    /** Absolute URL for a site-relative asset path such as "/media/logo.png". Absolute URLs pass through. */
    public static function absoluteUrl(?string $path, ?Request $request = null): ?string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $segments = array_map(
            fn (string $segment) => rawurlencode(rawurldecode($segment)),
            explode('/', ltrim($path, '/')),
        );

        return static::origin($request).'/'.implode('/', $segments);
    }

    /** Single-line plain text: no tags, no HTML entities, no decorative Unicode styling. */
    public static function text(?string $value): string
    {
        $value = html_entity_decode(strip_tags((string) $value), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim((string) preg_replace('/\s+/u', ' ', PlainText::flatten($value)));
    }

    /**
     * Keeps only URLs that can honestly go in sameAs: absolute http(s) profile pages. A bare
     * domain ("https://facebook.com") is not a profile, and WhatsApp chat links identify a
     * phone number rather than the organisation.
     *
     * @param  array<int, string|null>  $candidates
     * @return list<string>
     */
    public static function profileUrls(array $candidates): array
    {
        $urls = [];

        foreach ($candidates as $candidate) {
            $candidate = trim((string) $candidate);
            $parts = parse_url($candidate);

            if (! is_array($parts) || empty($parts['host'])
                || ! in_array(strtolower($parts['scheme'] ?? ''), ['http', 'https'], true)) {
                continue;
            }
            if (trim($parts['path'] ?? '', '/') === '') {
                continue;
            }
            if (preg_match('/(^|\.)(wa\.me|whatsapp\.com)$/i', $parts['host'])) {
                continue;
            }

            $urls[$candidate] = true;
        }

        return array_keys($urls);
    }

    /**
     * Site-wide Organization. The name, logo and profile URLs come from the same shared view
     * variables the footer uses, so the markup only ever lists what the site itself publishes.
     *
     * @param  array<int, string|null>  $profiles
     * @return array<string, mixed>
     */
    public static function organization(?string $name, ?string $logo, array $profiles = [], ?Request $request = null): array
    {
        return static::guard(fn () => static::prune([
            '@context' => self::SCHEMA_CONTEXT,
            '@type' => 'Organization',
            'name' => static::text($name),
            'url' => static::url('/', $request),
            'logo' => static::absoluteUrl($logo, $request),
            'sameAs' => static::profileUrls($profiles),
        ]));
    }

    /**
     * BreadcrumbList from ordered [name, path] pairs.
     *
     * @param  list<array{0: string, 1: string}>  $trail
     * @return array<string, mixed>
     */
    public static function breadcrumbs(array $trail, ?Request $request = null): array
    {
        $items = [];
        foreach (array_values($trail) as $index => [$name, $path]) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => static::text($name),
                'item' => static::url($path, $request),
            ];
        }

        return static::prune([
            '@context' => self::SCHEMA_CONTEXT,
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ]);
    }

    /**
     * Product with its Offer. Mirrors what the product page itself shows: new arrivals are
     * "Pre-Order" (and may have a TBA price), in-stock products show their price, and
     * out-of-stock products show no price, so their Offer has availability only.
     * There is no SKU column, so no sku is emitted, and no rating is emitted either.
     *
     * @return array<string, mixed>
     */
    public static function product(Product $product, ?Request $request = null): array
    {
        $url = static::url(static::path('product', $product->slug), $request);
        $condition = static::itemCondition($product->condition);

        return static::prune([
            '@context' => self::SCHEMA_CONTEXT,
            '@type' => 'Product',
            'name' => static::text($product->name),
            'image' => static::productImages($product, $request),
            'brand' => $product->brand
                ? ['@type' => 'Brand', 'name' => static::text($product->brand->name)]
                : null,
            'itemCondition' => $condition,
            'offers' => static::offer($product, $url, $condition),
        ]);
    }

    /** schema.org itemCondition URL for a product condition, or null for a condition we have no mapping for. */
    public static function itemCondition(?Condition $condition): ?string
    {
        $type = self::ITEM_CONDITIONS[$condition?->slug ?? ''] ?? null;

        return $type ? self::SCHEMA_CONTEXT.'/'.$type : null;
    }

    /**
     * Product + its breadcrumb trail (Home > Shop > product).
     *
     * @return list<array<string, mixed>>
     */
    public static function productSchemas(Product $product, ?Request $request = null): array
    {
        return static::guard(fn () => [
            static::product($product, $request),
            static::breadcrumbs([
                ['Home', '/'],
                ['Shop', '/shop'],
                [$product->name, static::path('product', $product->slug)],
            ], $request),
        ]);
    }

    /**
     * Breadcrumb for a category listing: Home > Shop > any parent categories > this category.
     *
     * @return list<array<string, mixed>>
     */
    public static function categorySchemas(Category $category, ?Request $request = null): array
    {
        return static::guard(function () use ($category, $request) {
            $trail = [];
            $node = $category;
            for ($depth = 0; $node && $depth < 6; $depth++) {
                array_unshift($trail, [$node->name, static::path('shop', $node->slug)]);
                $node = $node->parent;
            }

            return [static::breadcrumbs([['Home', '/'], ['Shop', '/shop'], ...$trail], $request)];
        });
    }

    /**
     * BlogPosting. Posts have no author field, so the site itself is the author.
     *
     * @return list<array<string, mixed>>
     */
    public static function blogPostSchemas(BlogPost $post, ?Request $request = null): array
    {
        return static::guard(fn () => [static::prune([
            '@context' => self::SCHEMA_CONTEXT,
            '@type' => 'BlogPosting',
            'headline' => static::text($post->title),
            'datePublished' => $post->published_at?->toDateString(),
            'dateModified' => $post->updated_at?->toAtomString(),
            'author' => [
                '@type' => 'Organization',
                'name' => static::text(SiteSetting::getValue('site_name', 'Khan Gadget')),
            ],
            'image' => static::absoluteUrl($post->featured_image, $request),
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => static::url(static::path('blog', $post->slug), $request),
            ],
        ])]);
    }

    /**
     * Renders schema arrays as <script type="application/ld+json"> blocks. "<" and "&" are
     * hex-escaped so a stray "</script>" in product data can never end the block early.
     *
     * @param  array<int, array<string, mixed>>  $schemas
     */
    public static function jsonLd(array $schemas): string
    {
        $html = '';

        foreach ($schemas as $schema) {
            if (! is_array($schema) || $schema === []) {
                continue;
            }

            $json = json_encode(
                $schema,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_INVALID_UTF8_SUBSTITUTE,
            );

            if ($json !== false) {
                $html .= '<script type="application/ld+json">'.$json.'</script>'."\n";
            }
        }

        return $html;
    }

    /** @return list<string> Primary image first, then the gallery order. */
    private static function productImages(Product $product, ?Request $request): array
    {
        $urls = $product->images
            ->sortByDesc('is_primary')
            ->map(fn ($image) => static::absoluteUrl($image->image_path, $request))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return $urls;
    }

    /** @return array<string, mixed>|null */
    private static function offer(Product $product, string $url, ?string $condition): ?array
    {
        // The page shows no price for an out-of-stock product, only "Out of Stock". Structured data
        // must match what is visible, so that Offer carries availability and no price.
        if (! $product->is_new_arrival && ! $product->in_stock) {
            return [
                '@type' => 'Offer',
                'url' => $url,
                'availability' => 'https://schema.org/OutOfStock',
                'itemCondition' => $condition,
            ];
        }

        // A TBA price is shown as "TBA" on the page, and an Offer without a price is invalid.
        if (($product->is_new_arrival && $product->price_is_tba) || (int) $product->price <= 0) {
            return null;
        }

        return [
            '@type' => 'Offer',
            'url' => $url,
            'priceCurrency' => 'BDT',
            'price' => (string) (int) $product->price,
            'availability' => $product->is_new_arrival
                ? 'https://schema.org/PreOrder'
                : 'https://schema.org/InStock',
            'itemCondition' => $condition,
        ];
    }

    /** Drops null, empty-string and empty-array values recursively; keeps lists as lists. */
    private static function prune(array $data): array
    {
        $isList = array_is_list($data);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = static::prune($value);
            }

            if ($value === null || $value === '' || $value === []) {
                unset($data[$key]);
            } else {
                $data[$key] = $value;
            }
        }

        return $isList ? array_values($data) : $data;
    }

    /** Structured data is an extra: an error while building it must never break the page. */
    private static function guard(callable $build): array
    {
        try {
            return $build();
        } catch (Throwable $e) {
            report($e);

            return [];
        }
    }
}
