<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Marks a response as never to be stored by a browser, proxy or shared cache (cache stage 1).
 * Applied to every route that shows order or customer data: the order confirmation and invoice
 * pages, the customer live-chat endpoints, and everything behind a login (dashboard, orders,
 * customers, abandoned carts, chats, analytics, profile).
 *
 * "no-cache, private" (Laravel's default) still lets a browser keep a copy and show it again from
 * the back button. "no-store" does not. It is set on the way out, so it also covers redirects and
 * error responses such as a 404 for an unknown order.
 */
class NoStore
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-store, private');
        // For old HTTP/1.0 proxies that ignore Cache-Control.
        $response->headers->set('Pragma', 'no-cache');

        return $response;
    }
}
