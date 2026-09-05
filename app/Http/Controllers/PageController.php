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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController extends Controller
{
    public function home()
    {
        $heroSliders = HeroSlider::where('is_active', true)->orderBy('sort_order')->get();
        $promoBanners = PromoBanner::where('is_active', true)->orderBy('sort_order')->get();
        $allProducts = Product::with(['category', 'brand', 'condition', 'images', 'highlights'])->orderByDesc('in_stock')->orderByDesc('price')->get();

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
        $homeNewArrivalTitleStyle = SectionTitleStyle::sanitizeFull(
            json_decode(SiteSetting::getValue('home_new_arrival_title_style', '{}'), true)
        );
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
            'backdrop_blur' => SiteSetting::getValue('popup_offer_backdrop_blur', '8'),
        ];

        $homeTrustbarActive = SiteSetting::getValue('home_trustbar_active', '1') == '1';
        $trustbarJson = SiteSetting::getValue('home_trustbar_items_json');
        $trustbarItems = [];
        if ($trustbarJson) {
            $decoded = json_decode($trustbarJson, true);
            if (is_array($decoded) && count($decoded) > 0) {
                $trustbarItems = $decoded;
            }
        }
        if (empty($trustbarItems)) {
            $trustbarItems = HomeSettingController::getDefaultTrustBarItems();
        }
        $trustbarItems = array_values(array_filter($trustbarItems, fn ($item) => ! isset($item['active']) || $item['active']));

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
            'homeNewArrivalTitleStyle',
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
            'popupOfferSettings',
            'homeTrustbarActive',
            'trustbarItems'
        ));
    }

    private function getFilteredProducts($allProducts, $filter, $limit)
    {
        $limit = (int) ($limit ?: 4);
        if (str_starts_with($filter, 'cond_')) {
            $slug = substr($filter, 5);

            return $allProducts->filter(function ($p) use ($slug) {
                return optional($p->condition)->slug === $slug;
            })->sortBy([
                ['in_stock', 'desc'],
                ['price', 'desc'],
            ])->values()->take($limit);
        } elseif (str_starts_with($filter, 'cat_')) {
            $catId = (int) substr($filter, 4);

            return $allProducts->filter(function ($p) use ($catId) {
                return $p->category_id == $catId;
            })->sortBy([
                ['in_stock', 'desc'],
                ['price', 'desc'],
            ])->values()->take($limit);
        }

        return $allProducts->sortBy([
            ['in_stock', 'desc'],
            ['price', 'desc'],
        ])->values()->take($limit);
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
                    'in_stock' => $product->in_stock,
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

        $products = Product::with(['brand', 'category', 'condition', 'highlights', 'specs'])
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
                    'specs' => $product->specs->sortBy('sort_order')->map(fn ($spec) => [
                        'label' => $spec->label,
                        'value' => $spec->value,
                    ])->values(),
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

    /**
     * Categories + the brands that actually have products in each, for the
     * "ALL PRODUCTS" header mega-menu. Cached briefly since it only changes
     * when products/categories/brands are added or edited in the admin panel.
     */
    public function navCategories()
    {
        $menu = Cache::remember('nav.category_brands', now()->addMinutes(30), function () {
            return Category::orderBy('sort_order')->orderBy('name')->get()
                ->map(function (Category $category) {
                    $brands = Brand::whereHas('products', function ($q) use ($category) {
                        $q->where('category_id', $category->id);
                    })->orderBy('name')->get(['slug', 'name']);

                    return [
                        'slug' => $category->slug,
                        'name' => $category->name,
                        'brands' => $brands,
                    ];
                })
                ->filter(fn ($category) => $category['brands']->isNotEmpty())
                ->values();
        });

        return response()->json($menu);
    }

    public function page(string $page, Request $request)
    {
        if ($page === 'shop') {
            return $this->shop($request);
        }

        if ($page === 'blog') {
            return $this->blogIndex($request);
        }

        if ($page === 'customer-spotlight') {
            return $this->customerSpotlightIndex($request);
        }

        if ($page === 'customer-feedback') {
            return $this->customerFeedbackIndex($request);
        }

        if ($page === 'philanthropic-work') {
            return $this->philanthropicWorkIndex($request);
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
            'quantity' => max(1, min(10, $request->integer('qty', 1))),
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

        if ($request->boolean('exclude_out_of_stock')) {
            $query->where('in_stock', true);
        }

        // Spec filters (RAM, Storage, Processor, ...) are defined per category,
        // but the same filter (e.g. "RAM") is normally redefined identically on
        // every laptop-ish category so it also shows on the all-products / no-
        // category view instead of only appearing once a category is picked.
        // Attributes across categories that share the same slug-derived `key`
        // (e.g. "ram") are merged into a single sidebar filter here, matched
        // against any of their underlying attribute IDs — a mouse's category
        // simply never defines a "processor" key, so that filter never appears
        // for it regardless of whether a category is selected.
        $attributesQuery = FilterAttribute::query();
        if (! empty($categorySlugs)) {
            $attributesQuery->whereHas('category', function ($q) use ($categorySlugs) {
                $q->whereIn('slug', $categorySlugs);
            });
        }
        $rawAttributes = $attributesQuery->orderBy('sort_order')->get()->groupBy('key');

        $filterAttributes = collect();
        foreach ($rawAttributes as $key => $group) {
            $ids = $group->pluck('id');
            $attribute = clone $group->first();
            $attribute->options = $group->pluck('options')
                ->filter()
                ->flatMap(fn ($options) => array_map('trim', explode(',', $options)))
                ->filter()
                ->unique()->values()->implode(', ');

            $bounds = ProductFilterValue::whereIn('filter_attribute_id', $ids)
                ->selectRaw('MIN(numeric_value) as min_bound, MAX(numeric_value) as max_bound')
                ->first();
            $attribute->min_bound = $bounds->min_bound;
            $attribute->max_bound = $bounds->max_bound;

            if ($attribute->type === 'range') {
                $min = $request->input("spec_min.{$key}");
                $max = $request->input("spec_max.{$key}");
                if (($min !== null && $min !== '') || ($max !== null && $max !== '')) {
                    $query->whereHas('filterValues', function ($q) use ($ids, $min, $max) {
                        $q->whereIn('filter_attribute_id', $ids);
                        if ($min !== null && $min !== '') {
                            $q->where('numeric_value', '>=', (float) $min);
                        }
                        if ($max !== null && $max !== '') {
                            $q->where('numeric_value', '<=', (float) $max);
                        }
                    });
                }
            } else {
                $selected = array_filter((array) $request->input("spec_select.{$key}", []));
                if (! empty($selected)) {
                    $query->whereHas('filterValues', function ($q) use ($ids, $selected) {
                        $q->whereIn('filter_attribute_id', $ids)->whereIn('text_value', $selected);
                    });
                }
            }

            $filterAttributes->push($attribute);
        }

        // Search query
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // The price slider's ceiling reflects the highest price among products
        // matching every other active filter (category/condition/brand/spec/
        // search), rounded up to the nearest ৳10k — so narrowing to a category
        // with a lower top price (e.g. Pre-Owned maxing at 49k) also narrows the
        // slider (to 50k), instead of a fixed ceiling hiding pricier products in
        // other categories. Computed from a clone, before max_price is applied
        // to $query itself, so dragging the slider down can't shrink its own max.
        $rawMaxPrice = (clone $query)->max('price');
        $priceMax = $rawMaxPrice ? (int) (ceil($rawMaxPrice / 10000) * 10000) : 300000;

        // Max Price filter
        if ($request->filled('max_price')) {
            $query->where('price', '<=', (float) $request->max_price);
        }

        // Always place in-stock products first, and out-of-stock products at the end
        $query->orderByDesc('in_stock');

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
            'conditionSlugs', 'categorySlugs', 'brandSlugs', 'filterAttributes', 'priceMax'
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

        throw new NotFoundHttpException;
    }

    public function blogIndex(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $posts = BlogPost::published()
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%")))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(9)
            ->withQueryString();

        return view('pages.blog', compact('posts', 'search'));
    }

    public function blogLoadMore(Request $request)
    {
        $page = max(1, (int) $request->query('page', 2));
        $search = trim((string) $request->query('search', ''));

        $posts = BlogPost::published()
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%")))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
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
            return view('pages.blog.detail', compact('post'));
        }

        throw new NotFoundHttpException;
    }

    public function blogSearchSuggest(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $posts = BlogPost::published()
            ->where(fn ($w) => $w->where('title', 'like', "%{$query}%")->orWhere('content', 'like', "%{$query}%"))
            ->orderByDesc('published_at')
            ->take(6)
            ->get()
            ->map(fn (BlogPost $post) => [
                'title' => $post->title,
                'subtitle' => $post->excerptText(80),
                'image' => $post->featured_image ?: '/assets/no-image-placeholder.svg',
                'url' => route('blog', $post->slug),
            ]);

        return response()->json(['results' => $posts]);
    }

    public function customerSpotlightIndex(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $spotlights = CustomerSpotlight::orderByDesc('date')
            ->orderByDesc('id')
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w->where('product', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%")->orWhere('location', 'like', "%{$search}%")))
            ->paginate(24)
            ->withQueryString();

        return view('pages.customer-spotlight', compact('spotlights', 'search'));
    }

    public function customerSpotlightLoadMore(Request $request)
    {
        $page = max(1, (int) $request->query('page', 2));
        $search = trim((string) $request->query('search', ''));

        $spotlights = CustomerSpotlight::orderByDesc('date')
            ->orderByDesc('id')
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w->where('product', 'like', "%{$search}%")->orWhere('name', 'like', "%{$search}%")->orWhere('location', 'like', "%{$search}%")))
            ->paginate(24, ['*'], 'page', $page);

        return response()->json([
            'html' => view('partials.spotlight-cards', compact('spotlights'))->render(),
            'has_more' => $spotlights->hasMorePages(),
        ]);
    }

    public function customerSpotlightSearchSuggest(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $spotlights = CustomerSpotlight::where(fn ($w) => $w->where('product', 'like', "%{$query}%")->orWhere('name', 'like', "%{$query}%")->orWhere('location', 'like', "%{$query}%"))
            ->orderByDesc('date')
            ->take(6)
            ->get()
            ->map(fn (CustomerSpotlight $spotlight) => [
                'title' => $spotlight->product,
                'subtitle' => trim($spotlight->name.($spotlight->location ? ' · '.$spotlight->location : '')),
                'image' => $spotlight->image ?: '/assets/no-image-placeholder.svg',
                'url' => '/customer-spotlight?search='.urlencode($spotlight->product),
            ]);

        return response()->json(['results' => $spotlights]);
    }

    public function customerFeedbackIndex(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $feedbacks = CustomerFeedback::latest()
            ->when($search !== '', fn ($q) => $q->where('message', 'like', "%{$search}%"))
            ->paginate(24)
            ->withQueryString();

        return view('pages.customer-feedback', compact('feedbacks', 'search'));
    }

    public function customerFeedbackLoadMore(Request $request)
    {
        $page = max(1, (int) $request->query('page', 2));
        $search = trim((string) $request->query('search', ''));

        $feedbacks = CustomerFeedback::latest()
            ->when($search !== '', fn ($q) => $q->where('message', 'like', "%{$search}%"))
            ->paginate(24, ['*'], 'page', $page);

        return response()->json([
            'html' => view('partials.feedback-cards', compact('feedbacks'))->render(),
            'has_more' => $feedbacks->hasMorePages(),
        ]);
    }

    public function customerFeedbackSearchSuggest(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $feedbacks = CustomerFeedback::where('message', 'like', "%{$query}%")
            ->latest()
            ->take(6)
            ->get()
            ->map(fn (CustomerFeedback $feedback) => [
                'title' => Str::limit($feedback->message, 80),
                'subtitle' => null,
                'image' => $feedback->image ?: '/assets/no-image-placeholder.svg',
                'url' => '/customer-feedback?search='.urlencode(Str::limit($feedback->message, 40, '')),
            ]);

        return response()->json(['results' => $feedbacks]);
    }

    public function philanthropicWorkIndex(Request $request)
    {
        $search = trim((string) $request->query('search', ''));

        $works = PhilanthropicWork::orderByDesc('id')
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%")))
            ->paginate(24)
            ->withQueryString();

        return view('pages.philanthropic-work', compact('works', 'search'));
    }

    public function philanthropicWorkLoadMore(Request $request)
    {
        $page = max(1, (int) $request->query('page', 2));
        $search = trim((string) $request->query('search', ''));

        $works = PhilanthropicWork::orderByDesc('id')
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w->where('title', 'like', "%{$search}%")->orWhere('content', 'like', "%{$search}%")))
            ->paginate(24, ['*'], 'page', $page);

        return response()->json([
            'html' => view('partials.philanthropic-cards', compact('works'))->render(),
            'has_more' => $works->hasMorePages(),
        ]);
    }

    public function philanthropicWorkSearchSuggest(Request $request)
    {
        $query = trim((string) $request->query('q', ''));
        if (strlen($query) < 2) {
            return response()->json(['results' => []]);
        }

        $works = PhilanthropicWork::where(fn ($w) => $w->where('title', 'like', "%{$query}%")->orWhere('content', 'like', "%{$query}%"))
            ->orderByDesc('id')
            ->take(6)
            ->get()
            ->map(fn (PhilanthropicWork $work) => [
                'title' => $work->title,
                'subtitle' => null,
                'image' => $work->image ?: '/assets/no-image-placeholder.svg',
                'url' => route('philanthropic-work', $work->slug),
            ]);

        return response()->json(['results' => $works]);
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
