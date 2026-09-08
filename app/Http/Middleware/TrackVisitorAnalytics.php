<?php

namespace App\Http\Middleware;

use App\Services\AnalyticsTracker;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitorAnalytics
{
    public function __construct(
        protected AnalyticsTracker $tracker
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only track successful storefront HTML responses (HTTP 200)
        if ($response->getStatusCode() === 200) {
            try {
                $this->tracker->track($request);
            } catch (\Throwable $e) {
                // Silently catch to never break customer experience
            }
        }

        return $response;
    }
}
