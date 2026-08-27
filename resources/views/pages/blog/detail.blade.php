@verbatim
<!DOCTYPE html><html lang="en"><head><meta charSet="utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/><link rel="preload" as="image" href="/media/b3ca13-kg-lockup-v2.png"/>
@endverbatim
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" data-precedence="default"/>
<title>{{ $post->title }} | {{ $siteName ?? 'Khan Gadget' }}</title>
<meta name="author" content="{{ $siteName ?? 'Khan Gadget' }}"/>
<meta name="description" content="{{ $post->excerptText() }}"/>
<meta name="twitter:card" content="summary_large_image"/>
<meta name="twitter:title" content="{{ $post->title }} | {{ $siteName ?? 'Khan Gadget' }}"/>
<meta name="twitter:description" content="{{ $post->excerptText() }}"/>
@if($post->featured_image)<meta property="og:image" content="{{ url($post->featured_image) }}"/><meta name="twitter:image" content="{{ url($post->featured_image) }}"/>@endif
<meta property="og:title" content="{{ $post->title }} | {{ $siteName ?? 'Khan Gadget' }}"/>
<meta property="og:description" content="{{ $post->excerptText() }}"/>
<meta property="og:type" content="article"/>
<link rel="icon" href="{{ $siteFavicon ?? '/favicon.png' }}" type="image/png"/>
<link rel="shortcut icon" href="{{ $siteFavicon ?? '/favicon.ico' }}" type="image/x-icon"/>
<link rel="preconnect" href="https://fonts.googleapis.com"/>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous"/>
<link rel="stylesheet" href="/assets/styles-CC_Lznyw.css">
<script src="https://unpkg.com/lucide@latest"></script>
<script src="/assets/theme.js"></script>
</head>
@verbatim
<body><div class="flex min-h-screen flex-col bg-background"><header class="notranslate sticky top-0 z-40 border-b border-border bg-background/95 backdrop-blur" translate="no"><div class="container-page flex h-20 items-center gap-3"><a aria-label="Khan Gadget home" href="/" class="flex shrink-0 items-center"><img src="/media/b3ca13-kg-lockup-v2.png" alt="Khan Gadget - Eternal Tech Companion" class="h-9 w-auto object-contain sm:h-12 lg:h-14"/></a><div class="hidden flex-1 sm:block"><label class="flex items-center gap-3 rounded-full border border-border bg-secondary/60 px-5 py-3 text-foreground transition-colors focus-within:border-foreground/30 focus-within:bg-secondary hover:bg-secondary"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4 text-muted-foreground" aria-hidden="true"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input type="search" placeholder="Search products, brands, categories…" class="w-full bg-transparent text-sm placeholder:text-muted-foreground focus:outline-none"/></label></div><div class="flex-1 sm:hidden" aria-hidden="true"></div><div class="flex items-center gap-1.5 sm:gap-2"><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full" aria-label="Switch to dark mode"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon h-5 w-5" aria-hidden="true"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"></path></svg></button><a href="/compare" aria-label="Compare products" title="Compare products" class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-git-compare h-5 w-5" aria-hidden="true"><circle cx="18" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><path d="M13 6h3a2 2 0 0 1 2 2v7"></path><path d="M11 18H8a2 2 0 0 1-2-2V9"></path></svg></a><a href="/cart" class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-5 w-5" aria-hidden="true"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg></a><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full lg:hidden" aria-label="Menu"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-5 w-5" aria-hidden="true"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg></button></div></div><div class="hidden border-t border-border lg:block"><div class="container-page flex h-11 items-center gap-6 text-xs"><nav class="flex items-center gap-1"><a href="/shop" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">ALL PRODUCTS</a><a href="/shop?condition=intact" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BRAND NEW INTACT BOX</a><a href="/shop?condition=without-box" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BRAND NEW WITHOUT BOX</a><a href="/shop?condition=pre-owned" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">PRE-OWNED</a></nav><div class="flex-1" aria-hidden="true"></div><nav class="flex items-center gap-1"><a href="/blog" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground">BLOG</a><a href="/customer-spotlight" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">CUSTOMER SPOTLIGHT</a><a href="/philanthropic-work" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">PHILANTHROPIC WORK</a><a href="/customer-feedback" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">CUSTOMER FEEDBACK</a></nav></div></div></header><main class="flex-1 pb-14 sm:pb-0">
@endverbatim

<style>
    .blog-rich-text {
        color: var(--color-foreground, #09090b);
        font-size: 15px;
        line-height: 1.5;
    }
    .blog-rich-text p {
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.5;
    }
    .blog-rich-text p:empty,
    .blog-rich-text p:has(> br:only-child) {
        margin: 0 !important;
        padding: 0 !important;
        min-height: 1.2em;
    }
    .blog-rich-text h1, .blog-rich-text h2, .blog-rich-text h3 {
        font-weight: 600;
        margin: 1rem 0 0.25rem;
        line-height: 1.3;
    }
    .blog-rich-text h2 { font-size: 1.35rem; }
    .blog-rich-text h3 { font-size: 1.15rem; }
    .blog-rich-text ul, .blog-rich-text ol {
        margin: 0.25rem 0 0.5rem;
        padding-left: 1.3rem;
    }
    .blog-rich-text li {
        margin: 0;
        padding: 0;
        line-height: 1.5;
    }
    .blog-rich-text a {
        text-decoration: underline;
        color: var(--color-foreground, #09090b);
    }
    .blog-rich-text img {
        max-width: 100%;
        height: auto;
        border-radius: 6px;
        margin: 0.75rem 0;
    }
    .blog-rich-text blockquote {
        border-left: 3px solid var(--color-border, #e5e7eb);
        padding-left: 0.75rem;
        margin: 0.75rem 0;
        color: var(--color-muted-foreground, #71717a);
        font-style: italic;
    }
</style>

<script src="/assets/kg-translate.js?v=2"></script>
<script>document.addEventListener('DOMContentLoaded', function () { if (window.kgInitTranslateToggle) window.kgInitTranslateToggle('#blog-translate-scope'); });</script>

<div class="container-page py-10" id="blog-translate-scope">
    

    <div class="notranslate mt-4 flex w-fit items-center gap-1 rounded-full border border-border bg-surface p-1" translate="no" id="kg-lang-toggle">
        <button type="button" data-lang="en" class="kg-lang-btn rounded-full px-3 py-1 text-xs font-medium transition-colors">EN</button>
        <button type="button" data-lang="bn" class="kg-lang-btn rounded-full px-3 py-1 text-xs font-medium transition-colors">বাং</button>
    </div>

    <div class="mx-auto mt-6 max-w-3xl">
        <h1 class="text-3xl font-semibold tracking-tight text-foreground sm:text-4xl">{{ $post->title }}</h1>
    </div>

    @if($post->featured_image)
        <div class="mx-auto mt-8 max-w-4xl overflow-hidden rounded-md bg-surface">
            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="aspect-[16/9] w-full object-cover">
        </div>
    @endif

    <div class="blog-rich-text mx-auto mt-8 max-w-3xl text-base text-foreground">
        {!! $post->content !!}
    </div>

</div>

@verbatim
</main>@endverbatim
@include('partials.footer', ['hideOutlets' => true])
@verbatim
<nav aria-label="Mobile footer navigation" class="notranslate fixed bottom-0 left-0 right-0 z-40 border-t border-border bg-background/95 backdrop-blur sm:hidden" translate="no"><ul class="grid grid-cols-4"><li><a class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground" href="/"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-5 w-5 opacity-80" aria-hidden="true"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>Home</a></li><li><a href="/shop" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-grid h-5 w-5 opacity-80" aria-hidden="true"><rect width="7" height="7" x="3" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="14" rx="1"></rect><rect width="7" height="7" x="3" y="14" rx="1"></rect></svg>Explore</a></li><li><a href="/customer-spotlight" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-5 w-5 opacity-80" aria-hidden="true"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>Spotlight</a></li><li><a href="/customer-feedback" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square-heart h-5 w-5 opacity-80" aria-hidden="true"><path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"></path><path d="M7.5 9.5c0 .687.265 1.383.697 1.844l3.009 3.264a1.14 1.14 0 0 0 .407.314 1 1 0 0 0 .783-.004 1.14 1.14 0 0 0 .398-.31l3.008-3.264A2.77 2.77 0 0 0 16.5 9.5 2.5 2.5 0 0 0 12 8a2.5 2.5 0 0 0-4.5 1.5"></path></svg>Feedback</a></li></ul></nav>
<div class="notranslate fixed bottom-20 right-4 z-50 flex flex-col items-center sm:bottom-6" translate="no"><button class="flex flex-col items-center transition-transform hover:scale-105 active:scale-95" aria-label="Open live chat" title="Need help?"><img src="/assets/support-agent-BWJyOWv2.png" alt="Live chat support agent" width="512" height="512" loading="lazy" class="agent-float h-20 w-20 select-none object-contain drop-shadow-lg sm:h-24 sm:w-24"/><span class="-mt-1 rounded-full bg-foreground px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-background shadow-sm">Live Chat</span></button></div>
@endverbatim
@include('partials.mobile-drawer')
</body></html>
