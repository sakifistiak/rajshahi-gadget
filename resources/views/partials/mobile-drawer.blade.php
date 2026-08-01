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
                <button class="kg-drawer-group-btn" data-target="submenu-laptop">
                    <span style="display:flex;align-items:center;gap:10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m14 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16"/></svg>
                        Laptop
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>
                </button>
                <div class="kg-drawer-submenu" id="submenu-laptop">
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
            $drawerStoreText = \App\Models\SiteSetting::getValue('mobile_drawer_store_info', $mobileDrawerStoreInfo ?? 'Shop No: 136 | Ground Floor | Computer City Center (Ex Multiplan Center) | New Elephant Road, Dhaka-1205');
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
        drawer.classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeDrawer() {
        drawer.classList.remove('open');
        document.body.style.overflow = '';
    }

    // Hamburger button — works for both home and shop pages
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
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
                            waBtn.href = "https://wa.me/8801700000001?text=" + encodeURIComponent("I want to order " + productName);
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
    setTimeout(applySingleProductButtonStyles, 300);
});
</script>
