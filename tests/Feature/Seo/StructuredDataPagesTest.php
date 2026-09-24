<?php

namespace Tests\Feature\Seo;

use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * SEO phase 2 on real rendered pages: every public page carries the Organization block, and
 * product, category and blog pages add their own. Needs a real schema, so it uses
 * RefreshDatabase like the rest of the DB-backed suite.
 */
class StructuredDataPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.canonical_url' => null]);
        // SiteSetting keeps a static in-process map that survives the per-test database rollback.
        SiteSetting::flushCache();
    }

    private function product(string $slug = 'acer-predator', array $attributes = [], string $condition = 'intact'): Product
    {
        $brand = Brand::query()->firstOrCreate(['slug' => 'acer'], ['name' => 'Acer']);
        $conditionModel = Condition::query()->firstOrCreate(
            ['slug' => $condition],
            ['label' => strtoupper($condition), 'short' => 'X', 'tagline' => 'Tagline'],
        );
        $category = Category::query()->firstOrCreate(['slug' => 'laptops'], ['name' => 'Laptops']);

        return Product::forceCreate(array_merge([
            'slug' => $slug,
            'name' => 'Acer '.$slug,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'condition_id' => $conditionModel->id,
            'price' => 100000,
            'description' => 'A laptop.',
        ], $attributes));
    }

    /** @return list<array<string, mixed>> Every JSON-LD block on the page, decoded. */
    private function blocks(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $matches);

        return array_map(fn (string $json) => json_decode($json, true, 512, JSON_THROW_ON_ERROR), $matches[1]);
    }

    /** @return list<string> */
    private function types(string $html): array
    {
        return array_column($this->blocks($html), '@type');
    }

    private function block(string $html, string $type): array
    {
        foreach ($this->blocks($html) as $block) {
            if (($block['@type'] ?? null) === $type) {
                return $block;
            }
        }

        $this->fail("No $type block found.");
    }

    public function test_product_page_has_organization_product_and_breadcrumb_blocks(): void
    {
        $this->product('acer-predator');

        $html = $this->get('/product/acer-predator')->assertOk()->getContent();

        $this->assertSame(['Organization', 'Product', 'BreadcrumbList'], $this->types($html));
        $this->assertGreaterThanOrEqual(2, substr_count($html, 'application/ld+json'), 'The brief expects at least 2 blocks on product pages.');

        $product = $this->block($html, 'Product');
        $this->assertSame('Acer acer-predator', $product['name']);
        $this->assertSame('Acer', $product['brand']['name']);
        $this->assertSame('https://schema.org/NewCondition', $product['itemCondition']);
        $this->assertSame('100000', $product['offers']['price']);
        $this->assertSame('BDT', $product['offers']['priceCurrency']);
        $this->assertSame('https://schema.org/InStock', $product['offers']['availability']);

        // The Offer URL, the breadcrumb leaf and the canonical tag all agree.
        preg_match('/<link rel="canonical" href="([^"]+)"/', $html, $canonical);
        $this->assertSame($canonical[1], $product['offers']['url']);
        $this->assertSame($canonical[1], $this->block($html, 'BreadcrumbList')['itemListElement'][2]['item']);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function conditionPages(): array
    {
        return [
            'intact box' => ['intact', 'https://schema.org/NewCondition'],
            'without box' => ['without-box', 'https://schema.org/NewCondition'],
            'pre-owned' => ['pre-owned', 'https://schema.org/UsedCondition'],
        ];
    }

    #[DataProvider('conditionPages')]
    public function test_each_condition_renders_its_item_condition_on_product_and_offer(string $slug, string $expected): void
    {
        $this->product('cond-test', [], $slug);

        $product = $this->block($this->get('/product/cond-test')->assertOk()->getContent(), 'Product');

        $this->assertSame($expected, $product['itemCondition']);
        $this->assertSame($expected, $product['offers']['itemCondition']);
    }

    public function test_a_condition_without_a_mapping_emits_no_item_condition(): void
    {
        $this->product('cond-test', [], 'open-box-clearance');

        $product = $this->block($this->get('/product/cond-test')->assertOk()->getContent(), 'Product');

        $this->assertArrayNotHasKey('itemCondition', $product);
        $this->assertArrayNotHasKey('itemCondition', $product['offers']);
    }

    public function test_out_of_stock_and_new_arrival_products_render_the_matching_availability(): void
    {
        $this->product('sold-out', ['in_stock' => false]);
        $this->product('coming-soon', ['is_new_arrival' => true, 'in_stock' => false]);
        $this->product('tba-price', ['is_new_arrival' => true, 'price_is_tba' => true]);

        $sold = $this->block($this->get('/product/sold-out')->assertOk()->getContent(), 'Product');
        $soon = $this->block($this->get('/product/coming-soon')->assertOk()->getContent(), 'Product');
        $tba = $this->block($this->get('/product/tba-price')->assertOk()->getContent(), 'Product');

        $this->assertSame('https://schema.org/OutOfStock', $sold['offers']['availability']);
        $this->assertSame('https://schema.org/PreOrder', $soon['offers']['availability']);
        $this->assertArrayNotHasKey('offers', $tba);
    }

    public function test_the_price_in_the_markup_matches_the_price_the_page_shows(): void
    {
        // Distinct prices, so a price on one page cannot be mistaken for a related product's.
        $this->product('in-stock-item', ['price' => 111111]);
        $this->product('sold-out-item', ['price' => 222222, 'in_stock' => false]);
        $this->product('preorder-item', ['price' => 333333, 'is_new_arrival' => true, 'in_stock' => false]);

        $inStock = $this->get('/product/in-stock-item')->assertOk()->getContent();
        $soldOut = $this->get('/product/sold-out-item')->assertOk()->getContent();
        $preOrder = $this->get('/product/preorder-item')->assertOk()->getContent();

        // In stock: the price is visible and in the markup.
        $this->assertStringContainsString('৳ 111,111', $inStock);
        $this->assertSame('111111', $this->block($inStock, 'Product')['offers']['price']);

        // Out of stock: the page shows no price, so neither does the markup.
        $this->assertStringNotContainsString('222,222', $soldOut);
        $offer = $this->block($soldOut, 'Product')['offers'];
        $this->assertSame('https://schema.org/OutOfStock', $offer['availability']);
        $this->assertArrayNotHasKey('price', $offer);
        $this->assertArrayNotHasKey('priceCurrency', $offer);
        $this->assertStringNotContainsString('222222', json_encode($this->blocks($soldOut)));

        // Pre-order: the page shows the price, and so does the markup.
        $this->assertStringContainsString('৳ 333,333', $preOrder);
        $this->assertSame('333333', $this->block($preOrder, 'Product')['offers']['price']);
    }

    public function test_category_page_gets_a_breadcrumb_with_its_parent_and_no_product_block(): void
    {
        $parent = Category::forceCreate(['slug' => 'computers', 'name' => 'Computers']);
        Category::forceCreate(['slug' => 'gaming-laptops', 'name' => 'Gaming Laptops', 'parent_id' => $parent->id]);

        $html = $this->get('/shop/gaming-laptops')->assertOk()->getContent();

        $this->assertSame(['Organization', 'BreadcrumbList'], $this->types($html));
        $this->assertSame(
            ['Home', 'Shop', 'Computers', 'Gaming Laptops'],
            array_column($this->block($html, 'BreadcrumbList')['itemListElement'], 'name'),
        );
    }

    public function test_blog_post_page_gets_a_blog_posting_block(): void
    {
        BlogPost::forceCreate([
            'slug' => 'buying-guide',
            'title' => 'Buying guide',
            'content' => '<p>Body</p>',
            'featured_image' => '/assets/guide.jpg',
            'published_at' => now()->subDay()->toDateString(),
        ]);

        $html = $this->get('/blog/buying-guide')->assertOk()->getContent();

        $this->assertSame(['Organization', 'BlogPosting'], $this->types($html));
        $post = $this->block($html, 'BlogPosting');
        $this->assertSame('Buying guide', $post['headline']);
        $this->assertSame(now()->subDay()->toDateString(), $post['datePublished']);
        $this->assertSame('http://localhost/assets/guide.jpg', $post['image']);
        $this->assertSame('http://localhost/blog/buying-guide', $post['mainEntityOfPage']['@id']);
    }

    /** @return array<string, array{0: string}> */
    public static function sitePages(): array
    {
        return [
            'home' => ['/'],
            'shop' => ['/shop'],
            'blog index' => ['/blog'],
            'customer feedback' => ['/customer-feedback'],
            'customer spotlight' => ['/customer-spotlight'],
            'cart' => ['/cart'],
            'compare' => ['/compare'],
        ];
    }

    #[DataProvider('sitePages')]
    public function test_every_other_public_page_carries_exactly_one_organization_block(string $uri): void
    {
        $html = $this->get($uri)->assertOk()->getContent();

        $this->assertSame(['Organization'], $this->types($html));
    }

    public function test_organization_block_uses_the_site_settings_and_only_real_profiles(): void
    {
        foreach ([
            'site_name' => 'Khan Gadget',
            'social_facebook' => 'https://facebook.com/khansgadget',
            'social_youtube' => 'https://youtube.com/@khansgadget',
            'social_instagram' => 'https://instagram.com',
            'social_daraz' => 'https://daraz.com.bd',
            'social_whatsapp' => 'https://wa.me/8801700000001',
        ] as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $organization = $this->block($this->get('/blog')->assertOk()->getContent(), 'Organization');

        $this->assertSame('Khan Gadget', $organization['name']);
        $this->assertSame('http://localhost/', $organization['url']);
        $this->assertSame(
            ['https://facebook.com/khansgadget', 'https://youtube.com/@khansgadget'],
            $organization['sameAs'],
        );
        $this->assertStringStartsWith('http://localhost/', $organization['logo']);
    }

    public function test_an_unset_profile_setting_falls_back_to_the_url_the_footer_shows(): void
    {
        // SiteSetting::getValue() falls back to a built-in default for empty settings, and the footer
        // links to that default. The markup mirrors the footer, so it lists the same URLs.
        $organization = $this->block($this->get('/blog')->assertOk()->getContent(), 'Organization');

        $this->assertSame([
            'https://facebook.com/khansgadget',
            'https://bikroy.com/en/shops/khangadgets',
            'https://youtube.com/@khansgadget',
            'https://daraz.com.bd/shop/ki2kz4ne',
        ], $organization['sameAs']);
    }

    public function test_product_data_cannot_inject_markup_into_the_page(): void
    {
        $this->product('evil', ['name' => 'Dell "XPS" </script><script>window.pwned=1</script> & <b>HP</b>']);

        $html = $this->get('/product/evil')->assertOk()->getContent();

        $this->assertStringNotContainsString('<script>window.pwned', $html);
        $this->assertSame(
            substr_count($html, 'application/ld+json'),
            count($this->blocks($html)),
            'Every JSON-LD block must still parse.',
        );
        $this->assertSame('Dell "XPS" window.pwned=1 & HP', $this->block($html, 'Product')['name']);
    }
}
