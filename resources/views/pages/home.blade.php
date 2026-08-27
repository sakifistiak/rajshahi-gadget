{{-- Dynamic Logo Swap + Shop-style Buttons from Site Settings --}}
<script>
    window.__SITE_LOGO_LIGHT = "{{ $siteLogo ?? '/media/b3ca13-kg-lockup-v2.png' }}";
    window.__SITE_LOGO_DARK  = "{{ $siteLogoDark ?? '/media/b3ca13-kg-lockup-v2.png' }}";
    window.__SITE_NAME       = "{{ $siteName ?? 'Khan Gadget' }}";

    document.addEventListener('DOMContentLoaded', function () {

        /* ── 1. Logo swap (dynamic + theme-aware) ── */
        function swapLogos() {
            var isDark = document.documentElement.classList.contains('dark');
            var logoUrl = isDark ? window.__SITE_LOGO_DARK : window.__SITE_LOGO_LIGHT;
            document.querySelectorAll('img[alt*="Khan Gadget"], img[alt*="' + window.__SITE_NAME + '"]').forEach(function(img) {
                img.src = logoUrl;
            });
        }
        swapLogos();
        var logoObserver = new MutationObserver(swapLogos);
        logoObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

        /* ── 2. Apply /shop-style button classes to ALL product cards ── */
        // Target static (React-generated) product card button rows
        document.querySelectorAll('[data-tsd-source*="ProductCard.tsx:88"]').forEach(function(row) {
            // "Buy Now" button (flex-1)
            var buyNowBtn = row.querySelector('button.flex-1, a.flex-1');
            if (buyNowBtn) {
                // Remove old square/sm styles, apply rounded-full dark style
                buyNowBtn.classList.remove('rounded-sm', 'rounded', 'bg-secondary', 'hover:bg-secondary\\/80');
                buyNowBtn.classList.add('rounded-full', 'font-bold', 'shadow-sm', 'active:scale-[0.98]');
                buyNowBtn.style.backgroundColor = '#24272c';
                buyNowBtn.style.color = '#ffffff';
            }
            // Cart icon button (w-9 h-9 square)
            var cartBtn = row.querySelector('button[aria-label="Add to cart"], a[aria-label="Add to Cart"]');
            if (cartBtn) {
                cartBtn.classList.remove('rounded-sm', 'rounded');
                cartBtn.classList.add(
                    'rounded-full',
                    'bg-slate-100', 'dark:bg-slate-800',
                    'text-slate-800', 'dark:text-slate-100',
                    'border', 'border-slate-200\\/80', 'dark:border-slate-700',
                    'hover:bg-slate-200', 'dark:hover:bg-slate-700',
                    'flex', 'items-center', 'justify-center', 'shrink-0',
                    'transition-colors', 'shadow-sm'
                );
                cartBtn.style.removeProperty('background');
                cartBtn.style.removeProperty('border-color');
            }
        });

    });
</script>


@php
use App\Support\SectionTitleStyle;

if (!function_exists('renderSectionTitle')) {
    function renderSectionTitle($fullTitle, $highlightWord, $style = null) {
        if (empty($fullTitle)) return '';

        $scoped  = SectionTitleStyle::sanitizeFull($style ?? []);
        $baseCss = SectionTitleStyle::toInlineCss($scoped['base']);
        $escapedFull = e($fullTitle);

        if (empty($highlightWord) || stripos($fullTitle, $highlightWord) === false) {
            return $baseCss ? '<span style="' . $baseCss . '">' . $escapedFull . '</span>' : $escapedFull;
        }

        $escapedHl = e($highlightWord);
        $quotedHl  = preg_quote($escapedHl, '/');
        $hlCss     = SectionTitleStyle::toInlineCss($scoped['highlight']);
        $inner     = preg_replace('/(' . $quotedHl . ')/i', '<span style="' . $hlCss . '">$1</span>', $escapedFull);

        return $baseCss ? '<span style="' . $baseCss . '">' . $inner . '</span>' : $inner;
    }
}

if (!function_exists('sectionTitleSizeClass')) {
    // Extra class for the <h2> wrapping a renderSectionTitle() call — fold
    // this into that tag's existing class list (never a separate class="").
    function sectionTitleSizeClass($style) {
        $scoped = SectionTitleStyle::sanitizeFull($style ?? []);
        return SectionTitleStyle::fontSizeClass($scoped['font_size']);
    }
}

if (!function_exists('sectionTitleSizeStyle')) {
    // style="..." attribute for that same <h2>, carrying the admin's custom
    // mobile/desktop font size as CSS custom properties (or '' if unset).
    function sectionTitleSizeStyle($style) {
        $scoped = SectionTitleStyle::sanitizeFull($style ?? []);
        return SectionTitleStyle::fontSizeStyleAttr($scoped['font_size']);
    }
}
@endphp
<!DOCTYPE html><html lang="en" data-tsd-source="/src/routes/__root.tsx:133:5"><head data-tsd-source="/src/routes/__root.tsx:134:7"><meta charSet="utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/><link rel="preload" as="image" href="/media/b3ca13-kg-lockup-v2.png"/><link rel="preload" as="image" href="/media/6767eb-hero-s26-ultra.png"/><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" data-precedence="default"/><link rel="stylesheet" href="{{ SectionTitleStyle::googleFontsUrl() }}"/><title>{{ $siteName ?? 'Khan Gadget' }} - {{ $siteSlogan ?? 'Brand NEW Intact BOX, Without BOX & Pre-Owned' }}</title><meta name="author" content="{{ $siteName ?? 'Khan Gadget' }}"/><meta property="og:type" content="website"/><meta name="twitter:card" content="summary_large_image"/><meta name="twitter:site" content="{{ $siteName ?? 'Khan Gadget' }}"/><meta name="twitter:title" content="{{ $siteName ?? 'Khan Gadget' }} - {{ $siteSlogan ?? 'Brand NEW Intact BOX, Without BOX & Pre-Owned' }}"/><meta name="twitter:description" content="{{ $siteDescription ?? 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.' }}"/><meta property="og:image" content="{{ url($siteShareImage ?? '/media/b3ca13-kg-lockup-v2.png') }}"/><meta name="twitter:image" content="{{ url($siteShareImage ?? '/media/b3ca13-kg-lockup-v2.png') }}"/><meta name="description" content="{{ $siteDescription ?? 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.' }}"/><meta property="og:title" content="{{ $siteName ?? 'Khan Gadget' }} - {{ $siteSlogan ?? 'Brand NEW Intact BOX, Without BOX & Pre-Owned' }}"/><meta property="og:description" content="{{ $siteDescription ?? 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.' }}"/><link rel="icon" href="{{ $siteFavicon ?? '/favicon.png' }}" type="image/png"/><link rel="preconnect" href="https://fonts.googleapis.com"/><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous"/><link rel="stylesheet" href="/assets/styles-CC_Lznyw.css"><script src="https://unpkg.com/lucide@latest"></script><script src="/assets/theme.js"></script></head><body data-tsd-source="/src/routes/__root.tsx:138:7"><div class="flex min-h-screen flex-col bg-background" data-tsd-source="/src/routes/__root.tsx:154:13"><header class="sticky top-0 z-40 border-b border-border bg-background/95 backdrop-blur" data-tsd-source="/src/components/site/Navbar.tsx:31:5"><div class="container-page flex h-20 items-center gap-3" data-tsd-source="/src/components/site/Navbar.tsx:33:7"><a aria-label="Khan Gadget home" data-tsd-source="/src/components/site/Navbar.tsx:34:9" class="flex shrink-0 items-center active" href="/" data-status="active" aria-current="page"><img src="/media/b3ca13-kg-lockup-v2.png" alt="Khan Gadget - Eternal Tech Companion" class="h-9 w-auto object-contain sm:h-12 lg:h-14" data-tsd-source="/src/components/site/Navbar.tsx:35:11"/></a><div class="hidden flex-1 sm:block" data-tsd-source="/src/components/site/Navbar.tsx:42:9"><label class="flex items-center gap-3 rounded-full border border-border bg-secondary/60 px-5 py-3 text-foreground transition-colors focus-within:border-foreground/30 focus-within:bg-secondary hover:bg-secondary" data-tsd-source="/src/components/site/Navbar.tsx:43:11"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4 text-muted-foreground" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:44:13"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input type="search" placeholder="Search products, brands, categories…" class="w-full bg-transparent text-sm placeholder:text-muted-foreground focus:outline-none" data-tsd-source="/src/components/site/Navbar.tsx:45:13"/></label></div><div class="flex-1 sm:hidden" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:53:9"></div><div class="flex items-center gap-1.5 sm:gap-2" data-tsd-source="/src/components/site/Navbar.tsx:57:9"><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full" aria-label="Switch to dark mode" data-tsd-source="/src/components/site/ThemeToggle.tsx:8:5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/ThemeToggle.tsx:15:60"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"></path></svg></button><a href="/compare" aria-label="Compare products" title="Compare products" class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-git-compare h-5 w-5" aria-hidden="true"><circle cx="18" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><path d="M13 6h3a2 2 0 0 1 2 2v7"></path><path d="M11 18H8a2 2 0 0 1-2-2V9"></path></svg></a><a href="/cart" class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:61:15"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg></a><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full lg:hidden" aria-label="Menu" data-tsd-source="/src/components/site/Navbar.tsx:69:11"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:76:49"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg></button></div></div><div class="container-page pb-3 sm:hidden" data-tsd-source="/src/components/site/Navbar.tsx:82:7"><label class="flex items-center gap-3 rounded-full border border-border bg-secondary/60 px-4 py-2.5 text-foreground focus-within:border-foreground/30 focus-within:bg-secondary" data-tsd-source="/src/components/site/Navbar.tsx:83:9"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4 text-muted-foreground" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:84:11"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input type="search" placeholder="Search products, brands, categories…" class="w-full bg-transparent text-sm placeholder:text-muted-foreground focus:outline-none" data-tsd-source="/src/components/site/Navbar.tsx:85:11"/></label></div><div class="hidden border-t border-border lg:block" data-tsd-source="/src/components/site/Navbar.tsx:96:7"><div class="container-page flex h-11 items-center gap-6 text-xs" data-tsd-source="/src/components/site/Navbar.tsx:97:9"><nav class="flex items-center gap-1" data-tsd-source="/src/components/site/Navbar.tsx:98:11"><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">ALL PRODUCTS</a><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop?condition=intact" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BRAND NEW INTACT BOX</a><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop?condition=without-box" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BRAND NEW WITHOUT BOX</a><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop?condition=pre-owned" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">PRE-OWNED</a></nav><div class="flex-1" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:115:11"></div><nav class="flex items-center gap-1" data-tsd-source="/src/components/site/Navbar.tsx:116:11"><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/blog" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BLOG</a><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/customer-spotlight" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">CUSTOMER SPOTLIGHT</a><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/philanthropic-work" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">PHILANTHROPIC WORK</a><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/customer-feedback" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">CUSTOMER FEEDBACK</a></nav></div></div></header><main class="flex-1 pb-14 sm:pb-0" data-tsd-source="/src/routes/__root.tsx:156:15"><!--$--><style>{!! SectionTitleStyle::fontSizeCssRule() !!}</style>@if($homeHeroActive ?? true)
<style>
    /* Side promo banners must always render at a fixed size regardless of the
       admin-uploaded image's own dimensions — otherwise a taller/shorter
       source image visibly grows or shrinks the card (and its neighbour, via
       row alignment), instead of being cropped to fit like the hero slider. */
    .promo-banner-item { aspect-ratio: 16 / 9; }
    @media (min-width: 1024px) {
        .promo-banner-grid { display: flex; flex-direction: column; height: 100%; }
        .promo-banner-item { aspect-ratio: auto; flex: 1 1 0; min-height: 0; }
    }
</style>
<section aria-label="Featured" class="container-page pt-6">
    <div class="grid gap-4 lg:grid-cols-[minmax(0,2fr)_minmax(0,1fr)]">
        <!-- Hero Main Slider -->
        <div class="relative overflow-hidden bg-surface rounded-md shadow-sm" id="hero-slider-root">
            <div class="relative aspect-[15/8] w-full" id="hero-slides-container">
                @foreach($heroSliders as $index => $slider)
                    <a href="{{ $slider->cta_link ?? '/shop' }}" 
                       class="hero-slide-item absolute inset-0 transition-opacity duration-700 ease-in-out {{ $index === 0 ? 'opacity-100 z-10' : 'opacity-0 pointer-events-none z-0' }}"
                       data-slide-index="{{ $index }}">
                        <img src="{{ $slider->image_path }}" alt="{{ $slider->title ?? 'Hero Banner' }}" loading="{{ $index === 0 ? 'eager' : 'lazy' }}" class="h-full w-full object-cover" />
                    </a>
                @endforeach
            </div>

            <!-- Previous Button -->
            <button type="button" aria-label="Previous slide" id="hero-btn-prev" class="absolute left-3 top-1/2 z-20 grid h-9 w-9 -translate-y-1/2 cursor-pointer place-items-center rounded-full bg-background/80 text-foreground shadow-md backdrop-blur transition-all hover:bg-background hover:scale-110 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 pointer-events-none"><path d="m15 18-6-6 6-6"></path></svg>
            </button>

            <!-- Next Button -->
            <button type="button" aria-label="Next slide" id="hero-btn-next" class="absolute right-3 top-1/2 z-20 grid h-9 w-9 -translate-y-1/2 cursor-pointer place-items-center rounded-full bg-background/80 text-foreground shadow-md backdrop-blur transition-all hover:bg-background hover:scale-110 active:scale-95">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5 pointer-events-none"><path d="m9 18 6-6-6-6"></path></svg>
            </button>

            <!-- Indicators -->
            <div class="absolute bottom-4 left-1/2 z-20 flex -translate-x-1/2 gap-2" id="hero-dots-container">
                @foreach($heroSliders as $index => $slider)
                    <button type="button" 
                            aria-label="Go to slide {{ $index + 1 }}" 
                            class="hero-dot-item h-2 rounded-full cursor-pointer transition-all duration-300 {{ $index === 0 ? 'w-8 bg-foreground' : 'w-3 bg-foreground/40 hover:bg-foreground/70' }}"
                            data-dot-index="{{ $index }}">
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Side Promo Banners -->
        <div class="promo-banner-grid grid {{ $promoBanners->count() === 1 ? 'grid-cols-1' : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-1' }} gap-3 sm:gap-4 h-full">
            @foreach($promoBanners as $promo)
                <a href="{{ $promo->link ?? '/shop' }}" class="promo-banner-item group relative block overflow-hidden rounded-md shadow-sm">
                    <div class="w-full h-full relative overflow-hidden bg-surface">
                        <img src="{{ $promo->image_path }}" alt="Promo Banner" loading="lazy" class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105" />
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Vanilla JS for Instant Single-Click Slider -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const slides = document.querySelectorAll('.hero-slide-item');
    const dots = document.querySelectorAll('.hero-dot-item');
    const prevBtn = document.getElementById('hero-btn-prev');
    const nextBtn = document.getElementById('hero-btn-next');
    
    if (!slides.length) return;
    
    let currentIndex = 0;
    let autoSlideTimer = null;

    function showSlide(index) {
        if (index < 0) index = slides.length - 1;
        if (index >= slides.length) index = 0;
        currentIndex = index;

        slides.forEach((slide, i) => {
            if (i === currentIndex) {
                slide.classList.remove('opacity-0', 'pointer-events-none', 'z-0');
                slide.classList.add('opacity-100', 'z-10');
            } else {
                slide.classList.remove('opacity-100', 'z-10');
                slide.classList.add('opacity-0', 'pointer-events-none', 'z-0');
            }
        });

        dots.forEach((dot, i) => {
            if (i === currentIndex) {
                dot.classList.remove('w-3', 'bg-foreground/40');
                dot.classList.add('w-8', 'bg-foreground');
            } else {
                dot.classList.remove('w-8', 'bg-foreground');
                dot.classList.add('w-3', 'bg-foreground/40');
            }
        });
    }

    function startAutoSlide() {
        stopAutoSlide();
        autoSlideTimer = setInterval(() => {
            showSlide(currentIndex + 1);
        }, 5000);
    }

    function stopAutoSlide() {
        if (autoSlideTimer) clearInterval(autoSlideTimer);
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            showSlide(currentIndex - 1);
            startAutoSlide();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            showSlide(currentIndex + 1);
            startAutoSlide();
        });
    }

    dots.forEach((dot) => {
        dot.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const idx = parseInt(this.getAttribute('data-dot-index') || '0', 10);
            showSlide(idx);
            startAutoSlide();
        });
    });

    if (slides.length > 1) {
        startAutoSlide();
    }
});
</script>

@if(($homeTickerActive ?? true) && !empty($homeTickerItems))
<div class="mt-8 sm:mt-12">
    <div class="container-page">
        <div class="rounded-md border border-border bg-secondary/60 text-foreground/80">
            <div class="flex items-center gap-3 px-5 py-2 text-xs">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-megaphone h-3.5 w-3.5 shrink-0 text-muted-foreground" aria-hidden="true"><path d="M11 6a13 13 0 0 0 8.4-2.8A1 1 0 0 1 21 4v12a1 1 0 0 1-1.6.8A13 13 0 0 0 11 14H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2z"></path><path d="M6 14a12 12 0 0 0 2.4 7.2 2 2 0 0 0 3.2-2.4A8 8 0 0 1 10 14"></path><path d="M8 6v8"></path></svg>
                <div class="relative flex-1 overflow-hidden" style="height:1.25rem;">
                    <div id="home-ticker-track" style="position:relative; height:100%;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    (function () {
        var items = @json($homeTickerItems);
        var effect = @json($homeTickerEffect);
        var speed = @json($homeTickerSpeed);
        var track = document.getElementById('home-ticker-track');
        if (!track || !items.length) return;

        var index = 0;

        function showNextFade() {
            var el = document.createElement('span');
            el.textContent = items[index % items.length];
            index++;

            el.style.position = 'absolute';
            el.style.left = '0';
            el.style.top = '0';
            el.style.whiteSpace = 'nowrap';
            el.style.transform = 'translateX(100%)';
            el.style.opacity = '0';
            el.style.transition = 'transform 0.7s ease, opacity 0.7s ease';
            track.appendChild(el);

            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    el.style.transform = 'translateX(0)';
                    el.style.opacity = '1';
                });
            });

            setTimeout(function () {
                el.style.transform = 'translateX(-100%)';
                el.style.opacity = '0';
                setTimeout(function () {
                    el.remove();
                    showNextFade();
                }, 700);
            }, speed * 1000);
        }

        function showNextScroll() {
            var el = document.createElement('span');
            el.textContent = items[index % items.length];
            index++;

            el.style.position = 'absolute';
            el.style.left = '0';
            el.style.top = '0';
            el.style.whiteSpace = 'nowrap';
            el.style.transform = 'translateX(100%)';
            el.style.transition = 'transform ' + speed + 's linear';
            track.appendChild(el);

            requestAnimationFrame(function () {
                requestAnimationFrame(function () {
                    el.style.transform = 'translateX(-100%)';
                });
            });

            el.addEventListener('transitionend', function handler() {
                el.removeEventListener('transitionend', handler);
                el.remove();
                showNextScroll();
            });
        }

        if (effect === 'scroll') {
            showNextScroll();
        } else {
            showNextFade();
        }
    })();
</script>
@endif

@if($homeTrustbarActive ?? true)
<style>
    /* Mobile: the static CSS bundle has no `grid-cols-5`, so instead of the
       horizontal snap-scroll carousel (which pushed the last badge off-screen),
       lay the badges out as an equal-width flex row that always fits the
       viewport — however many badges the admin configures. Desktop (>=640px)
       keeps the existing sm:grid / lg:grid-cols-5 layout untouched. */
    @media (max-width: 639.98px) {
        #home-trustbar {
            display: flex;
            gap: 6px;
            margin: 0;
            padding: 0;
            overflow: visible;
            scroll-snap-type: none;
        }
        #home-trustbar > li {
            flex: 1 1 0;
            width: auto;
            min-width: 0;
        }
        #home-trustbar .trustbar-label {
            font-size: 10px;
        }
    }
</style>
<section aria-label="Store benefits" class="container-page pt-6"><div class="rounded-md border border-border bg-surface px-3 py-4 sm:px-8 sm:py-5"><ul id="home-trustbar" class="-mx-3 flex snap-x snap-mandatory gap-2 overflow-x-auto px-3 pb-1 [scrollbar-width:none] [&amp;::-webkit-scrollbar]:hidden sm:mx-0 sm:grid sm:snap-none sm:grid-cols-3 sm:gap-4 sm:overflow-visible sm:px-0 sm:pb-0 lg:grid-cols-5">@foreach($trustbarItems ?? [] as $tbItem)<li class="flex w-[104px] shrink-0 snap-start flex-col items-center gap-2 text-center sm:w-auto sm:flex-row sm:justify-center sm:gap-3 sm:text-left"><span class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-accent/10 text-accent sm:h-11 sm:w-11">@if(($tbItem['icon_type'] ?? 'lucide') === 'image' && !empty($tbItem['icon_image']))<img src="{{ $tbItem['icon_image'] }}" alt="" class="h-5 w-5 object-contain" />@else<i data-lucide="{{ $tbItem['icon_lucide'] ?? 'check' }}" class="h-5 w-5"></i>@endif</span><span class="trustbar-label text-[11px] font-semibold leading-tight text-foreground sm:text-sm">{{ $tbItem['label'] ?? '' }}</span></li>@endforeach</ul></div></section>@endif
@if(($homeNewArrivalPosition ?? 'below_flash') === 'above_flash')
    @include('partials.home-new-arrival-section')
@endif
@if(($homeFlashActive ?? true) && $activeFlashSale && $flashSaleItems->count())
<section class="container-page py-12">
    <style>
        .flash-card { display: flex; flex-direction: column; height: 100%; border: 1px solid var(--color-border, #e5e7eb); background: var(--color-secondary, #f4f4f5); border-radius: 10px; overflow: hidden; }
        .flash-card-body { display: flex; flex: 1; flex-direction: column; padding: 10px 12px 12px; }
        .flash-stock-bar { margin-top: 8px; height: 6px; border-radius: 9999px; background: var(--color-border, #e5e7eb); overflow: hidden; }
        .flash-stock-bar > span { display: block; height: 100%; background: var(--color-accent, #ea580c); }
    </style>
    <div class="rounded-md border border-border bg-gradient-to-br from-accent/10 via-surface to-background p-4 sm:p-10">
        <div class="flex flex-wrap items-end justify-between gap-6">
            <div>
                @if($homeFlashBadgeActive ?? true)
                    <span class="inline-flex items-center gap-2 rounded-full bg-accent/15 px-3 py-1 text-xs font-medium text-accent">
                        @if($homeFlashBadgeIcon ?? '')
                            <span aria-hidden="true">{{ $homeFlashBadgeIcon }}</span>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-flame h-3.5 w-3.5" aria-hidden="true"><path d="M12 3q1 4 4 6.5t3 5.5a1 1 0 0 1-14 0 5 5 0 0 1 1-3 1 1 0 0 0 5 0c0-2-1.5-3-1.5-5q0-2 2.5-4"></path></svg>
                        @endif
                        {{ $homeFlashBadgeText ?? 'Flash Deals' }}
                    </span>
                @endif
                <h2 class="mt-3 text-2xl font-semibold tracking-tight sm:text-3xl {{ sectionTitleSizeClass($homeFlashTitleStyle ?? null) }}"{!! sectionTitleSizeStyle($homeFlashTitleStyle ?? null) !!}>{!! renderSectionTitle($homeFlashTitle ?? "Today's best prices in Bangladesh", $homeFlashHighlight ?? "best prices", $homeFlashTitleStyle ?? null) !!}</h2>
                @if(($homeFlashSubtitleActive ?? true) && ($homeFlashSubtitleText ?? ''))
                    <p class="mt-2 text-sm text-muted-foreground">{{ $homeFlashSubtitleText }}</p>
                @endif
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs text-muted-foreground">Ends in</span>
                <div class="flex items-center gap-1.5" id="flash-countdown" data-ends-at="{{ $activeFlashSale->ends_at->toIso8601String() }}">
                    <div class="flex flex-col items-center"><span class="grid h-10 w-10 place-items-center rounded-lg bg-foreground text-sm font-semibold tabular-nums text-background sm:h-11 sm:w-11 sm:text-base" data-unit="hours">00</span><span class="mt-1 text-[10px] uppercase tracking-wider text-muted-foreground">Hrs</span></div>
                    <span class="pb-4 text-foreground/40">:</span>
                    <div class="flex flex-col items-center"><span class="grid h-10 w-10 place-items-center rounded-lg bg-foreground text-sm font-semibold tabular-nums text-background sm:h-11 sm:w-11 sm:text-base" data-unit="minutes">00</span><span class="mt-1 text-[10px] uppercase tracking-wider text-muted-foreground">Min</span></div>
                    <span class="pb-4 text-foreground/40">:</span>
                    <div class="flex flex-col items-center"><span class="grid h-10 w-10 place-items-center rounded-lg bg-foreground text-sm font-semibold tabular-nums text-background sm:h-11 sm:w-11 sm:text-base" data-unit="seconds">00</span><span class="mt-1 text-[10px] uppercase tracking-wider text-muted-foreground">Sec</span></div>
                </div>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-2 gap-3 sm:gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($flashSaleItems as $item)
                @php
                    $product = $item->product;
                    $salePrice = $item->priceFor($product->price);
                    $percentOff = $product->price > 0 ? round((($product->price - $salePrice) / $product->price) * 100) : 0;
                    $remaining = $item->remainingStock();
                @endphp
                <article class="flash-card">
                    <div class="relative overflow-hidden bg-surface">
                        <a href="/product/{{ $product->slug }}" class="block">
                            <div class="aspect-square w-full overflow-hidden">
                                <img src="{{ $product->primaryImage() }}" alt="{{ $product->name }}" loading="lazy" width="900" height="900" class="h-full w-full object-cover transition-transform duration-700 ease-out" />
                            </div>
                        </a>
                        @if($percentOff > 0)
                            <span class="absolute rounded font-extrabold uppercase pointer-events-none z-10 bg-accent text-accent-foreground" style="left: 6px !important; top: 6px !important; font-size: 8px !important; line-height: 10px !important; padding: 1.5px 4px !important;">{{ $percentOff }}% OFF</span>
                        @endif
                    </div>
                    <div class="flash-card-body">
                        <h3 class="line-clamp-2 text-xs font-semibold leading-snug text-foreground" style="margin:0">
                            <a href="/product/{{ $product->slug }}" style="text-decoration:none">{{ $product->name }}</a>
                        </h3>
                        <div class="flex flex-wrap items-baseline" style="margin-top:12px; gap:6px">
                            <span class="text-sm font-semibold text-foreground">৳ {{ number_format($salePrice) }}</span>
                            <span class="text-xs text-muted-foreground line-through">৳ {{ number_format($product->price) }}</span>
                        </div>
                        @if($item->stock_limit)
                            <div>
                                <p class="mt-2 text-[10px] font-medium text-muted-foreground">{{ $remaining }} left · {{ $item->sold_count }} sold</p>
                                <div class="flash-stock-bar"><span style="width: {{ min(100, round(($item->sold_count / max(1, $item->stock_limit)) * 100)) }}%"></span></div>
                            </div>
                        @endif
                        <div class="flex items-center" style="margin-top:12px">
                            <a href="/checkout?product={{ $product->slug }}" class="btn-buy-now flex-1 inline-flex items-center justify-center rounded-full font-bold text-sm transition-all shadow-sm" style="background-color: #24272c !important; color: #ffffff !important; height:36px; padding:0 16px;">
                                Buy Now
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var el = document.getElementById('flash-countdown');
    if (!el) return;
    var endsAt = new Date(el.dataset.endsAt).getTime();
    var hoursEl = el.querySelector('[data-unit="hours"]');
    var minutesEl = el.querySelector('[data-unit="minutes"]');
    var secondsEl = el.querySelector('[data-unit="seconds"]');
    var pad = function (n) { return String(n).padStart(2, '0'); };

    function tick() {
        var diff = endsAt - Date.now();
        if (diff <= 0) {
            hoursEl.textContent = minutesEl.textContent = secondsEl.textContent = '00';
            var section = el.closest('section');
            if (section) section.style.display = 'none';
            clearInterval(timer);
            return;
        }
        var totalSeconds = Math.floor(diff / 1000);
        hoursEl.textContent = pad(Math.floor(totalSeconds / 3600));
        minutesEl.textContent = pad(Math.floor((totalSeconds % 3600) / 60));
        secondsEl.textContent = pad(totalSeconds % 60);
    }

    tick();
    var timer = setInterval(tick, 1000);
});
</script>
@endif
@if(($homeNewArrivalPosition ?? 'below_flash') === 'below_flash')
    @include('partials.home-new-arrival-section')
@endif

@if(isset($productSections) && count($productSections) > 0)
    @foreach ($productSections as $index => $sec)
        <section class="container-page py-10 {{ $index > 0 ? 'border-t border-border/50' : '' }}">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl">
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-foreground sm:text-4xl {{ sectionTitleSizeClass($sec['style'] ?? null) }}"{!! sectionTitleSizeStyle($sec['style'] ?? null) !!}>
                        {!! renderSectionTitle($sec['title'], $sec['highlight'], $sec['style'] ?? null) !!}
                    </h2>
                </div>
                <a href="{{ $sec['viewAllLink'] ?? '/shop' }}" class="group inline-flex items-center gap-1.5 text-sm font-medium text-foreground">View all<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right h-4 w-4 transition-transform group-hover:translate-x-0.5"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg></a>
            </div>
            <div class="mt-8 grid gap-5 grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                @foreach ($sec['products'] as $product)
                    @include('partials.product-card', ['product' => $product])
                @endforeach
            </div>
        </section>
    @endforeach
@endif

@if($homeTestimonialsActive ?? true)
<section class="container-page py-12 border-t border-border/50">
    <div class="text-center max-w-xl mx-auto">
        <h2 class="text-2xl font-bold tracking-tight text-foreground sm:text-3xl">What Our Customers Say</h2>
        <p class="mt-2 text-sm text-muted-foreground">Real feedback from thousands of satisfied tech lovers across Bangladesh</p>
    </div>
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="rounded-xl border border-border bg-card p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-1 text-amber-500 text-sm">★★★★★</div>
                <p class="mt-3 text-sm text-foreground/90 leading-relaxed">"Got my iPhone 15 Pro intact box from Khan Gadget. Delivery was super fast and authentic product!"</p>
            </div>
            <div class="mt-4 flex items-center gap-3 pt-3 border-t border-border/40">
                <div class="h-9 w-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-xs text-foreground">RA</div>
                <div>
                    <h4 class="text-xs font-semibold text-foreground">Rafiq Ahmed</h4>
                    <p class="text-[10px] text-muted-foreground">Dhaka</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-1 text-amber-500 text-sm">★★★★★</div>
                <p class="mt-3 text-sm text-foreground/90 leading-relaxed">"Bought a pre-owned MacBook Air M1. Condition was literally like brand new without a single scratch!"</p>
            </div>
            <div class="mt-4 flex items-center gap-3 pt-3 border-t border-border/40">
                <div class="h-9 w-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-xs text-foreground">TI</div>
                <div>
                    <h4 class="text-xs font-semibold text-foreground">Tariqul Islam</h4>
                    <p class="text-[10px] text-muted-foreground">Chittagong</p>
                </div>
            </div>
        </div>
        <div class="rounded-xl border border-border bg-card p-6 shadow-xs flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-1 text-amber-500 text-sm">★★★★★</div>
                <p class="mt-3 text-sm text-foreground/90 leading-relaxed">"Khan Gadget customer support is top tier. They answered all my queries patiently before purchase."</p>
            </div>
            <div class="mt-4 flex items-center gap-3 pt-3 border-t border-border/40">
                <div class="h-9 w-9 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center font-bold text-xs text-foreground">NK</div>
                <div>
                    <h4 class="text-xs font-semibold text-foreground">Nusrat Khan</h4>
                    <p class="text-[10px] text-muted-foreground">Rajshahi</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

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
        resultsBox.className = 'absolute left-0 right-0 top-full mt-2 bg-background rounded-md shadow-lg border border-border z-50 overflow-hidden divide-y divide-border hidden text-left overflow-y-auto';
        resultsBox.style.maxHeight = 'min(24rem, 70vh)';
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
                                        ${item.in_stock ? `
                                            <div class="text-xs font-bold text-gray-900">${item.price}</div>
                                            ${item.compare_at_price ? `<div class="text-[10px] text-gray-400 line-through">${item.compare_at_price}</div>` : ''}
                                        ` : `<div class="text-[10px] font-bold text-muted-foreground uppercase">Out of Stock</div>`}
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
</main>
@include('partials.footer')
</div><nav aria-label="Mobile footer navigation" class="fixed bottom-0 left-0 right-0 z-40 border-t border-border bg-background/95 backdrop-blur sm:hidden" data-tsd-source="/src/components/site/MobileFooterNav.tsx:14:5"><ul class="grid grid-cols-4" data-tsd-source="/src/components/site/MobileFooterNav.tsx:18:7"><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-foreground active" href="/" data-status="active" aria-current="page"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>Home</a></li><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/shop" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-grid h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><rect width="7" height="7" x="3" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="14" rx="1"></rect><rect width="7" height="7" x="3" y="14" rx="1"></rect></svg>Explore</a></li><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/customer-spotlight" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>Spotlight</a></li><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/customer-feedback" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square-heart h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"></path><path d="M7.5 9.5c0 .687.265 1.383.697 1.844l3.009 3.264a1.14 1.14 0 0 0 .407.314 1 1 0 0 0 .783-.004 1.14 1.14 0 0 0 .398-.31l3.008-3.264A2.77 2.77 0 0 0 16.5 9.5 2.5 2.5 0 0 0 12 8a2.5 2.5 0 0 0-4.5 1.5"></path></svg>Feedback</a></li></ul></nav><section aria-label="Notifications alt+T" tabindex="-1" aria-live="polite" aria-relevant="additions text" aria-atomic="false"></section>
@include('partials.mobile-drawer')
@include('partials.popup-offer')
</body></html>
