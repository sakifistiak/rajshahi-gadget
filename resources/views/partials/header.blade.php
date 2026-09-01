<header class="sticky top-0 z-40 border-b border-border bg-background/95 backdrop-blur">
    <div class="container-page flex h-20 items-center gap-3">
        <a aria-label="Khan Gadget home" href="/" class="flex shrink-0 items-center">
            <img src="{{ \App\Models\SiteSetting::getValue('logo_light', '/media/b3ca13-kg-lockup-v2.png') }}" alt="{{ \App\Models\SiteSetting::getValue('site_name', 'Khan Gadget') }}" class="h-9 w-auto object-contain sm:h-12 lg:h-14" />
        </a>

        <!-- Search Bar -->
        <div class="hidden flex-1 sm:block">
            <form action="/shop" method="GET">
                <label class="flex items-center gap-3 rounded-full border border-border bg-secondary/60 px-5 py-3 text-foreground transition-colors focus-within:border-foreground/30 focus-within:bg-secondary hover:bg-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-search h-4 w-4 text-muted-foreground"><path d="m21 21-4.34-4.34"></path><circle cx="11" cy="11" r="8"></circle></svg>
                    <input type="search" name="search" placeholder="Search products, brands, categories…" class="w-full bg-transparent text-sm placeholder:text-muted-foreground focus:outline-none" />
                </label>
            </form>
        </div>

        <div class="flex-1 sm:hidden"></div>

        <!-- Action Icons -->
        <div class="flex items-center gap-1.5 sm:gap-2">
            <!-- Theme Toggle -->
            <button class="inline-flex items-center justify-center gap-2 text-sm font-medium cursor-pointer transition-colors hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full" aria-label="Switch theme" onclick="toggleTheme()">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon h-5 w-5"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"></path></svg>
            </button>

            <!-- Compare Button -->
            <a href="/compare" aria-label="Compare products" title="Compare products" class="inline-flex items-center justify-center gap-2 text-sm font-medium cursor-pointer transition-colors hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-git-compare h-5 w-5"><circle cx="18" cy="18" r="3"></circle><circle cx="6" cy="6" r="3"></circle><path d="M13 6h3a2 2 0 0 1 2 2v7"></path><path d="M11 18H8a2 2 0 0 1-2-2V9"></path></svg>
            </a>

            <!-- Cart Button -->
            <a aria-label="Cart" href="/cart" class="relative">
                <button class="inline-flex items-center justify-center gap-2 text-sm font-medium cursor-pointer transition-colors hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-5 w-5"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                </button>
            </a>

            <!-- Mobile Drawer Menu Trigger -->
            <button onclick="openDrawer()" class="inline-flex items-center justify-center gap-2 text-sm font-medium cursor-pointer transition-colors hover:bg-accent hover:text-accent-foreground h-9 w-9 rounded-full lg:hidden" aria-label="Menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-menu h-5 w-5"><path d="M4 5h16"></path><path d="M4 12h16"></path><path d="M4 19h16"></path></svg>
            </button>
        </div>
    </div>

    <!-- Desktop Secondary Navigation Bar -->
    <div class="hidden border-t border-border lg:block">
        <div class="container-page flex h-11 items-center gap-6 text-xs">
            <nav class="flex items-center gap-1">
                <div class="relative kg-mega-menu-wrap" id="kg-all-products-menu">
                    <a href="/shop" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">ALL PRODUCTS</a>
                    <div class="kg-mega-menu" id="kg-mega-menu" role="menu" aria-label="All products">
                        <div class="kg-mega-menu-cats" id="kg-mega-menu-cats"></div>
                        <div class="kg-mega-menu-brands" id="kg-mega-menu-brands"></div>
                    </div>
                </div>
                <a href="/shop?condition=intact" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BRAND NEW INTACT BOX</a>
                <a href="/shop?condition=without-box" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BRAND NEW WITHOUT BOX</a>
                <a href="/shop?condition=pre-owned" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">PRE-OWNED</a>
            </nav>
            <div class="flex-1"></div>
            <nav class="flex items-center gap-1">
                <a href="/blog" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">BLOG</a>
                <a href="/customer-spotlight" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">CUSTOMER SPOTLIGHT</a>
                <a href="/philanthropic-work" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">PHILANTHROPIC WORK</a>
                <a href="/customer-feedback" class="rounded-full px-2.5 py-1 font-medium transition-colors hover:bg-secondary text-foreground/80">CUSTOMER FEEDBACK</a>
            </nav>
        </div>
    </div>
</header>
