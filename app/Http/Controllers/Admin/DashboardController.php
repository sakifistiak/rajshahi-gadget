<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\BlogPost;
use App\Models\CustomerFeedback;
use App\Models\HeroSlider;
use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $productStats = Product::selectRaw('
            COUNT(*) as total_products,
            COUNT(CASE WHEN in_stock = 1 THEN 1 END) as in_stock,
            COUNT(CASE WHEN in_stock = 0 THEN 1 END) as out_of_stock
        ')->first();

        $orderStats = Order::selectRaw('
            COUNT(*) as total_orders,
            COUNT(CASE WHEN DATE(created_at) = CURRENT_DATE THEN 1 END) as today_orders,
            COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_orders,
            COALESCE(SUM(CASE WHEN status = "delivered" THEN total ELSE 0 END), 0) as total_revenue
        ')->first();

        $stats = [
            'total_products' => $productStats->total_products ?? 0,
            'in_stock' => $productStats->in_stock ?? 0,
            'out_of_stock' => $productStats->out_of_stock ?? 0,
            'total_categories' => Category::count(),
            'total_brands' => Brand::count(),
            'total_blogs' => BlogPost::count(),
            'total_feedbacks' => CustomerFeedback::count(),
            'total_sliders' => HeroSlider::count(),
            'today_orders' => $orderStats->today_orders ?? 0,
            'total_orders' => $orderStats->total_orders ?? 0,
            'pending_orders' => $orderStats->pending_orders ?? 0,
            'total_revenue' => $orderStats->total_revenue ?? 0,
        ];

        $recentProducts = Product::with(['category', 'brand', 'condition'])->latest()->take(5)->get();
        $recentOrders = Order::with('items')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentProducts', 'recentOrders'));
    }
}
