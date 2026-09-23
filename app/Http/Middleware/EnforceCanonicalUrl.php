<?php

namespace App\Http\Middleware;

use App\Support\Seo;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps every public page reachable at exactly one URL (SEO phase 1: C2, C3).
 *
 *  - Always (GET/HEAD): strips a leading "/public" segment and a trailing slash.
 *  - Only when CANONICAL_URL is set (production): redirects any other host and
 *    plain http to the canonical origin, and sends the HSTS header on https.
 *
 * All of it collapses into a single 301 hop. Runs as global middleware so it
 * also covers URLs that match no route (e.g. /public/shop when the document
 * root is the public folder itself).
 */
class EnforceCanonicalUrl
{
    public const HSTS = 'max-age=31536000';

    public function handle(Request $request, Closure $next): Response
    {
        $configured = Seo::configuredOrigin();

        if ($request->isMethod('GET') || $request->isMethod('HEAD')) {
            $target = $this->redirectTarget($request, $configured);

            if ($target !== null) {
                return $this->withHsts(new RedirectResponse($target, 301), $request, $configured);
            }
        }

        return $this->withHsts($next($request), $request, $configured);
    }

    private function redirectTarget(Request $request, ?string $configured): ?string
    {
        // The URI exactly as the client sent it. getPathInfo()/path() are relative to
        // Laravel's base URL, which hides the "/public" prefix we need to detect.
        $uri = $request->getRequestUri();
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $query = (string) parse_url($uri, PHP_URL_QUERY);

        $clean = preg_replace('#^/public(?=/|$)#', '', $path);
        $clean = $clean === '/' ? '/' : rtrim($clean, '/');
        $clean = $clean === '' ? '/' : $clean;

        // Load-balancer / uptime probes must get a plain answer, not a redirect.
        if ($clean === '/up') {
            return null;
        }

        $currentOrigin = $request->getSchemeAndHttpHost();
        $targetOrigin = $configured ?? $currentOrigin;

        if ($clean === $path && strcasecmp($currentOrigin, $targetOrigin) === 0) {
            return null;
        }

        return $targetOrigin.$clean.($query !== '' ? '?'.$query : '');
    }

    private function withHsts(Response $response, Request $request, ?string $configured): Response
    {
        if ($configured !== null && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', self::HSTS);
        }

        return $response;
    }
}
