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

        // Home settings
        $homeHeroActive        = SiteSetting::getValue('home_hero_active', '1') == '1';
        $homeFlashActive       = SiteSetting::getValue('home_flash_active', '1') == '1';
        $homeFlashTitle        = SiteSetting::getValue('home_flash_title', 'Limited time deals');
        $homeFlashHighlight    = SiteSetting::getValue('home_flash_highlight', 'deals');

        $homeSec1Active        = SiteSetting::getValue('home_sec1_active', '1') == '1';
        $homeSec1Title         = SiteSetting::getValue('home_sec1_title', 'Brand new intact box');
        $homeSec1Highlight     = SiteSetting::getValue('home_sec1_highlight', 'intact box');
        $homeSec1Filter        = SiteSetting::getValue('home_sec1_filter', 'cond_intact');
        $homeSec1Limit         = SiteSetting::getValue('home_sec1_limit', '4');

        $homeSec2Active        = SiteSetting::getValue('home_sec2_active', '1') == '1';
        $homeSec2Title         = SiteSetting::getValue('home_sec2_title', 'Brand new without box');
        $homeSec2Highlight     = SiteSetting::getValue('home_sec2_highlight', 'without box');
        $homeSec2Filter        = SiteSetting::getValue('home_sec2_filter', 'cond_without-box');
        $homeSec2Limit         = SiteSetting::getValue('home_sec2_limit', '4');

        $homeSec3Active        = SiteSetting::getValue('home_sec3_active', '1') == '1';
        $homeSec3Title         = SiteSetting::getValue('home_sec3_title', 'Certified pre-owned');
        $homeSec3Highlight     = SiteSetting::getValue('home_sec3_highlight', 'pre-owned');
        $homeSec3Filter        = SiteSetting::getValue('home_sec3_filter', 'cond_pre-owned');
        $homeSec3Limit         = SiteSetting::getValue('home_sec3_limit', '4');

        $homePromosActive       = SiteSetting::getValue('home_promos_active', '1') == '1';
        $homeTestimonialsActive = SiteSetting::getValue('home_testimonials_active', '1') == '1';

        $sec1Products = $this->getFilteredProducts($allProducts, $homeSec1Filter, $homeSec1Limit);
        $sec2Products = $this->getFilteredProducts($allProducts, $homeSec2Filter, $homeSec2Limit);
        $sec3Products = $this->getFilteredProducts($allProducts, $homeSec3Filter, $homeSec3Limit);

        // Backwards compatibility fallbacks
        $intactProducts     = $sec1Products;
        $withoutBoxProducts = $sec2Products;
        $preOwnedProducts   = $sec3Products;

        return view('pages.home', compact(
            'heroSliders',
            'promoBanners',
            'allProducts',
            'flashDeals',
            'intactProducts',
            'withoutBoxProducts',
            'preOwnedProducts',
            'sec1Products',
            'sec2Products',
            'sec3Products',
            'homeHeroActive',
            'homeFlashActive',
            'homeFlashTitle',
            'homeFlashHighlight',
            'homeSec1Active',
            'homeSec1Title',
            'homeSec1Highlight',
            'homeSec2Active',
            'homeSec2Title',
            'homeSec2Highlight',
            'homeSec3Active',
            'homeSec3Title',
            'homeSec3Highlight',
            'homePromosActive',
            'homeTestimonialsActive'
        ));
    }

    private function getFilteredProducts($allProducts, $filter, $limit)
    {
        $limit = (int) ($limit ?: 4);
        if (str_starts_with($filter, 'cond_')) {
            $slug = substr($filter, 5);
            return $allProducts->filter(function($p) use ($slug) {
                return optional($p->condition)->slug === $slug;
            })->take($limit);
        } elseif (str_starts_with($filter, 'cat_')) {
            $catId = (int) substr($filter, 4);
            return $allProducts->filter(function($p) use ($catId) {
                return $p->category_id == $catId;
            })->take($limit);
        }
        return $allProducts->take($limit);
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

        // Max Price filter
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float)$request->max_price);
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
