<!DOCTYPE html><html lang="en" data-tsd-source="/src/routes/__root.tsx:133:5"><head data-tsd-source="/src/routes/__root.tsx:134:7"><meta charSet="utf-8"/><meta name="viewport" content="width=device-width, initial-scale=1"/><link rel="preload" as="image" href="/media/b3ca13-kg-lockup-v2.png"/><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;display=swap" data-precedence="default"/><title>Shopping Cart | {{ $siteName ?? 'Khan Gadget' }}</title><meta name="author" content="{{ $siteName ?? 'Khan Gadget' }}"/><meta property="og:type" content="website"/><meta name="twitter:card" content="summary_large_image"/><meta name="twitter:site" content="{{ $siteName ?? 'Khan Gadget' }}"/><meta name="twitter:title" content="Shopping Cart | {{ $siteName ?? 'Khan Gadget' }}"/><meta name="twitter:description" content="{{ $siteDescription ?? 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.' }}"/><meta property="og:image" content="https://pub-bb2e103a32db4e198524a2e9ed8f35b4.r2.dev/e4f4ef3d-bc55-4272-bf8e-77a19f63e327/id-preview-59d9b9ce--d146692d-212a-4234-ab9b-1057d9ddd7d8.lovable.app-1785051870536.png"/><meta name="twitter:image" content="https://pub-bb2e103a32db4e198524a2e9ed8f35b4.r2.dev/e4f4ef3d-bc55-4272-bf8e-77a19f63e327/id-preview-59d9b9ce--d146692d-212a-4234-ab9b-1057d9ddd7d8.lovable.app-1785051870536.png"/><meta name="description" content="{{ $siteDescription ?? 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.' }}"/><meta property="og:title" content="Shopping Cart | {{ $siteName ?? 'Khan Gadget' }}"/><meta property="og:description" content="{{ $siteDescription ?? 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.' }}"/><meta name="robots" content="noindex"/><link rel="icon" href="{{ $siteFavicon ?? '/favicon.png' }}" type="image/png"/><link rel="preconnect" href="https://fonts.googleapis.com"/><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous"/><link rel="stylesheet" href="/assets/styles-CC_Lznyw.css"><script src="https://unpkg.com/lucide@latest"></script><script src="/assets/theme.js"></script></head>@verbatim<body data-tsd-source="/src/routes/__root.tsx:138:7"><div class="flex min-h-screen flex-col bg-background" data-tsd-source="/src/routes/__root.tsx:154:13"><header class="sticky top-0 z-40 border-b border-border bg-background/95 backdrop-blur" data-tsd-source="/src/components/site/Navbar.tsx:31:5"><div class="container-page flex h-20 items-center gap-3" data-tsd-source="/src/components/site/Navbar.tsx:33:7"><a aria-label="Khan Gadget home" data-tsd-source="/src/components/site/Navbar.tsx:34:9" href="/" class="flex shrink-0 items-center"><img src="/media/b3ca13-kg-lockup-v2.png" alt="Khan Gadget - Eternal Tech Companion" class="h-9 w-auto object-contain sm:h-12 lg:h-14" data-tsd-source="/src/components/site/Navbar.tsx:35:11"/></a><div class="hidden flex-1 sm:block" data-tsd-source="/src/components/site/Navbar.tsx:42:9"><label class="flex items-center gap-3 rounded-full border border-border bg-secondary/60 px-5 py-3 text-foreground transition-colors focus-within:border-foreground/30 focus-within:bg-secondary hover:bg-secondary" data-tsd-source="/src/components/site/Navbar.tsx:43:11"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4 text-muted-foreground" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:44:13"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input type="search" placeholder="Search products, brands, categories…" class="w-full bg-transparent text-sm placeholder:text-muted-foreground focus:outline-none" data-tsd-source="/src/components/site/Navbar.tsx:45:13"/></label></div><div class="flex-1 sm:hidden" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:53:9"></div><div class="flex items-center gap-1.5 sm:gap-2" data-tsd-source="/src/components/site/Navbar.tsx:57:9"><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full" aria-label="Switch to dark mode" data-tsd-source="/src/components/site/ThemeToggle.tsx:8:5"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/ThemeToggle.tsx:15:60"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"></path></svg></button><a href="/compare" aria-label="Compare products" title="Compare products" class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-git-compare h-5 w-5" aria-hidden="true"><circle cx="18" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><path d="M13 6h3a2 2 0 0 1 2 2v7"></path><path d="M11 18H8a2 2 0 0 1-2-2V9"></path></svg></a><a aria-label="Cart, 0 items" data-tsd-source="/src/components/site/Navbar.tsx:59:11" class="relative active" href="/cart" data-status="active" aria-current="page"><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full" data-tsd-source="/src/components/site/Navbar.tsx:60:13"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:61:15"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg></button></a><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full lg:hidden" aria-label="Menu" data-tsd-source="/src/components/site/Navbar.tsx:69:11"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-5 w-5" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:76:49"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg></button></div></div><div class="container-page pb-3 sm:hidden" data-tsd-source="/src/components/site/Navbar.tsx:82:7"><label class="flex items-center gap-3 rounded-full border border-border bg-secondary/60 px-4 py-2.5 text-foreground focus-within:border-foreground/30 focus-within:bg-secondary" data-tsd-source="/src/components/site/Navbar.tsx:83:9"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4 text-muted-foreground" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:84:11"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg><input type="search" placeholder="Search products, brands, categories…" class="w-full bg-transparent text-sm placeholder:text-muted-foreground focus:outline-none" data-tsd-source="/src/components/site/Navbar.tsx:85:11"/></label></div><div class="hidden border-t border-border lg:block" data-tsd-source="/src/components/site/Navbar.tsx:96:7"><div class="container-page flex h-11 items-center gap-6 text-xs" data-tsd-source="/src/components/site/Navbar.tsx:97:9"><nav class="flex items-center gap-1" data-tsd-source="/src/components/site/Navbar.tsx:98:11"><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">ALL PRODUCTS</a><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop?condition=intact" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BRAND NEW INTACT BOX</a><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop?condition=without-box" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BRAND NEW WITHOUT BOX</a><a data-tsd-source="/src/components/site/Navbar.tsx:102:17" href="/shop?condition=pre-owned" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">PRE-OWNED</a></nav><div class="flex-1" aria-hidden="true" data-tsd-source="/src/components/site/Navbar.tsx:115:11"></div><nav class="flex items-center gap-1" data-tsd-source="/src/components/site/Navbar.tsx:116:11"><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/blog" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BLOG</a><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/customer-spotlight" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">CUSTOMER SPOTLIGHT</a><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/philanthropic-work" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">PHILANTHROPIC WORK</a><a data-tsd-source="/src/components/site/Navbar.tsx:120:17" href="/customer-feedback" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">CUSTOMER FEEDBACK</a></nav></div></div></header><main class="flex-1 pb-14 sm:pb-0" data-tsd-source="/src/routes/__root.tsx:156:15"><!--$--><div class="container-page py-24" data-tsd-source="/src/routes/cart.tsx:27:7"><div class="mx-auto max-w-md rounded-md border border-border bg-card p-10 text-center" data-tsd-source="/src/routes/cart.tsx:28:9"><span class="mx-auto grid h-14 w-14 place-items-center rounded-sm bg-secondary" data-tsd-source="/src/routes/cart.tsx:29:11"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag h-6 w-6" aria-hidden="true" data-tsd-source="/src/routes/cart.tsx:30:13"><path d="M16 10a4 4 0 0 1-8 0"></path><path d="M3.103 6.034h17.794"></path><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"></path></svg></span><h1 class="mt-6 text-2xl font-semibold tracking-tight" data-tsd-source="/src/routes/cart.tsx:32:11">Your cart is empty</h1><p class="mt-2 text-sm text-muted-foreground" data-tsd-source="/src/routes/cart.tsx:33:11">Add a few things you love. We&#x27;ll keep them safe here.</p><a data-tsd-source="/src/routes/cart.tsx:36:11" href="/shop"><button class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:pointer-events-none disabled:opacity-50 disabled:cursor-not-allowed [&amp;_svg]:pointer-events-none [&amp;_svg]:size-4 [&amp;_svg]:shrink-0 bg-primary text-primary-foreground shadow hover:bg-primary/90 py-2 mt-8 h-11 rounded-full px-6" data-tsd-source="/src/routes/cart.tsx:37:13">Start shopping<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right ml-2 h-4 w-4" aria-hidden="true" data-tsd-source="/src/routes/cart.tsx:39:15"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg></button></a></div></div><!--/$--></main>@endverbatim
@include('partials.footer')
@verbatim</div><nav aria-label="Mobile footer navigation" class="fixed bottom-0 left-0 right-0 z-40 border-t border-border bg-background/95 backdrop-blur sm:hidden" data-tsd-source="/src/components/site/MobileFooterNav.tsx:14:5"><ul class="grid grid-cols-4" data-tsd-source="/src/components/site/MobileFooterNav.tsx:18:7"><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-house h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"></path><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path></svg>Home</a></li><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/shop" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-layout-grid h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><rect width="7" height="7" x="3" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="3" rx="1"></rect><rect width="7" height="7" x="14" y="14" rx="1"></rect><rect width="7" height="7" x="3" y="14" rx="1"></rect></svg>Explore</a></li><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/customer-spotlight" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sparkles h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"></path><path d="M20 2v4"></path><path d="M22 4h-4"></path><circle cx="4" cy="20" r="2"></circle></svg>Spotlight</a></li><li data-tsd-source="/src/components/site/MobileFooterNav.tsx:22:13"><a data-tsd-source="/src/components/site/MobileFooterNav.tsx:23:15" href="/customer-feedback" class="flex flex-col items-center gap-1 py-2 text-[10px] font-medium text-muted-foreground"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-message-square-heart h-5 w-5 opacity-80" aria-hidden="true" data-tsd-source="/src/components/site/MobileFooterNav.tsx:29:17"><path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"></path><path d="M7.5 9.5c0 .687.265 1.383.697 1.844l3.009 3.264a1.14 1.14 0 0 0 .407.314 1 1 0 0 0 .783-.004 1.14 1.14 0 0 0 .398-.31l3.008-3.264A2.77 2.77 0 0 0 16.5 9.5 2.5 2.5 0 0 0 12 8a2.5 2.5 0 0 0-4.5 1.5"></path></svg>Feedback</a></li></ul></nav><div class="fixed bottom-20 right-4 z-50 flex flex-col items-center sm:bottom-6" data-tsd-source="/src/components/site/ChatWidget.tsx:214:7"><button class="flex flex-col items-center transition-transform hover:scale-105 active:scale-95" aria-label="Open live chat" title="Need help?" data-tsd-source="/src/components/site/ChatWidget.tsx:215:9"><img src="/assets/support-agent-BWJyOWv2.png" alt="Live chat support agent" width="512" height="512" loading="lazy" class="agent-float h-20 w-20 select-none object-contain drop-shadow-lg sm:h-24 sm:w-24" data-tsd-source="/src/components/site/ChatWidget.tsx:221:11"/><span class="-mt-1 rounded-full bg-foreground px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-background shadow-sm" data-tsd-source="/src/components/site/ChatWidget.tsx:229:11">Live Chat</span></button></div><section aria-label="Notifications alt+T" tabindex="-1" aria-live="polite" aria-relevant="additions text" aria-atomic="false"></section>@endverbatim
@include('partials.mobile-drawer')
@verbatim</body></html>
@endverbatim

<div id="stock-notice-template" class="hidden">
    @include('partials.stock-price-notice')
</div>

<script>
(function() {
    var CART_KEY = 'kg_shopping_cart';

    function getCart() {
        try { return JSON.parse(localStorage.getItem(CART_KEY) || '[]'); } catch(e) { return []; }
    }
    function saveCart(cart) {
        try { localStorage.setItem(CART_KEY, JSON.stringify(cart)); } catch(e) {}
    }
    function fmt(n) { return '৳ ' + Number(n).toLocaleString('en-IN'); }

    function renderCartPage() {
        var cart = getCart();
        var main = document.querySelector('main');
        if (!main) return;

        var noticeElem = document.getElementById('stock-notice-template');
        var noticeHtml = noticeElem ? noticeElem.innerHTML : '';

        var totalCount = cart.reduce(function(s, i) { return s + (i.quantity || 1); }, 0);
        var subtotal = cart.reduce(function(s, i) { return s + (i.price || 0) * (i.quantity || 1); }, 0);

        if (cart.length === 0) {
            main.innerHTML = '<div class="container-page py-24">' +
                '<div class="mx-auto max-w-md rounded-md border border-border bg-card p-10 text-center">' +
                    '<span class="mx-auto grid h-14 w-14 place-items-center rounded-sm bg-secondary">' +
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-bag h-6 w-6"><path d="M16 10a4 4 0 0 1-8 0"></path><path d="M3.103 6.034h17.794"></path><path d="M3.4 5.467a2 2 0 0 0-.4 1.2V20a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6.667a2 2 0 0 0-.4-1.2l-2-2.667A2 2 0 0 0 17 2H7a2 2 0 0 0-1.6.8z"></path></svg>' +
                    '</span>' +
                    '<h1 class="mt-6 text-2xl font-semibold tracking-tight">Your cart is empty</h1>' +
                    '<p class="mt-2 text-sm text-muted-foreground">Add a few things you love. We\'ll keep them safe here.</p>' +
                    '<a href="/shop" class="inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-medium cursor-pointer transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 py-2 mt-8 h-11 rounded-full px-6">' +
                        'Start shopping' +
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right ml-2 h-4 w-4"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>' +
                    '</a>' +
                '</div>' +
            '</div>';
            return;
        }

        var shipping = subtotal > 99000 || subtotal === 0 ? 0 : 100;
        var tax = Math.round(subtotal * 0.05);
        var grandTotal = subtotal + shipping + tax;

        var html = '<div class="container-page py-12">' +
            '<h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">Your cart</h1>' +
            '<p class="mt-2 text-sm text-muted-foreground">' + totalCount + ' item' + (totalCount === 1 ? '' : 's') + ' · Free next‑day delivery over ৳ 99,000</p>' +
            '<div class="mt-10 grid gap-10 lg:grid-cols-[1.5fr_1fr]">' +
                '<ul class="divide-y divide-border rounded-md border border-border bg-card">';

        cart.forEach(function(item, idx) {
            html += '<li class="grid grid-cols-[88px_1fr] gap-4 p-5 sm:grid-cols-[112px_1fr_auto]">' +
                '<a href="/product/' + (item.slug || '') + '" class="overflow-hidden rounded-sm bg-surface">' +
                    '<div class="aspect-square">' +
                        (item.image ? '<img src="' + item.image + '" alt="' + (item.name || '').replace(/"/g, '&quot;') + '" loading="lazy" class="h-full w-full object-cover"/>' : '<div class="h-full w-full bg-secondary flex items-center justify-center text-muted-foreground"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg></div>') +
                    '</div>' +
                '</a>' +
                '<div class="min-w-0">' +
                    '<p class="text-xs uppercase tracking-wider text-muted-foreground">KHAN GADGET</p>' +
                    '<a href="/product/' + (item.slug || '') + '" class="mt-1 block truncate text-base font-medium hover:underline">' + (item.name || 'Product') + '</a>' +
                    '<p class="mt-1 text-sm text-muted-foreground">' + fmt(item.price || 0) + ' each</p>' +
                    '<div class="mt-4 flex items-center gap-3 sm:hidden">' +
                        '<div class="inline-flex items-center rounded-full border border-border">' +
                            '<button class="kg-qty-btn grid h-9 w-9 place-items-center hover:bg-secondary" data-action="dec" data-idx="' + idx + '" aria-label="Decrease"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"></path></svg></button>' +
                            '<span class="w-8 text-center text-sm font-medium tabular-nums">' + (item.quantity || 1) + '</span>' +
                            '<button class="kg-qty-btn grid h-9 w-9 place-items-center hover:bg-secondary" data-action="inc" data-idx="' + idx + '" aria-label="Increase"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"></path></svg></button>' +
                        '</div>' +
                        '<button class="kg-remove-btn text-xs text-muted-foreground hover:text-destructive" data-idx="' + idx + '">Remove</button>' +
                        '<span class="ms-auto font-medium">' + fmt((item.price || 0) * (item.quantity || 1)) + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="hidden flex-col items-end justify-between gap-3 sm:flex">' +
                    '<span class="font-medium">' + fmt((item.price || 0) * (item.quantity || 1)) + '</span>' +
                    '<div class="inline-flex items-center rounded-full border border-border">' +
                        '<button class="kg-qty-btn grid h-9 w-9 place-items-center hover:bg-secondary" data-action="dec" data-idx="' + idx + '" aria-label="Decrease"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"></path></svg></button>' +
                        '<span class="w-8 text-center text-sm font-medium tabular-nums">' + (item.quantity || 1) + '</span>' +
                        '<button class="kg-qty-btn grid h-9 w-9 place-items-center hover:bg-secondary" data-action="inc" data-idx="' + idx + '" aria-label="Increase"><svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12h14"></path></svg></button>' +
                    '</div>' +
                    '<button class="kg-remove-btn inline-flex items-center gap-1 text-xs text-muted-foreground hover:text-destructive" data-idx="' + idx + '">' +
                        '<svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg> Remove' +
                    '</button>' +
                '</div>' +
            '</li>';
        });

        html += '</ul>' +
            '<aside class="h-max rounded-md border border-border bg-surface p-6">' +
                '<h2 class="text-lg font-semibold">Order summary</h2>' +
                '<dl class="mt-4 space-y-3 text-sm">' +
                    '<div class="flex items-center justify-between"><dt class="text-muted-foreground">Subtotal</dt><dd class="font-medium">' + fmt(subtotal) + '</dd></div>' +
                    '<div class="flex items-center justify-between"><dt class="text-muted-foreground">Shipping</dt><dd class="font-medium">' + (shipping === 0 ? 'Free' : fmt(shipping)) + '</dd></div>' +
                    '<div class="flex items-center justify-between"><dt class="text-muted-foreground">Estimated tax</dt><dd class="font-medium">' + fmt(tax) + '</dd></div>' +
                    '<div class="mt-3 flex items-center justify-between border-t border-border pt-3"><dt class="text-base font-semibold">Total</dt><dd class="text-lg font-semibold">' + fmt(grandTotal) + '</dd></div>' +
                '</dl>' +
                noticeHtml +
                '<a href="/checkout" class="mt-6 block">' +
                    '<button class="inline-flex items-center justify-center gap-2 whitespace-nowrap font-medium transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring bg-primary text-primary-foreground shadow hover:bg-primary/90 h-12 w-full rounded-full text-base">' +
                        'Secure checkout' +
                        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-arrow-right ml-2 h-4 w-4"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>' +
                    '</button>' +
                '</a>' +
                '<p class="mt-3 text-center text-xs text-muted-foreground">Taxes and shipping calculated at checkout.</p>' +
            '</aside>' +
        '</div>' +
    '</div>';

        main.innerHTML = html;

        main.addEventListener('click', function(ev) {
            var qb = ev.target.closest('.kg-qty-btn');
            var rb = ev.target.closest('.kg-remove-btn');

            if (qb) {
                var c = getCart();
                var i = parseInt(qb.dataset.idx);
                if (qb.dataset.action === 'inc') { c[i].quantity = (c[i].quantity || 1) + 1; }
                else { c[i].quantity = (c[i].quantity || 1) - 1; if (c[i].quantity < 1) c.splice(i, 1); }
                saveCart(c);
                renderCartPage();
            }
            if (rb) {
                var c = getCart();
                c.splice(parseInt(rb.dataset.idx), 1);
                saveCart(c);
                renderCartPage();
            }
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', renderCartPage);
    else renderCartPage();
})();
</script>

