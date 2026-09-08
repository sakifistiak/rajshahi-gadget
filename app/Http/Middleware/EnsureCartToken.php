<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Identifies an anonymous shopper across visits for cart-abandonment tracking.
 * Deliberately a dedicated long-lived cookie rather than reusing the
 * session-based chat_token pattern (ChatController) — a 120-minute session
 * lifetime is too short for "come back tomorrow and get matched as recovered."
 */
class EnsureCartToken
{
    public const COOKIE_NAME = 'kg_cart_token';

    public const LIFETIME_MINUTES = 60 * 24 * 30; // 30 days

    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->cookie(self::COOKIE_NAME)) {
            $token = (string) Str::uuid();
            $request->cookies->set(self::COOKIE_NAME, $token);
            Cookie::queue(self::COOKIE_NAME, $token, self::LIFETIME_MINUTES);
        }

        return $next($request);
    }
}
