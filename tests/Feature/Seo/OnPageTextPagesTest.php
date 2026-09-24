<?php

namespace Tests\Feature\Seo;

use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\CustomPage;
use App\Models\PhilanthropicWork;
use App\Models\Product;
use App\Models\ProductHighlight;
use App\Models\SiteSetting;
use App\Models\User;
use App\Support\Seo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * SEO phase 3 on real rendered pages: one H1, a keyword home title, short product titles, and a
 * unique, plain-text meta description on every page. Needs a real schema, so it uses
 * RefreshDatabase like the rest of the DB-backed suite.
 */
class OnPageTextPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.canonical_url' => null]);
        // SiteSetting keeps a static in-process map that survives the per-test database rollback.
        SiteSetting::flushCache();
    }

    private function product(string $slug = 'hp-probook-430-g8', array $attributes = [], string $condition = 'intact', array $highlights = []): Product
    {
        $brand = Brand::query()->firstOrCreate(['slug' => 'hp'], ['name' => 'HP']);
        $conditionModel = Condition::query()->firstOrCreate(
            ['slug' => $condition],
            ['label' => ['intact' => 'BRAND NEW INTACT BOX', 'without-box' => 'BRAND NEW WITHOUT BOX', 'pre-owned' => 'PRE-OWNED'][$condition] ?? strtoupper($condition), 'short' => 'X', 'tagline' => 'Tagline'],
        );
        $category = Category::query()->firstOrCreate(['slug' => 'laptops'], ['name' => 'Laptops']);

        $product = Product::forceCreate(array_merge([
            'slug' => $slug,
            'name' => 'HP ProBook 430 G8 Core i7-1165G7 512GB SSD 13.3" Display Business Series Laptop',
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'condition_id' => $conditionModel->id,
            'price' => 45000,
            'description' => 'A laptop.',
        ], $attributes));

        foreach ($highlights as $order => $text) {
            ProductHighlight::forceCreate(['product_id' => $product->id, 'text' => $text, 'sort_order' => $order]);
        }

        return $product;
    }

    private function meta(string $html, string $attribute): ?string
    {
        if (! preg_match('#<meta '.$attribute.' content="([^"]*)"#', $html, $match)) {
            return null;
        }

        return html_entity_decode($match[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function description(string $html): ?string
    {
        return $this->meta($html, 'name="description"');
    }

    private function title(string $html): string
    {
        preg_match('#<title>(.*?)</title>#s', $html, $match);

        return html_entity_decode($match[1] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /** @return list<string> Text of every <h1>, tags removed. */
    private function h1s(string $html): array
    {
        preg_match_all('#<h1\b[^>]*>(.*?)</h1>#s', $html, $matches);

        return array_map(fn (string $h) => trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags($h), ENT_QUOTES | ENT_HTML5, 'UTF-8'))), $matches[1]);
    }

    public function test_home_page_has_one_keyword_h1_and_the_keyword_title(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(['Genuine Imported Laptops & Gadgets in Bangladesh'], $this->h1s($html));
        $this->assertSame('Khan Gadget — Genuine Imported Laptops & Gadgets in Bangladesh', $this->title($html));
        $this->assertSame($this->title($html), $this->meta($html, 'property="og:title"'));
        $this->assertSame($this->title($html), $this->meta($html, 'name="twitter:title"'));
    }

    public function test_home_title_follows_the_site_name_setting(): void
    {
        SiteSetting::updateOrCreate(['key' => 'site_name'], ['value' => 'Acme Gadgets']);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame('Acme Gadgets — Genuine Imported Laptops & Gadgets in Bangladesh', $this->title($html));
    }

    public function test_home_h1_survives_every_admin_toggle_being_switched_off(): void
    {
        foreach (['home_hero_active', 'home_ticker_active', 'home_trustbar_active', 'home_flash_active', 'home_new_arrival_active'] as $key) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => '0']);
        }

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(['Genuine Imported Laptops & Gadgets in Bangladesh'], $this->h1s($html));
    }

    public function test_home_description_is_its_own_and_matches_og_and_twitter(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertSame(Seo::pageDescription('home'), $this->description($html));
        $this->assertSame($this->description($html), $this->meta($html, 'property="og:description"'));
        $this->assertSame($this->description($html), $this->meta($html, 'name="twitter:description"'));
    }

    public function test_shop_page_has_one_h1_and_its_own_description(): void
    {
        $html = $this->get('/shop')->assertOk()->getContent();

        $this->assertSame(['Explore All Products'], $this->h1s($html));
        $this->assertSame('Shop | Khan Gadget', $this->title($html));
        $this->assertSame(Seo::pageDescription('shop'), $this->description($html));
    }

    public function test_category_page_h1_title_and_description_reflect_the_category(): void
    {
        Category::forceCreate(['slug' => 'gaming-laptops', 'name' => 'Gaming Laptops']);

        $html = $this->get('/shop/gaming-laptops')->assertOk()->getContent();

        $this->assertSame(['Gaming Laptops'], $this->h1s($html));
        $this->assertSame('Gaming Laptops | Khan Gadget', $this->title($html));
        $this->assertStringContainsString('Gaming Laptops', $this->description($html));
        $this->assertNotSame(Seo::pageDescription('shop'), $this->description($html));
    }

    public function test_blog_page_has_one_h1_and_its_own_description(): void
    {
        $html = $this->get('/blog')->assertOk()->getContent();

        $this->assertSame(['Khan Gadget Blog'], $this->h1s($html));
        $this->assertSame(Seo::pageDescription('blog'), $this->description($html));
    }

    public function test_every_listing_page_has_a_different_description(): void
    {
        Category::forceCreate(['slug' => 'gaming-laptops', 'name' => 'Gaming Laptops']);
        $siteDescription = "Khan Gadget Is A Genuine Wholesaler &amp; Retailer ;\nServing &amp; Building Trust In Gadget Industry Since 2012.";
        SiteSetting::updateOrCreate(['key' => 'site_description'], ['value' => $siteDescription]);

        $descriptions = [];
        foreach (['/', '/shop', '/shop/gaming-laptops', '/blog', '/customer-feedback', '/customer-spotlight', '/philanthropic-work'] as $uri) {
            $description = $this->description($this->get($uri)->assertOk()->getContent());
            $this->assertNotNull($description, $uri);
            $this->assertStringNotContainsString("\n", $description, $uri);
            $this->assertNotSame(Seo::text($siteDescription), $description, "$uri must not use the shared boilerplate.");
            $descriptions[$uri] = $description;
        }

        $this->assertSame($descriptions, array_unique($descriptions), 'No two listing pages may share a description.');
    }

    public function test_the_shared_site_description_has_no_line_breaks_on_pages_that_fall_back_to_it(): void
    {
        SiteSetting::updateOrCreate(['key' => 'site_description'], ['value' => "Line one &amp;\nline two"]);

        $html = $this->get('/cart')->assertOk()->getContent();

        $this->assertSame('Line one & line two', $this->description($html));
        $this->assertStringNotContainsString('&amp;amp;', $html, 'The description must be escaped exactly once.');
    }

    public function test_product_title_is_short_and_the_h1_keeps_the_full_name(): void
    {
        $this->product();

        $html = $this->get('/product/hp-probook-430-g8')->assertOk()->getContent();

        $this->assertLessThanOrEqual(60, mb_strlen($this->title($html)));
        $this->assertSame('HP ProBook 430 G8 Core i7-1165G7 · Intact Box | Khan Gadget', $this->title($html));
        $this->assertSame(['HP ProBook 430 G8 Core i7-1165G7 512GB SSD 13.3" Display Business Series Laptop'], $this->h1s($html));
        $this->assertStringContainsString('Business Series Laptop | Khan Gadget', $this->meta($html, 'property="og:title"'), 'Social titles keep the full name.');
    }

    public function test_product_description_is_plain_text_even_when_the_pasted_description_is_not(): void
    {
        $this->product('hp-probook-430-g8', [
            'description' => '<p>𝐇𝐏 𝐏𝐫𝐨𝐁𝐨𝐨𝐤 𝟒𝟑𝟎 𝐆𝟖| 𝟏𝟏𝐭𝐡 𝐆𝐞𝐧 𝐂𝐨𝐫𝐞 𝐢𝟕&nbsp;𝐌𝐎𝐃𝐄𝐋: HP ProBook 430 G8</p><p>Dɪsᴄᴏᴜɴᴛ Pʀɪcᴇ</p>',
        ], 'intact', ['11th Gen Core i7', 'Up to 4.70GHz']);

        $html = $this->get('/product/hp-probook-430-g8')->assertOk()->getContent();
        $description = $this->description($html);

        $this->assertLessThanOrEqual(155, mb_strlen($description));
        $this->assertStringContainsString('Brand New Intact Box.', $description);
        $this->assertStringContainsString('Price: ৳ 45,000.', $description);
        $this->assertStringContainsString('11th Gen Core i7.', $description);
        $this->assertDoesNotMatchRegularExpression('/[\x{1D400}-\x{1D7FF}\x{1D00}-\x{1D2B}\x{200E}\x{00A0}]/u', $description);
        preg_match_all('#<(?:title|meta)[^>]*>(?:[^<]*</title>)?#', $html, $tags);
        $this->assertStringNotContainsString('nbsp', implode(' ', $tags[0]), 'No entity text may reach the title or any meta tag.');
        $this->assertSame($description, $this->meta($html, 'property="og:description"'));
        $this->assertSame($description, $this->meta($html, 'name="twitter:description"'));
    }

    public function test_out_of_stock_product_description_states_no_price(): void
    {
        $this->product('sold-out', ['in_stock' => false, 'price' => 123456]);

        $html = $this->get('/product/sold-out')->assertOk()->getContent();

        $this->assertStringNotContainsString('৳', $this->description($html));
        $this->assertStringNotContainsString('123', $this->description($html));
    }

    public function test_cms_page_without_a_visible_title_still_has_exactly_one_h1(): void
    {
        CustomPage::forceCreate(['title' => 'About Us', 'slug' => 'about-us', 'is_active' => true, 'show_title' => false, 'content' => '<p>Registered with the Bangladesh Computer Samity.</p>']);

        $html = $this->get('/page/about-us')->assertOk()->getContent();

        $this->assertSame(['About Us'], $this->h1s($html));
        $this->assertMatchesRegularExpression('#<h1 class="sr-only">\s*About Us\s*</h1>#', $html);
    }

    public function test_cms_page_with_a_visible_title_has_one_visible_h1_and_no_hidden_one(): void
    {
        CustomPage::forceCreate(['title' => 'Warranty', 'slug' => 'warranty', 'is_active' => true, 'show_title' => true, 'content' => '<p>Text</p>']);

        $html = $this->get('/page/warranty')->assertOk()->getContent();

        $this->assertSame(['Warranty'], $this->h1s($html));
        $this->assertStringNotContainsString('sr-only', $html);
    }

    public function test_cms_page_that_brings_its_own_h1_gets_no_extra_one(): void
    {
        CustomPage::forceCreate(['title' => 'Terms', 'slug' => 'terms', 'is_active' => true, 'show_title' => false, 'content' => '<h1>Terms of sale</h1><p>Text</p>']);

        $html = $this->get('/page/terms')->assertOk()->getContent();

        $this->assertSame(['Terms of sale'], $this->h1s($html));
    }

    public function test_cms_page_description_uses_the_editors_text_else_the_content(): void
    {
        CustomPage::forceCreate(['title' => 'Own', 'slug' => 'own', 'is_active' => true, 'meta_description' => "Written by\nthe editor &amp; kept", 'content' => '<p>Ignored</p>']);
        CustomPage::forceCreate(['title' => 'Body', 'slug' => 'body', 'is_active' => true, 'content' => '<p>Registered with the Bangladesh Computer Samity.</p><p>Trusted since 2012.</p>']);
        CustomPage::forceCreate(['title' => 'Empty', 'slug' => 'empty', 'is_active' => true, 'content' => '<p><br></p>']);

        $this->assertSame('Written by the editor & kept', $this->description($this->get('/page/own')->assertOk()->getContent()));
        $this->assertSame(
            'Registered with the Bangladesh Computer Samity. Trusted since 2012.',
            $this->description($this->get('/page/body')->assertOk()->getContent()),
        );
        $this->assertNull($this->description($this->get('/page/empty')->assertOk()->getContent()), 'No description is better than an empty one.');
    }

    public function test_blog_post_description_is_decoded_once(): void
    {
        BlogPost::forceCreate([
            'slug' => 'ssd-guide',
            'title' => 'SSD guide',
            'content' => '<p>Fast&nbsp;SSD &amp; more</p><p>Second paragraph.</p>',
            'published_at' => now()->subDay()->toDateString(),
        ]);

        $html = $this->get('/blog/ssd-guide')->assertOk()->getContent();

        $this->assertSame('Fast SSD & more Second paragraph.', $this->description($html));
        $this->assertStringNotContainsString('&amp;nbsp;', $html);
    }

    public function test_philanthropic_work_description_comes_from_its_own_text(): void
    {
        PhilanthropicWork::forceCreate(['title' => 'Food drive', 'slug' => 'food-drive', 'content' => '<p>We fed 500 families in Rajshahi.</p>']);
        PhilanthropicWork::forceCreate(['title' => 'Blood camp', 'slug' => 'blood-camp', 'content' => null]);

        $withText = $this->description($this->get('/philanthropic-work/food-drive')->assertOk()->getContent());
        $withoutText = $this->description($this->get('/philanthropic-work/blood-camp')->assertOk()->getContent());

        $this->assertSame('We fed 500 families in Rajshahi.', $withText);
        $this->assertSame('Blood camp: philanthropic work supported by Khan Gadget in Bangladesh.', $withoutText);
    }

    public function test_the_storefront_menu_defaults_point_at_the_cms_pages_not_the_old_placeholder_urls(): void
    {
        // Read the data the storefront receives. The drawer partial itself only prints once per PHP
        // process (it defines a constant), so its HTML cannot be checked reliably across tests.
        $shared = [];
        View::composer('*', function ($view) use (&$shared) {
            $shared = $view->getData();
        });

        Blade::render('');

        $urls = array_column($shared['mobileDrawerInfoLinks'], 'url');
        $this->assertContains('/page/about-us', $urls);
        $this->assertContains('/page/contact', $urls);
        $this->assertNotContains('/about', $urls);
        $this->assertNotContains('/contact', $urls);
    }

    public function test_the_admin_settings_page_offers_the_cms_pages_as_the_default_menu_links(): void
    {
        $admin = User::forceCreate([
            'name' => 'Admin',
            'email' => 'admin@example.test',
            'password' => bcrypt('secret'),
            'email_verified_at' => now(),
            'is_admin' => true,
        ]);

        $html = $this->actingAs($admin)->get('/admin/settings')->assertOk()->getContent();

        $this->assertMatchesRegularExpression('#page\\\\?/about-us#', $html);
        $this->assertMatchesRegularExpression('#page\\\\?/contact#', $html);
        $this->assertDoesNotMatchRegularExpression('#(?:&quot;|")url(?:&quot;|")\s*:\s*(?:&quot;|")\\\\?/(?:about|contact)(?:&quot;|")#', $html);
    }

    /** @return array<string, array{0: string}> */
    public static function untouchedPages(): array
    {
        return [
            'cart' => ['/cart'],
            'compare' => ['/compare'],
            'account' => ['/account'],
        ];
    }

    #[DataProvider('untouchedPages')]
    public function test_utility_pages_keep_their_titles(string $uri): void
    {
        $html = $this->get($uri)->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/ \| Khan Gadget$/', $this->title($html));
        $this->assertNotNull($this->description($html));
    }
}
