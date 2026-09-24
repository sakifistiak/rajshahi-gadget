<?php

namespace Tests\Feature\Performance;

use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\CustomPage;
use App\Models\PhilanthropicWork;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * SEO phase 4 on real rendered pages: the icon script, dev-only attributes, the fonts request and the
 * share images. Needs a real schema, so it uses RefreshDatabase like the rest of the DB-backed suite.
 */
class AssetHygienePagesTest extends TestCase
{
    use RefreshDatabase;

    private static bool $viewsCleared = false;

    private string $public;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.canonical_url' => null]);
        SiteSetting::flushCache();
        // Views compiled before the attribute stripping existed would still carry the attributes.
        if (! self::$viewsCleared) {
            $this->artisan('view:clear');
            self::$viewsCleared = true;
        }

        // Share-image derivatives go into a throwaway public folder, never the real one.
        $this->public = sys_get_temp_dir().DIRECTORY_SEPARATOR.'kg-pages-'.bin2hex(random_bytes(4));
        mkdir($this->public.'/media', 0777, true);
        mkdir($this->public.'/uploads', 0777, true);
        $this->app->usePublicPath($this->public);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->public);

        parent::tearDown();
    }

    private function removeDirectory(string $directory): void
    {
        if (! is_dir($directory)) {
            return;
        }
        foreach (scandir($directory) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $path = $directory.DIRECTORY_SEPARATOR.$entry;
            is_dir($path) ? $this->removeDirectory($path) : @unlink($path);
        }
        @rmdir($directory);
    }

    private function image(string $relative, int $width = 1400, int $height = 900): void
    {
        $image = imagecreatetruecolor($width, $height);
        imagefill($image, 0, 0, imagecolorallocate($image, 30, 90, 160));
        imagepng($image, $this->public.'/'.ltrim($relative, '/'));
    }

    private function product(string $slug, array $photos = []): Product
    {
        $brand = Brand::query()->firstOrCreate(['slug' => 'hp'], ['name' => 'HP']);
        $condition = Condition::query()->firstOrCreate(['slug' => 'intact'], ['label' => 'BRAND NEW INTACT BOX', 'short' => 'INT', 'tagline' => 'Sealed']);
        $category = Category::query()->firstOrCreate(['slug' => 'laptops'], ['name' => 'Laptops']);
        $product = Product::forceCreate([
            'slug' => $slug, 'name' => 'HP '.$slug, 'brand_id' => $brand->id, 'category_id' => $category->id,
            'condition_id' => $condition->id, 'price' => 45000, 'description' => 'A laptop.',
        ]);
        foreach ($photos as $order => [$path, $primary]) {
            ProductImage::forceCreate(['product_id' => $product->id, 'image_path' => $path, 'is_primary' => $primary, 'sort_order' => $order]);
        }

        return $product;
    }

    /** @return array<string, array{0: string}> */
    public static function pages(): array
    {
        return [
            'home' => ['/'],
            'shop' => ['/shop'],
            'category' => ['/shop/laptops'],
            'blog' => ['/blog'],
            'blog post' => ['/blog/a-post'],
            'product' => ['/product/hp-one'],
            'cms page' => ['/page/about-us'],
            'customer feedback' => ['/customer-feedback'],
            'customer spotlight' => ['/customer-spotlight'],
            'philanthropic work' => ['/philanthropic-work'],
            'philanthropic detail' => ['/philanthropic-work/food-drive'],
            'cart' => ['/cart'],
            'compare' => ['/compare'],
        ];
    }

    private function seedForPages(): void
    {
        $this->product('hp-one');
        BlogPost::forceCreate(['slug' => 'a-post', 'title' => 'A post', 'content' => '<p>Body</p>', 'published_at' => now()->subDay()->toDateString()]);
        CustomPage::forceCreate(['title' => 'About Us', 'slug' => 'about-us', 'is_active' => true, 'show_title' => true, 'content' => '<p>Hello</p>']);
        PhilanthropicWork::forceCreate(['title' => 'Food drive', 'slug' => 'food-drive', 'content' => 'Content']);
    }

    #[DataProvider('pages')]
    public function test_every_page_loads_the_pinned_icon_library_deferred_from_its_head(string $uri): void
    {
        $this->seedForPages();

        $html = $this->get($uri)->assertOk()->getContent();

        $tag = '<script src="/assets/vendor/lucide-1.48.0.min.js" defer></script>';
        $this->assertSame(1, substr_count($html, $tag), 'Exactly one deferred, pinned icon script.');
        $this->assertLessThan(strpos($html, '</head>'), strpos($html, $tag), 'It belongs in the head, where defer keeps it off the critical path.');
        $this->assertStringNotContainsString('unpkg.com', $html);
        $this->assertStringNotContainsString('@include', $html, 'The partial must be rendered, not printed.');
    }

    #[DataProvider('pages')]
    public function test_no_dev_only_source_attribute_reaches_any_page(string $uri): void
    {
        $this->seedForPages();

        $html = $this->get($uri)->assertOk()->getContent();

        // The attribute itself must be gone. Three inline scripts still mention the name inside a selector
        // (closest('[data-tsd-source*="ProductCard"]')), which only ever looks for the kept ProductCard.tsx value.
        $this->assertDoesNotMatchRegularExpression('/\sdata-tsd-source\s*=/', $html);
        $this->assertStringNotContainsString('/src/components/', $html, 'Internal source paths must not leak.');
        $this->assertStringNotContainsString('/src/routes/', $html, 'Internal source paths must not leak.');
    }

    #[DataProvider('pages')]
    public function test_every_page_makes_exactly_one_google_fonts_stylesheet_request(string $uri): void
    {
        $this->seedForPages();

        $html = $this->get($uri)->assertOk()->getContent();

        $this->assertSame(1, preg_match_all('#<link[^>]+rel="stylesheet"[^>]+href="https://fonts\.googleapis\.com/css2[^"]*"|<link[^>]+href="https://fonts\.googleapis\.com/css2[^"]*"[^>]+rel="stylesheet"#', $html), 'One stylesheet request, not two.');
    }

    public function test_home_requests_inter_only_when_no_section_title_uses_another_font(): void
    {
        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap"', $html);
        foreach (['Poppins', 'Oswald', 'Playfair', 'Roboto'] as $family) {
            $this->assertStringNotContainsString($family, $html, "$family must not be requested when nothing uses it.");
        }
    }

    public function test_home_requests_exactly_the_fonts_its_titles_use(): void
    {
        SiteSetting::updateOrCreate(['key' => 'home_flash_title_style'], ['value' => json_encode(['highlight' => ['font' => 'poppins']])]);
        SiteSetting::updateOrCreate(['key' => 'home_sections_json'], ['value' => json_encode([[
            'id' => 's1', 'title' => 'New stock', 'highlight' => 'New', 'filter' => 'all', 'limit' => 4, 'active' => true,
            'style' => ['base' => ['font' => 'oswald']],
        ]])]);

        $html = $this->get('/')->assertOk()->getContent();

        $this->assertStringContainsString('family=Inter:wght@400;500;600;700&amp;family=Oswald:wght@700&amp;family=Poppins:wght@700&amp;display=swap', $html);
        $this->assertStringNotContainsString('Playfair', $html);
        $this->assertStringNotContainsString('Roboto', $html);
    }

    public function test_home_survives_a_corrupt_stored_title_style(): void
    {
        SiteSetting::updateOrCreate(['key' => 'home_flash_title_style'], ['value' => json_encode(['base' => ['font' => ['not', 'a', 'string'], 'shadow' => 42]])]);

        $this->get('/')->assertOk();
    }

    public function test_the_site_share_image_tags_point_at_one_small_resized_copy(): void
    {
        $this->image('media/share_image_1.png', 1869, 899);
        SiteSetting::updateOrCreate(['key' => 'site_share_image'], ['value' => '/media/share_image_1.png']);

        $html = $this->get('/')->assertOk()->getContent();

        preg_match('#<meta property="og:image" content="([^"]+)"#', $html, $og);
        preg_match('#<meta name="twitter:image" content="([^"]+)"#', $html, $twitter);
        $this->assertMatchesRegularExpression('#^https?://[^/]+/uploads/og/og-[0-9a-f]{40}\.jpg$#', $og[1]);
        $this->assertSame($og[1], $twitter[1]);
        $file = $this->public.'/uploads/og/'.basename($og[1]);
        $this->assertFileExists($file);
        $this->assertSame([1200, 630], array_slice(getimagesize($file), 0, 2));
        $this->assertLessThan(200000, filesize($file));
    }

    public function test_an_external_share_image_url_is_left_exactly_as_configured(): void
    {
        SiteSetting::updateOrCreate(['key' => 'site_share_image'], ['value' => 'https://cdn.example.com/share.jpg']);

        $html = $this->get('/shop')->assertOk()->getContent();

        $this->assertStringContainsString('<meta property="og:image" content="https://cdn.example.com/share.jpg"/>', $html);
    }

    public function test_a_product_page_shares_a_resized_copy_of_its_primary_photo(): void
    {
        $this->image('uploads/side.png', 1200, 1200);
        $this->image('uploads/front.png', 2000, 1500);
        $this->product('hp-one', [['/uploads/side.png', false], ['/uploads/front.png', true]]);

        $html = $this->get('/product/hp-one')->assertOk()->getContent();

        preg_match('#<meta property="og:image" content="([^"]+)"#', $html, $og);
        preg_match('#<meta name="twitter:image" content="([^"]+)"#', $html, $twitter);
        $this->assertMatchesRegularExpression('#^https?://[^/]+/uploads/og/og-[0-9a-f]{40}\.jpg$#', $og[1], 'Not the full-size upload.');
        $this->assertSame($og[1], $twitter[1]);
        $this->assertFileExists($this->public.'/uploads/og/'.basename($og[1]));
        $this->assertStringNotContainsString('content="http://localhost:8000/uploads/front.png"', $html);
    }

    public function test_a_product_without_photos_shares_the_site_image_instead_of_a_placeholder(): void
    {
        $this->image('media/b3ca13-kg-lockup-v2.png', 1000, 400);
        $this->product('hp-one');

        $html = $this->get('/product/hp-one')->assertOk()->getContent();

        preg_match('#<meta property="og:image" content="([^"]+)"#', $html, $og);
        $this->assertMatchesRegularExpression('#/uploads/og/og-[0-9a-f]{40}\.jpg$#', $og[1]);
        $this->assertStringNotContainsString('no-image-placeholder.svg', $og[1]);
    }
}
