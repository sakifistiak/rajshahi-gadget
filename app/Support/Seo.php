<?php

namespace App\Support;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Condition;
use App\Models\CustomPage;
use App\Models\PhilanthropicWork;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Throwable;

/**
 * Builds absolute, canonical-host URLs for canonical tags and the sitemap (SEO phase 1),
 * the JSON-LD structured data blocks that sit next to the canonical tag (SEO phase 2), and the
 * page titles, meta descriptions and text cleaning behind them (SEO phase 3).
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

    /** Small capitals ("Dɪsᴄᴏᴜɴᴛ") that sellers paste in as styling; Unicode normalisation does not fold them. */
    private const SMALL_CAPS = [
        "\u{1D00}" => 'a', "\u{0299}" => 'b', "\u{1D04}" => 'c', "\u{1D05}" => 'd', "\u{1D07}" => 'e',
        "\u{A730}" => 'f', "\u{0262}" => 'g', "\u{029C}" => 'h', "\u{026A}" => 'i', "\u{1D0A}" => 'j',
        "\u{1D0B}" => 'k', "\u{029F}" => 'l', "\u{1D0D}" => 'm', "\u{0274}" => 'n', "\u{1D0F}" => 'o',
        "\u{1D18}" => 'p', "\u{A7AF}" => 'q', "\u{0280}" => 'r', "\u{A731}" => 's', "\u{1D1B}" => 't',
        "\u{1D1C}" => 'u', "\u{1D20}" => 'v', "\u{1D21}" => 'w', "\u{028F}" => 'y', "\u{1D22}" => 'z',
    ];

    /** Visible headline of the home page. The home <title> and its H1 both use it. */
    public const HOME_HEADLINE = 'Genuine Imported Laptops & Gadgets in Bangladesh';

    /** Longest <title> that search results show in full. */
    private const TITLE_LIMIT = 60;

    /** Longest meta description that search results show in full. */
    private const DESCRIPTION_LIMIT = 155;

    /**
     * One unique description per listing page, 140 to 160 characters. Pages that are not listed
     * here (cart, account and so on) fall back to the cleaned site-wide description.
     */
    private const PAGE_DESCRIPTIONS = [
        'home' => 'Khan Gadget is a genuine wholesaler and retailer of imported laptops, MacBooks and gadgets in Bangladesh since 2012. Intact box, without box and pre-owned.',
        'shop' => 'Shop genuine imported laptops, MacBooks and gadgets at Khan Gadget. Filter by brand, category and condition: brand new intact box, without box or pre-owned.',
        'blog' => 'Buying guides, laptop comparisons and tech tips from Khan Gadget, a genuine wholesaler and retailer of imported laptops and gadgets in Bangladesh since 2012.',
        'customer-feedback' => 'Honest words from verified Khan Gadget buyers across Bangladesh about their imported laptops and gadgets, delivery, warranty and after-sales service.',
        'customer-spotlight' => 'Real experiences and genuine smiles from Khan Gadget customers across Bangladesh. See who bought imported laptops and gadgets from us and what they have to say.',
        'philanthropic-work' => 'Every Khan Gadget sale contributes to humanity programs. See the philanthropic work our team and customers support across Bangladesh, and how to join in.',
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

    /**
     * Single-line plain text: no tags, no HTML entities (even double-encoded ones), no invisible
     * marks and no decorative Unicode styling (bold/italic/script letters, small capitals).
     */
    public static function text(?string $value): string
    {
        $value = strip_tags((string) $value);

        // Two passes undo "&amp;nbsp;", the double encoding left behind when entity text is pasted
        // into a field that is later escaped again.
        for ($pass = 0; $pass < 2; $pass++) {
            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        // Invisible marks (left-to-right/right-to-left marks, bidi controls, zero-width space, BOM,
        // soft hyphen). Zero-width joiners are kept: Bangla conjuncts need them.
        $value = (string) preg_replace('/[\x{00AD}\x{200B}\x{200E}\x{200F}\x{202A}-\x{202E}\x{2060}\x{FEFF}]/u', '', $value);

        // Compatibility normalisation folds the Mathematical Alphanumeric block (bold, italic, script,
        // fraktur, monospace), full-width and circled letters back to plain ones, and turns no-break
        // spaces into ordinary spaces. Bangla and accented letters stay composed.
        if (class_exists(\Normalizer::class)) {
            $value = \Normalizer::normalize($value, \Normalizer::FORM_KC) ?: $value;
        }

        $value = strtr($value, self::SMALL_CAPS);

        return trim((string) preg_replace('/\s+/u', ' ', $value));
    }

    /**
     * Plain-text excerpt of HTML, cut at a word boundary with an ellipsis when it is too long.
     * Block-level tags become spaces first, so adjacent paragraphs never run their words together.
     */
    public static function excerpt(?string $html, int $limit = 155): string
    {
        $spaced = (string) preg_replace('#</?(?:p|div|li|ul|ol|h[1-6]|tr|td|th|table|br|section|article|blockquote)\b[^>]*>#i', ' ', (string) $html);
        $text = static::text($spaced);

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return static::fit($text, $limit - 1).'…';
    }

    /** Cuts text to at most $limit characters at a word boundary, dropping dangling punctuation and joiner words. */
    public static function fit(string $text, int $limit): string
    {
        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        $cut = mb_substr($text, 0, $limit);
        // Only back up to a word boundary when the cut landed inside a word.
        if (! preg_match('/\s/u', mb_substr($text, $limit, 1)) && ($space = mb_strrpos($cut, ' ')) !== false && $space > $limit / 2) {
            $cut = mb_substr($cut, 0, $space);
        }

        $cut = (string) preg_replace('/(?:[\s,;:|\/&+\-–—·.]+|\s+(?:with|and|for|in|of|the|to|by)\b)+$/iu', '', $cut);

        return $cut;
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

    /*
    |--------------------------------------------------------------------------
    | On-page text (SEO phase 3): titles and meta descriptions
    |--------------------------------------------------------------------------
    | Everything here returns plain text. The templates print it with {{ }}, which escapes it
    | exactly once, so nothing is ever double-encoded.
    */

    /** Home page <title>: the site name plus the keyword headline. */
    public static function homeTitle(?string $siteName): string
    {
        return static::siteName($siteName).' — '.self::HOME_HEADLINE;
    }

    /** Unique description for a listing page, or the cleaned fallback when the page has none of its own. */
    public static function pageDescription(string $key, ?string $fallback = null): string
    {
        return self::PAGE_DESCRIPTIONS[$key] ?? static::text($fallback);
    }

    /**
     * Product <title>, at most about 60 characters: "{Brand} {name up to the room left} · {Condition} | {Site}".
     * The full name stays in the H1 and the body. It is cut at a word boundary.
     */
    public static function productTitle(Product $product, ?string $siteName): string
    {
        $suffix = ' | '.static::siteName($siteName);
        $condition = static::shortCondition($product->condition);
        $tail = ($condition !== null ? ' · '.$condition : '').$suffix;

        // A long site name leaves too little room for the product name: drop the condition first.
        if ($condition !== null && self::TITLE_LIMIT - mb_strlen($tail) < 25) {
            $tail = $suffix;
        }

        return static::fit(static::productName($product), max(self::TITLE_LIMIT - mb_strlen($tail), 20)).$tail;
    }

    /**
     * Product meta description, at most 155 characters, built from the structured data: name,
     * condition, price and up to three headline features. The pasted marketing description is not
     * used, so styled Unicode and stray entities in it can never reach the tag. The price is left
     * out whenever the page itself does not show it.
     */
    public static function productDescription(Product $product, ?string $siteName): string
    {
        $limit = self::DESCRIPTION_LIMIT;
        $name = static::productName($product);
        $description = static::fit($name, $limit - 1).'.';

        $parts = [];
        if ($condition = static::conditionLabel($product->condition)) {
            $parts[] = $condition.'.';
        }
        if (static::priceShown($product)) {
            $parts[] = 'Price: ৳ '.number_format((int) $product->price).'.';
        }

        $features = 0;
        foreach ($product->highlights as $highlight) {
            $feature = rtrim(static::text($highlight->text), " .;,\n");
            // Skip features the name already says.
            if ($feature === '' || mb_stripos($name, $feature) !== false) {
                continue;
            }
            $parts[] = $feature.'.';
            if (++$features === 3) {
                break;
            }
        }

        $parts[] = 'Buy at '.static::siteName($siteName).', Bangladesh.';

        foreach ($parts as $part) {
            if (mb_strlen($description.' '.$part) <= $limit) {
                $description .= ' '.$part;
            }
        }

        return $description;
    }

    /** Category listing <title>: "{Category} | {Site}", so it differs from the plain shop page. */
    public static function categoryTitle(Category $category, ?string $siteName): string
    {
        $suffix = ' | '.static::siteName($siteName);

        return static::fit(static::text($category->name), max(self::TITLE_LIMIT - mb_strlen($suffix), 20)).$suffix;
    }

    public static function categoryDescription(Category $category, ?string $siteName): string
    {
        return static::excerpt(
            'Shop genuine imported '.static::text($category->name).' at '.static::siteName($siteName)
            .'. Brand new intact box, without box and certified pre-owned, from a trusted wholesaler and retailer in Bangladesh.',
            160,
        );
    }

    /** Shop page <title>: the category's own title on category listings, "Shop | {Site}" otherwise. */
    public static function shopTitle(?Category $category, ?string $siteName): string
    {
        return $category ? static::categoryTitle($category, $siteName) : 'Shop | '.static::siteName($siteName);
    }

    /** Shop page description: the category's own on category listings, the shop's otherwise. */
    public static function shopDescription(?Category $category, ?string $siteName, ?string $fallback = null): string
    {
        return $category ? static::categoryDescription($category, $siteName) : static::pageDescription('shop', $fallback);
    }

    /** Philanthropic work description: an excerpt of its own text, else a line built from its title. */
    public static function philanthropicWorkDescription(PhilanthropicWork $work, ?string $siteName): string
    {
        $excerpt = static::excerpt($work->content, self::DESCRIPTION_LIMIT);

        return $excerpt !== ''
            ? $excerpt
            : static::excerpt(static::text($work->title).': philanthropic work supported by '.static::siteName($siteName).' in Bangladesh.', self::DESCRIPTION_LIMIT);
    }

    /** CMS page description: the editor's own meta description, else an excerpt of the page content. */
    public static function customPageDescription(CustomPage $page): string
    {
        $own = static::text($page->meta_description);

        return $own !== '' ? $own : static::excerpt($page->content, self::DESCRIPTION_LIMIT);
    }

    private static function siteName(?string $siteName): string
    {
        return static::text($siteName ?? SiteSetting::getValue('site_name', 'Khan Gadget'));
    }

    /** "{Brand} {name}": titles start with the brand, so add it when the name does not already say it. */
    private static function productName(Product $product): string
    {
        $name = static::text($product->name);
        $brand = static::text($product->brand?->name);

        return ($brand !== '' && mb_stripos($name, $brand) === false) ? $brand.' '.$name : $name;
    }

    /** Condition label in title case: "Brand New Intact Box", "Pre-Owned". */
    private static function conditionLabel(?Condition $condition): ?string
    {
        $label = static::text($condition?->label);

        return $label === '' ? null : mb_convert_case(mb_strtolower($label), MB_CASE_TITLE, 'UTF-8');
    }

    /** Condition without its "Brand New" lead-in, for titles: "Intact Box", "Without Box", "Pre-Owned". */
    private static function shortCondition(?Condition $condition): ?string
    {
        $label = static::conditionLabel($condition);
        $short = $label === null ? '' : trim((string) preg_replace('/^Brand New\s+/i', '', $label));

        return $short === '' ? null : $short;
    }

    /** Whether the product page itself shows a price: not for out-of-stock items or TBA new arrivals. */
    private static function priceShown(Product $product): bool
    {
        if ((int) $product->price <= 0) {
            return false;
        }

        return $product->is_new_arrival ? ! $product->price_is_tba : (bool) $product->in_stock;
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
