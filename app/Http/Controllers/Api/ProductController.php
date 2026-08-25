<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Condition;
use App\Models\BlogPost;
use App\Models\Testimonial;
use App\Models\CustomerFeedback;
use App\Models\CustomerSpotlight;
use App\Models\PhilanthropicWork;
use App\Models\HeroSlider;
use App\Models\SiteSetting;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * সব প্রোডাক্ট লিস্ট (filter, sort, search সহ)
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['brand', 'category', 'condition', 'images', 'highlights', 'specs', 'colors']);

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', fn($q) => $q->where('slug', $request->category));
        }

        // Filter by condition
        if ($request->has('condition')) {
            $query->whereHas('condition', fn($q) => $q->where('slug', $request->condition));
        }

        // Filter by brand
        if ($request->has('brand')) {
            $query->whereHas('brand', fn($q) => $q->where('slug', $request->brand));
        }

        // Price range filter
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('brand', fn($bq) => $bq->where('name', 'like', "%{$search}%"));
            });
        }

        // Sort
        $sort = $request->get('sort', 'featured');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'rating':
                $query->orderBy('rating', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderByRaw("FIELD(badge, 'Bestseller', 'New', 'Sale', 'Limited', 'Trending') ASC")
                      ->orderBy('reviews_count', 'desc');
        }

        return response()->json($query->get());
    }

    /**
     * সিঙ্গেল প্রোডাক্ট (slug দিয়ে)
     */
    public function show(string $slug): JsonResponse
    {
        $product = Product::with(['brand', 'category', 'condition', 'images', 'highlights', 'specs', 'colors'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($product);
    }

    /**
     * ক্যাটাগরি লিস্ট
     */
    public function categories(): JsonResponse
    {
        return response()->json(Category::orderBy('sort_order')->get());
    }

    /**
     * ক্যাটাগরি-wise প্রোডাক্ট
     */
    public function byCategory(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $products = Product::with(['brand', 'category', 'condition', 'images', 'highlights', 'specs', 'colors'])
            ->where('category_id', $category->id)
            ->get();

        return response()->json([
            'category' => $category,
            'products' => $products,
        ]);
    }

    /**
     * Condition-wise প্রোডাক্ট
     */
    public function byCondition(string $slug): JsonResponse
    {
        $condition = Condition::where('slug', $slug)->firstOrFail();
        $products = Product::with(['brand', 'category', 'condition', 'images', 'highlights', 'specs', 'colors'])
            ->where('condition_id', $condition->id)
            ->get();

        return response()->json([
            'condition' => $condition,
            'products' => $products,
        ]);
    }

    /**
     * ব্র্যান্ড লিস্ট
     */
    public function brands(): JsonResponse
    {
        return response()->json(Brand::all());
    }

    /**
     * কন্ডিশন লিস্ট
     */
    public function conditions(): JsonResponse
    {
        return response()->json(Condition::all());
    }

    /**
     * ব্লগ লিস্ট
     */
    public function blogIndex(): JsonResponse
    {
        return response()->json(BlogPost::orderBy('published_at', 'desc')->get());
    }

    /**
     * সিঙ্গেল ব্লগ
     */
    public function blogShow(string $slug): JsonResponse
    {
        return response()->json(
            BlogPost::where('slug', $slug)->firstOrFail()
        );
    }

    /**
     * Testimonials
     */
    public function testimonials(): JsonResponse
    {
        return response()->json(Testimonial::orderBy('sort_order')->get());
    }

    /**
     * Customer Feedbacks
     */
    public function feedbacks(): JsonResponse
    {
        return response()->json(CustomerFeedback::orderBy('date', 'desc')->get());
    }

    /**
     * Customer Spotlights
     */
    public function spotlights(): JsonResponse
    {
        return response()->json(CustomerSpotlight::orderBy('date', 'desc')->get());
    }

    /**
     * Philanthropic Works
     */
    public function philanthropicWorks(): JsonResponse
    {
        return response()->json(PhilanthropicWork::orderBy('id', 'desc')->get());
    }

    /**
     * Hero Sliders
     */
    public function heroSliders(): JsonResponse
    {
        return response()->json(HeroSlider::active()->get());
    }

    /**
     * Site Settings
     */
    public function settings(): JsonResponse
    {
        $settings = SiteSetting::pluck('value', 'key');
        return response()->json($settings);
    }
}
