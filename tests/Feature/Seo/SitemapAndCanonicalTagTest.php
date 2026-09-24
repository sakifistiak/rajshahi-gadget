<?php

namespace Tests\Feature\Seo;

use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\CustomPage;
use App\Models\PhilanthropicWork;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * SEO phase 1 (C1 canonical tags, C4 sitemap). Needs a real schema, so it uses RefreshDatabase
 * like the rest of the DB-backed suite.
 */
class SitemapAndCanonicalTagTest extends TestCase
{
    use RefreshDatabase;

    private function product(string $slug, ?Category $category = null): Product
    {
        $brand = Brand::query()->firstOrCreate(['slug' => 'acer'], ['name' => 'Acer']);
        $condition = Condition::query()->firstOrCreate(
            ['slug' => 'intact'],
            ['label' => 'Brand New Intact Box', 'short' => 'Intact', 'tagline' => 'Sealed'],
        );
        $category ??= Category::query()->firstOrCreate(['slug' => 'laptops'], ['name' => 'Laptops']);

        return Product::forceCreate([
            'slug' => $slug,
            'name' => 'Acer '.$slug,
            'brand_id' => $brand->id,
            'category_id' => $category->id,
            'condition_id' => $condition->id,
            'price' => 100000,
            'description' => 'A laptop.',
        ]);
    }

    private function blogPost(string $slug, ?string $publishedAt): BlogPost
    {
        return BlogPost::forceCreate([
            'slug' => $slug,
            'title' => ucfirst($slug),
            'content' => '<p>Body</p>',
            'published_at' => $publishedAt,
        ]);
    }

    private function customPage(string $slug, bool $active): CustomPage
    {
        return CustomPage::forceCreate(['title' => ucfirst($slug), 'slug' => $slug, 'is_active' => $active]);
    }

    private function philanthropicWork(string $slug): PhilanthropicWork
    {
        return PhilanthropicWork::forceCreate([
            'title' => ucfirst($slug),
            'slug' => $slug,
            'content' => 'Content',
        ]);
    }

    private function sitemap(): string
    {
        $response = $this->get('/sitemap.xml');
        $response->assertOk();

        return $response->getContent();
    }

    /** @return list<string> */
    private function locs(string $xml): array
    {
        $doc = simplexml_load_string($xml);
        $this->assertNotFalse($doc, 'Sitemap must be well-formed XML.');

        $locs = [];
        foreach ($doc->url as $url) {
            $locs[] = (string) $url->loc;
        }

        return $locs;
    }

    /** @return list<string> */
    private function canonicalTags(string $html): array
    {
        preg_match_all('/<link rel="canonical" href="([^"]*)"\s*\/?>/', $html, $matches);

        return $matches[1];
    }

    public function test_sitemap_is_valid_xml_with_the_right_headers_and_no_cookies(): void
    {
        $this->product('acer-predator');

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringStartsWith('application/xml', $response->headers->get('Content-Type'));
        $this->assertSame([], $response->headers->getCookies(), 'The sitemap must not set session/cart cookies.');
        $doc = simplexml_load_string($response->getContent());
        $this->assertNotFalse($doc);
        $this->assertSame('urlset', $doc->getName());
    }

    public function test_sitemap_lists_every_canonical_public_url_and_only_those(): void
    {
        $laptops = Category::forceCreate(['slug' => 'laptops', 'name' => 'Laptops']);
        Category::forceCreate(['slug' => 'empty-category', 'name' => 'Empty']);
        $this->product('acer-predator', $laptops);
        $this->blogPost('live-post', now()->subDay()->toDateString());
        $this->blogPost('draft-post', null);
        $this->blogPost('future-post', now()->addWeek()->toDateString());
        $this->customPage('warranty', true);
        $this->customPage('hidden', false);
        $this->philanthropicWork('food-drive');

        $locs = $this->locs($this->sitemap());

        $expected = [
            '/', '/shop', '/blog', '/shop/laptops', '/product/acer-predator',
            '/blog/live-post', '/page/warranty', '/philanthropic-work/food-drive',
        ];
        foreach ($expected as $path) {
            $this->assertContains('http://localhost'.$path, $locs, "missing $path");
        }

        // /about and /contact permanently redirect to the CMS pages, so they must never be listed.
        $excluded = ['/blog/draft-post', '/blog/future-post', '/page/hidden', '/shop/empty-category', '/cart', '/checkout', '/compare', '/about', '/contact'];
        foreach ($excluded as $path) {
            $this->assertNotContains('http://localhost'.$path, $locs, "must not list $path");
        }

        foreach ($locs as $loc) {
            $this->assertStringNotContainsString('/public', $loc);
            $this->assertStringNotContainsString('?', $loc);
        }
        $this->assertSame(count($locs), count(array_unique($locs)), 'No duplicate URLs.');
    }

    public function test_parent_categories_are_listed_when_only_a_child_has_products(): void
    {
        $parent = Category::forceCreate(['slug' => 'computers', 'name' => 'Computers']);
        $child = Category::forceCreate(['slug' => 'gaming-laptops', 'name' => 'Gaming', 'parent_id' => $parent->id]);
        $this->product('acer-predator', $child);

        $locs = $this->locs($this->sitemap());

        $this->assertContains('http://localhost/shop/gaming-laptops', $locs);
        $this->assertContains('http://localhost/shop/computers', $locs);
    }

    public function test_lastmod_comes_from_the_real_updated_at_timestamp(): void
    {
        $product = $this->product('acer-predator');
        $product->forceFill(['updated_at' => '2026-03-04 05:06:07'])->saveQuietly();

        $doc = simplexml_load_string($this->sitemap());
        $entry = null;
        foreach ($doc->url as $url) {
            if ((string) $url->loc === 'http://localhost/product/acer-predator') {
                $entry = $url;
            }
        }

        $this->assertNotNull($entry);
        $this->assertSame(1, preg_match('/^2026-03-04T\d\d:\d\d:07/', (string) $entry->lastmod), (string) $entry->lastmod);
    }

    public function test_sitemap_uses_the_canonical_origin_when_configured(): void
    {
        config(['app.canonical_url' => 'https://www.khangadget.com']);
        $this->product('acer-predator');

        $response = $this->get('https://www.khangadget.com/sitemap.xml')->assertOk();
        $locs = $this->locs($response->getContent());

        $this->assertContains('https://www.khangadget.com/', $locs);
        $this->assertContains('https://www.khangadget.com/product/acer-predator', $locs);
        foreach ($locs as $loc) {
            $this->assertStringStartsWith('https://www.khangadget.com/', $loc);
        }
    }

    public function test_sitemap_picks_up_new_edited_and_bulk_deleted_products_without_a_manual_purge(): void
    {
        $a = $this->product('product-a');
        $this->assertContains('http://localhost/product/product-a', $this->locs($this->sitemap()));

        $b = $this->product('product-b');
        $this->assertContains('http://localhost/product/product-b', $this->locs($this->sitemap()), 'A newly created product must appear.');

        // The admin bulk-delete and the stock CSV import use query-builder writes that skip model events.
        Product::whereIn('id', [$a->id])->delete();
        $locs = $this->locs($this->sitemap());
        $this->assertNotContains('http://localhost/product/product-a', $locs, 'A bulk-deleted product must disappear.');
        $this->assertContains('http://localhost/product/product-b', $locs);

        Product::whereKey($b->id)->update(['slug' => 'product-b-renamed']);
        $locs = $this->locs($this->sitemap());
        $this->assertContains('http://localhost/product/product-b-renamed', $locs, 'A bulk-updated product must be reflected.');
        $this->assertNotContains('http://localhost/product/product-b', $locs);
    }

    public function test_pages_render_exactly_one_self_referencing_canonical_without_query_params(): void
    {
        $this->product('acer-predator');

        $cases = [
            '/shop' => 'http://localhost/shop',
            '/shop?sort=price-asc&page=2&condition=intact' => 'http://localhost/shop',
            '/shop/laptops?brand=acer&utm_source=fb' => 'http://localhost/shop/laptops',
            '/product/acer-predator?ref=home' => 'http://localhost/product/acer-predator',
            '/blog?search=asus' => 'http://localhost/blog',
            '/customer-feedback' => 'http://localhost/customer-feedback',
            '/customer-spotlight' => 'http://localhost/customer-spotlight',
        ];

        foreach ($cases as $uri => $expected) {
            $response = $this->get($uri);
            $response->assertOk();
            $this->assertSame([$expected], $this->canonicalTags($response->getContent()), "canonical for $uri");
        }
    }

    public function test_home_page_canonical_is_the_bare_origin(): void
    {
        $response = $this->get('/?utm_source=fb');

        $response->assertOk();
        $this->assertSame(['http://localhost/'], $this->canonicalTags($response->getContent()));
    }

    public function test_detail_pages_canonical_uses_the_canonical_host_and_stored_slug(): void
    {
        config(['app.canonical_url' => 'https://www.khangadget.com']);
        $this->blogPost('live-post', now()->subDay()->toDateString());
        $this->customPage('warranty', true);
        $this->philanthropicWork('food-drive');
        $this->product('acer-predator');

        $cases = [
            '/blog/live-post' => 'https://www.khangadget.com/blog/live-post',
            '/page/warranty' => 'https://www.khangadget.com/page/warranty',
            '/philanthropic-work/food-drive' => 'https://www.khangadget.com/philanthropic-work/food-drive',
            '/product/acer-predator' => 'https://www.khangadget.com/product/acer-predator',
        ];

        foreach ($cases as $path => $expected) {
            $response = $this->get('https://www.khangadget.com'.$path)->assertOk();
            $this->assertSame([$expected], $this->canonicalTags($response->getContent()), "canonical for $path");
        }
    }
}
