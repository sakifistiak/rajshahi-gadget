<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets browsers and shared caches keep an identical-for-everyone JSON response for a few minutes
 * (SEO/performance phase 4, cache stage 1). Use as "public.json:300" on routes that also run without
 * the session and cookie middleware.
 *
 * It only ever makes a response public when that is safe: a successful GET or HEAD with no cookie
 * attached. Errors, redirects, other methods, and any response that somehow carries a cookie keep
 * Laravel's default "no-cache, private", so a failure can never be cached for minutes and a cookie
 * can never be handed to another visitor.
 */
class CachePublicJson
{
    public function handle(Request $request, Closure $next, int|string $seconds = 300): Response
    {
        $response = $next($request);

        if (! $request->isMethodCacheable() || ! $response->isSuccessful() || $response->headers->getCookies() !== []) {
            return $response;
        }

        $seconds = max(0, (int) $seconds);
        $response->setPublic();
        $response->setMaxAge($seconds);
        $response->setSharedMaxAge($seconds);
        // A strong validator, so a repeat visit after the cache time is a cheap 304 instead of a full body.
        $response->setEtag(md5((string) $response->getContent()));
        if ($response->isNotModified($request)) {
            $response->setNotModified();
        }

        return $response;
    }
}
