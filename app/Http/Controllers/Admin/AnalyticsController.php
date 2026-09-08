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

        $periodLabels = [
            'today' => 'Today',
            'yesterday' => 'Yesterday',
            '7d' => 'Last 7 Days',
            '30d' => 'Last 30 Days',
            'all' => 'All Time',
        ];
        $periodLabel = $periodLabels[$period] ?? 'Last 7 Days';

        // 1. Live Active Sessions (<5 minutes ago)
        $liveCount = AnalyticsActiveSession::active(5)->count();
        $liveSessions = AnalyticsActiveSession::active(5)
            ->orderByDesc('last_active_at')
            ->limit(50)
            ->get();

        $liveSessionsFormatted = $liveSessions->map(function ($s) {
            $timeSpentSeconds = $s->first_seen_at && $s->last_active_at
                ? max(1, $s->last_active_at->diffInSeconds($s->first_seen_at))
                : 0;

            if ($timeSpentSeconds < 60) {
                $duration = $timeSpentSeconds . 's';
            } elseif ($timeSpentSeconds < 3600) {
                $duration = floor($timeSpentSeconds / 60) . 'm ' . ($timeSpentSeconds % 60) . 's';
            } else {
                $duration = floor($timeSpentSeconds / 3600) . 'h ' . floor(($timeSpentSeconds % 3600) / 60) . 'm';
            }

            return [
                'id' => $s->id,
                'session_id' => substr($s->session_id, 0, 8),
                'ip_address' => $s->ip_address ?: '127.0.0.1',
                'current_title' => $s->current_title ?: 'Browsing Store',
                'current_url' => $s->current_url,
                'referrer_domain' => $s->referrer_domain ?: 'Direct',
                'device_type' => $s->device_type ?: 'Desktop',
                'browser' => $s->browser ?: 'Browser',
                'platform' => $s->platform ?: 'OS',
                'duration' => $duration,
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
            ->limit(30)
            ->get();

        $productIds = $topProductsData->pluck('viewable_id')->all();
        $products = Product::whereIn('id', $productIds)
            ->with(['category', 'images'])
            ->get()
            ->keyBy('id');

        $productReach = $topProductsData->map(function ($row) use ($products, $productViews) {
            $product = $products->get($row->viewable_id);
            return [
                'product' => $product,
                'product_id' => $row->viewable_id,
                'name' => $product ? $product->name : 'Product #' . $row->viewable_id,
                'category_name' => $product?->category?->name ?? 'Uncategorized',
                'slug' => $product?->slug,
                'price' => $product?->price,
                'thumbnail' => $product?->primaryImage() ?? $product?->image,
                'in_stock' => $product?->in_stock ?? false,
                'views_total' => $row->views_total,
                'unique_visitors' => $row->unique_visitors,
                'all_time_views' => $product?->views_count ?? 0,
                'share_pct' => $productViews > 0 ? round(($row->views_total / $productViews) * 100, 1) : 0,
            ];
        });

        // 4. Blog Reach (Top Blog Posts in period)
        $topBlogsData = AnalyticsVisit::period($period)
            ->where('viewable_type', 'blog_post')
            ->whereNotNull('viewable_id')
            ->select('viewable_id', DB::raw('count(*) as views_total'), DB::raw('count(distinct visitor_hash) as unique_readers'))
            ->groupBy('viewable_id')
            ->orderByDesc('views_total')
            ->limit(30)
            ->get();

        $blogIds = $topBlogsData->pluck('viewable_id')->all();
        $blogs = BlogPost::whereIn('id', $blogIds)
            ->get()
            ->keyBy('id');

        $blogReach = $topBlogsData->map(function ($row) use ($blogs, $blogViews) {
            $blog = $blogs->get($row->viewable_id);
            return [
                'blog' => $blog,
                'blog_id' => $row->viewable_id,
                'title' => $blog ? $blog->title : 'Post #' . $row->viewable_id,
                'slug' => $blog?->slug,
                'image' => $blog?->featured_image ?: '/assets/no-image-placeholder.svg',
                'created_at' => $blog?->created_at?->format('d M, Y') ?? '—',
                'views_total' => $row->views_total,
                'unique_readers' => $row->unique_readers,
                'all_time_views' => $blog?->views_count ?? 0,
                'share_pct' => $blogViews > 0 ? round(($row->views_total / $blogViews) * 100, 1) : 0,
            ];
        });

        // 5. Traffic Sources
        $sourcesData = AnalyticsVisit::period($period)
            ->select('referrer_domain', DB::raw('count(*) as count'), DB::raw('count(distinct visitor_hash) as unique_visitors'))
            ->groupBy('referrer_domain')
            ->orderByDesc('count')
            ->limit(20)
            ->get();

        $trafficSources = $sourcesData->map(function ($s) use ($totalVisits) {
            return [
                'domain' => $s->referrer_domain ?: 'Direct',
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
            ->limit(10)
            ->get();

        // 8. Platform breakdown
        $platformData = AnalyticsVisit::period($period)
            ->select('platform', DB::raw('count(*) as count'))
            ->groupBy('platform')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // 9. Daily History (Last 14 days or in period)
        $dailyData = AnalyticsVisit::period($period)
            ->select(
                DB::raw('DATE(created_at) as visit_date'),
                DB::raw('count(*) as total_visits'),
                DB::raw('count(distinct visitor_hash) as unique_visitors'),
                DB::raw('sum(case when viewable_type = "product" then 1 else 0 end) as product_views'),
                DB::raw('sum(case when viewable_type = "blog_post" then 1 else 0 end) as blog_views')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderByDesc('visit_date')
            ->limit(30)
            ->get();

        return view('admin.analytics.index', compact(
            'period',
            'periodLabel',
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
            'platformData',
            'dailyData'
        ));
    }

    /**
     * Polling endpoint returning live active visitors count and real-time feed
     */
    public function liveStats(): JsonResponse
    {
        $activeSessions = AnalyticsActiveSession::active(5)
            ->orderByDesc('last_active_at')
            ->limit(50)
            ->get();

        $formatted = $activeSessions->map(function ($s) {
            $timeSpentSeconds = $s->first_seen_at && $s->last_active_at
                ? max(1, $s->last_active_at->diffInSeconds($s->first_seen_at))
                : 0;

            if ($timeSpentSeconds < 60) {
                $duration = $timeSpentSeconds . 's';
            } elseif ($timeSpentSeconds < 3600) {
                $duration = floor($timeSpentSeconds / 60) . 'm ' . ($timeSpentSeconds % 60) . 's';
            } else {
                $duration = floor($timeSpentSeconds / 3600) . 'h ' . floor(($timeSpentSeconds % 3600) / 60) . 'm';
            }

            return [
                'id' => $s->id,
                'session_id' => substr($s->session_id, 0, 8),
                'ip_address' => $s->ip_address ?: '127.0.0.1',
                'current_title' => $s->current_title ?: 'Browsing Store',
                'current_url' => $s->current_url,
                'referrer_domain' => $s->referrer_domain ?: 'Direct',
                'device_type' => $s->device_type ?: 'Desktop',
                'browser' => $s->browser ?: 'Browser',
                'platform' => $s->platform ?: 'OS',
                'duration' => $duration,
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
