{{-- Mobile Navigation Drawer --}}
{{-- Include this at the bottom of any page's <body> to enable the hamburger mobile menu --}}

<style>
/* ── Mobile Drawer ── */
#kg-mobile-drawer {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    pointer-events: none;
    opacity: 0;
    transition: opacity 0.25s ease;
}
#kg-mobile-drawer.open {
    pointer-events: all;
    opacity: 1;
}

/* Backdrop */
#kg-mobile-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,0.55);
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
}

/* Drawer panel */
#kg-mobile-panel {
    position: relative;
    width: min(320px, 88vw);
    height: 100%;
    background: var(--color-background, #ffffff);
    border-right: 1px solid var(--color-border, #e5e7eb);
    box-shadow: 4px 0 32px rgba(0,0,0,0.18);
    display: flex;
    flex-direction: column;
    transform: translateX(-100%);
    transition: transform 0.3s cubic-bezier(0.32, 0, 0.15, 1);
    overflow-y: auto;
}
#kg-mobile-drawer.open #kg-mobile-panel {
    transform: translateX(0);
}

/* Dark mode */
html.dark #kg-mobile-panel {
    background: #0f1117;
    border-right-color: #1e2330;
}

/* Header */
#kg-mobile-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 20px;
    border-bottom: 1px solid var(--color-border, #e5e7eb);
}
html.dark #kg-mobile-header {
    border-bottom-color: #1e2330;
}
#kg-mobile-close {
    width: 36px; height: 36px;
    display: grid; place-items: center;
    border-radius: 50%;
    border: none;
    background: var(--color-secondary, #f4f4f5);
    color: var(--color-foreground, #09090b);
    cursor: pointer;
    transition: background 0.15s;
    flex-shrink: 0;
}
#kg-mobile-close:hover { background: var(--color-accent, #e4e4e7); }
html.dark #kg-mobile-close {
    background: #1e2330;
    color: #f1f5f9;
}
html.dark #kg-mobile-close:hover { background: #252d3d; }

/* ── Hotline Call Card Button ── */
.kg-hotline-btn {
    display: flex;
    align-items: center;
    width: 100%;
    padding: 12px 16px;
    border-radius: 12px;
    background: #ffffff;
    border: 1px solid #e4e4e7;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.06);
    text-decoration: none;
    transition: all 0.2s ease;
}
.kg-hotline-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
}
.kg-hotline-icon {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #000000;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid #000000;
}
.kg-hotline-divider {
    width: 1px;
    height: 34px;
    background: #e4e4e7;
    margin: 0 16px;
    flex-shrink: 0;
}
.kg-hotline-text {
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.kg-hotline-label {
    font-size: 10.5px;
    font-weight: 700;
    color: #52525b;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    line-height: 1;
}
.kg-hotline-number {
    font-size: 18px;
    font-weight: 800;
    color: #000000;
    letter-spacing: 0.02em;
    margin-top: 4px;
    line-height: 1.15;
}

/* Dark theme for Hotline Button */
html.dark .kg-hotline-btn {
    background: #000000;
    border-color: #27272a;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.4);
}
html.dark .kg-hotline-icon {
    background: #000000;
    border-color: #3f3f46;
    color: #ffffff;
}
html.dark .kg-hotline-divider {
    background: #27272a;
}
html.dark .kg-hotline-label {
    color: #a1a1aa;
}
html.dark .kg-hotline-number {
    color: #ffffff;
}

/* Nav sections */
.kg-drawer-section {
    padding: 0 12px;
    margin-top: 8px;
}
.kg-drawer-section-title {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    color: var(--color-muted-foreground, #71717a);
    padding: 10px 8px 4px;
}
.kg-drawer-group {
    margin-bottom: 4px;
}
.kg-drawer-group-btn {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 11px 12px;
    border-radius: 10px;
    border: none;
    background: none;
    font-size: 15px;
    font-weight: 600;
    color: var(--color-foreground, #09090b);
    cursor: pointer;
    text-align: left;
    transition: background 0.15s;
}
.kg-drawer-group-btn:hover { background: var(--color-secondary, #f4f4f5); }
html.dark .kg-drawer-group-btn { color: #f1f5f9; }
html.dark .kg-drawer-group-btn:hover { background: #1a2035; }

.kg-drawer-group-btn svg {
    transition: transform 0.25s ease;
    flex-shrink: 0;
    color: var(--color-muted-foreground, #71717a);
}
.kg-drawer-group-btn.expanded svg { transform: rotate(180deg); }

.kg-drawer-submenu {
    overflow: hidden;
    max-height: 0;
    transition: max-height 0.3s ease;
}
.kg-drawer-submenu.open { max-height: 600px; }

.kg-drawer-subitem {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 12px 9px 36px;
    border-radius: 8px;
    font-size: 14px;
    color: var(--color-foreground, #09090b);
    text-decoration: none;
    transition: background 0.15s;
    margin: 1px 0;
}
.kg-drawer-subitem:hover { background: var(--color-secondary, #f4f4f5); }
html.dark .kg-drawer-subitem { color: #cbd5e1; }
html.dark .kg-drawer-subitem:hover { background: #1a2035; }

.kg-drawer-subitem-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--color-muted-foreground, #a1a1aa);
    flex-shrink: 0;
}

/* Divider */
.kg-drawer-divider {
    height: 1px;
    background: var(--color-border, #e5e7eb);
    margin: 10px 20px;
}
html.dark .kg-drawer-divider { background: #1e2330; }

/* Quick links */
.kg-drawer-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 20px;
    font-size: 14px;
    font-weight: 500;
    color: var(--color-foreground, #09090b);
    text-decoration: none;
    transition: background 0.15s;
}
.kg-drawer-link:hover { background: var(--color-secondary, #f4f4f5); }
html.dark .kg-drawer-link { color: #f1f5f9; }
html.dark .kg-drawer-link:hover { background: #1a2035; }

/* Store slider dark mode */
html.dark #kg-drawer-store-slider > div {
    background: #1a2035;
    border-color: #1e2330 !important;
}
html.dark .kg-store-slide span {
    color: #f1f5f9;
}
html.dark #kg-store-prev, html.dark #kg-store-next {
    color: #94a3b8;
}
</style>

<!-- Mobile Drawer HTML -->
<div id="kg-mobile-drawer" role="dialog" aria-modal="true" aria-label="Main menu">
    <div id="kg-mobile-backdrop"></div>
    <div id="kg-mobile-panel">
        <!-- Header -->
        <div id="kg-mobile-header">
            <img id="kg-drawer-logo"
                 src="{{ $siteLogo }}"
                 alt="{{ $siteName }}"
                 style="height:36px;width:auto;object-fit:contain;">
            <button id="kg-mobile-close" aria-label="Close menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <!-- Section 1: Main Navigation -->
        <div class="kg-drawer-section">
            <a href="/" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/><path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>
                Home
            </a>
            <a href="/shop" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                All Products
            </a>

            <!-- Laptop group -->
            <div class="kg-drawer-group">
                <button class="kg-drawer-group-btn expanded" data-target="submenu-laptop">
                    <span style="display:flex;align-items:center;gap:10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m14 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16"/></svg>
                        Laptop
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="kg-drawer-submenu open" id="submenu-laptop">
                    <a href="/shop?condition=intact" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>BRAND NEW INTACT BOX</a>
                    <a href="/shop?condition=without-box" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>BRAND NEW WITHOUT BOX</a>
                    <a href="/shop?condition=pre-owned" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>PRE-OWNED</a>
                </div>
            </div>

            <!-- Brands group -->
            <div class="kg-drawer-group">
                <button class="kg-drawer-group-btn" data-target="submenu-brands">
                    <span style="display:flex;align-items:center;gap:10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/><path d="M7 7h.01"/></svg>
                        Brands
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="kg-drawer-submenu" id="submenu-brands">
                    <a href="/shop?brand=acer" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>Acer</a>
                    <a href="/shop?brand=apple" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>Apple</a>
                    <a href="/shop?brand=asus" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>ASUS</a>
                    <a href="/shop?brand=dell" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>Dell</a>
                    <a href="/shop?brand=hp" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>HP</a>
                    <a href="/shop?brand=lenovo" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>Lenovo</a>
                    <a href="/shop?brand=microsoft" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>Microsoft</a>
                    <a href="/shop?brand=msi" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>MSI</a>
                    <a href="/shop?brand=razer" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>Razer</a>
                    <a href="/shop?brand=samsung" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>Samsung</a>
                    <a href="/shop?brand=toshiba" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>Toshiba</a>
                    <a href="/shop?brand=xiaomi" class="kg-drawer-subitem"><span class="kg-drawer-subitem-dot"></span>Xiaomi</a>
                </div>
            </div>
        </div>

        <div class="kg-drawer-divider"></div>

        <!-- Section 2: Content Links -->
        <div class="kg-drawer-section" style="padding:0;">
            <a href="/blog" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 22h16a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v16a2 2 0 0 1-2 2Zm0 0a2 2 0 0 1-2-2v-9c0-1.1.9-2 2-2h2"/><path d="M18 14h-8"/><path d="M15 18h-5"/><path d="M10 6h8v4h-8V6Z"/></svg>
                Blog
            </a>
            <a href="/customer-spotlight" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                Customer Spotlight
            </a>
            <a href="/philanthropic-work" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/></svg>
                Philanthropic Work
            </a>
            <a href="/customer-feedback" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"/></svg>
                Customer Feedback
            </a>
        </div>

        <div class="kg-drawer-divider"></div>

        <!-- Section 3: Info Links -->
        <div class="kg-drawer-section" style="padding:0;">
            <a href="/about" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                About Us
            </a>
            <a href="/contact" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                Contact
            </a>
            <a href="/privacy-policy" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/></svg>
                Privacy & Policy
            </a>
            <a href="/terms-conditions" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Terms & Conditions
            </a>
            <a href="/complain-advice" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                Complain / Advice
            </a>
        </div>

        <!-- Section 4: Store Location Info Text -->
        @php
            $drawerStoreText = \App\Models\SiteSetting::getValue('mobile_drawer_store_info', $mobileDrawerStoreInfo ?? 'Shop No: 136 - Ground Floor - Computer City Center (Ex Multiplan Center) - New Elephant Road, Dhaka-1205');
        @endphp
        @if(!empty($drawerStoreText))
        <div style="padding:10px 20px 6px;">
            <div style="background:var(--color-secondary, #f4f4f5);border:1px solid var(--color-border, #e5e7eb);border-radius:2px;padding:10px 14px;">
                <p style="font-size:12.5px;font-weight:500;color:var(--color-foreground,#09090b);line-height:1.55;margin:0;">
                    {{ $drawerStoreText }}
                </p>
            </div>
        </div>
        @endif

        <!-- Section 5: Hotline Call Card Button -->
        @php
            $mobileMenuContact = \App\Models\SiteSetting::getValue('mobile_menu_contact', $sitePhone ?? '+8801700000000');
        @endphp
        <div style="padding:8px 16px 20px;">
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $mobileMenuContact) }}" class="kg-hotline-btn">
                <div class="kg-hotline-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path>
                        <path d="M14.05 2a9 9 0 0 1 8 7.94"></path>
                        <path d="M14.05 6a5 5 0 0 1 4 3.9"></path>
                    </svg>
                </div>
                <div class="kg-hotline-divider"></div>
                <div class="kg-hotline-text">
                    <span class="kg-hotline-label">HOTLINE</span>
                    <span class="kg-hotline-number">{{ $mobileMenuContact }}</span>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
(function () {
    var drawer   = document.getElementById('kg-mobile-drawer');
    var backdrop = document.getElementById('kg-mobile-backdrop');
    var closeBtn = document.getElementById('kg-mobile-close');

    /* ── Open/Close ── */
    function openDrawer() {
        // Keep Laptop submenu expanded by default when menu opens
        document.querySelectorAll('.kg-drawer-submenu').forEach(function (m) {
            if (m.id === 'submenu-laptop') {
                m.classList.add('open');
            } else {
                m.classList.remove('open');
            }
        });
        document.querySelectorAll('.kg-drawer-group-btn').forEach(function (b) {
            if (b.getAttribute('data-target') === 'submenu-laptop') {
                b.classList.add('expanded');
            } else {
                b.classList.remove('expanded');
            }
        });
        drawer.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        drawer.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Hamburger button - works for both home and shop pages
    document.querySelectorAll('button[aria-label="Menu"]').forEach(function (btn) {
        btn.addEventListener('click', openDrawer);
    });

    backdrop.addEventListener('click', closeDrawer);
    closeBtn.addEventListener('click', closeDrawer);

    // Esc key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDrawer();
    });

    /* ── Accordion groups ── */
    document.querySelectorAll('.kg-drawer-group-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.getAttribute('data-target');
            var submenu  = document.getElementById(targetId);
            var isOpen   = submenu.classList.contains('open');

            // Close all
            document.querySelectorAll('.kg-drawer-submenu').forEach(function (m) {
                m.classList.remove('open');
            });
            document.querySelectorAll('.kg-drawer-group-btn').forEach(function (b) {
                b.classList.remove('expanded');
            });

            // Toggle clicked
            if (!isOpen) {
                submenu.classList.add('open');
                btn.classList.add('expanded');
            }
        });

        // Set initial expanded state
        var targetId = btn.getAttribute('data-target');
        var submenu  = document.getElementById(targetId);
        if (submenu && submenu.classList.contains('open')) {
            btn.classList.add('expanded');
        }
    });

    /* ── Logo sync with site settings ── */
    document.addEventListener('DOMContentLoaded', function () {
        var drawerLogo = document.getElementById('kg-drawer-logo');
        if (drawerLogo && window.__SITE_LOGO_LIGHT) {
            function syncDrawerLogo() {
                var isDark = document.documentElement.classList.contains('dark');
                drawerLogo.src = isDark
                    ? (window.__SITE_LOGO_DARK  || window.__SITE_LOGO_LIGHT)
                    : window.__SITE_LOGO_LIGHT;
            }
            syncDrawerLogo();
            new MutationObserver(syncDrawerLogo)
                .observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });
        }
    });

})();
</script>

<style>
/* Single product page "Buy Now" button format matching Home/Shop page */
.product-single-buy-now,
button.bg-primary.text-primary-foreground,
a.bg-primary.text-primary-foreground,
button.bg-primary,
a.bg-primary {
    background-color: #24272c !important;
    color: #ffffff !important;
    border-radius: 9999px !important;
    font-weight: 700 !important;
    box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05) !important;
    transition: all 0.2s ease !important;
}

.product-single-buy-now:hover,
button.bg-primary.text-primary-foreground:hover,
a.bg-primary.text-primary-foreground:hover,
button.bg-primary:hover,
a.bg-primary:hover {
    background-color: #1a1c20 !important;
    transform: translateY(-1px) !important;
}

/* The main product image follows the mouse position while zoomed. */
@media (hover: hover) and (pointer: fine) {
    .product-image-magnifier {
        cursor: zoom-in;
    }
}

/* Left-side expandable live chat widget. */
#kg-live-chat {
    position: fixed;
    left: 1rem;
    bottom: 1.5rem;
    z-index: 60;
}
#kg-live-chat-menu {
    position: absolute;
    bottom: calc(100% + 1rem);
    left: 0;
    width: 236px;
    padding: 0.65rem;
    border: 1px solid var(--color-border, #e5e7eb);
    border-radius: 1rem;
    background: var(--color-background, #ffffff);
    box-shadow: 0 20px 40px -8px rgb(0 0 0 / 0.24), 0 2px 10px rgb(0 0 0 / 0.08);
    opacity: 0;
    transform: translateY(10px) scale(0.96);
    pointer-events: none;
    transition: opacity 0.2s ease, transform 0.2s ease;
}
#kg-live-chat-menu::after {
    content: '';
    position: absolute;
    left: 1.7rem;
    top: 100%;
    width: 14px;
    height: 14px;
    margin-top: -8px;
    background: inherit;
    border-right: 1px solid var(--color-border, #e5e7eb);
    border-bottom: 1px solid var(--color-border, #e5e7eb);
    border-bottom-right-radius: 3px;
    transform: rotate(45deg);
}
#kg-live-chat.is-open #kg-live-chat-menu {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}
.kg-live-chat-heading {
    margin: 0.1rem 0.5rem 0.55rem;
    padding-bottom: 0.55rem;
    border-bottom: 1px solid var(--color-border, #e5e7eb);
}
.kg-live-chat-option {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.65rem 0.6rem;
    border-radius: 0.7rem;
    color: inherit;
    font-size: 0.82rem;
    font-weight: 700;
    opacity: 0;
    transform: translateX(-8px);
    transition: background-color 0.15s ease, transform 0.15s ease, opacity 0.2s ease;
}
#kg-live-chat.is-open .kg-live-chat-option {
    opacity: 1;
    transform: translateX(0);
}
.kg-live-chat-option:nth-of-type(1) { transition-delay: 0.04s; }
.kg-live-chat-option:nth-of-type(2) { transition-delay: 0.09s; }
.kg-live-chat-option:nth-of-type(3) { transition-delay: 0.14s; }
.kg-live-chat-option:hover {
    background: var(--color-secondary, #f1f5f9);
    transform: translateX(3px);
}
.kg-live-chat-icon {
    display: grid;
    width: 2.25rem;
    height: 2.25rem;
    place-items: center;
    border-radius: 9999px;
    color: #fff;
    box-shadow: 0 4px 10px -2px rgb(0 0 0 / 0.4);
    transition: transform 0.15s ease;
}
.kg-live-chat-option:hover .kg-live-chat-icon { transform: scale(1.08); }
#kg-live-chat-toggle {
    position: relative;
    display: grid;
    width: 4rem;
    height: 4rem;
    place-items: center;
    border: 0;
    border-radius: 9999px;
    padding: 0;
    color: #fff;
    font-size: 1.75rem;
    box-shadow: 0 6px 18px rgb(0 0 0 / 0.35), 0 0 0 1px rgb(255 255 255 / 0.08);
}
#kg-live-chat-toggle::before {
    content: '';
    position: absolute;
    inset: -8px;
    border-radius: 9999px;
    background: var(--kg-chat-pulse-color, #25D366);
    opacity: 0;
    animation: kg-live-chat-pulse 2.8s ease-out infinite;
    pointer-events: none;
    z-index: -1;
}
@keyframes kg-live-chat-pulse {
    0% { opacity: 0.45; transform: scale(0.82); }
    65% { opacity: 0; transform: scale(1.5); }
    100% { opacity: 0; transform: scale(1.5); }
}
@media (max-width: 639px) {
    #kg-live-chat { bottom: 5.5rem; }
}

.kg-live-chat-toggle-icon { display: grid; place-items: center; transition: opacity 0.18s ease, transform 0.18s ease; }
.kg-live-chat-toggle-icon.is-changing { opacity: 0; transform: scale(0.65) rotate(-12deg); }

.kg-live-chat-tooltip {
    position: absolute;
    left: calc(100% + 0.85rem);
    bottom: 0.85rem;
    padding: 0.6rem 0.9rem;
    border-radius: 0.75rem;
    border: 1px solid var(--color-border, #e5e7eb);
    background: var(--color-background, #ffffff);
    box-shadow: 0 10px 24px rgb(0 0 0 / 0.16);
    font-size: 0.8rem;
    font-weight: 700;
    white-space: nowrap;
    color: inherit;
    opacity: 0;
    transform: translateX(-6px) scale(0.96);
    pointer-events: none;
    transition: opacity 0.25s ease, transform 0.25s ease;
    cursor: pointer;
    z-index: 61;
}
.kg-live-chat-tooltip.is-visible {
    opacity: 1;
    transform: translateX(0) scale(1);
    pointer-events: auto;
}
.kg-live-chat-tooltip::after {
    content: '';
    position: absolute;
    left: -5px;
    bottom: 1.3rem;
    width: 10px;
    height: 10px;
    background: inherit;
    border-left: 1px solid var(--color-border, #e5e7eb);
    border-bottom: 1px solid var(--color-border, #e5e7eb);
    transform: rotate(45deg);
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var whatsappNumber = @json(preg_replace('/[^0-9]/', '', (string) ($whatsappNumber ?? '8801700000001')));

    function enableProductImageMagnifier() {
        if (!window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
            return;
        }

        document.querySelectorAll('main img').forEach(function(image) {
            var zoomArea = image.closest('.group.overflow-hidden.rounded-md.bg-surface');

            // Limit the effect to the large image on a product detail page, not product cards.
            if (!zoomArea || zoomArea.closest('article') || image.dataset.magnifierReady) {
                return;
            }

            image.dataset.magnifierReady = 'true';
            zoomArea.classList.add('product-image-magnifier');
            image.style.transition = 'transform 80ms ease-out';

            zoomArea.addEventListener('mousemove', function(event) {
                var rect = zoomArea.getBoundingClientRect();
                var x = ((event.clientX - rect.left) / rect.width) * 100;
                var y = ((event.clientY - rect.top) / rect.height) * 100;

                image.style.transformOrigin = x + '% ' + y + '%';
                image.style.transform = 'scale(2)';
            });

            zoomArea.addEventListener('mouseleave', function() {
                image.style.transformOrigin = '50% 50%';
                image.style.transform = 'scale(1)';
            });
        });
    }

    function applySingleProductButtonStyles() {
        document.querySelectorAll('button, a').forEach(function(el) {
            var text = (el.textContent || '').trim();
            if (text.includes('Buy Now')) {
                el.style.backgroundColor = '#24272c';
                el.style.color = '#ffffff';
                el.style.borderRadius = '9999px';
                el.classList.add('rounded-full', 'font-bold');

                // If button has nested price or icons, simplify it to just "Buy Now" text
                if (el.querySelector('.tabular-nums') || el.children.length > 0) {
                    // Check if it's the main single product page big Buy Now button
                    if (el.classList.contains('bg-primary') || el.closest('.min-w-56') || el.classList.contains('sm:min-w-56') || el.getAttribute('data-tsd-source')?.includes('product.$slug.tsx:177')) {
                        el.innerHTML = '<span class="text-sm font-bold">Buy Now</span>';
                        
                        // Add WhatsApp button next to it if not already added
                        if (!el.nextElementSibling || !el.nextElementSibling.classList.contains('whatsapp-btn')) {
                            var waBtn = document.createElement('a');
                            var productName = document.querySelector('h1') ? document.querySelector('h1').textContent : 'this product';
                            waBtn.href = "https://wa.me/" + whatsappNumber + "?text=" + encodeURIComponent("I want to order " + productName);
                            waBtn.target = "_blank";
                            waBtn.className = "whatsapp-btn inline-flex items-center justify-center gap-2 whitespace-nowrap text-sm font-bold transition-colors shadow-sm h-12 px-6 flex-1 rounded-full sm:flex-none";
                            waBtn.style.backgroundColor = "#25D366";
                            waBtn.style.color = "#ffffff";
                            waBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg> Order on WhatsApp';
                            el.parentNode.insertBefore(waBtn, el.nextSibling);
                        }
                    }
                }
            }

            // Hide "Save to wishlist" button on single product page (it lacks the 'absolute' class which card wishlist buttons have)
            if (el.getAttribute('aria-label') === 'Save to wishlist' && !el.classList.contains('absolute')) {
                el.style.display = 'none';
            }
        });
        
        // Hide the feature section (Free next-day, 2-year warranty, etc.)
        document.querySelectorAll('ul').forEach(function(el) {
            var text = el.textContent || '';
            if (text.includes('Free next') && text.includes('warranty')) {
                el.style.display = 'none';
            }
        });
        
        // Replace $ with ৳ globally in text nodes
        function replaceDollarWithTaka(node) {
            if (node.nodeType === 3) { // Text node
                if (node.nodeValue.includes('$')) {
                    node.nodeValue = node.nodeValue.replace(/\$(\d+)/g, '৳ $1');
                }
            } else if (node.nodeType === 1 && node.nodeName !== 'SCRIPT' && node.nodeName !== 'STYLE') {
                node.childNodes.forEach(replaceDollarWithTaka);
            }
        }
        replaceDollarWithTaka(document.body);
    }
    applySingleProductButtonStyles();
    enableProductImageMagnifier();

    var liveChatWidget = document.getElementById('kg-live-chat');
    if (liveChatWidget) {
        var liveChatToggle = document.getElementById('kg-live-chat-toggle');
        var liveChatToggleIcon = document.getElementById('kg-live-chat-toggle-icon');
        var liveChatIcons = {
            whatsapp: '<svg width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>',
            call: '<svg width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .8 2.9a2 2 0 0 1-.4 2.1L8.2 10a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.4 1.9.7 2.9.8a2 2 0 0 1 1.5 1.8Z"/></svg>'
        };
        liveChatToggle.addEventListener('click', function(event) {
            event.stopPropagation();
            var isOpen = liveChatWidget.classList.toggle('is-open');
            liveChatToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
        document.addEventListener('click', function(event) {
            if (liveChatWidget.classList.contains('is-open') && !liveChatWidget.contains(event.target)) {
                liveChatWidget.classList.remove('is-open');
                liveChatToggle.setAttribute('aria-expanded', 'false');
            }
        });

        var showingWhatsApp = true;
        window.setInterval(function() {
            liveChatToggleIcon.classList.add('is-changing');
            window.setTimeout(function() {
                showingWhatsApp = !showingWhatsApp;
                liveChatToggleIcon.innerHTML = showingWhatsApp ? liveChatIcons.whatsapp : liveChatIcons.call;
                liveChatToggleIcon.classList.remove('is-changing');
            }, 180);
        }, 2500);

        function playLiveChatChime() {
            try {
                var AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return;
                var ctx = new AudioCtx();
                [880, 1108.73].forEach(function(freq, i) {
                    var osc = ctx.createOscillator();
                    var gain = ctx.createGain();
                    osc.type = 'sine';
                    osc.frequency.value = freq;
                    var startTime = ctx.currentTime + i * 0.12;
                    gain.gain.setValueAtTime(0, startTime);
                    gain.gain.linearRampToValueAtTime(0.18, startTime + 0.02);
                    gain.gain.exponentialRampToValueAtTime(0.0001, startTime + 0.35);
                    osc.connect(gain);
                    gain.connect(ctx.destination);
                    osc.start(startTime);
                    osc.stop(startTime + 0.4);
                });
                window.setTimeout(function() { ctx.close(); }, 800);
            } catch (e) {}
        }

        var liveChatTooltip = document.getElementById('kg-live-chat-tooltip');
        if (liveChatTooltip) {
            if (sessionStorage.getItem('kg_chat_tooltip_shown')) {
                liveChatTooltip.remove();
            } else {
                var dismissLiveChatTooltip = function() {
                    liveChatTooltip.classList.remove('is-visible');
                    window.setTimeout(function() { liveChatTooltip.remove(); }, 260);
                };
                window.setTimeout(function() {
                    if (liveChatWidget.classList.contains('is-open')) return;
                    liveChatTooltip.classList.add('is-visible');
                    playLiveChatChime();
                    sessionStorage.setItem('kg_chat_tooltip_shown', '1');
                    window.setTimeout(dismissLiveChatTooltip, 5000);
                }, 2500);
                liveChatTooltip.addEventListener('click', function() {
                    dismissLiveChatTooltip();
                    liveChatToggle.click();
                });
                liveChatToggle.addEventListener('click', dismissLiveChatTooltip);
            }
        }
    }

    setTimeout(applySingleProductButtonStyles, 300);
});
</script>

@php
    $liveChatWhatsapp = preg_replace('/[^0-9]/', '', (string) ($liveChatWhatsappNumber ?? ''));
    $liveChatCall = preg_replace('/[^0-9+]/', '', (string) ($liveChatCallNumber ?? ''));
    $showWhatsapp = ($liveChatWhatsappEnabled ?? '1') === '1' && $liveChatWhatsapp !== '';
    $showMessenger = ($liveChatMessengerEnabled ?? '0') === '1' && !empty($liveChatMessengerUrl);
    $showCall = ($liveChatCallEnabled ?? '1') === '1' && $liveChatCall !== '';
@endphp

@if (($liveChatEnabled ?? '1') === '1' && ($showWhatsapp || $showMessenger || $showCall))
    <aside id="kg-live-chat" aria-label="Live chat options" style="--kg-chat-pulse-color: {{ $liveChatWhatsappColor ?? '#25D366' }}">
        <div id="kg-live-chat-tooltip" class="kg-live-chat-tooltip" role="status">Chat with us 👋</div>
        <div id="kg-live-chat-menu" role="menu">
            <p class="kg-live-chat-heading px-2 text-xs font-bold text-muted-foreground">How can we help? 💬</p>

            @if ($showWhatsapp)
                <a class="kg-live-chat-option" href="https://wa.me/{{ $liveChatWhatsapp }}?text={{ rawurlencode('Hello, I need help.') }}" target="_blank" rel="noopener noreferrer" role="menuitem">
                    <span class="kg-live-chat-icon" style="background:{{ $liveChatWhatsappColor ?? '#25D366' }}" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg></span>
                    <span>Chat on WhatsApp</span>
                </a>
            @endif

            @if ($showMessenger)
                <a class="kg-live-chat-option" href="{{ $liveChatMessengerUrl }}" target="_blank" rel="noopener noreferrer" role="menuitem">
                    <span class="kg-live-chat-icon" style="background:{{ $liveChatMessengerColor ?? '#0084FF' }}" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/></svg></span>
                    <span>Chat on Messenger</span>
                </a>
            @endif

            @if ($showCall)
                <a class="kg-live-chat-option" href="tel:{{ $liveChatCall }}" role="menuitem">
                    <span class="kg-live-chat-icon" style="background:{{ $liveChatCallColor ?? '#4f46e5' }}" aria-hidden="true"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .8 2.9a2 2 0 0 1-.4 2.1L8.2 10a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.4 1.9.7 2.9.8a2 2 0 0 1 1.5 1.8Z"/></svg></span>
                    <span>Call us</span>
                </a>
            @endif
        </div>

        <button id="kg-live-chat-toggle" type="button" aria-label="Toggle live chat options" aria-expanded="false" aria-controls="kg-live-chat-menu" style="background:{{ $liveChatToggleColor ?? '#24272c' }}">
            <span id="kg-live-chat-toggle-icon" class="kg-live-chat-toggle-icon" aria-hidden="true"><svg width="29" height="29" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg></span>
        </button>
    </aside>
@endif
