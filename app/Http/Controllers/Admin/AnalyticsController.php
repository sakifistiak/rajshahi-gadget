<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnalyticsActiveSession;
use App\Models\AnalyticsVisit;
use App\Models\BlogPost;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    /**
     * Display the main analytics dashboard
     */
    public function index(Request $request)
    {
        $period = $request->query('period', '7d');
        if (!in_array($period, ['today', 'yesterday', '7d', '30d', 'all'])) {
            $period = '7d';
        }

        // 1. Live Real-Time Active Sessions (<3 minutes ago)
        $liveCount = AnalyticsActiveSession::active(3)->count();
        $liveSessions = AnalyticsActiveSession::active(3)
            ->orderByDesc('last_active_at')
            ->limit(50)
            ->get();

        $liveSessionsFormatted = $liveSessions->map(function ($s) {
            return [
                'id' => $s->id,
                'session_id' => substr($s->session_id, 0, 8) . '...',
                'current_title' => $s->current_title ?: 'Browsing Store',
                'current_url' => $s->current_url,
                'referrer_domain' => $s->referrer_domain ?: 'direct',
                'device_type' => $s->device_type ?: 'desktop',
                'browser' => $s->browser ?: 'Browser',
                'platform' => $s->platform ?: 'OS',
                'time_ago' => $s->last_active_at ? Carbon::parse($s->last_active_at)->diffForHumans() : 'Just now',
                'is_viewing_product' => $s->viewable_type === 'product',
                'is_viewing_blog' => $s->viewable_type === 'blog_post',
            ];
        })->values()->all();

        // 2. High-level metric KPI counts for selected period
        $visitsQuery = AnalyticsVisit::period($period);
        $totalVisits = (clone $visitsQuery)->count();
        $uniqueVisitors = (clone $visitsQuery)->distinct('visitor_hash')->count('visitor_hash');
        $productViews = (clone $visitsQuery)->where('viewable_type', 'product')->count();
        $blogViews = (clone $visitsQuery)->where('viewable_type', 'blog_post')->count();

        // 3. Product Reach (Top Products in period)
        $topProductsData = AnalyticsVisit::period($period)
            ->where('viewable_type', 'product')
            ->whereNotNull('viewable_id')
            ->select('viewable_id', DB::raw('count(*) as views_total'), DB::raw('count(distinct visitor_hash) as unique_visitors'))
            ->groupBy('viewable_id')
            ->orderByDesc('views_total')
            ->limit(20)
            ->get();

        $productIds = $topProductsData->pluck('viewable_id')->all();
        $products = Product::whereIn('id', $productIds)
            ->with(['category', 'images'])
            ->get()
            ->keyBy('id');

        $productReach = $topProductsData->map(function ($row) use ($products, $totalVisits) {
            $product = $products->get($row->viewable_id);
            return [
                'product' => $product,
                'product_id' => $row->viewable_id,
                'name' => $product ? $product->name : 'Unknown / Deleted Product #' . $row->viewable_id,
                'slug' => $product?->slug,
                'price' => $product?->price,
                'thumbnail' => $product?->images?->first()?->image_url ?? $product?->image,
                'in_stock' => $product?->in_stock ?? false,
                'views_total' => $row->views_total,
                'unique_visitors' => $row->unique_visitors,
                'all_time_views' => $product?->views_count ?? 0,
                'share_pct' => $totalVisits > 0 ? round(($row->views_total / $totalVisits) * 100, 1) : 0,
            ];
        });

        // 4. Blog Reach (Top Blog Posts in period)
        $topBlogsData = AnalyticsVisit::period($period)
            ->where('viewable_type', 'blog_post')
            ->whereNotNull('viewable_id')
            ->select('viewable_id', DB::raw('count(*) as views_total'), DB::raw('count(distinct visitor_hash) as unique_readers'))
            ->groupBy('viewable_id')
            ->orderByDesc('views_total')
            ->limit(20)
            ->get();

        $blogIds = $topBlogsData->pluck('viewable_id')->all();
        $blogs = BlogPost::whereIn('id', $blogIds)
            ->get()
            ->keyBy('id');

        $blogReach = $topBlogsData->map(function ($row) use ($blogs, $totalVisits) {
            $blog = $blogs->get($row->viewable_id);
            return [
                'blog' => $blog,
                'blog_id' => $row->viewable_id,
                'title' => $blog ? $blog->title : 'Unknown / Deleted Post #' . $row->viewable_id,
                'slug' => $blog?->slug,
                'image' => $blog?->image,
                'views_total' => $row->views_total,
                'unique_readers' => $row->unique_readers,
                'all_time_views' => $blog?->views_count ?? 0,
                'share_pct' => $totalVisits > 0 ? round(($row->views_total / $totalVisits) * 100, 1) : 0,
            ];
        });

        // 5. Traffic Sources (Referrer distribution)
        $sourcesData = AnalyticsVisit::period($period)
            ->select('referrer_domain', DB::raw('count(*) as count'), DB::raw('count(distinct visitor_hash) as unique_visitors'))
            ->groupBy('referrer_domain')
            ->orderByDesc('count')
            ->limit(15)
            ->get();

        $trafficSources = $sourcesData->map(function ($s) use ($totalVisits) {
            return [
                'domain' => $s->referrer_domain ?: 'direct',
                'name' => ucfirst($s->referrer_domain ?: 'Direct / Unknown'),
                'count' => $s->count,
                'unique_visitors' => $s->unique_visitors,
                'share_pct' => $totalVisits > 0 ? round(($s->count / $totalVisits) * 100, 1) : 0,
            ];
        });

        // 6. Device breakdown
        $deviceData = AnalyticsVisit::period($period)
            ->select('device_type', DB::raw('count(*) as count'))
            ->groupBy('device_type')
            ->orderByDesc('count')
            ->get();

        // 7. Browser breakdown
        $browserData = AnalyticsVisit::period($period)
            ->select('browser', DB::raw('count(*) as count'))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

        // 8. Platform breakdown
        $platformData = AnalyticsVisit::period($period)
            ->select('platform', DB::raw('count(*) as count'))
            ->groupBy('platform')
            ->orderByDesc('count')
            ->limit(8)
            ->get();

        return view('admin.analytics.index', compact(
            'period',
            'liveCount',
            'liveSessions',
            'liveSessionsFormatted',
            'totalVisits',
            'uniqueVisitors',
            'productViews',
            'blogViews',
            'productReach',
            'blogReach',
            'trafficSources',
            'deviceData',
            'browserData',
            'platformData'
        ));
    }

    /**
     * Polling endpoint returning live active visitors count and real-time feed
     */
    public function liveStats(): JsonResponse
    {
        $activeSessions = AnalyticsActiveSession::active(3)
            ->orderByDesc('last_active_at')
            ->limit(40)
            ->get();

        $formatted = $activeSessions->map(function ($s) {
            return [
                'id' => $s->id,
                'session_id' => substr($s->session_id, 0, 8) . '...',
                'current_title' => $s->current_title ?: 'Browsing Store',
                'current_url' => $s->current_url,
                'referrer_domain' => $s->referrer_domain ?: 'direct',
                'device_type' => $s->device_type ?: 'desktop',
                'browser' => $s->browser ?: 'Browser',
                'platform' => $s->platform ?: 'OS',
                'time_ago' => $s->last_active_at ? Carbon::parse($s->last_active_at)->diffForHumans() : 'Just now',
                'is_viewing_product' => $s->viewable_type === 'product',
                'is_viewing_blog' => $s->viewable_type === 'blog_post',
            ];
        });

        return response()->json([
            'live_count' => $activeSessions->count(),
            'sessions' => $formatted,
            'updated_at' => now()->format('h:i:s A'),
        ]);
    }
}
