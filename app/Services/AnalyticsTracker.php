<?php

namespace App\Services;

use App\Models\AnalyticsActiveSession;
use App\Models\AnalyticsVisit;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\CustomPage;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

class AnalyticsTracker
{
    /**
     * Track a storefront request
     */
    public function track(Request $request): void
    {
        $userAgent = $request->userAgent() ?? '';

        // 1. Skip bots, crawlers, and scrapers
        if ($this->isBot($userAgent)) {
            return;
        }

        // 2. Skip non-GET, internal AJAX, debugbar, and asset requests
        if (!$request->isMethod('GET') || $request->isXmlHttpRequest()) {
            return;
        }

        $path = trim($request->path(), '/');
        if (
            str_starts_with($path, 'admin') ||
            str_starts_with($path, 'api') ||
            str_starts_with($path, 'chat') ||
            str_starts_with($path, 'livewire') ||
            str_starts_with($path, '_') ||
            str_contains($path, '.')
        ) {
            return;
        }

        // 3. Session & Visitor identification
        $sessionId = $request->cookie('kg_analytics_sid');
        if (!$sessionId || strlen($sessionId) < 10) {
            $sessionId = (string) Str::uuid();
            Cookie::queue('kg_analytics_sid', $sessionId, 60 * 24 * 30); // 30 days
        }

        $ip = $request->ip() ?? '127.0.0.1';
        $visitorHash = hash('sha256', $ip . '|' . $userAgent);

        $url = $request->fullUrl();
        $routeName = $request->route()?->getName();
        $referrer = $request->headers->get('referer');
        $trafficSource = $this->determineTrafficSource($request, $referrer);
        $deviceType = $this->detectDevice($userAgent);
        $browser = $this->detectBrowser($userAgent);
        $platform = $this->detectPlatform($userAgent);

        // 4. Detect viewable entity (Product, Blog, Category, etc.)
        $viewableInfo = $this->resolveViewable($request);
        $pageTitle = $viewableInfo['title'] ?? $this->formatTitleFromRoute($routeName, $path);

        $now = now();

        // 5. Update or insert active session (preserving original first_seen_at)
        try {
            $session = AnalyticsActiveSession::firstOrNew(['session_id' => $sessionId]);
            if (!$session->exists) {
                $session->first_seen_at = $now;
            }
            $session->ip_address = $ip;
            $session->current_url = Str::limit($url, 1024);
            $session->current_title = Str::limit($pageTitle, 255);
            $session->route_name = $routeName;
            $session->viewable_type = $viewableInfo['type'];
            $session->viewable_id = $viewableInfo['id'];
            $session->referrer = Str::limit($referrer, 1024);
            $session->referrer_domain = $trafficSource;
            $session->device_type = $deviceType;
            $session->browser = $browser;
            $session->platform = $platform;
            $session->last_active_at = $now;
            $session->save();
        } catch (\Throwable $e) {
            // Silently ignore to protect customer request flow
        }

        // 6. Record historical pageview in analytics_visits (with 10-second spam debounce for exact same URL)
        try {
            $recentVisit = AnalyticsVisit::where('session_id', $sessionId)
                ->where('url', Str::limit($url, 1024))
                ->where('created_at', '>=', $now->copy()->subSeconds(10))
                ->exists();

            if (!$recentVisit) {
                AnalyticsVisit::create([
                    'session_id' => $sessionId,
                    'visitor_hash' => $visitorHash,
                    'ip_address' => $ip,
                    'url' => Str::limit($url, 1024),
                    'route_name' => $routeName,
                    'page_title' => Str::limit($pageTitle, 255),
                    'viewable_type' => $viewableInfo['type'],
                    'viewable_id' => $viewableInfo['id'],
                    'referrer' => Str::limit($referrer, 1024),
                    'referrer_domain' => $trafficSource,
                    'device_type' => $deviceType,
                    'browser' => $browser,
                    'platform' => $platform,
                    'created_at' => $now,
                ]);

                // Increment total view counts on Product or BlogPost
                if ($viewableInfo['type'] === 'product' && $viewableInfo['id']) {
                    Product::where('id', $viewableInfo['id'])->increment('views_count');
                } elseif ($viewableInfo['type'] === 'blog_post' && $viewableInfo['id']) {
                    BlogPost::where('id', $viewableInfo['id'])->increment('views_count');
                }
            }
        } catch (\Throwable $e) {
            // Silently ignore to protect customer request flow
        }
    }

    /**
     * Heartbeat ping to keep session alive while visitor is reading/browsing
     */
    public function ping(Request $request): bool
    {
        $sessionId = $request->cookie('kg_analytics_sid') ?? $request->input('sid');
        if (!$sessionId) return false;

        $url = $request->input('url');
        $title = $request->input('title');

        try {
            $session = AnalyticsActiveSession::where('session_id', $sessionId)->first();
            if ($session) {
                $session->last_active_at = now();
                if ($url) $session->current_url = Str::limit($url, 1024);
                if ($title) $session->current_title = Str::limit($title, 255);
                $session->save();
                return true;
            }
        } catch (\Throwable $e) {}

        return false;
    }

    /**
     * Resolve if the current request is viewing a Product, Blog, Category, etc.
     */
    private function resolveViewable(Request $request): array
    {
        $routeName = $request->route()?->getName();
        $slug = $request->route('slug') ?? $request->route('category');

        if ($routeName === 'product' && $slug) {
            $product = Product::where('slug', $slug)->first(['id', 'name']);
            if ($product) {
                return [
                    'type' => 'product',
                    'id' => $product->id,
                    'title' => $product->name,
                ];
            }
        }

        if ($routeName === 'blog' && $slug) {
            $post = BlogPost::where('slug', $slug)->first(['id', 'title']);
            if ($post) {
                return [
                    'type' => 'blog_post',
                    'id' => $post->id,
                    'title' => $post->title,
                ];
            }
        }

        if ($routeName === 'category' && $slug) {
            $category = Category::where('slug', $slug)->first(['id', 'name']);
            if ($category) {
                return [
                    'type' => 'category',
                    'id' => $category->id,
                    'title' => $category->name . ' (Category)',
                ];
            }
        }

        return [
            'type' => null,
            'id' => null,
            'title' => null,
        ];
    }

    /**
     * Format a human-friendly page title from route name
     */
    private function formatTitleFromRoute(?string $routeName, string $path): string
    {
        return match ($routeName) {
            'home' => 'Home Page',
            'blog.index', 'blog' => 'Blog Posts',
            'checkout' => 'Checkout Page',
            'thank-you' => 'Order Confirmation (Thank You)',
            'category' => 'Shop Category',
            'customer-spotlight.load-more' => 'Customer Spotlight',
            'customer-feedback.load-more' => 'Customer Feedback',
            'philanthropic-work' => 'Philanthropic Work',
            default => $path === '' ? 'Home Page' : ucfirst(str_replace(['-', '/'], [' ', ' > '], $path)),
        };
    }

    /**
     * Determine accurate traffic source combining UTM, query tags, and referrer domain
     */
    private function determineTrafficSource(Request $request, ?string $referrer): string
    {
        // 1. Check UTM tags or ad identifiers
        $utmSource = strtolower(trim($request->query('utm_source', '')));
        if (!empty($utmSource)) {
            if (str_contains($utmSource, 'facebook') || str_contains($utmSource, 'fb')) return 'Facebook (Ad/Campaign)';
            if (str_contains($utmSource, 'google')) return 'Google (Ad/Campaign)';
            if (str_contains($utmSource, 'instagram') || str_contains($utmSource, 'ig')) return 'Instagram (Campaign)';
            if (str_contains($utmSource, 'youtube')) return 'YouTube (Campaign)';
            if (str_contains($utmSource, 'tiktok')) return 'TikTok (Campaign)';
            if (str_contains($utmSource, 'email') || str_contains($utmSource, 'newsletter')) return 'Email / Newsletter';
            return Str::limit(ucfirst($utmSource), 50);
        }

        if ($request->has('fbclid')) return 'Facebook';
        if ($request->has('gclid')) return 'Google Ads';
        if ($request->has('ttclid')) return 'TikTok';

        // 2. Check Referrer
        if (empty($referrer)) {
            return 'Direct';
        }

        $parsed = parse_url($referrer);
        $host = strtolower($parsed['host'] ?? '');
        $currentHost = strtolower($request->getHost());

        if (empty($host) || $host === $currentHost || str_ends_with($host, '.' . $currentHost)) {
            return 'Direct';
        }

        $host = preg_replace('/^www\./', '', $host);

        if (str_contains($host, 'google.')) return 'Google';
        if (str_contains($host, 'facebook.') || str_contains($host, 'fb.com') || str_contains($host, 'fb.watch')) return 'Facebook';
        if (str_contains($host, 'instagram.')) return 'Instagram';
        if (str_contains($host, 'youtube.') || str_contains($host, 'youtu.be')) return 'YouTube';
        if (str_contains($host, 'tiktok.')) return 'TikTok';
        if (str_contains($host, 'whatsapp') || str_contains($host, 'wa.me')) return 'WhatsApp';
        if (str_contains($host, 'bikroy.')) return 'Bikroy';
        if (str_contains($host, 'daraz.')) return 'Daraz';
        if (str_contains($host, 'twitter.') || str_contains($host, 'x.com')) return 'X (Twitter)';
        if (str_contains($host, 'linkedin.')) return 'LinkedIn';
        if (str_contains($host, 'bing.')) return 'Bing';

        return Str::limit($host, 60);
    }

    /**
     * Detect device type: mobile, tablet, desktop
     */
    private function detectDevice(string $ua): string
    {
        $uaLower = strtolower($ua);
        if (preg_match('/(ipad|tablet|(android(?!.*mobile))|(windows(?!.*phone)(.*touch))|kindle|playbook|silk)/i', $uaLower)) {
            return 'Tablet';
        }
        if (preg_match('/(mobile|android|iphone|ipod|blackberry|opera mini|iemobile|wpdesktop)/i', $uaLower)) {
            return 'Mobile';
        }
        return 'Desktop';
    }

    /**
     * Detect browser
     */
    private function detectBrowser(string $ua): string
    {
        if (str_contains($ua, 'Edg/')) return 'Edge';
        if (str_contains($ua, 'OPR/') || str_contains($ua, 'Opera')) return 'Opera';
        if (str_contains($ua, 'SamsungBrowser')) return 'Samsung Browser';
        if (str_contains($ua, 'Chrome/') && !str_contains($ua, 'Chromium/')) return 'Chrome';
        if (str_contains($ua, 'Safari/') && !str_contains($ua, 'Chrome/')) return 'Safari';
        if (str_contains($ua, 'Firefox/')) return 'Firefox';
        return 'Browser';
    }

    /**
     * Detect OS / Platform
     */
    private function detectPlatform(string $ua): string
    {
        if (str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'iPhone')) return 'iPhone (iOS)';
        if (str_contains($ua, 'iPad')) return 'iPad (iPadOS)';
        if (str_contains($ua, 'Windows NT 10.0')) return 'Windows 10/11';
        if (str_contains($ua, 'Windows')) return 'Windows';
        if (str_contains($ua, 'Macintosh') || str_contains($ua, 'Mac OS')) return 'macOS';
        if (str_contains($ua, 'Linux')) return 'Linux';
        return 'Unknown OS';
    }

    /**
     * Detect bot / crawler user agents
     */
    private function isBot(string $ua): bool
    {
        if (empty($ua)) return true;
        return (bool) preg_match('/(bot|crawl|spider|slurp|facebookexternalhit|whatsapp|telegrambot|twitterbot|pinterest|curl|wget|python|postman|lighthouse)/i', $ua);
    }
}
