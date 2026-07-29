<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\BlogPost;
use App\Models\CustomerFeedback;
use App\Models\HeroSlider;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalProducts = Product::count();
        $inStockProducts = Product::where('in_stock', true)->count();
        $outOfStockProducts = Product::where('in_stock', false)->count();

        $stats = [
            'total_products' => $totalProducts,
            'in_stock' => $inStockProducts,
            'out_of_stock' => $outOfStockProducts,
            'total_categories' => Category::count(),
            'total_brands' => Brand::count(),
            'total_blogs' => BlogPost::count(),
            'total_feedbacks' => CustomerFeedback::count(),
            'total_sliders' => HeroSlider::count(),
        ];

        $recentProducts = Product::with(['category', 'brand', 'condition'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentProducts'));
    }
}
