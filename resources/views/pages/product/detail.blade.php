
<script>
    window.__SITE_LOGO_LIGHT = "{{ $siteLogo ?? '/media/b3ca13-kg-lockup-v2.png' }}";
    window.__SITE_LOGO_DARK  = "{{ $siteLogoDark ?? '/media/logo_dark_1786184552.png' }}";
    window.__SITE_NAME       = "{{ $siteName ?? 'Khan Gadget' }}";
</script>
<!DOCTYPE html><html lang="en" data-tsd-source="/src/routes/__root.tsx:133:5"><head data-tsd-source="/src/routes/__root.tsx:134:7"><meta charSet="utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/><link rel="preload" as="image" href="/media/b3ca13-kg-lockup-v2.png"/><link rel="preload" as="image" href="{{ $product->primaryImage() }}"/><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" data-precedence="default"/><title>{{ $product->name }} | {{ $siteName ?? 'Khan Gadget' }}</title><meta name="author" content="{{ $siteName ?? 'Khan Gadget' }}"/><meta name="twitter:card" content="summary_large_image"/><meta name="twitter:site" content="{{ $siteName ?? 'Khan Gadget' }}"/><meta name="twitter:title" content="{{ $product->name }} | {{ $siteName ?? 'Khan Gadget' }}"/><meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 160) }}"/><meta property="og:image" content="{{ $product->primaryImage() }}"/><meta name="twitter:image" content="{{ $product->primaryImage() }}"/><meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 160) }}"/><meta property="og:title" content="{{ $product->name }} | {{ $siteName ?? 'Khan Gadget' }}"/><meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 160) }}"/><meta property="og:type" content="product"/><link rel="icon" href="{{ $siteFavicon ?? '/favicon.png' }}" type="image/png"/><link rel="preconnect" href="https://fonts.googleapis.com"/><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous"/><link rel="stylesheet" href="/assets/styles-CC_Lznyw.css"><script src="https://unpkg.com/lucide@latest"></script><script src="/assets/theme.js"></script></head><body data-tsd-source="/src/routes/__root.tsx:138:7"><div class="flex min-h-screen flex-col bg-background" data-tsd-source="/src/routes/__root.tsx:154:13"><header class="sticky top-0 z-40 border-b border-border bg-background/95 backdrop-blur" data-tsd-source="/src/components/site/Navbar.tsx:31:5"><div class="container-page flex h-20 items-center gap-3" data-tsd-source="/src/components/site/Navbar.tsx:33:7"><a aria-label="Khan Gadget home" data-tsd-source="/src/components/site/Navbar.tsx:34:9" href="/" class="flex shrink-0 items-center"><img src="/media/b3ca13-kg-lockup-v2.png" alt="Khan Gadget - Eternal Tech Companion" class="h-9 w-auto object-contain sm:h-12 lg:h-14" data-tsd-source="/src/components/site/Navbar.tsx:35:11"/></a><div class="hidden flex-1 sm:block" data-tsd-source="/src/components/site/Navbar.tsx:42:9"><label class="flex items-center gap-3 rounded-full border border-border bg-secondary/60 px-5 py-3 text-foreground transition-colors focus-within:border-foreground/30 focus-within:bg-secondary hover:bg-secondary" data-tsd-source="/src/components/site/Navbar.tsx:43:11"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4 text-muted-foreground" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:44:13"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input type="search" placeholder="Search products, brands, categories…" class="w-full bg-transparent text-sm placeholder:text-muted-foreground focus:outline-none" data-tsd-source="/src/components/site/Navbar.tsx:45:13"/></label></div><div class="flex-1 sm:hidden" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:53:9"></div><div class="flex items-center gap-1.5 sm:gap-2" data-tsd-source="/src/components/site/Navbar.tsx:57:9"><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full" aria-label="Switch to dark mode" data-tsd-source="/src/components/site/ThemeToggle.tsx:8:5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/ThemeToggle.tsx:15:60"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"></path></svg></button><a href="/compare" aria-label="Compare products" title="Compare products" class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-git-compare h-5 w-5" aria-hidden="true"><circle cx="18" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><path d="M13 6h3a2 2 0 0 1 2 2v7"></path><path d="M11 18H8a2 2 0 0 1-2-2V9"></path></svg></a><a href="/cart" class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:61:15"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg></a><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full lg:hidden" aria-label="Menu" data-tsd-source="/src/components/site/Navbar.tsx:69:11"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:76:49"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg></button></div></div><div class="container-page pb-3 sm:hidden" data-tsd-source="/src/components/site/Navbar.tsx:82:7"><label class="flex items-center gap-3 rounded-full border border-border bg-secondary/60 px-4 py-2.5 text-foreground focus-within:border-foreground/30 focus-within:bg-secondary" data-tsd-source="/src/components/site/Navbar.tsx:83:9"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4 text-muted-foreground" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:84:11"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input type="search" placeholder="Search products, brands, categories…" class="w-full bg-transparent text-sm placeholder:text-muted-foreground focus:outline-none" data-tsd-source="/src/components/site/Navbar.tsx:85:11"/></label></div><div class="hidden border-t border-border lg:block" data-tsd-source="/src/components/site/Navbar.tsx:96:7"><div class="container-page flex h-11 items-center gap-6 text-xs" data-tsd-source="/src/components/site/Navbar.tsx:97:9"><nav class="flex items-center gap-1" data-tsd-source="/src/components/site/Navbar.tsx:98:11"><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">ALL PRODUCTS</a><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop?condition=intact" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BRAND NEW INTACT BOX</a><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop?condition=without-box" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BRAND NEW WITHOUT BOX</a><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop?condition=pre-owned" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">PRE-OWNED</a></nav><div class="flex-1" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:115:11"></div><nav class="flex items-center gap-1" data-tsd-source="/src/components/site/Navbar.tsx:116:11"><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/blog" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BLOG</a><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/customer-spotlight" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">CUSTOMER SPOTLIGHT</a><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/philanthropic-work" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">PHILANTHROPIC WORK</a><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/customer-feedback" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">CUSTOMER FEEDBACK</a></nav></div></div></header><main class="flex-1 pb-14 sm:pb-0" data-tsd-source="/src/routes/__root.tsx:156:15"><!--$-->

<div class="container-page py-10" data-tsd-source="/src/routes/product.$slug.tsx:46:5">

    <style>
        .product-title { font-size: 28px; }
        @media (min-width: 640px) {
            .product-title { font-size: 34px; }
        }
        .product-price-amount { font-size: 27px; }
        @media (min-width: 640px) {
            .product-price-amount { font-size: 33px; }
        }
        .product-rich-text a { text-decoration: underline; color: var(--color-foreground, #09090b); }
        .product-rich-text img { max-width: 100%; height: auto; border-radius: 2px; }
        .product-rich-text p { margin: 0 0 1em; }
        .product-rich-text p:last-child { margin-bottom: 0; }
        .product-rich-text ul, .product-rich-text ol { margin: 0 0 1em; padding-left: 1.25em; }

        /* Compact, capped-width image column instead of scaling with viewport width. */
        @media (min-width: 1024px) {
            .product-card { grid-template-columns: 460px 1fr; gap: 4rem; }
        }

        /* Specifications + Recently Viewed sidebar, side by side on wider screens. */
        .spec-recently-viewed-grid { display: grid; gap: 2.5rem; }
        @media (min-width: 1024px) {
            .spec-recently-viewed-grid { grid-template-columns: 1fr 360px; align-items: start; }
        }

        /* Mobile only: push Recently Viewed to the very bottom of the page,
           after Description and Related Products. Desktop keeps the
           sidebar-beside-specifications layout above untouched. */
        @media (max-width: 1023.98px) {
            .product-flow { display: flex; flex-direction: column; }
            .product-flow > .spec-recently-viewed-grid { display: contents; }
            .specs-block { order: 0; margin-top: 2.5rem; }
            .product-flow > #description { order: 1; }
            .product-flow > #relatedProducts { order: 2; }
            #recentlyViewedSidebar { order: 3; margin-top: 2.5rem; }
        }

        /* Compact specification table with a fixed label column and zebra striping. */
        .spec-table { position: relative; }
        .spec-table::before { content: ''; position: absolute; top: 0; bottom: 0; left: calc(1.25rem + 160px + 1rem); width: 1px; background: var(--color-border, #e5e7eb); pointer-events: none; }
        .spec-row { display: grid; grid-template-columns: 160px 1fr; gap: 1rem; border-bottom: 1px solid var(--color-border, #e5e7eb); }
        .spec-row dd { padding-left: 1rem; }
        .spec-table > .spec-row:last-child { border-bottom: none; }
        .spec-table > .spec-row:nth-child(even) { background: var(--color-secondary, #f4f4f5); }
        @media (max-width: 640px) {
            .spec-row { grid-template-columns: 1fr; gap: 0.25rem; }
            .spec-table::before { display: none; }
            .spec-row dd { padding-left: 0; }
        }

        .recently-viewed-card { transition: border-color .15s ease, box-shadow .15s ease; }
        .recently-viewed-card:hover { border-color: var(--color-foreground, #09090b); box-shadow: 0 1px 4px rgb(0 0 0 / 0.06); }
    </style>

<div class="mt-8 grid gap-10 product-card">

        {{-- Image gallery --}}
        <div>
            <div class="group overflow-hidden rounded-md border border-border bg-surface">
                <div class="aspect-square" id="mainImageStage" style="position:relative;overflow:hidden">
                    <img id="mainProductImage" src="{{ $product->primaryImage() }}" alt="{{ $product->name }}" width="1200" height="1200" style="transform-origin:50% 50%" class="h-full w-full object-cover transition-transform duration-300 ease-out group-hover:scale-[1.8]" />
                </div>
            </div>
            @if($product->images->count() > 1)
                <div class="mt-4 grid grid-cols-4 gap-3">
                    @foreach($product->images->take(4) as $image)
                        <div class="gallery-thumb overflow-hidden rounded-sm bg-surface ring-1 ring-border {{ $loop->first ? 'is-active' : '' }}" data-full="{{ $image->image_path }}" data-index="{{ $loop->index }}">
                            <div class="aspect-square">
                                <img src="{{ $image->image_path }}" alt="{{ $product->name }}" loading="eager" fetchpriority="high" class="h-full w-full object-cover" />
                            </div>
                        </div>
                    @endforeach
                </div>
                <style>
                    .gallery-thumb { cursor: pointer; outline: 2px solid transparent; outline-offset: 2px; transition: outline-color .15s ease; }
                    .gallery-thumb.is-active { outline-color: var(--foreground); }
                </style>
                <script>
                    (function () {
                        var stage = document.getElementById('mainImageStage');
                        var mainImg = document.getElementById('mainProductImage');
                        var thumbs = Array.prototype.slice.call(document.querySelectorAll('.gallery-thumb'));
                        var currentIndex = 0;
                        var animating = false;
                        thumbs.forEach(function (t) {
                            if (t.classList.contains('is-active')) currentIndex = parseInt(t.dataset.index, 10) || 0;
                        });

                        // The persistent <img id="mainProductImage"> is never destroyed/recreated
                        // or animated itself (that previously left the hover-zoom stuck) — instead
                        // a temporary overlay image slides in on top of it from the left or right
                        // depending on which thumbnail was clicked or which way the user swiped,
                        // and only once the slide finishes do we swap the real image's `src`
                        // underneath and remove the overlay.
                        function goToIndex(newIndex) {
                            if (newIndex < 0 || newIndex >= thumbs.length) return;
                            var thumb = thumbs[newIndex];
                            var newSrc = thumb.dataset.full;
                            if (!mainImg || !stage || !newSrc || newIndex === currentIndex || animating) return;

                            var direction = newIndex > currentIndex ? 1 : -1;
                            animating = true;

                            var overlay = document.createElement('img');
                            overlay.src = newSrc;
                            overlay.alt = mainImg.alt;
                            overlay.draggable = false;
                            overlay.setAttribute('aria-hidden', 'true');
                            overlay.className = 'h-full w-full object-cover';
                            overlay.style.position = 'absolute';
                            overlay.style.inset = '0';
                            overlay.style.transform = 'translateX(' + (direction * 100) + '%)';
                            overlay.style.transition = 'transform .38s cubic-bezier(.22,.61,.36,1)';
                            overlay.style.willChange = 'transform';
                            stage.appendChild(overlay);

                            // Force a reflow so the starting transform is applied before animating to 0.
                            void overlay.offsetWidth;

                            requestAnimationFrame(function () {
                                overlay.style.transform = 'translateX(0)';
                            });

                            overlay.addEventListener('transitionend', function handler() {
                                overlay.removeEventListener('transitionend', handler);
                                mainImg.src = newSrc;
                                if (overlay.parentNode === stage) stage.removeChild(overlay);
                                animating = false;
                            });

                            currentIndex = newIndex;
                            thumbs.forEach(function (t) { t.classList.remove('is-active'); });
                            thumb.classList.add('is-active');
                        }

                        thumbs.forEach(function (thumb) {
                            thumb.addEventListener('click', function () {
                                goToIndex(parseInt(thumb.dataset.index, 10) || 0);
                            });
                        });

                        // Touch swipe: drag the main image left/right on mobile to move
                        // between gallery photos, same transition as tapping a thumbnail.
                        if (stage && thumbs.length > 1) {
                            var touchStartX = 0;
                            var touchStartY = 0;
                            var touchActive = false;

                            stage.addEventListener('touchstart', function (e) {
                                if (e.touches.length !== 1) return;
                                touchStartX = e.touches[0].clientX;
                                touchStartY = e.touches[0].clientY;
                                touchActive = true;
                            }, { passive: true });

                            stage.addEventListener('touchmove', function (e) {
                                if (!touchActive || e.touches.length !== 1) return;
                                var dx = e.touches[0].clientX - touchStartX;
                                var dy = e.touches[0].clientY - touchStartY;
                                // Once the gesture is clearly more horizontal than vertical,
                                // stop the page from scrolling so the swipe feels natural.
                                if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) > 10) {
                                    e.preventDefault();
                                }
                            }, { passive: false });

                            stage.addEventListener('touchend', function (e) {
                                if (!touchActive) return;
                                touchActive = false;
                                var touch = e.changedTouches[0];
                                var dx = touch.clientX - touchStartX;
                                var dy = touch.clientY - touchStartY;
                                var SWIPE_THRESHOLD = 40;
                                if (Math.abs(dx) < SWIPE_THRESHOLD || Math.abs(dx) < Math.abs(dy)) return;
                                goToIndex(currentIndex + (dx < 0 ? 1 : -1));
                            });
                        }
                    })();
                </script>
            @endif
        </div>

        {{-- Product info --}}
        <div>
            <a href="/product/{{ $product->slug }}" class="sr-only">{{ $product->name }}</a>
            @if($product->brand && $product->brand->logo_path)
                <img src="{{ $product->brand->logo_path }}" alt="{{ $product->brand->name }}" class="h-6 w-auto object-contain" />
            @else
                <p class="text-xs uppercase tracking-[0.18em] text-muted-foreground">{{ $product->brand->name ?? '' }}</p>
            @endif
            <h1 class="product-title mt-2 font-semibold tracking-tight text-foreground">{{ $product->name }}</h1>

            @if($product->is_new_arrival)
                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <span class="inline-flex items-center gap-1 rounded-full bg-secondary px-3 py-1.5 text-sm text-muted-foreground shadow-sm">
                        Price:
                        @if($product->price_is_tba)
                            <span class="font-semibold text-accent">TBA</span>
                        @else
                            <span class="font-semibold text-accent">৳ {{ number_format($product->price) }}</span>
                        @endif
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-secondary px-3 py-1.5 text-sm text-muted-foreground shadow-sm">
                        Status: <span class="font-semibold text-accent">Pre-Order</span>
                    </span>
                </div>
            @else
                <div class="mt-6 inline-flex flex-wrap items-center gap-3 rounded-md border border-border bg-secondary/30 px-4 py-3" style="max-width:100%;">
                    <span class="product-price-amount font-bold" style="color:#EA580C">৳ {{ number_format($product->price) }}</span>
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <span class="text-base text-muted-foreground line-through">৳ {{ number_format($product->compare_at_price) }}</span>
                    @endif
                    @if($product->compare_at_price && $product->compare_at_price > $product->price)
                        <span class="rounded-full bg-success/10 px-2 py-0.5 text-xs font-medium text-success">Save ৳ {{ number_format($product->discount()) }}</span>
                    @endif
                    <span class="hidden h-5 w-px bg-border sm:inline-block"></span>
                    <span class="text-sm font-semibold text-foreground">
                        Status:
                        @if($product->in_stock)
                            <span class="text-success">In Stock</span>
                        @else
                            <span class="text-rose-600">Out of Stock</span>
                        @endif
                    </span>
                </div>
            @endif


            @if($product->highlights->count())
                <div class="mt-6">
                    <p class="text-sm font-semibold text-foreground">Key Features</p>
                    <ul class="mt-3 space-y-2">
                        @foreach($product->highlights as $highlight)
                            <li class="flex items-start gap-2 text-sm text-muted-foreground">
                                <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-muted-foreground"></span>
                                <span>{{ $highlight->text }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if($product->colors->count())
                <div class="mt-8">
                    <p class="text-sm font-medium text-foreground">Color</p>
                    <div class="mt-3 flex items-center gap-2">
                        @foreach($product->colors as $index => $color)
                            <button type="button" class="h-9 w-9 rounded-full border-2 transition-all {{ $index === 0 ? 'border-foreground scale-105' : 'border-border' }}" style="background-color:{{ $color->hex_code }}" aria-label="Color {{ $color->name ?? $color->hex_code }}" title="{{ $color->name ?? $color->hex_code }}"></button>
                        @endforeach
                    </div>
                </div>
            @endif

            @include('partials.stock-price-notice')

            <div class="mt-8 space-y-3">
                @if($product->is_new_arrival)
                    <a href="https://wa.me/{{ \App\Support\PhoneNumber::whatsapp($whatsappNumber ?? '8801700000001') }}?text={{ rawurlencode('I want to order ' . $product->name . ' ' . route('product', $product->slug)) }}" target="_blank" rel="noopener noreferrer" class="btn-buy-now inline-flex w-full items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground hover:bg-primary/90 group relative h-12 overflow-hidden rounded-sm px-8 shadow-md transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.99]">
                        <span class="pointer-events-none absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-700 group-hover:translate-x-full"></span>
                        <span class="relative text-xs font-bold uppercase tracking-[0.14em]">Order Now</span>
                    </a>
                @else
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="inline-flex items-center rounded-full border border-border bg-background">
                            <button type="button" id="qty-decrease" class="grid h-11 w-11 place-items-center rounded-full hover:bg-secondary" aria-label="Decrease quantity"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-minus h-4 w-4" aria-hidden="true"><path d="M5 12h14"></path></svg></button>
                            <span id="qty-value" class="w-8 text-center text-sm font-medium tabular-nums">1</span>
                            <button type="button" id="qty-increase" class="grid h-11 w-11 place-items-center rounded-full hover:bg-secondary" aria-label="Increase quantity"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus h-4 w-4" aria-hidden="true"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg></button>
                        </div>
                        <a href="/checkout?product={{ $product->slug }}" id="buy-now-link" data-base-href="/checkout?product={{ $product->slug }}" class="btn-buy-now inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground hover:bg-primary/90 group relative h-12 flex-1 overflow-hidden rounded-sm px-8 shadow-md transition-all duration-200 hover:-translate-y-0.5 active:translate-y-0 active:scale-[0.99]">
                            <span class="pointer-events-none absolute inset-0 -translate-x-full bg-gradient-to-r from-transparent via-white/20 to-transparent transition-transform duration-700 group-hover:translate-x-full"></span>
                            <span class="relative flex items-center gap-3">
                                <span class="text-xs font-bold uppercase tracking-[0.14em]">Buy Now</span>
                                <span class="h-4 w-px bg-current opacity-30"></span>
                                <span class="text-base font-semibold tabular-nums">{{ $product->price_is_tba ? 'TBA' : '৳ ' . number_format($product->price) }}</span>
                            </span>
                        </a>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="https://wa.me/{{ \App\Support\PhoneNumber::whatsapp($whatsappNumber ?? '8801700000001') }}?text={{ rawurlencode('I want to order ' . $product->name . ' ' . route('product', $product->slug)) }}" target="_blank" rel="noopener noreferrer" class="whatsapp-btn inline-flex min-w-0 items-center justify-center gap-2 whitespace-nowrap text-sm font-bold h-12 rounded-full shadow-sm transition-colors" style="background-color:#25D366;color:#ffffff">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.571-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .105 5.36.103 11.943c0 2.105.549 4.16 1.595 5.973L0 24l6.335-1.652a11.882 11.882 0 005.71 1.447h.006c6.585 0 11.94-5.36 11.943-11.943a11.874 11.874 0 00-3.474-8.403"/></svg>
                            <span class="truncate">WhatsApp</span>
                        </a>
                        <button class="inline-flex min-w-0 items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-secondary text-secondary-foreground shadow-sm hover:bg-secondary/80 h-12 rounded-full" aria-label="Add to Cart">Add to cart</button>
                    </div>
                @endif
            </div>

            @if(($productTrustBadgesActive ?? '1') == '1')
                <ul class="mt-8 grid gap-4 rounded-md border border-border bg-card p-5 text-sm sm:grid-cols-3">
                    <li class="flex items-start gap-3"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-sm bg-secondary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck h-4 w-4" aria-hidden="true"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"></path><path d="M15 18H9"></path><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"></path><circle cx="17" cy="18" r="2"></circle><circle cx="7" cy="18" r="2"></circle></svg></span><div class="min-w-0"><p class="font-medium text-foreground">Fast delivery</p><p class="text-xs text-muted-foreground">Dhaka: 1–2 days · Nationwide: 2–5 days</p></div></li>
                    <li class="flex items-start gap-3"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-sm bg-secondary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shield-check h-4 w-4" aria-hidden="true"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg></span><div class="min-w-0"><p class="font-medium text-foreground">{{ $product->warranty ?: 'Standard warranty' }}</p><p class="text-xs text-muted-foreground">On every product</p></div></li>
                    <li class="flex items-start gap-3"><span class="grid h-9 w-9 shrink-0 place-items-center rounded-sm bg-secondary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-refresh-ccw h-4 w-4" aria-hidden="true"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"></path><path d="M16 16h5v5"></path></svg></span><div class="min-w-0"><p class="font-medium text-foreground">7-day easy replacement</p><p class="text-xs text-muted-foreground">Unused, original condition</p></div></li>
                </ul>
            @endif
        </div>
    </div>

    <script>
        (function () {
            var MIN_QTY = 1;
            var MAX_QTY = 10;
            var qty = MIN_QTY;
            var qtyValue = document.getElementById('qty-value');
            var decreaseBtn = document.getElementById('qty-decrease');
            var increaseBtn = document.getElementById('qty-increase');
            var buyNowLink = document.getElementById('buy-now-link');

            if (!qtyValue || !decreaseBtn || !increaseBtn) return;

            function render() {
                qtyValue.textContent = qty;
                decreaseBtn.disabled = qty <= MIN_QTY;
                decreaseBtn.classList.toggle('opacity-50', qty <= MIN_QTY);
                increaseBtn.disabled = qty >= MAX_QTY;
                increaseBtn.classList.toggle('opacity-50', qty >= MAX_QTY);
                if (buyNowLink) {
                    buyNowLink.href = buyNowLink.dataset.baseHref + '&qty=' + qty;
                }
            }

            decreaseBtn.addEventListener('click', function () {
                if (qty > MIN_QTY) { qty -= 1; render(); }
            });
            increaseBtn.addEventListener('click', function () {
                if (qty < MAX_QTY) { qty += 1; render(); }
            });

            render();
        })();
    </script>

    <nav aria-label="Product sections" class="mt-14 flex flex-wrap items-center gap-2 border-b border-border pb-4">
        @if($product->specs->count())
            <a href="#specifications" class="rounded-full px-2.5 py-1 text-xs font-medium transition-colors hover:bg-secondary text-foreground/80 border border-border">Specifications</a>
        @endif
        <a href="#description" class="rounded-full px-2.5 py-1 text-xs font-medium transition-colors hover:bg-secondary text-foreground/80 border border-border">Description</a>
    </nav>

    <div class="product-flow">
    <div class="mt-10 spec-recently-viewed-grid">
        <div class="specs-block">
            @if($product->specs->count())
                <div id="specifications" style="scroll-margin-top: 120px">
                    <h2 class="text-2xl font-semibold tracking-tight">Specifications</h2>
                    <dl class="spec-table mt-6 rounded-md border border-border">
                        @foreach($product->specs as $spec)
                            <div class="spec-row px-5 py-3 text-sm">
                                <dt class="text-muted-foreground">{{ $spec->label }}</dt>
                                <dd class="font-medium text-foreground">{{ $spec->value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif
        </div>

        <aside id="recentlyViewedSidebar" style="display:none">
            <h2 class="text-lg font-semibold tracking-tight">Recently Viewed</h2>
            <div id="recentlyViewedList" class="mt-4 space-y-3"></div>
        </aside>
    </div>

    <script>
        (function () {
            var STORAGE_KEY = 'kg_recently_viewed';
            var MAX_ITEMS = 8;
            var SHOW_LIMIT = 5;

            var currentProduct = {
                slug: @json($product->slug),
                name: @json($product->name),
                image: @json($product->primaryImage()),
                price: {{ (int) $product->price }},
                compareAtPrice: {{ $product->compare_at_price ? (int) $product->compare_at_price : 'null' }},
                url: @json(route('product', $product->slug))
            };

            function loadList() {
                try {
                    var raw = localStorage.getItem(STORAGE_KEY);
                    var list = raw ? JSON.parse(raw) : [];
                    return Array.isArray(list) ? list : [];
                } catch (e) {
                    return [];
                }
            }

            function formatTaka(amount) {
                return '৳ ' + Number(amount).toLocaleString('en-US');
            }

            function buildCard(p) {
                var card = document.createElement('a');
                card.href = p.url;
                card.className = 'recently-viewed-card flex gap-3 rounded-sm border border-border bg-card p-3';

                var imgWrap = document.createElement('div');
                imgWrap.className = 'h-16 w-16 shrink-0 overflow-hidden rounded-sm bg-surface';
                var img = document.createElement('img');
                img.src = p.image;
                img.alt = p.name;
                img.loading = 'lazy';
                img.className = 'h-full w-full object-cover';
                imgWrap.appendChild(img);

                var info = document.createElement('div');
                info.className = 'min-w-0';

                var name = document.createElement('p');
                name.className = 'text-xs font-medium text-foreground line-clamp-2';
                name.textContent = p.name;
                info.appendChild(name);

                var priceRow = document.createElement('div');
                priceRow.className = 'mt-1 flex items-center gap-1.5';
                var priceEl = document.createElement('span');
                priceEl.className = 'text-xs font-semibold text-foreground';
                priceEl.textContent = formatTaka(p.price);
                priceRow.appendChild(priceEl);

                var hasDiscount = p.compareAtPrice && p.compareAtPrice > p.price;
                if (hasDiscount) {
                    var compareEl = document.createElement('span');
                    compareEl.className = 'text-[11px] text-muted-foreground line-through';
                    compareEl.textContent = formatTaka(p.compareAtPrice);
                    priceRow.appendChild(compareEl);
                }
                info.appendChild(priceRow);

                if (hasDiscount) {
                    var offBadge = document.createElement('span');
                    offBadge.className = 'mt-1 inline-block rounded-full bg-success/10 px-2 py-0.5 text-[10px] font-medium text-success';
                    offBadge.textContent = formatTaka(p.compareAtPrice - p.price) + ' OFF';
                    info.appendChild(offBadge);
                }

                card.appendChild(imgWrap);
                card.appendChild(info);
                return card;
            }

            var list = loadList().filter(function (p) { return p && p.slug !== currentProduct.slug; });
            list.unshift(currentProduct);
            if (list.length > MAX_ITEMS) list = list.slice(0, MAX_ITEMS);
            try { localStorage.setItem(STORAGE_KEY, JSON.stringify(list)); } catch (e) {}

            var toShow = list.filter(function (p) { return p.slug !== currentProduct.slug; }).slice(0, SHOW_LIMIT);
            if (toShow.length === 0) return;

            var listEl = document.getElementById('recentlyViewedList');
            var sidebarEl = document.getElementById('recentlyViewedSidebar');
            if (!listEl || !sidebarEl) return;

            toShow.forEach(function (p) { listEl.appendChild(buildCard(p)); });
            sidebarEl.style.display = '';
        })();
    </script>

    <section id="description" class="mt-20" style="scroll-margin-top: 120px">
        <h2 class="text-2xl font-semibold tracking-tight">Description</h2>
        <div class="mt-6 max-w-3xl space-y-4 text-sm leading-relaxed text-muted-foreground sm:text-base">
            <div class="product-rich-text">{!! $product->description !!}</div>
        </div>
    </section>

    <section id="relatedProducts" class="mt-24">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="max-w-2xl">
                <p class="text-xs font-medium uppercase tracking-[0.18em] text-muted-foreground">You may also like</p>
                <h2 class="mt-2 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">Related Products</h2>
            </div>
            <a href="/shop" class="group inline-flex items-center gap-1.5 text-sm font-medium text-foreground">Browse all<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right h-4 w-4 transition-transform group-hover:translate-x-0.5" aria-hidden="true"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg></a>
        </div>
        @if($relatedProducts->count())
            <div class="mt-10 grid grid-cols-2 gap-8 lg:grid-cols-4">
                @foreach($relatedProducts as $relatedProduct)
                    @include('partials.product-card', ['product' => $relatedProduct])
                @endforeach
            </div>
        @endif
    </section>
    </div>
</div>
<!--/$--></main>@include('partials.footer', ['hideOutlets' => true])</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInputs = document.querySelectorAll('input[type="search"]');

    searchInputs.forEach(input => {
        const parentContainer = input.closest('label') || input.parentElement;
        if (!parentContainer) return;

        // Make parent container relative for positioning search dropdown
        parentContainer.classList.add('relative');

        // Create results overlay element
        const resultsBox = document.createElement('div');
        resultsBox.className = 'absolute left-0 right-0 top-full mt-2 bg-background rounded-xl shadow-lg border border-border z-50 overflow-hidden divide-y divide-border hidden text-left max-h-96 overflow-y-auto';
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
<nav aria-label="Mobile footer navigation" class="fixed bottom-0 left-0 right-0 z-40 border-t border-border bg-background/95 backdrop-blur sm:hidden" data-tsd-source="/src/components/site/MobileFooterNav.tsx:14:5"><ul class="grid grid-cols-4" data-tsd-source="/src/components/site/MobileFooterNav.tsx:18:7"><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground" href="/"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>Home</a></li><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/shop" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-foreground active" aria-current="page"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-grid h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><rect width="7" height="7" x="3" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="14" rx="1"></rect><rect width="7" height="7" x="3" y="14" rx="1"></rect></svg>Explore</a></li><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/customer-spotlight" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>Spotlight</a></li><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/customer-feedback" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square-heart h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"></path><path d="M7.5 9.5c0 .687.265 1.383.697 1.844l3.009 3.264a1.14 1.14 0 0 0 .407.314 1 1 0 0 0 .783-.004 1.14 1.14 0 0 0 .398-.31l3.008-3.264A2.77 2.77 0 0 0 16.5 9.5 2.5 2.5 0 0 0 12 8a2.5 2.5 0 0 0-4.5 1.5"></path></svg>Feedback</a></li></ul></nav>
<div class="fixed bottom-20 right-4 z-50 flex flex-col items-center sm:bottom-6" data-tsd-source="/src/components/site/ChatWidget.tsx:214:7"><button class="flex flex-col items-center transition-transform hover:scale-105 active:scale-95" aria-label="Open live chat" title="Need help?" data-tsd-source="/src/components/site/ChatWidget.tsx:215:9"><img src="/assets/support-agent-BWJyOWv2.png" alt="Live chat support agent" width="512" height="512" loading="lazy" class="agent-float h-20 w-20 select-none object-contain drop-shadow-lg sm:h-24 sm:w-24" data-tsd-source="/src/components/site/ChatWidget.tsx:221:11"/><span class="-mt-1 rounded-full bg-foreground px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-background shadow-sm" data-tsd-source="/src/components/site/ChatWidget.tsx:229:11">Live Chat</span></button></div>
@include('partials.mobile-drawer')
</body></html>
