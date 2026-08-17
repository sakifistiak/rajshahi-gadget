<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Admin\HomeSettingController;
use App\Models\BlogPost;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\CustomerFeedback;
use App\Models\CustomerSpotlight;
use App\Models\FilterAttribute;
use App\Models\FlashSale;
use App\Models\HeroSlider;
use App\Models\Order;
use App\Models\PhilanthropicWork;
use App\Models\Product;
use App\Models\ProductFilterValue;
use App\Models\PromoBanner;
use App\Models\SiteSetting;
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

        // The flash sale shown on the homepage is whichever campaign is
        // currently within its start/end window and marked active — there is
        // no manual product picking here, it's fully driven by the admin's
        // Flash Sales campaigns (see Admin\FlashSaleController).
        $activeFlashSale = FlashSale::live()
            ->with(['items' => function ($query) {
                $query->orderBy('sort_order')->with(['product.images', 'product.highlights']);
            }])
            ->first();
        $flashSaleItems = $activeFlashSale
            ? $activeFlashSale->items->filter(fn ($item) => $item->product && ! $item->isSoldOut())
            : collect();

        // Home settings
        $homeHeroActive = SiteSetting::getValue('home_hero_active', '1') == '1';
        $homeFlashActive = SiteSetting::getValue('home_flash_active', '1') == '1';
        $homeFlashTitle = SiteSetting::getValue('home_flash_title', 'Limited time deals');
        $homeFlashHighlight = SiteSetting::getValue('home_flash_highlight', 'deals');
        $homeFlashTitleStyle = SectionTitleStyle::sanitizeFull(
            json_decode(SiteSetting::getValue('home_flash_title_style', '{}'), true)
        );
        $homeFlashBadgeActive = SiteSetting::getValue('home_flash_badge_active', '1') == '1';
        $homeFlashBadgeIcon = SiteSetting::getValue('home_flash_badge_icon', '');
        $homeFlashBadgeText = SiteSetting::getValue('home_flash_badge_text', 'Flash Deals');
        $homeFlashSubtitleActive = SiteSetting::getValue('home_flash_subtitle_active', '1') == '1';
        $homeFlashSubtitleText = SiteSetting::getValue('home_flash_subtitle_text', 'Limited stock · 0% EMI up to 12 months · Free Dhaka delivery');
        // is_new_arrival is independent of in_stock — these products are
        // sourced on order rather than kept in stock, so they show here
        // regardless of the in_stock flag.
        $homeNewArrivalActive = SiteSetting::getValue('home_new_arrival_active', '0') == '1';
        $homeNewArrivalTitle = SiteSetting::getValue('home_new_arrival_title', 'New Arrivals');
        $homeNewArrivalHighlight = SiteSetting::getValue('home_new_arrival_highlight', 'New');
        $homeNewArrivalPosition = SiteSetting::getValue('home_new_arrival_position', 'below_flash');
        $homeNewArrivalLimit = (int) SiteSetting::getValue('home_new_arrival_limit', '4');
        $homeNewArrivalBadgeActive = SiteSetting::getValue('home_new_arrival_badge_active', '1') == '1';
        $homeNewArrivalBadgeIcon = SiteSetting::getValue('home_new_arrival_badge_icon', '');
        $homeNewArrivalBadgeText = SiteSetting::getValue('home_new_arrival_badge_text', 'New Arrival');
        $homeNewArrivalSubtitleActive = SiteSetting::getValue('home_new_arrival_subtitle_active', '1') == '1';
        $homeNewArrivalSubtitleText = SiteSetting::getValue('home_new_arrival_subtitle_text', 'Fresh stock, sourced on request — order now, get it soon');
        $newArrivalProducts = Product::with(['category', 'brand', 'condition', 'images', 'highlights'])
            ->newArrival()
            ->latest()
            ->take($homeNewArrivalLimit)
            ->get();

        $homePromosActive = SiteSetting::getValue('home_promos_active', '1') == '1';
        $homeTestimonialsActive = SiteSetting::getValue('home_testimonials_active', '1') == '1';
        $homeTickerActive = SiteSetting::getValue('home_ticker_active', '1') == '1';
        $defaultTickerText = "🎉 Eid Special: Up to 15% off on Brand New Intact Box iPhones\n🚚 Same-day delivery inside Dhaka on orders before 3 PM\n🛡️ 7-day easy replacement on all Pre-Owned products\n💳 0% EMI up to 12 months on selected products\n📞 Chat with us on WhatsApp for instant support";
        $homeTickerText = SiteSetting::getValue('home_ticker_text', $defaultTickerText);
        $homeTickerItems = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $homeTickerText))));
        $homeTickerEffect = in_array(SiteSetting::getValue('home_ticker_effect', 'fade'), ['fade', 'scroll'], true)
            ? SiteSetting::getValue('home_ticker_effect', 'fade')
            : 'fade';
        $homeTickerSpeed = (float) SiteSetting::getValue('home_ticker_speed', '6');

        // Popup Offer Settings
        $popupOfferSettings = [
            'active' => SiteSetting::getValue('popup_offer_active', '0') == '1',
            'image' => SiteSetting::getValue('popup_offer_image', ''),
            'image_mobile' => SiteSetting::getValue('popup_offer_image_mobile', ''),
            'link' => SiteSetting::getValue('popup_offer_link', '/shop'),
            'target' => SiteSetting::getValue('popup_offer_target', '_self'),
            'frequency' => SiteSetting::getValue('popup_offer_frequency', 'session'),
            'delay' => (float) SiteSetting::getValue('popup_offer_delay', '1'),
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
            $sectionsList = HomeSettingController::getDefaultSections();
        }

        $productSections = [];
        foreach ($sectionsList as $sec) {
            if (isset($sec['active']) && ! $sec['active']) {
                continue;
            }
            $filter = $sec['filter'] ?? 'all';
            $limit = (int) ($sec['limit'] ?? 4);

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
                    $viewAllLink = '/shop?category='.$cat->slug;
                }
            }

            $productSections[] = [
                'id' => $sec['id'] ?? uniqid('sec_'),
                'title' => $sec['title'] ?? 'Product Section',
                'highlight' => $sec['highlight'] ?? '',
                'style' => SectionTitleStyle::sanitizeFull($sec['style'] ?? null),
                'viewAllLink' => $viewAllLink,
                'products' => $this->getFilteredProducts($allProducts, $filter, $limit),
            ];
        }

        // Legacy fallbacks
        $sec1Products = $productSections[0]['products'] ?? collect();
        $sec2Products = $productSections[1]['products'] ?? collect();
        $sec3Products = $productSections[2]['products'] ?? collect();
        $intactProducts = $sec1Products;
        $withoutBoxProducts = $sec2Products;
        $preOwnedProducts = $sec3Products;

        return view('pages.home', compact(
            'heroSliders',
            'promoBanners',
            'allProducts',
            'activeFlashSale',
            'flashSaleItems',
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
            'homeFlashBadgeActive',
            'homeFlashBadgeIcon',
            'homeFlashBadgeText',
            'homeFlashSubtitleActive',
            'homeFlashSubtitleText',
            'homeNewArrivalActive',
            'homeNewArrivalTitle',
            'homeNewArrivalHighlight',
            'homeNewArrivalPosition',
            'newArrivalProducts',
            'homeNewArrivalBadgeActive',
            'homeNewArrivalBadgeIcon',
            'homeNewArrivalBadgeText',
            'homeNewArrivalSubtitleActive',
            'homeNewArrivalSubtitleText',
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

            return $allProducts->filter(function ($p) use ($slug) {
                return optional($p->condition)->slug === $slug;
            })->take($limit);
        } elseif (str_starts_with($filter, 'cat_')) {
            $catId = (int) substr($filter, 4);

            return $allProducts->filter(function ($p) use ($catId) {
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
            ->orWhereHas('category', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->orWhereHas('brand', function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%");
            })
            ->take(8)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => '৳ '.number_format($product->price),
                    'compare_at_price' => $product->compare_at_price ? '৳ '.number_format($product->compare_at_price) : null,
                    'image' => $product->primaryImage(),
                    'category' => optional($product->category)->name ?? 'Gadget',
                    'url' => route('product', $product->slug),
                ];
            });

        return response()->json(['products' => $products]);
    }

    public function compareData(Request $request)
    {
        $slugs = array_slice(array_filter(explode(',', $request->input('slugs', ''))), 0, 3);

        $products = Product::with(['brand', 'category', 'condition', 'highlights'])
            ->whereIn('slug', $slugs)
            ->get()
            ->sortBy(fn ($p) => array_search($p->slug, $slugs))
            ->values()
            ->map(function ($product) {
                return [
                    'slug' => $product->slug,
                    'name' => $product->name,
                    'image' => $product->primaryImage(),
                    'price' => $product->price_is_tba ? 'TBA' : ('৳ '.number_format($product->price)),
                    'compare_at_price' => $product->compare_at_price ? '৳ '.number_format($product->compare_at_price) : null,
                    'brand' => optional($product->brand)->name,
                    'category' => optional($product->category)->name,
                    'condition' => optional($product->condition)->name,
                    'stock_status' => $product->stockStatusLabel(),
                    'warranty' => $product->warranty,
                    'rating' => $product->rating,
                    'reviews_count' => $product->reviews_count,
                    'url' => route('product', $product->slug),
                    'highlights' => $product->highlights->sortBy('sort_order')->pluck('text')->values(),
                ];
            });

        return response()->json(['products' => $products]);
    }

    public function siteFonts()
    {
        return response()->json([
            'english' => SiteSetting::getValue('site_font_english', 'Inter'),
            'bangla' => SiteSetting::getValue('site_font_bangla', 'Hind Siliguri'),
        ]);
    }

    public function page(string $page, Request $request)
    {
        if ($page === 'shop') {
            return $this->shop($request);
        }

        if ($page === 'blog') {
            return $this->blogIndex();
        }

        if ($page === 'customer-spotlight') {
            return $this->customerSpotlightIndex();
        }

        if ($page === 'customer-feedback') {
            return $this->customerFeedbackIndex();
        }

        if ($page === 'philanthropic-work') {
            return $this->philanthropicWorkIndex();
        }

        return $this->render('pages.'.$page);
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

    public function thankYou(Request $request)
    {
        $order = Order::with(['items.product.images', 'storeLocation'])
            ->where('order_number', $request->query('order'))
            ->first();

        if (! $order) {
            return redirect('/');
        }

        return view('pages.thank-you', compact('order'));
    }

    public function shop(Request $request)
    {
        $query = Product::with(['category', 'brand', 'condition', 'images', 'highlights']);

        // Each filter accepts either a single value (?condition=intact, used by
        // plain nav links) or multiple (?condition[]=intact&condition[]=pre-owned,
        // used by the multi-select checkboxes) — (array) casting a string wraps
        // it into a single-element array so both forms work identically.
        $conditionSlugs = array_filter((array) $request->input('condition', []));
        $categorySlugs = array_filter((array) $request->input('category', []));
        $brandSlugs = array_filter((array) $request->input('brand', []));

        if (! empty($conditionSlugs)) {
            $query->whereHas('condition', function ($q) use ($conditionSlugs) {
                $q->whereIn('slug', $conditionSlugs);
            });
        }

        if (! empty($categorySlugs)) {
            $query->whereHas('category', function ($q) use ($categorySlugs) {
                $q->whereIn('slug', $categorySlugs);
            });
        }

        if (! empty($brandSlugs)) {
            $query->whereHas('brand', function ($q) use ($brandSlugs) {
                $q->whereIn('slug', $brandSlugs);
            });
        }

        // Spec filters (RAM, Storage, Connection, ...) only exist per category,
        // so they only render — and only apply — once at least one category is
        // selected. A mouse's category has no "Storage" attribute defined, so
        // that filter simply never appears for it.
        $filterAttributes = collect();
        if (! empty($categorySlugs)) {
            $filterAttributes = FilterAttribute::whereHas('category', function ($q) use ($categorySlugs) {
                $q->whereIn('slug', $categorySlugs);
            })->orderBy('sort_order')->get();

            foreach ($filterAttributes as $attribute) {
                $bounds = ProductFilterValue::where('filter_attribute_id', $attribute->id)
                    ->selectRaw('MIN(numeric_value) as min_bound, MAX(numeric_value) as max_bound')
                    ->first();
                $attribute->min_bound = $bounds->min_bound;
                $attribute->max_bound = $bounds->max_bound;

                if ($attribute->type === 'range') {
                    $min = $request->input("spec_min.{$attribute->id}");
                    $max = $request->input("spec_max.{$attribute->id}");
                    if (($min !== null && $min !== '') || ($max !== null && $max !== '')) {
                        $query->whereHas('filterValues', function ($q) use ($attribute, $min, $max) {
                            $q->where('filter_attribute_id', $attribute->id);
                            if ($min !== null && $min !== '') {
                                $q->where('numeric_value', '>=', (float) $min);
                            }
                            if ($max !== null && $max !== '') {
                                $q->where('numeric_value', '<=', (float) $max);
                            }
                        });
                    }
                } else {
                    $selected = array_filter((array) $request->input("spec_select.{$attribute->id}", []));
                    if (! empty($selected)) {
                        $query->whereHas('filterValues', function ($q) use ($attribute, $selected) {
                            $q->where('filter_attribute_id', $attribute->id)->whereIn('text_value', $selected);
                        });
                    }
                }
            }
        }

        // Max Price filter
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // Search query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
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

        return view('pages.shop', compact(
            'products', 'categories', 'brands', 'conditions',
            'conditionSlugs', 'categorySlugs', 'brandSlugs', 'filterAttributes'
        ));
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

        if (View::exists('pages.product.'.$slug)) {
            return view('pages.product.'.$slug);
        }

        throw new NotFoundHttpException;
    }

    public function blogIndex()
    {
        $featuredPost = BlogPost::published()->where('is_featured', true)->orderByDesc('published_at')->first();

        $posts = BlogPost::published()
            ->when($featuredPost, fn ($q) => $q->where('id', '!=', $featuredPost->id))
            ->orderByDesc('published_at')
            ->paginate(9);

        return view('pages.blog', compact('posts', 'featuredPost'));
    }

    public function blogLoadMore(Request $request)
    {
        $page = max(1, (int) $request->query('page', 2));
        $featuredPost = BlogPost::published()->where('is_featured', true)->orderByDesc('published_at')->first();
        $posts = BlogPost::published()
            ->when($featuredPost, fn ($q) => $q->where('id', '!=', $featuredPost->id))
            ->orderByDesc('published_at')
            ->paginate(9, ['*'], 'page', $page);

        return response()->json([
            'html' => view('partials.blog-cards', compact('posts'))->render(),
            'has_more' => $posts->hasMorePages(),
        ]);
    }

    public function blog(string $slug)
    {
        $post = BlogPost::where('slug', $slug)->published()->first();
        if ($post) {
            $relatedPosts = BlogPost::published()
                ->where('id', '!=', $post->id)
                ->orderByDesc('published_at')
                ->take(3)
                ->get();

            return view('pages.blog.detail', compact('post', 'relatedPosts'));
        }

        return $this->render('pages.blog.'.$slug);
    }

    public function customerSpotlightIndex()
    {
        $spotlights = CustomerSpotlight::orderByDesc('date')->get();

        return view('pages.customer-spotlight', compact('spotlights'));
    }

    public function customerFeedbackIndex()
    {
        $feedbacks = CustomerFeedback::orderByDesc('date')->get();

        return view('pages.customer-feedback', compact('feedbacks'));
    }

    public function philanthropicWorkIndex()
    {
        $works = PhilanthropicWork::orderByDesc('date')->get();

        return view('pages.philanthropic-work', compact('works'));
    }

    public function philanthropicWork(string $slug)
    {
        $work = PhilanthropicWork::where('slug', $slug)->first();
        if ($work) {
            return view('pages.philanthropic-work.detail', compact('work'));
        }
        throw new NotFoundHttpException;
    }

    public function category(string $category, Request $request)
    {
        $request->merge(['category' => $category]);

        return $this->shop($request);
    }

    private function render(string $view)
    {
        if (! View::exists($view)) {
            throw new NotFoundHttpException;
        }

        return view($view);
    }
}
