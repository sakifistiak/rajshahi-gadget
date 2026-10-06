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
use App\Services\AnalyticsTracker;
use App\Support\SectionTitleStyle;
use App\Support\Seo;
use App\Support\SslCommerz;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
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
        $homeNewArrivalSubtitleText = SiteSetting::getValue('home_new_arrival_subtitle_text', 'Fresh stock, sourced on request - order now, get it soon');
        $newArrivalProducts = Product::with(['category', 'brand', 'condition', 'images', 'highlights'])
            ->newArrival()
            ->latest()
            ->take($homeNewArrivalLimit)
            ->get();

        $homePromosActive = SiteSetting::getValue('home_promos_active', '1') == '1';
        $homeTestimonialsActive = SiteSetting::getValue('home_testimonials_active', '1') == '1';
        $homeHeadline = SiteSetting::getValue('home_headline', Seo::HOME_HEADLINE);
        $homeHeadlineSubtext = SiteSetting::getValue('home_headline_subtext', Seo::HOME_SUBTEXT);
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
            'trustbarItems',
            'homeHeadline',
            'homeHeadlineSubtext'
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
     * "ALL PRODUCTS" header mega-menu. Categories are intentionally not
     * filtered by product count so newly-created categories are visible
     * immediately, even before the first product is added.
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
                        'parent_id' => $category->parent_id,
                        'brands' => $brands,
                    ];
                });
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

        $onlinePaymentEnabled = SslCommerz::enabled();

        return view('pages.checkout', compact('buyNow', 'onlinePaymentEnabled'));
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
        $categoryContext = $request->attributes->get('category_context');

        // Each filter accepts either a single value (?condition=intact, used by
        // plain nav links) or multiple (?condition[]=intact&condition[]=pre-owned,
        // used by the multi-select checkboxes) — (array) casting a string wraps
        // it into a single-element array so both forms work identically.
        $conditionSlugs = array_filter((array) $request->input('condition', []));
        $categorySlugs = array_filter((array) $request->input('category', []));
        $brandSlugs = array_filter((array) $request->input('brand', []));

        // Category filters are hierarchical: selecting a parent category also
        // includes every descendant, while selecting a child remains specific
        // to that child. Keep the original slugs for checkbox state/URLs.
        $categoryOptions = Category::orderBy('sort_order')->orderBy('name')->get();
        $selectedCategoryIds = $categoryOptions
            ->whereIn('slug', $categorySlugs)
            ->pluck('id')
            ->values();
        $filterCategoryIds = $this->withDescendantIds($categoryOptions, $selectedCategoryIds->all());

        // Every active filter is a closure, so the sidebar can work out what
        // each option would match with all the *other* filters applied.
        $filters = [];

        if (! empty($conditionSlugs)) {
            $filters['condition'] = fn ($q) => $q->whereHas('condition', fn ($c) => $c->whereIn('slug', $conditionSlugs));
        }

        if (! empty($categorySlugs)) {
            $filters['category'] = fn ($q) => $q->whereIn('category_id', $filterCategoryIds);
        }

        if (! empty($brandSlugs)) {
            $filters['brand'] = fn ($q) => $q->whereHas('brand', fn ($b) => $b->whereIn('slug', $brandSlugs));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $filters['search'] = fn ($q) => $q->where(function ($w) use ($search) {
                $w->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Spec filters (RAM, Storage, Processor, ...) are defined per category,
        // but the same filter (e.g. "RAM") is normally redefined identically on
        // every laptop-ish category so it also shows on the all-products / no-
        // category view instead of only appearing once a category is picked.
        // Attributes across categories that share the same slug-derived `key`
        // (e.g. "ram") are merged into a single sidebar filter here, matched
        // against any of their underlying attribute IDs — a mouse's category
        // simply never defines a "processor" key, so that filter never appears
        // for it regardless of whether a category is selected. A sub category
        // also gets the filters defined on its main category (e.g. Laptops).
        $attributesQuery = FilterAttribute::query();
        if (! empty($filterCategoryIds)) {
            $attributeCategoryIds = $filterCategoryIds;
            foreach ($selectedCategoryIds as $id) {
                $parentId = $categoryOptions->firstWhere('id', $id)?->parent_id;
                while ($parentId && ! in_array($parentId, $attributeCategoryIds, true)) {
                    $attributeCategoryIds[] = $parentId;
                    $parentId = $categoryOptions->firstWhere('id', $parentId)?->parent_id;
                }
            }
            $attributesQuery->whereIn('category_id', $attributeCategoryIds);
        }

        $rawAttributes = $attributesQuery->orderBy('sort_order')->get()->groupBy('key');

        $filterAttributes = collect();
        foreach ($rawAttributes as $key => $group) {
            $ids = $group->pluck('id');
            $attribute = clone $group->first();
            $attribute->filter_ids = $ids;
            $attribute->options = $group->pluck('options')
                ->filter()
                ->flatMap(fn ($options) => array_map('trim', explode(',', $options)))
                ->filter()
                ->unique()->values()->implode(', ');

            if ($attribute->type === 'range') {
                $min = $request->input("spec_min.{$key}");
                $max = $request->input("spec_max.{$key}");
                if (($min !== null && $min !== '') || ($max !== null && $max !== '')) {
                    $filters["spec:{$key}"] = fn ($q) => $q->whereHas('filterValues', function ($v) use ($ids, $min, $max) {
                        $v->whereIn('filter_attribute_id', $ids);
                        if ($min !== null && $min !== '') {
                            $v->where('numeric_value', '>=', (float) $min);
                        }
                        if ($max !== null && $max !== '') {
                            $v->where('numeric_value', '<=', (float) $max);
                        }
                    });
                }
            } else {
                $selected = array_filter((array) $request->input("spec_select.{$key}", []));
                if (! empty($selected)) {
                    $filters["spec:{$key}"] = fn ($q) => $q->whereHas('filterValues', fn ($v) => $v->whereIn('filter_attribute_id', $ids)->whereIn('text_value', $selected));
                }
            }

            $filterAttributes->push($attribute);
        }

        if ($request->filled('max_price')) {
            $maxPrice = (float) $request->max_price;
            $filters['max_price'] = fn ($q) => $q->where('price', '<=', $maxPrice);
        }

        // In-stock products matching every active filter except $except.
        // Out-of-stock products are always excluded from the shop listing —
        // this used to be an optional checkbox filter, but is now permanent.
        $matching = function (?string $except = null) use ($filters) {
            $query = Product::query()->where('in_stock', true);
            foreach ($filters as $name => $apply) {
                if ($name !== $except) {
                    $apply($query);
                }
            }

            return $query;
        };

        // Hide every sidebar option that no product would match. Each group is
        // checked without its own selection, so ticking "i5" never hides "i7"
        // (options of one filter combine), while picking a brand or category
        // hides the processors that brand never has. A ticked option always
        // stays visible so it can be unticked.
        $filterAttributes = $filterAttributes->filter(function ($attribute) use ($matching, $request) {
            $key = $attribute->key;
            $values = ProductFilterValue::whereIn('filter_attribute_id', $attribute->filter_ids)
                ->whereIn('product_id', $matching("spec:{$key}")->select('id'));

            if ($attribute->type === 'range') {
                $bounds = $values->selectRaw('MIN(numeric_value) as min_bound, MAX(numeric_value) as max_bound')->first();
                $attribute->min_bound = $bounds->min_bound;
                $attribute->max_bound = $bounds->max_bound;

                return $bounds->min_bound !== null
                    || filled($request->input("spec_min.{$key}"))
                    || filled($request->input("spec_max.{$key}"));
            }

            $available = $values->distinct()->pluck('text_value')->all();
            $selected = (array) $request->input("spec_select.{$key}", []);
            $attribute->options = collect($attribute->optionList())
                ->filter(fn ($option) => in_array($option, $available, true) || in_array($option, $selected, true))
                ->implode(', ');

            return $attribute->options !== '';
        })->values();

        $conditionIds = $matching('condition')->distinct()->pluck('condition_id')->all();
        $conditions = Condition::all()
            ->filter(fn ($c) => in_array($c->id, $conditionIds) || in_array($c->slug, $conditionSlugs, true))
            ->values();

        $brandIds = $matching('brand')->distinct()->pluck('brand_id')->all();
        $brands = Brand::all()
            ->filter(fn ($b) => in_array($b->id, $brandIds) || in_array($b->slug, $brandSlugs, true))
            ->values();

        // The shop filter must follow the admin-managed display order too.
        // Category::all() uses database/insert order and ignores sort_order.
        // A category stays listed while it or one of its sub categories has a match.
        $usedCategoryIds = $matching('category')->distinct()->pluck('category_id')->all();
        $categories = ($categoryContext
            ? $categoryOptions->where('parent_id', $categoryContext->id)
            : $categoryOptions)
            ->filter(fn ($c) => in_array($c->slug, $categorySlugs, true)
                || array_intersect($this->withDescendantIds($categoryOptions, [$c->id]), $usedCategoryIds) !== [])
            ->values();

        // The price slider's ceiling reflects the highest price among products
        // matching every other active filter (category/condition/brand/spec/
        // search), rounded up to the nearest ৳10k — so narrowing to a category
        // with a lower top price (e.g. Pre-Owned maxing at 49k) also narrows the
        // slider (to 50k), instead of a fixed ceiling hiding pricier products in
        // other categories. Computed without max_price itself, so dragging the
        // slider down can't shrink its own max.
        $rawMaxPrice = $matching('max_price')->max('price');
        $priceMax = $rawMaxPrice ? (int) (ceil($rawMaxPrice / 10000) * 10000) : 300000;

        $query = $matching()->with(['category', 'brand', 'condition', 'images', 'highlights']);

        // Sort order. Reset any order already attached to the query so price
        // sorting is always the first and authoritative ordering rule.
        // The UI arrows are used as the requested direction: Price ↑ means
        // highest price first, while Price ↓ means lowest price first.
        if ($request->input('sort') === 'price-asc') {
            $query->reorder('price', 'desc')->orderBy('id', 'asc');
        } elseif ($request->input('sort') === 'price-desc') {
            $query->reorder('price', 'asc')->orderBy('id', 'asc');
        } else {
            $query->reorder()->latest();
        }

        $products = $query->paginate(48)->withQueryString();

        return view('pages.shop', compact(
            'products', 'categories', 'brands', 'conditions',
            'conditionSlugs', 'categorySlugs', 'brandSlugs', 'filterAttributes', 'priceMax', 'categoryContext'
        ));
    }

    /**
     * The given category ids plus every descendant of them.
     *
     * @param  Collection<int, Category>  $categories
     * @param  array<int, int>  $ids
     * @return array<int, int>
     */
    private function withDescendantIds($categories, array $ids): array
    {
        $all = $ids;
        $known = $ids;
        do {
            $childIds = $categories
                ->whereIn('parent_id', $known)
                ->pluck('id')
                ->reject(fn ($id) => in_array($id, $all, true))
                ->values()
                ->all();
            $all = array_merge($all, $childIds);
            $known = $childIds;
        } while (! empty($childIds));

        return $all;
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
        $categoryContext = Category::where('slug', $category)->firstOrFail();
        $request->attributes->set('category_context', $categoryContext);

        // A category URL provides the parent scope by default. If the user
        // selected one or more child categories in the sidebar, preserve those
        // submitted values so the checkbox state and filtered results survive.
        if (! $request->has('category')) {
            $request->merge(['category' => $categoryContext->slug]);
        }

        return $this->shop($request);
    }

    public function analyticsPing(Request $request, AnalyticsTracker $tracker)
    {
        $tracker->ping($request);

        return response()->json(['ok' => true]);
    }

    private function render(string $view)
    {
        if (! View::exists($view)) {
            throw new NotFoundHttpException;
        }

        return view($view);
    }
}
