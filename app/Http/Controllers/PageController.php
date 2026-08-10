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
use App\Support\SectionTitleStyle;
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
        $homeFlashTitleStyle   = SectionTitleStyle::sanitizeFull(
            json_decode(SiteSetting::getValue('home_flash_title_style', '{}'), true)
        );
        $homePromosActive       = SiteSetting::getValue('home_promos_active', '1') == '1';
        $homeTestimonialsActive = SiteSetting::getValue('home_testimonials_active', '1') == '1';
        $homeTickerActive       = SiteSetting::getValue('home_ticker_active', '1') == '1';
        $defaultTickerText      = "🎉 Eid Special: Up to 15% off on Brand New Intact Box iPhones\n🚚 Same-day delivery inside Dhaka on orders before 3 PM\n🛡️ 7-day easy replacement on all Pre-Owned products\n💳 0% EMI up to 12 months on selected products\n📞 Chat with us on WhatsApp for instant support";
        $homeTickerText         = SiteSetting::getValue('home_ticker_text', $defaultTickerText);
        $homeTickerItems        = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $homeTickerText))));
        $homeTickerEffect       = in_array(SiteSetting::getValue('home_ticker_effect', 'fade'), ['fade', 'scroll'], true)
            ? SiteSetting::getValue('home_ticker_effect', 'fade')
            : 'fade';
        $homeTickerSpeed        = (float) SiteSetting::getValue('home_ticker_speed', '6');



        // Popup Offer Settings
        $popupOfferSettings = [
            'active'        => SiteSetting::getValue('popup_offer_active', '0') == '1',
            'image'         => SiteSetting::getValue('popup_offer_image', ''),
            'image_mobile'  => SiteSetting::getValue('popup_offer_image_mobile', ''),
            'link'          => SiteSetting::getValue('popup_offer_link', '/shop'),
            'target'        => SiteSetting::getValue('popup_offer_target', '_self'),
            'frequency'     => SiteSetting::getValue('popup_offer_frequency', 'session'),
            'delay'         => (float) SiteSetting::getValue('popup_offer_delay', '1'),
            'backdrop_blur' => SiteSetting::getValue('popup_offer_backdrop_blur', 'md'),
        ];



        // Load dynamic product sections
        $sectionsJson = SiteSetting::getValue('home_sections_json');
        $sectionsList = [];
        if ($sectionsJson) {
            $decoded = json_decode($sectionsJson, true);
            if (is_array($decoded) && count($decoded) > 0) {
                $sectionsList = $decoded;
            }
        }

        if (empty($sectionsList)) {
            $sectionsList = \App\Http\Controllers\Admin\HomeSettingController::getDefaultSections();
        }

        $productSections = [];
        foreach ($sectionsList as $sec) {
            if (isset($sec['active']) && !$sec['active']) {
                continue;
            }
            $filter = $sec['filter'] ?? 'all';
            $limit  = (int)($sec['limit'] ?? 4);

            $viewAllLink = '/shop';
            if ($filter === 'cond_intact') {
                $viewAllLink = '/shop?condition=intact';
            } elseif ($filter === 'cond_without-box') {
                $viewAllLink = '/shop?condition=without-box';
            } elseif ($filter === 'cond_pre-owned') {
                $viewAllLink = '/shop?condition=pre-owned';
            } elseif (str_starts_with($filter, 'cat_')) {
                $catId = (int) substr($filter, 4);
                $cat = Category::find($catId);
                if ($cat) {
                    $viewAllLink = '/shop?category=' . $cat->slug;
                }
            }

            $productSections[] = [
                'id'          => $sec['id'] ?? uniqid('sec_'),
                'title'       => $sec['title'] ?? 'Product Section',
                'highlight'   => $sec['highlight'] ?? '',
                'style'       => SectionTitleStyle::sanitizeFull($sec['style'] ?? null),
                'viewAllLink' => $viewAllLink,
                'products'    => $this->getFilteredProducts($allProducts, $filter, $limit),
            ];
        }

        // Legacy fallbacks
        $sec1Products = $productSections[0]['products'] ?? collect();
        $sec2Products = $productSections[1]['products'] ?? collect();
        $sec3Products = $productSections[2]['products'] ?? collect();
        $intactProducts     = $sec1Products;
        $withoutBoxProducts = $sec2Products;
        $preOwnedProducts   = $sec3Products;

        return view('pages.home', compact(
            'heroSliders',
            'promoBanners',
            'allProducts',
            'flashDeals',
            'productSections',
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
            'homeFlashTitleStyle',
            'homePromosActive',
            'homeTestimonialsActive',
            'homeTickerActive',
            'homeTickerItems',
            'homeTickerEffect',
            'homeTickerSpeed',
            'popupOfferSettings'
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

    public function siteFonts()
    {
        return response()->json([
            'english' => SiteSetting::getValue('site_font_english', 'Inter'),
            'bangla'  => SiteSetting::getValue('site_font_bangla', 'Hind Siliguri'),
        ]);
    }

    public function page(string $page, Request $request)
    {
        if ($page === 'shop') {
            return $this->shop($request);
        }

        return $this->render('pages.' . $page);
    }

    public function checkout(Request $request)
    {
        $buyNowProduct = null;

        if ($request->filled('product')) {
            $buyNowProduct = Product::with('images')
                ->where('slug', $request->string('product'))
                ->where('in_stock', true)
                ->firstOrFail();
        }

        $buyNow = $buyNowProduct ? [
            'slug' => $buyNowProduct->slug,
            'name' => $buyNowProduct->name,
            'price' => $buyNowProduct->price,
            'image' => $buyNowProduct->primaryImage(),
            'quantity' => 1,
        ] : null;

        return view('pages.checkout', compact('buyNow'));
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
