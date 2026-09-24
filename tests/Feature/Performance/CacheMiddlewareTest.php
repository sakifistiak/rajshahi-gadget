<?php

namespace Tests\Feature\Performance;

use App\Http\Middleware\CachePublicJson;
use App\Http\Middleware\EnsureCartToken;
use App\Http\Middleware\NoStore;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * Cache stage 1, the parts that need no database: the two middleware on their own, and an audit of
 * how the real routes are wired.
 */
class CacheMiddlewareTest extends TestCase
{
    private const SESSION_MIDDLEWARE = [
        StartSession::class, EnsureCartToken::class, EncryptCookies::class,
        AddQueuedCookiesToResponse::class, ShareErrorsFromSession::class, ValidateCsrfToken::class,
    ];

    private function publicJson(Request $request, Response $response, string $seconds = '300'): Response
    {
        return (new CachePublicJson)->handle($request, fn () => $response, $seconds);
    }

    private function cacheControl(Response $response): string
    {
        return (string) $response->headers->get('Cache-Control');
    }

    public function test_a_successful_get_becomes_public_for_five_minutes_with_an_etag(): void
    {
        $response = $this->publicJson(Request::create('/api/x'), new JsonResponse(['a' => 1]));

        $this->assertStringContainsString('public', $this->cacheControl($response));
        $this->assertStringContainsString('max-age=300', $this->cacheControl($response));
        $this->assertStringContainsString('s-maxage=300', $this->cacheControl($response));
        $this->assertNotNull($response->getEtag());
        $this->assertStringNotContainsString('private', $this->cacheControl($response));
    }

    public function test_the_lifetime_comes_from_the_route_parameter(): void
    {
        $response = $this->publicJson(Request::create('/api/x'), new JsonResponse([]), '60');

        $this->assertStringContainsString('max-age=60', $this->cacheControl($response));
        $this->assertStringContainsString('s-maxage=60', $this->cacheControl($response));
    }

    public function test_a_repeat_request_with_the_same_etag_gets_an_empty_304(): void
    {
        $first = $this->publicJson(Request::create('/api/x'), new JsonResponse(['fonts' => 'Inter']));

        $request = Request::create('/api/x');
        $request->headers->set('If-None-Match', $first->getEtag());
        $second = $this->publicJson($request, new JsonResponse(['fonts' => 'Inter']));

        $this->assertSame(304, $second->getStatusCode());
        $this->assertSame('', (string) $second->getContent());
    }

    public function test_changed_content_gets_a_new_etag_and_a_full_response(): void
    {
        $first = $this->publicJson(Request::create('/api/x'), new JsonResponse(['fonts' => 'Inter']));

        $request = Request::create('/api/x');
        $request->headers->set('If-None-Match', $first->getEtag());
        $second = $this->publicJson($request, new JsonResponse(['fonts' => 'Poppins']));

        $this->assertSame(200, $second->getStatusCode());
        $this->assertNotSame($first->getEtag(), $second->getEtag());
    }

    public function test_errors_and_redirects_are_never_made_cacheable(): void
    {
        foreach ([new JsonResponse(['message' => 'boom'], 500), new JsonResponse([], 404), new RedirectResponse('/elsewhere')] as $response) {
            $result = $this->publicJson(Request::create('/api/x'), $response);

            $this->assertStringNotContainsString('public', $this->cacheControl($result), 'Status '.$result->getStatusCode().' must not be cached for minutes.');
            $this->assertStringNotContainsString('max-age=300', $this->cacheControl($result));
        }
    }

    public function test_non_cacheable_methods_are_left_alone(): void
    {
        $result = $this->publicJson(Request::create('/api/x', 'POST'), new JsonResponse(['ok' => true]));

        $this->assertStringNotContainsString('public', $this->cacheControl($result));
    }

    public function test_head_requests_are_cacheable_like_get(): void
    {
        $result = $this->publicJson(Request::create('/api/x', 'HEAD'), new JsonResponse(['ok' => true]));

        $this->assertStringContainsString('public', $this->cacheControl($result));
    }

    public function test_a_response_that_carries_a_cookie_is_never_made_public(): void
    {
        $response = new JsonResponse(['ok' => true]);
        $response->headers->setCookie(new Cookie('khan_gadget_session', 'secret'));

        $result = $this->publicJson(Request::create('/api/x'), $response);

        $this->assertStringNotContainsString('public', $this->cacheControl($result), 'A cookie must never be cached and handed to another visitor.');
        $this->assertNull($result->getEtag());
    }

    public function test_no_store_overrides_any_earlier_cache_header_and_covers_every_response_type(): void
    {
        $public = (new JsonResponse(['x' => 1]))->setPublic()->setMaxAge(300);

        foreach ([$public, new RedirectResponse('/'), new Response('page'), new JsonResponse([], 404)] as $response) {
            $result = (new NoStore)->handle(Request::create('/thank-you'), fn () => $response);

            $this->assertSame('no-store, private', $result->headers->get('Cache-Control'));
            $this->assertSame('no-cache', $result->headers->get('Pragma'));
        }
    }

    /** @return array<int, Route> */
    private function routes(): array
    {
        return $this->app['router']->getRoutes()->getRoutes();
    }

    /** @return list<string> Middleware classes a route really runs, after aliases, ordering and exclusions. */
    private function middlewareFor(Route $route): array
    {
        // Building the HTTP kernel is what registers the middleware aliases and priority list on the router.
        $this->app->make(HttpKernel::class);

        return array_values(array_map(
            fn ($middleware) => is_string($middleware) ? explode(':', $middleware)[0] : get_debug_type($middleware),
            $this->app['router']->gatherRouteMiddleware($route),
        ));
    }

    public function test_the_session_free_json_routes_skip_every_session_and_cookie_middleware(): void
    {
        $found = [];
        foreach ($this->routes() as $route) {
            if (in_array($route->uri(), ['api/site-fonts', 'api/nav-categories'], true)) {
                $found[] = $route->uri();
                $middleware = $this->middlewareFor($route);

                $this->assertContains(CachePublicJson::class, $middleware, $route->uri());
                foreach (self::SESSION_MIDDLEWARE as $forbidden) {
                    $this->assertNotContains($forbidden, $middleware, $route->uri().' must not run '.$forbidden);
                }
            }
        }

        $this->assertEqualsCanonicalizing(['api/site-fonts', 'api/nav-categories'], $found);
    }

    public function test_only_the_intended_routes_are_publicly_cacheable(): void
    {
        $cacheable = [];
        foreach ($this->routes() as $route) {
            if (in_array(CachePublicJson::class, $this->middlewareFor($route), true)) {
                $cacheable[] = $route->uri();
            }
        }

        $this->assertEqualsCanonicalizing(
            ['api/site-fonts', 'api/nav-categories', 'api/v1/categories'],
            $cacheable,
            'A page or endpoint that is not on this list must never be made publicly cacheable by accident.',
        );
    }

    public function test_routes_that_show_order_or_customer_data_are_all_no_store(): void
    {
        $mustBeNoStore = 0;
        foreach ($this->routes() as $route) {
            $uri = $route->uri();
            $action = $route->getActionName();
            $isDataRoute = preg_match('#^(thank-you|orders/\{order\}/invoice|chat/.+|dashboard|profile|admin(/.*)?)$#', $uri) === 1
                || str_starts_with($action, 'App\\Http\\Controllers\\Admin\\')
                || in_array($route->getName(), ['thank-you', 'orders.invoice'], true);

            if ($isDataRoute) {
                $mustBeNoStore++;
                $this->assertContains(NoStore::class, $this->middlewareFor($route), implode('|', $route->methods()).' '.$uri.' shows private data but is not no-store.');
            }
        }

        $this->assertGreaterThan(60, $mustBeNoStore, 'The audit must actually be looking at the admin routes.');
    }

    public function test_the_public_pages_that_must_keep_their_behaviour_are_not_touched(): void
    {
        $untouched = ['/', 'shop', 'product/{slug}', 'blog', 'checkout', 'api/search', 'api/compare', 'sitemap.xml'];
        foreach ($this->routes() as $route) {
            if (in_array($route->uri(), $untouched, true)) {
                $middleware = $this->middlewareFor($route);
                $this->assertNotContains(NoStore::class, $middleware, $route->uri());
                $this->assertNotContains(CachePublicJson::class, $middleware, $route->uri());
            }
        }
    }
}
