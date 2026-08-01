{{-- Dynamic Logo Swap from Site Settings --}}
<script>
    window.__SITE_LOGO_LIGHT = "{{ $siteLogo ?? '/media/b3ca13-kg-lockup-v2.png' }}";
    window.__SITE_LOGO_DARK  = "{{ $siteLogoDark ?? '/media/b3ca13-kg-lockup-v2.png' }}";
    window.__SITE_NAME       = "{{ $siteName ?? 'Khan Gadget' }}";
    document.addEventListener('DOMContentLoaded', function () {
        function swapLogos() {
            var isDark = document.documentElement.classList.contains('dark');
            var logoUrl = isDark ? window.__SITE_LOGO_DARK : window.__SITE_LOGO_LIGHT;
            document.querySelectorAll('img[alt*="Khan Gadget"], img[alt*="' + window.__SITE_NAME + '"]').forEach(function(img) {
                img.src = logoUrl;
            });
        }
        swapLogos();
        // Watch for theme changes
        var observer = new MutationObserver(swapLogos);
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
    });
</script>

<!DOCTYPE html><html lang="en" data-tsd-source="/src/routes/__root.tsx:133:5"><head data-tsd-source="/src/routes/__root.tsx:134:7"><meta charSet="utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/><link rel="preload" as="image" href="/media/b3ca13-kg-lockup-v2.png"/><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" data-precedence="default"/><title>Shop — {{ $siteName ?? 'Khan Gadget' }}</title><meta name="author" content="{{ $siteName ?? 'Khan Gadget' }}"/><meta property="og:type" content="website"/><meta name="twitter:card" content="summary_large_image"/><meta name="twitter:site" content="{{ $siteName ?? 'Khan Gadget' }}"/><meta name="twitter:title" content="Shop — {{ $siteName ?? 'Khan Gadget' }}"/><meta name="twitter:description" content="{{ $siteDescription ?? 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.' }}"/><meta property="og:image" content="https://pub-bb2e103a32db4e198524a2e9ed8f35b4.r2.dev/e4f4ef3d-bc55-4272-bf8e-77a19f63e327/id-preview-59d9b9ce--d146692d-212a-4234-ab9b-1057d9ddd7d8.lovable.app-1785051870536.png"/><meta name="twitter:image" content="https://pub-bb2e103a32db4e198524a2e9ed8f35b4.r2.dev/e4f4ef3d-bc55-4272-bf8e-77a19f63e327/id-preview-59d9b9ce--d146692d-212a-4234-ab9b-1057d9ddd7d8.lovable.app-1785051870536.png"/><meta name="description" content="{{ $siteDescription ?? 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.' }}"/><meta property="og:title" content="Shop — {{ $siteName ?? 'Khan Gadget' }}"/><meta property="og:description" content="{{ $siteDescription ?? 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.' }}"/><link rel="icon" href="/favicon.png" type="image/png"/><link rel="preconnect" href="https://fonts.googleapis.com"/><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous"/><link rel="stylesheet" href="/assets/styles-CC_Lznyw.css"><script src="https://unpkg.com/lucide@latest"></script><script src="/assets/theme.js"></script></head><body data-tsd-source="/src/routes/__root.tsx:138:7"><div class="flex min-h-screen flex-col bg-background" data-tsd-source="/src/routes/__root.tsx:154:13"><header class="sticky top-0 z-40 border-b border-border bg-background/95 backdrop-blur" data-tsd-source="/src/components/site/Navbar.tsx:31:5"><div class="container-page flex h-20 items-center gap-3" data-tsd-source="/src/components/site/Navbar.tsx:33:7"><a aria-label="Khan Gadget home" data-tsd-source="/src/components/site/Navbar.tsx:34:9" href="/" class="flex shrink-0 items-center"><img src="/media/b3ca13-kg-lockup-v2.png" alt="Khan Gadget — Eternal Tech Companion" class="h-9 w-auto object-contain sm:h-12 lg:h-14" data-tsd-source="/src/components/site/Navbar.tsx:35:11"/></a><div class="hidden flex-1 sm:block" data-tsd-source="/src/components/site/Navbar.tsx:42:9"><label class="flex items-center gap-3 rounded-full border border-border bg-secondary/60 px-5 py-3 text-foreground transition-colors focus-within:border-foreground/30 focus-within:bg-secondary hover:bg-secondary" data-tsd-source="/src/components/site/Navbar.tsx:43:11"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4 text-muted-foreground" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:44:13"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input type="search" placeholder="Search products, brands, categories…" class="w-full bg-transparent text-sm placeholder:text-muted-foreground focus:outline-none" data-tsd-source="/src/components/site/Navbar.tsx:45:13"/></label></div><div class="flex-1 sm:hidden" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:53:9"></div><div class="flex items-center gap-1.5 sm:gap-2" data-tsd-source="/src/components/site/Navbar.tsx:57:9"><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full" aria-label="Switch to dark mode" data-tsd-source="/src/components/site/ThemeToggle.tsx:8:5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/ThemeToggle.tsx:15:60"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"></path></svg></button><a href="/compare" aria-label="Compare products" title="Compare products" class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-git-compare h-5 w-5" aria-hidden="true"><circle cx="18" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><path d="M13 6h3a2 2 0 0 1 2 2v7"></path><path d="M11 18H8a2 2 0 0 1-2-2V9"></path></svg></a><a href="/cart" class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:61:15"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg></a><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full lg:hidden" aria-label="Menu" data-tsd-source="/src/components/site/Navbar.tsx:69:11"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:76:49"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg></button></div></div><div class="container-page pb-3 sm:hidden" data-tsd-source="/src/components/site/Navbar.tsx:82:7"><label class="flex items-center gap-3 rounded-full border border-border bg-secondary/60 px-4 py-2.5 text-foreground focus-within:border-foreground/30 focus-within:bg-secondary" data-tsd-source="/src/components/site/Navbar.tsx:83:9"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4 text-muted-foreground" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:84:11"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input type="search" placeholder="Search products, brands, categories…" class="w-full bg-transparent text-sm placeholder:text-muted-foreground focus:outline-none" data-tsd-source="/src/components/site/Navbar.tsx:85:11"/></label></div><div class="hidden border-t border-border lg:block" data-tsd-source="/src/components/site/Navbar.tsx:96:7"><div class="container-page flex h-11 items-center gap-6 text-xs" data-tsd-source="/src/components/site/Navbar.tsx:97:9"><nav class="flex items-center gap-1" data-tsd-source="/src/components/site/Navbar.tsx:98:11"><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">HOME</a><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop?condition=intact" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground">BRAND NEW INTACT BOX</a><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop?condition=without-box" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground">BRAND NEW WITHOUT BOX</a><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop?condition=pre-owned" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground">PRE-OWNED</a></nav><div class="flex-1" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:115:11"></div><nav class="flex items-center gap-1" data-tsd-source="/src/components/site/Navbar.tsx:116:11"><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/blog" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BLOG</a><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/customer-spotlight" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">CUSTOMER SPOTLIGHT</a><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/philanthropic-work" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">PHILANTHROPIC WORK</a><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/customer-feedback" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">CUSTOMER FEEDBACK</a></nav></div></div></header><main class="flex-1 pb-14 sm:pb-0" data-tsd-source="/src/routes/__root.tsx:156:15"><!--$--><div class="container-page py-14" data-tsd-source="/src/routes/shop.tsx:116:5"><div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between" data-tsd-source="/src/components/site/SectionHeader.tsx:23:5"><div class="max-w-2xl" data-tsd-source="/src/components/site/SectionHeader.tsx:28:7"><p class="text-xs font-medium uppercase tracking-[0.18em] text-muted-foreground" data-tsd-source="/src/components/site/SectionHeader.tsx:30:11">Catalog</p><h2 class="mt-2 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl" data-tsd-source="/src/components/site/SectionHeader.tsx:34:9">Shop Khan Gadget</h2><p class="mt-3 text-sm leading-relaxed text-muted-foreground sm:text-base" data-tsd-source="/src/components/site/SectionHeader.tsx:38:11">Filter by condition, category, brand and price. Everything in stock, ready to ship.</p></div></div><div class="mt-8 grid gap-8 lg:grid-cols-[260px_1fr]" data-tsd-source="/src/routes/shop.tsx:123:7">        <aside class="hidden lg:block">
            <form id="shop-filter-form" action="/shop" method="GET" class="sticky top-32 space-y-5 rounded-md border border-border bg-surface p-5">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-semibold uppercase tracking-widest text-foreground">Filters</p>
                    @if(request()->hasAny(['condition', 'category', 'brand', 'max_price', 'search']))
                        <a href="/shop" class="text-[11px] font-medium text-rose-500 hover:underline">Clear All</a>
                    @endif
                </div>

                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <!-- Condition Filter -->
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-muted-foreground">Condition</p>
                    <div class="space-y-2">
                        @foreach($conditions as $cond)
                            <label class="flex cursor-pointer items-center gap-2 text-sm group">
                                <input type="radio" name="condition" value="{{ $cond->slug }}" 
                                       class="h-4 w-4 rounded-full border-border accent-foreground cursor-pointer"
                                       {{ request('condition') === $cond->slug ? 'checked' : '' }}
                                       onchange="this.form.submit()" />
                                <span class="text-foreground/90 group-hover:text-foreground transition-colors {{ request('condition') === $cond->slug ? 'font-bold text-foreground' : '' }}">
                                    {{ strtoupper($cond->name) }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Category Filter -->
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-muted-foreground">Category</p>
                    <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                        @foreach($categories as $cat)
                            <label class="flex cursor-pointer items-center gap-2 text-sm group">
                                <input type="radio" name="category" value="{{ $cat->slug }}" 
                                       class="h-4 w-4 rounded-full border-border accent-foreground cursor-pointer"
                                       {{ request('category') === $cat->slug ? 'checked' : '' }}
                                       onchange="this.form.submit()" />
                                <span class="text-foreground/90 group-hover:text-foreground transition-colors {{ request('category') === $cat->slug ? 'font-bold text-foreground' : '' }}">
                                    {{ $cat->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Brand Filter -->
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-muted-foreground">Brand</p>
                    <div class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        @foreach($brands as $b)
                            <label class="flex cursor-pointer items-center gap-2 text-sm group">
                                <input type="radio" name="brand" value="{{ $b->slug }}" 
                                       class="h-4 w-4 rounded-full border-border accent-foreground cursor-pointer"
                                       {{ request('brand') === $b->slug ? 'checked' : '' }}
                                       onchange="this.form.submit()" />
                                <span class="text-foreground/90 group-hover:text-foreground transition-colors {{ request('brand') === $b->slug ? 'font-bold text-foreground' : '' }}">
                                    {{ $b->name }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Max Price Filter -->
                <div>
                    <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-muted-foreground">
                        Max price · ৳<span id="price-display">{{ number_format(request('max_price', 300000)) }}</span>
                    </p>
                    <div class="space-y-2">
                        <input type="range" name="max_price" min="5000" max="300000" step="5000" 
                               value="{{ request('max_price', 300000) }}"
                               class="w-full accent-foreground cursor-pointer" 
                               oninput="document.getElementById('price-display').innerText = new Intl.NumberFormat().format(this.value)"
                               onchange="this.form.submit()" />
                    </div>
                </div>

                <!-- Submit Button if needed -->
                <button type="submit" class="w-full py-2 bg-primary text-primary-foreground text-xs font-bold rounded shadow-sm hover:opacity-90 transition-opacity">
                    Apply Filters
                </button>
            </form>
        </aside><div data-tsd-source="/src/routes/shop.tsx:132:9"><div class="flex flex-wrap items-center gap-3" data-tsd-source="/src/routes/shop.tsx:133:11"><button class="inline-flex items-center gap-2 rounded-full border border-border px-4 py-2 text-sm lg:hidden" data-tsd-source="/src/routes/shop.tsx:134:13"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sliders-horizontal h-4 w-4" aria-hidden="true" data-tsd-source="/src/routes/shop.tsx:138:15"><path d="M10 5H3"></path><path d="M12 19H3"></path><path d="M14 3v4"></path><path d="M16 17v4"></path><path d="M21 12h-9"></path><path d="M21 19h-5"></path><path d="M21 5h-7"></path><path d="M8 10v4"></path><path d="M8 12H3"></path></svg>Filters </button><div class="ms-auto flex flex-wrap items-center gap-2 text-xs" data-tsd-source="/src/routes/shop.tsx:141:13"><a data-tsd-source="/src/routes/shop.tsx:148:17" class="rounded-full px-3 py-1.5 bg-foreground text-background active" href="/shop?sort=featured&amp;condition=" data-status="active" aria-current="page">Featured</a><a data-tsd-source="/src/routes/shop.tsx:148:17" href="/shop?sort=price-asc&amp;condition=" class="rounded-full px-3 py-1.5 border border-border hover:bg-secondary">Price ↑</a><a data-tsd-source="/src/routes/shop.tsx:148:17" href="/shop?sort=price-desc&amp;condition=" class="rounded-full px-3 py-1.5 border border-border hover:bg-secondary">Price ↓</a><a data-tsd-source="/src/routes/shop.tsx:148:17" href="/shop?sort=rating&amp;condition=" class="rounded-full px-3 py-1.5 border border-border hover:bg-secondary">Top rated</a></div></div><p class="mt-4 text-sm text-muted-foreground">Showing <span class="font-medium text-foreground">{{ $products->total() ?? $products->count() }}</span> products</p><div class="mt-6 grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 xl:grid-cols-3">@foreach ($products as $product)
<article class="group flex h-full flex-col">
    <div class="relative overflow-hidden rounded-md bg-surface">
        <a href="/product/{{ $product->slug }}" class="block">
            <div class="aspect-square w-full overflow-hidden">
                <img src="{{ $product->primaryImage() }}" alt="{{ $product->name }}" loading="lazy" width="900" height="900" class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" />
            </div>
        </a>
        @if ($product->badge)
            <span class="product-card-badge absolute left-1.5 top-1.5 rounded font-extrabold uppercase tracking-normal shadow-xs pointer-events-none z-10" style="font-size: 8px !important; line-height: 10px !important; padding: 1.5px 4px !important; width: max-content !important; max-width: calc(100% - 12px) !important;">
                {{ $product->badge }}
            </span>
        @endif
    </div>
    <div class="flex flex-1 flex-col pt-2 sm:pt-3">
        <h3 class="line-clamp-2 text-xs sm:text-sm font-semibold leading-snug tracking-tight text-foreground">
            <a href="/product/{{ $product->slug }}" class="hover:underline">{{ $product->name }}</a>
        </h3>
        <ul class="mt-1.5 sm:mt-2 space-y-0.5 sm:space-y-1 text-[10px] sm:text-xs leading-tight text-muted-foreground">
            @if($product->relationLoaded('highlights') && $product->highlights->count())
                @foreach ($product->highlights->take(4) as $highlight)
                    <li class="flex items-start gap-1.5">
                        <span aria-hidden="true" class="mt-[4px] h-1 w-1 shrink-0 rounded-full bg-muted-foreground/60"></span>
                        <span class="line-clamp-1">{{ $highlight->text }}</span>
                    </li>
                @endforeach
            @else
                @if($product->brand)
                    <li class="flex items-start gap-1.5">
                        <span aria-hidden="true" class="mt-[4px] h-1 w-1 shrink-0 rounded-full bg-muted-foreground/60"></span>
                        <span class="line-clamp-1">{{ $product->brand->name }}</span>
                    </li>
                @endif
                @if($product->category)
                    <li class="flex items-start gap-1.5">
                        <span aria-hidden="true" class="mt-[4px] h-1 w-1 shrink-0 rounded-full bg-muted-foreground/60"></span>
                        <span class="line-clamp-1">{{ $product->category->name }}</span>
                    </li>
                @endif
            @endif
        </ul>
        <div class="mt-2 sm:mt-3 flex flex-wrap items-baseline gap-x-1.5 sm:gap-x-2">
            <span class="text-sm sm:text-base font-semibold text-foreground">৳ {{ number_format($product->price) }}</span>
            @if ($product->compare_at_price)
                <span class="text-xs sm:text-sm text-muted-foreground line-through">৳ {{ number_format($product->compare_at_price) }}</span>
            @endif
        </div>
        <div class="mt-2 sm:mt-3 flex items-center justify-between gap-1.5 sm:gap-2">
            <a href="/cart" class="h-8 w-8 sm:h-9 sm:w-9 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200/80 dark:border-slate-700 hover:bg-slate-200 dark:hover:bg-slate-700 flex items-center justify-center shrink-0 transition-colors shadow-sm" title="Add to Cart" aria-label="Add to Cart">
                <i data-lucide="plus" class="h-4 w-4"></i>
            </a>
            <a href="/checkout?product={{ $product->slug }}" class="btn-buy-now flex-1 inline-flex items-center justify-center h-8 sm:h-9 px-3 sm:px-4 rounded-full font-bold text-xs sm:text-sm transition-all shadow-sm active:scale-[0.98]" style="background-color: #24272c !important; color: #ffffff !important;">
                Buy Now
            </a>

        </div>
    </div>
</article>
@endforeach</div></div></div></div><!--/$--></main>@include('partials.footer')</div><nav aria-label="Mobile footer navigation" class="fixed bottom-0 left-0 right-0 z-40 border-t border-border bg-background/95 backdrop-blur sm:hidden" data-tsd-source="/src/components/site/MobileFooterNav.tsx:14:5"><ul class="grid grid-cols-4" data-tsd-source="/src/components/site/MobileFooterNav.tsx:18:7"><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>Home</a></li><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-foreground active" href="/shop" data-status="active" aria-current="page"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-grid h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><rect width="7" height="7" x="3" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="14" rx="1"></rect><rect width="7" height="7" x="3" y="14" rx="1"></rect></svg>Explore</a></li><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/customer-spotlight" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>Spotlight</a></li><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/customer-feedback" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square-heart h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"></path><path d="M7.5 9.5c0 .687.265 1.383.697 1.844l3.009 3.264a1.14 1.14 0 0 0 .407.314 1 1 0 0 0 .783-.004 1.14 1.14 0 0 0 .398-.31l3.008-3.264A2.77 2.77 0 0 0 16.5 9.5 2.5 2.5 0 0 0 12 8a2.5 2.5 0 0 0-4.5 1.5"></path></svg>Feedback</a></li></ul></nav><div class="fixed bottom-20 right-4 z-50 flex flex-col items-center sm:bottom-6" data-tsd-source="/src/components/site/ChatWidget.tsx:214:7"><button class="flex flex-col items-center transition-transform hover:scale-105 active:scale-95" aria-label="Open live chat" title="Need help?" data-tsd-source="/src/components/site/ChatWidget.tsx:215:9"><img src="/assets/support-agent-BWJyOWv2.png" alt="Live chat support agent" width="512" height="512" loading="lazy" class="agent-float h-20 w-20 select-none object-contain drop-shadow-lg sm:h-24 sm:w-24" data-tsd-source="/src/components/site/ChatWidget.tsx:221:11"/><span class="-mt-1 rounded-full bg-foreground px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-background shadow-sm" data-tsd-source="/src/components/site/ChatWidget.tsx:229:11">Live Chat</span></button></div><section aria-label="Notifications alt+T" tabindex="-1" aria-live="polite" aria-relevant="additions text" aria-atomic="false"></section><script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInputs = document.querySelectorAll('input[type="search"]');

    searchInputs.forEach(input => {
        const parentContainer = input.closest('label') || input.parentElement;
        if (!parentContainer) return;

        // Make parent container relative for positioning search dropdown
        parentContainer.classList.add('relative');

        // Create results overlay element
        const resultsBox = document.createElement('div');
        resultsBox.className = 'absolute left-0 right-0 top-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 z-50 overflow-hidden divide-y divide-gray-100 hidden text-left max-h-96 overflow-y-auto';
        parentContainer.appendChild(resultsBox);

        let debounceTimer = null;

        input.addEventListener('input', function () {
            const query = this.value.trim();
            if (debounceTimer) clearTimeout(debounceTimer);

            if (query.length < 3) {
                resultsBox.innerHTML = '';
                resultsBox.classList.add('hidden');
                return;
            }

            // Show loading state
            resultsBox.innerHTML = '<div class="p-4 text-xs text-gray-500 font-medium text-center">Searching gadgets for "' + escapeHtml(query) + '"...</div>';
            resultsBox.classList.remove('hidden');

            debounceTimer = setTimeout(() => {
                fetch('/api/search?q=' + encodeURIComponent(query))
                    .then(res => res.json())
                    .then(data => {
                        if (!data.products || data.products.length === 0) {
                            resultsBox.innerHTML = '<div class="p-4 text-xs text-gray-400 font-medium text-center">No products found matching "' + escapeHtml(query) + '"</div>';
                            return;
                        }

                        let html = '<div class="p-2 text-[10px] font-bold uppercase tracking-wider text-gray-400 bg-gray-50/80 px-4 py-2 border-b border-gray-100">Products (' + data.products.length + ')</div>';
                        
                        data.products.forEach(item => {
                            html += `
                                <a href="${item.url}" class="flex items-center gap-3 p-3 hover:bg-slate-50 transition-colors text-slate-800 group">
                                    <div class="h-12 w-12 rounded-lg bg-gray-100 overflow-hidden shrink-0 border border-gray-200">
                                        <img src="${item.image}" alt="${escapeHtml(item.name)}" class="h-full w-full object-cover group-hover:scale-105 transition-transform" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-xs font-bold truncate text-gray-900 group-hover:text-blue-600 transition-colors">${escapeHtml(item.name)}</div>
                                        <div class="text-[10px] text-gray-500 mt-0.5">${escapeHtml(item.category)}</div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div class="text-xs font-bold text-gray-900">${item.price}</div>
                                        ${item.compare_at_price ? `<div class="text-[10px] text-gray-400 line-through">${item.compare_at_price}</div>` : ''}
                                    </div>
                                </a>
                            `;
                        });

                        resultsBox.innerHTML = html;
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                        resultsBox.innerHTML = '<div class="p-4 text-xs text-rose-500 font-medium text-center">Search error. Please try again.</div>';
                    });
            }, 300);
        });

        // Close on clicking outside
        document.addEventListener('click', function (e) {
            if (!parentContainer.contains(e.target)) {
                resultsBox.classList.add('hidden');
            }
        });

        // Close on Escape key
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                resultsBox.classList.add('hidden');
            }
        });
    });

    function escapeHtml(str) {
        return (str || '').replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;");
    }
});
</script>
@include('partials.mobile-drawer')
</body></html>

