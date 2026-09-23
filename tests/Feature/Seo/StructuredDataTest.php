<?php

namespace Tests\Feature\Seo;

use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Exceptions;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * SEO phase 2: JSON-LD builders on App\Support\Seo. These use in-memory models, so none of
 * them touch the database.
 */
class StructuredDataTest extends TestCase
{
    private const ORIGIN = 'https://www.khangadget.com';

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.canonical_url' => self::ORIGIN]);
    }

    private function product(array $attributes = [], ?string $conditionSlug = 'intact', bool $withBrand = true, bool $withImages = true): Product
    {
        $product = Product::make(array_merge([
            'slug' => 'acer-predator-helios',
            'name' => 'Acer Predator Helios 300',
            'price' => 320000,
            'in_stock' => true,
            'is_new_arrival' => false,
            'price_is_tba' => false,
        ], $attributes));

        $product->setRelation('brand', $withBrand ? new Brand(['name' => 'Acer']) : null);
        $product->setRelation('condition', $conditionSlug ? new Condition(['slug' => $conditionSlug]) : null);
        $product->setRelation('images', collect($withImages ? [
            new ProductImage(['image_path' => '/media/second.jpg', 'is_primary' => false, 'sort_order' => 1]),
            new ProductImage(['image_path' => '/media/main image.jpg', 'is_primary' => true, 'sort_order' => 2]),
        ] : []));

        return $product;
    }

    /** @return array<string, array{0: ?string, 1: ?string}> */
    public static function conditionMap(): array
    {
        return [
            'intact box' => ['intact', 'https://schema.org/NewCondition'],
            'without box' => ['without-box', 'https://schema.org/NewCondition'],
            'pre-owned' => ['pre-owned', 'https://schema.org/UsedCondition'],
            'unknown condition makes no claim' => ['open-box-clearance', null],
            'no condition' => [null, null],
        ];
    }

    #[DataProvider('conditionMap')]
    public function test_condition_slugs_map_to_schema_org_item_conditions(?string $slug, ?string $expected): void
    {
        $condition = $slug ? new Condition(['slug' => $slug]) : null;

        $this->assertSame($expected, Seo::itemCondition($condition));
    }

    public function test_product_schema_matches_the_brief(): void
    {
        $schema = Seo::product($this->product());

        $this->assertSame([
            '@context' => 'https://schema.org',
            '@type' => 'Product',
            'name' => 'Acer Predator Helios 300',
            'image' => [self::ORIGIN.'/media/main%20image.jpg', self::ORIGIN.'/media/second.jpg'],
            'brand' => ['@type' => 'Brand', 'name' => 'Acer'],
            'itemCondition' => 'https://schema.org/NewCondition',
            'offers' => [
                '@type' => 'Offer',
                'url' => self::ORIGIN.'/product/acer-predator-helios',
                'priceCurrency' => 'BDT',
                'price' => '320000',
                'availability' => 'https://schema.org/InStock',
                'itemCondition' => 'https://schema.org/NewCondition',
            ],
        ], $schema);
    }

    public function test_product_schema_never_invents_a_sku_or_ratings(): void
    {
        $schema = Seo::product($this->product());

        foreach (['sku', 'mpn', 'aggregateRating', 'review', 'description'] as $key) {
            $this->assertArrayNotHasKey($key, $schema);
        }
    }

    /** @return array<string, array{0: array<string, mixed>, 1: string}> */
    public static function availabilityCases(): array
    {
        return [
            'in stock' => [['in_stock' => true], 'https://schema.org/InStock'],
            'out of stock' => [['in_stock' => false], 'https://schema.org/OutOfStock'],
            'new arrival is shown as Pre-Order' => [['is_new_arrival' => true, 'in_stock' => false], 'https://schema.org/PreOrder'],
        ];
    }

    #[DataProvider('availabilityCases')]
    public function test_availability_follows_what_the_product_page_shows(array $attributes, string $expected): void
    {
        $schema = Seo::product($this->product($attributes));

        $this->assertSame($expected, $schema['offers']['availability']);
    }

    public function test_offer_is_left_out_when_the_price_is_tba_or_missing(): void
    {
        $tba = Seo::product($this->product(['is_new_arrival' => true, 'price_is_tba' => true]));
        $free = Seo::product($this->product(['price' => 0]));

        $this->assertArrayNotHasKey('offers', $tba);
        $this->assertArrayNotHasKey('offers', $free);
        $this->assertSame('Acer Predator Helios 300', $tba['name']);
    }

    public function test_tba_flag_is_ignored_when_the_page_shows_a_real_price(): void
    {
        // The page only shows "TBA" for new arrivals; any other product shows its price.
        $schema = Seo::product($this->product(['price_is_tba' => true, 'is_new_arrival' => false]));

        $this->assertSame('320000', $schema['offers']['price']);
    }

    public function test_out_of_stock_offer_carries_availability_but_no_price(): void
    {
        // The product page shows only "Out of Stock" for these, so the markup must not state a price.
        $schema = Seo::product($this->product(['in_stock' => false]));

        $this->assertSame([
            '@type' => 'Offer',
            'url' => self::ORIGIN.'/product/acer-predator-helios',
            'availability' => 'https://schema.org/OutOfStock',
            'itemCondition' => 'https://schema.org/NewCondition',
        ], $schema['offers']);
    }

    public function test_out_of_stock_offer_does_not_depend_on_the_stored_price(): void
    {
        $schema = Seo::product($this->product(['in_stock' => false, 'price' => 0]));

        $this->assertSame('https://schema.org/OutOfStock', $schema['offers']['availability']);
        $this->assertArrayNotHasKey('price', $schema['offers']);
        $this->assertArrayNotHasKey('priceCurrency', $schema['offers']);
    }

    public function test_offers_that_show_a_price_on_the_page_still_state_it(): void
    {
        $inStock = Seo::product($this->product(['in_stock' => true]));
        // A new arrival shows its price (Pre-Order), even when the stock flag is off.
        $preOrder = Seo::product($this->product(['is_new_arrival' => true, 'in_stock' => false]));

        foreach ([$inStock, $preOrder] as $schema) {
            $this->assertSame('320000', $schema['offers']['price']);
            $this->assertSame('BDT', $schema['offers']['priceCurrency']);
        }
    }

    public function test_missing_brand_images_and_condition_are_omitted_not_emptied(): void
    {
        $schema = Seo::product($this->product([], null, false, false));

        foreach (['brand', 'image', 'itemCondition'] as $key) {
            $this->assertArrayNotHasKey($key, $schema);
        }
        $this->assertArrayNotHasKey('itemCondition', $schema['offers']);
    }

    public function test_product_name_is_plain_text(): void
    {
        $decorative = Seo::product($this->product(['name' => '𝐀𝐜𝐞𝐫 𝐏𝐫𝐞𝐝𝐚𝐭𝐨𝐫 𝟑𝟎𝟎']));
        $markup = Seo::product($this->product(['name' => "Dell &amp; <b>HP</b>\n  Laptop"]));

        $this->assertSame('Acer Predator 300', $decorative['name']);
        $this->assertSame('Dell & HP Laptop', $markup['name']);
    }

    public function test_absolute_url_handles_relative_absolute_and_awkward_paths(): void
    {
        $this->assertSame(self::ORIGIN.'/media/logo.png', Seo::absoluteUrl('/media/logo.png'));
        $this->assertSame(self::ORIGIN.'/media/logo.png', Seo::absoluteUrl('media/logo.png'));
        $this->assertSame('https://cdn.example.com/a.jpg', Seo::absoluteUrl('https://cdn.example.com/a.jpg'));
        $this->assertSame(self::ORIGIN.'/uploads/my%20photo.jpg', Seo::absoluteUrl('/uploads/my photo.jpg'));
        $this->assertSame(self::ORIGIN.'/uploads/my%20photo.jpg', Seo::absoluteUrl('/uploads/my%20photo.jpg'), 'Must not double-encode.');
        $this->assertNull(Seo::absoluteUrl(''));
        $this->assertNull(Seo::absoluteUrl(null));
    }

    public function test_breadcrumbs_are_numbered_and_use_canonical_urls(): void
    {
        $schema = Seo::breadcrumbs([['Home', '/'], ['Shop', '/shop'], ['Acer Predator', Seo::path('product', 'acer predator')]]);

        $this->assertSame('BreadcrumbList', $schema['@type']);
        $this->assertSame([
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => self::ORIGIN.'/'],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Shop', 'item' => self::ORIGIN.'/shop'],
            ['@type' => 'ListItem', 'position' => 3, 'name' => 'Acer Predator', 'item' => self::ORIGIN.'/product/acer%20predator'],
        ], $schema['itemListElement']);
    }

    public function test_product_page_gets_a_product_and_a_breadcrumb_schema(): void
    {
        [$product, $breadcrumbs] = Seo::productSchemas($this->product());

        $this->assertSame('Product', $product['@type']);
        $this->assertSame(['Home', 'Shop', 'Acer Predator Helios 300'], array_column($breadcrumbs['itemListElement'], 'name'));
        $this->assertSame(self::ORIGIN.'/product/acer-predator-helios', $breadcrumbs['itemListElement'][2]['item']);
    }

    public function test_category_breadcrumb_includes_parent_categories(): void
    {
        $parent = Category::make(['slug' => 'laptops', 'name' => 'Laptops'])->setRelation('parent', null);
        $child = Category::make(['slug' => 'gaming-laptops', 'name' => 'Gaming Laptops'])->setRelation('parent', $parent);

        [$breadcrumbs] = Seo::categorySchemas($child);

        $this->assertSame(['Home', 'Shop', 'Laptops', 'Gaming Laptops'], array_column($breadcrumbs['itemListElement'], 'name'));
        $this->assertSame(self::ORIGIN.'/shop/gaming-laptops', $breadcrumbs['itemListElement'][3]['item']);
    }

    public function test_a_category_parent_loop_cannot_hang_the_page(): void
    {
        $a = Category::make(['slug' => 'a', 'name' => 'A']);
        $b = Category::make(['slug' => 'b', 'name' => 'B']);
        $a->setRelation('parent', $b);
        $b->setRelation('parent', $a);

        [$breadcrumbs] = Seo::categorySchemas($a);

        $this->assertLessThanOrEqual(8, count($breadcrumbs['itemListElement']));
    }

    public function test_blog_posting_schema(): void
    {
        $post = BlogPost::make(['slug' => 'buying-guide', 'title' => 'Buying guide', 'featured_image' => '/assets/guide.jpg']);
        $post->forceFill(['published_at' => '2026-09-01', 'updated_at' => '2026-09-05 10:00:00']);

        [$schema] = Seo::blogPostSchemas($post);

        $this->assertSame('BlogPosting', $schema['@type']);
        $this->assertSame('Buying guide', $schema['headline']);
        $this->assertSame('2026-09-01', $schema['datePublished']);
        $this->assertMatchesRegularExpression('/^2026-09-05T10:00:00[+-]\d\d:\d\d$/', $schema['dateModified']);
        $this->assertSame(['@type' => 'Organization', 'name' => 'Khan Gadget'], $schema['author']);
        $this->assertSame(self::ORIGIN.'/assets/guide.jpg', $schema['image']);
        $this->assertSame(['@type' => 'WebPage', '@id' => self::ORIGIN.'/blog/buying-guide'], $schema['mainEntityOfPage']);
    }

    public function test_blog_posting_omits_the_image_when_there_is_none(): void
    {
        $post = BlogPost::make(['slug' => 'no-image', 'title' => 'No image']);
        $post->forceFill(['published_at' => '2026-09-01']);

        [$schema] = Seo::blogPostSchemas($post);

        $this->assertArrayNotHasKey('image', $schema);
    }

    public function test_organization_lists_only_real_profile_urls(): void
    {
        $schema = Seo::organization('Khan Gadget', '/media/b3ca13-kg-lockup-v2.png', [
            'https://facebook.com/khansgadget',
            'https://youtube.com/@khansgadget',
            'https://facebook.com',
            'https://facebook.com/',
            'https://wa.me/8801700000001',
            'https://api.whatsapp.com/send?phone=8801700000001',
            'ftp://example.com/khan',
            'not a url',
            '',
            null,
            'https://facebook.com/khansgadget',
        ]);

        $this->assertSame([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Khan Gadget',
            'url' => self::ORIGIN.'/',
            'logo' => self::ORIGIN.'/media/b3ca13-kg-lockup-v2.png',
            'sameAs' => ['https://facebook.com/khansgadget', 'https://youtube.com/@khansgadget'],
        ], $schema);
    }

    public function test_organization_drops_same_as_when_no_profile_is_usable(): void
    {
        $schema = Seo::organization('Khan Gadget', null, ['https://facebook.com', null, '']);

        $this->assertArrayNotHasKey('sameAs', $schema);
        $this->assertArrayNotHasKey('logo', $schema);
    }

    public function test_json_ld_output_cannot_be_broken_out_of(): void
    {
        $product = $this->product(['name' => 'Dell "XPS" & 13\' a < b']);
        $html = Seo::jsonLd([Seo::product($product)]);

        preg_match('#^<script type="application/ld\+json">(.*)</script>\n$#s', $html, $match);
        $this->assertNotEmpty($match, 'One well-formed script block expected.');
        $this->assertStringNotContainsString('<', $match[1], 'No raw "<" may appear inside the block.');
        $this->assertSame('Dell "XPS" & 13\' a < b', json_decode($match[1], true)['name'], 'The value must survive a round trip.');
    }

    public function test_json_ld_survives_invalid_utf8_and_skips_empty_schemas(): void
    {
        $html = Seo::jsonLd([[], ['@type' => 'Thing', 'name' => "bad \xB1 bytes"], []]);

        $this->assertSame(1, substr_count($html, '<script type="application/ld+json">'));
        $this->assertNotNull(json_decode(strip_tags($html), true));
    }

    public function test_a_failing_builder_reports_the_error_and_returns_no_markup(): void
    {
        Exceptions::fake();
        $product = $this->product();
        $product->setRelation('images', 'not a collection');

        $this->assertSame([], Seo::productSchemas($product));
        Exceptions::assertReportedCount(1);
    }

    public function test_urls_follow_the_request_origin_when_no_canonical_url_is_configured(): void
    {
        config(['app.canonical_url' => null]);

        $schema = Seo::organization('Khan Gadget', '/media/logo.png', [], Request::create('http://localhost/shop'));

        $this->assertSame('http://localhost/', $schema['url']);
        $this->assertSame('http://localhost/media/logo.png', $schema['logo']);
    }
}
