<?php

namespace Tests\Feature\Seo;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * The static /about and /contact templates hold placeholder text and made-up company details
 * ("Nova", "Founded in Dhaka 2021", a fake phone number). They must never be served: both URLs
 * redirect permanently to the real CMS pages. None of these tests touch the database.
 */
class PlaceholderPageRedirectTest extends TestCase
{
    private const CANONICAL = 'https://www.khangadget.com';

    private const PLACEHOLDER_TEXT = ['Nova', 'Founded in Dhaka', 'House 42', '1712 000 000', 'hello@nova'];

    /** @return array<string, array{0: string, 1: string}> */
    public static function redirects(): array
    {
        return [
            'about' => ['/about', '/page/about-us'],
            'contact' => ['/contact', '/page/contact'],
        ];
    }

    /**
     * Sends the request through the real HTTP kernel, so the route order is exercised too: a
     * redirect declared below the catch-all would be shadowed and answer 200 instead.
     */
    private function send(string $uri): Response
    {
        return $this->app->make(Kernel::class)->handle(Request::create($uri));
    }

    /**
     * Follows redirects the way a browser or crawler would, until the response is not a redirect
     * or the next hop is a real page (which these database-free tests cannot render).
     *
     * @return list<array{0: int, 1: string}> Each hop as [status, requested URL].
     */
    private function chain(string $uri, string $stopAt): array
    {
        $hops = [];
        for ($i = 0; $i < 5; $i++) {
            if (str_ends_with($uri, $stopAt)) {
                $hops[] = [0, $uri];
                break;
            }
            $response = $this->send($uri);
            $hops[] = [$response->getStatusCode(), $uri];
            if (! $response->isRedirect()) {
                break;
            }
            $uri = $response->headers->get('Location');
        }

        return $hops;
    }

    #[DataProvider('redirects')]
    public function test_the_old_url_redirects_permanently_to_the_cms_page(string $from, string $to): void
    {
        $response = $this->send('http://localhost'.$from);

        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame('http://localhost'.$to, $response->headers->get('Location'));
    }

    #[DataProvider('redirects')]
    public function test_the_redirect_target_uses_the_canonical_origin(string $from, string $to): void
    {
        config(['app.canonical_url' => self::CANONICAL]);

        $response = $this->send(self::CANONICAL.$from);

        $this->assertSame(301, $response->getStatusCode());
        $this->assertSame(self::CANONICAL.$to, $response->headers->get('Location'));
    }

    #[DataProvider('redirects')]
    public function test_every_way_of_reaching_the_old_url_ends_at_the_cms_page(string $from, string $to): void
    {
        config(['app.canonical_url' => self::CANONICAL]);
        $destination = self::CANONICAL.$to;

        foreach ([
            'http://khangadget.com'.$from,
            'https://khangadget.com'.$from,
            'http://www.khangadget.com'.$from,
            self::CANONICAL.$from.'/',
            self::CANONICAL.'/public'.$from,
            self::CANONICAL.$from.'?utm_source=old-link',
        ] as $entry) {
            $hops = $this->chain($entry, $to);

            $this->assertSame($destination, $hops[array_key_last($hops)][1], "Chain from $entry must end at the CMS page.");
            $this->assertLessThanOrEqual(3, count($hops), "Chain from $entry is too long.");
            foreach (array_slice($hops, 0, -1) as [$status]) {
                $this->assertSame(301, $status, "Every hop from $entry must be a permanent redirect.");
            }
        }
    }

    #[DataProvider('redirects')]
    public function test_the_placeholder_content_is_never_served(string $from): void
    {
        foreach (['http://localhost'.$from, 'http://localhost'.$from.'?x=1', 'http://localhost/public'.$from] as $uri) {
            $response = $this->send($uri);

            $this->assertNotSame(200, $response->getStatusCode(), $uri);
            foreach (self::PLACEHOLDER_TEXT as $text) {
                $this->assertStringNotContainsString($text, (string) $response->getContent(), "$uri must not expose \"$text\".");
            }
        }
    }

    public function test_the_real_cms_pages_still_go_to_the_cms_controller(): void
    {
        $router = $this->app['router'];

        foreach (['/page/about-us', '/page/contact'] as $path) {
            $route = $router->getRoutes()->match(Request::create('http://localhost'.$path));

            $this->assertStringEndsWith('CustomPageController@show', $route->getActionName(), $path);
        }
    }
}
