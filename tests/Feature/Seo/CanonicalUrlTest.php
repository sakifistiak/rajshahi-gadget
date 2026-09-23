<?php

namespace Tests\Feature\Seo;

use App\Http\Middleware\EnforceCanonicalUrl;
use App\Support\Seo;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * SEO phase 1 (C2, C3, robots): one canonical host, no /public/ URLs, no trailing slashes.
 * None of these tests touch the database.
 */
class CanonicalUrlTest extends TestCase
{
    private const CANONICAL = 'https://www.khangadget.com';

    /**
     * Sends the request through the real HTTP kernel (so the *global* middleware registration is
     * exercised) with the URL exactly as given. $this->get() can't be used for these cases: it
     * trims trailing slashes and resolves relative URLs against APP_URL.
     */
    private function send(string $uri): Response
    {
        return $this->app->make(Kernel::class)->handle(Request::create($uri));
    }

    private function assertMovedPermanently(Response $response, string $location): void
    {
        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame($location, $response->headers->get('Location'));
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function pathRedirects(): array
    {
        return [
            '/public prefix' => ['/public/shop', 'http://localhost/shop'],
            'bare /public' => ['/public', 'http://localhost/'],
            '/public/' => ['/public/', 'http://localhost/'],
            'trailing slash' => ['/shop/', 'http://localhost/shop'],
            'trailing slash keeps query' => ['/shop/?condition=intact&sort=price-asc', 'http://localhost/shop?condition=intact&sort=price-asc'],
            'both at once' => ['/public/product/foo/', 'http://localhost/product/foo'],
        ];
    }

    #[DataProvider('pathRedirects')]
    public function test_public_prefix_and_trailing_slash_redirect_in_one_301(string $from, string $to): void
    {
        $this->assertMovedPermanently($this->send('http://localhost'.$from), $to);
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function hostRedirects(): array
    {
        return [
            'http apex' => ['http://khangadget.com/', 'https://www.khangadget.com/'],
            'https apex' => ['https://khangadget.com/shop?x=1', 'https://www.khangadget.com/shop?x=1'],
            'http www' => ['http://www.khangadget.com/shop', 'https://www.khangadget.com/shop'],
            'apex + /public + slash' => ['http://khangadget.com/public/shop/', 'https://www.khangadget.com/shop'],
        ];
    }

    #[DataProvider('hostRedirects')]
    public function test_other_hosts_and_plain_http_redirect_to_the_canonical_origin_in_one_hop(string $from, string $to): void
    {
        config(['app.canonical_url' => self::CANONICAL]);

        $this->assertMovedPermanently($this->send($from), $to);
    }

    public function test_canonical_https_responses_carry_hsts(): void
    {
        config(['app.canonical_url' => self::CANONICAL]);

        $this->get(self::CANONICAL.'/up')
            ->assertOk()
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
    }

    public function test_hsts_is_also_sent_on_the_redirect_from_the_apex_domain(): void
    {
        config(['app.canonical_url' => self::CANONICAL]);

        $this->get('https://khangadget.com/')
            ->assertStatus(301)
            ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
    }

    public function test_nothing_is_forced_and_no_hsts_is_sent_when_no_canonical_url_is_configured(): void
    {
        $this->get('https://localhost/up')
            ->assertOk()
            ->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_health_probe_is_never_redirected(): void
    {
        config(['app.canonical_url' => self::CANONICAL]);

        $this->get('http://203.0.113.10/up')->assertOk();
    }

    public function test_only_get_and_head_are_redirected(): void
    {
        config(['app.canonical_url' => self::CANONICAL]);

        $response = (new EnforceCanonicalUrl)->handle(
            Request::create('http://khangadget.com/orders/', 'POST'),
            fn () => new Response('handled'),
        );

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('handled', $response->getContent());
    }

    public function test_paths_that_merely_start_with_public_are_left_alone(): void
    {
        foreach (['/publications', '/public-relations', '/publicity/'] as $uri) {
            $target = $uri === '/publicity/' ? 'http://localhost/publicity' : null;
            $response = (new EnforceCanonicalUrl)->handle(Request::create($uri), fn () => new Response('handled'));

            $this->assertSame($target === null ? 200 : 301, $response->getStatusCode(), $uri);
            if ($target !== null) {
                $this->assertSame($target, $response->headers->get('Location'));
            }
        }
    }

    public function test_canonical_url_strips_query_strings_and_uses_the_configured_origin(): void
    {
        $request = Request::create('/shop?sort=price-asc&page=2&utm_source=x');
        $this->assertSame('http://localhost/shop', Seo::canonicalUrl(null, $request));
        $this->assertSame('http://localhost/', Seo::canonicalUrl(null, Request::create('/?ref=abc')));

        config(['app.canonical_url' => self::CANONICAL.'/']);
        $this->assertSame('https://www.khangadget.com/shop', Seo::canonicalUrl(null, $request));
        $this->assertSame('https://www.khangadget.com/', Seo::canonicalUrl(null, Request::create('/')));
    }

    public function test_canonical_url_can_be_pinned_to_a_stored_slug_and_encodes_it(): void
    {
        config(['app.canonical_url' => self::CANONICAL]);

        $this->assertSame(
            'https://www.khangadget.com/product/acer-predator',
            Seo::canonicalUrl(Seo::path('product', 'acer-predator'), Request::create('/product/ACER-Predator?x=1')),
        );
        $this->assertSame('/product/a%20b%2Fc', Seo::path('product', 'a b/c'));
    }

    public function test_an_invalid_canonical_url_setting_is_ignored(): void
    {
        config(['app.canonical_url' => 'www.khangadget.com']);

        $this->assertNull(Seo::configuredOrigin());
        $this->assertMovedPermanently($this->send('http://localhost/public/shop'), 'http://localhost/shop');
    }

    public function test_robots_txt_blocks_utility_pages_and_advertises_the_sitemap(): void
    {
        $lines = preg_split('/\R/', trim(file_get_contents(public_path('robots.txt'))));

        foreach (['User-agent: *', 'Disallow: /cart', 'Disallow: /checkout', 'Disallow: /compare', 'Disallow: /*?*sort=', 'Disallow: /*?*page=', 'Sitemap: https://www.khangadget.com/sitemap.xml'] as $expected) {
            $this->assertContains($expected, $lines);
        }
        $this->assertNotContains('Disallow:', $lines, 'The old allow-everything rule must be gone.');
    }

    public function test_htaccess_no_longer_carries_the_rule_that_redirected_to_public(): void
    {
        $htaccess = file_get_contents(public_path('.htaccess'));

        $this->assertStringNotContainsString('R=301', $htaccess);
        $this->assertStringContainsString('index.php', $htaccess);
    }
}
