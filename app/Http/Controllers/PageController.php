<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\BlogPost;
use App\Models\HeroSlider;
use App\Models\PromoBanner;
use App\Models\Testimonial;
use App\Models\CustomerFeedback;
use App\Models\CustomerSpotlight;
use App\Models\PhilanthropicWork;
use App\Models\SiteSetting;
use App\Models\Condition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    public function home()
    {
        $heroSliders = HeroSlider::where('is_active', true)->orderBy('sort_order')->get();
        $promoBanners = PromoBanner::where('is_active', true)->orderBy('sort_order')->get();
        $allProducts = Product::with(['category', 'brand', 'condition', 'images', 'highlights'])->where('in_stock', true)->latest()->get();
        
        $flashDeals = $allProducts->where('compare_at_price', '!=', null)->take(4);
        if ($flashDeals->isEmpty()) {
            $flashDeals = $allProducts->take(4);
        }

        $intactProducts = $allProducts->filter(function($p) {
            return optional($p->condition)->slug === 'intact';
        })->take(4);

        $withoutBoxProducts = $allProducts->filter(function($p) {
            return optional($p->condition)->slug === 'without-box';
        })->take(4);

        $preOwnedProducts = $allProducts->filter(function($p) {
            return optional($p->condition)->slug === 'pre-owned';
        })->take(4);

        return view('pages.home', compact(
            'heroSliders',
            'promoBanners',
            'allProducts',
            'flashDeals',
            'intactProducts',
            'withoutBoxProducts',
            'preOwnedProducts'
        ));
    }

    public function ajaxSearch(Request $request)
    {
        $query = trim($request->input('q', ''));
        if (strlen($query) < 3) {
            return response()->json(['products' => []]);
        }

        $products = Product::with(['category', 'brand', 'images'])
            ->where('name', 'like', "%{$query}%")
            ->orWhere('description', 'like', "%{$query}%")
            ->orWhereHas('category', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhereHas('brand', function($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->take(8)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => '৳ ' . number_format($product->price),
                    'compare_at_price' => $product->compare_at_price ? '৳ ' . number_format($product->compare_at_price) : null,
                    'image' => $product->primaryImage(),
                    'category' => optional($product->category)->name ?? 'Gadget',
                    'url' => route('product', $product->slug),
                ];
            });

        return response()->json(['products' => $products]);
    }

    public function page(string $page, Request $request)
    {
        if ($page === 'shop') {
            return $this->shop($request);
        }

        return $this->render('pages.' . $page);
    }

    public function shop(Request $request)
    {
        $query = Product::with(['category', 'brand', 'condition', 'images', 'highlights']);

        // Condition filter
        if ($request->filled('condition')) {
            $cond = $request->condition;
            $query->whereHas('condition', function($q) use ($cond) {
                $q->where('slug', $cond);
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $cat = $request->category;
            $query->whereHas('category', function($q) use ($cat) {
                $q->where('slug', $cat);
            });
        }

        // Brand filter
        if ($request->filled('brand')) {
            $b = $request->brand;
            $query->whereHas('brand', function($q) use ($b) {
                $q->where('slug', $b);
            });
        }

        // Search query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort order
        if ($request->sort === 'price-asc') {
            $query->orderBy('price', 'asc');
        } elseif ($request->sort === 'price-desc') {
            $query->orderBy('price', 'desc');
        } else {
            $query->latest();
        }

        $products = $query->paginate(48)->withQueryString();
        $categories = Category::all();
        $brands = Brand::all();
        $conditions = Condition::all();

        return view('pages.shop', compact('products', 'categories', 'brands', 'conditions'));
    }

    public function product(string $slug)
    {
        $product = Product::with(['category', 'brand', 'condition', 'images', 'highlights', 'specs', 'colors'])
            ->where('slug', $slug)
            ->first();

        if ($product) {
            $relatedProducts = Product::with(['category', 'brand', 'condition', 'images'])
                ->where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->take(4)
                ->get();

            return view('pages.product.detail', compact('product', 'relatedProducts'));
        }

        if (View::exists('pages.product.' . $slug)) {
            return view('pages.product.' . $slug);
        }

        throw new NotFoundHttpException();
    }

    public function blog(string $slug)
    {
        $post = BlogPost::where('slug', $slug)->first();
        if ($post) {
            return view('pages.blog.detail', compact('post'));
        }
        return $this->render('pages.blog.' . $slug);
    }

    public function category(string $category, Request $request)
    {
        $request->merge(['category' => $category]);
        return $this->shop($request);
    }

    private function render(string $view)
    {
        if (! View::exists($view)) {
            throw new NotFoundHttpException();
        }

        return view($view);
    }
}
