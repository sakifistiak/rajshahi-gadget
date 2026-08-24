@php
    if (defined('KG_MOBILE_DRAWER_LOADED')) {
        return;
    }
    define('KG_MOBILE_DRAWER_LOADED', true);
@endphp

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
<div id="kg-mobile-drawer" class="notranslate" translate="no" role="dialog" aria-modal="true" aria-label="Main menu">
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

        <!-- Section 3: Info Links (admin-configurable — Admin > Settings > "Mobile Slide Menu Info Links") -->
        <div class="kg-drawer-section" style="padding:0;">
            @foreach($mobileDrawerInfoLinks ?? [] as $infoLink)
                <a href="{{ $infoLink['url'] ?? '#' }}" class="kg-drawer-link">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! \App\Support\DrawerIcons::svg($infoLink['icon'] ?? 'link') !!}</svg>
                    {{ $infoLink['label'] ?? '' }}
                </a>
            @endforeach
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
            <a href="tel:{{ \App\Support\PhoneNumber::tel($mobileMenuContact) }}" class="kg-hotline-btn">
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
    width: 272px;
    padding: 1rem;
    border-radius: 1.25rem;
    background: var(--color-background, #ffffff);
    box-shadow: 0 24px 48px -12px rgb(0 0 0 / 0.28), 0 4px 16px rgb(0 0 0 / 0.1);
    opacity: 0;
    transform: translateY(10px) scale(0.96);
    pointer-events: none;
    transition: opacity 0.2s ease, transform 0.2s ease;
}
#kg-live-chat-menu::after {
    content: '';
    position: absolute;
    left: 2rem;
    top: 100%;
    width: 16px;
    height: 16px;
    margin-top: -9px;
    background: inherit;
    border-bottom-right-radius: 4px;
    transform: rotate(45deg);
}
#kg-live-chat.is-open #kg-live-chat-menu {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}
.kg-live-chat-heading {
    margin: 0.15rem 0.35rem 0.75rem;
    padding-bottom: 0.65rem;
    border-bottom: 1px solid var(--color-border, #e5e7eb);
    font-size: 0.95rem;
    font-weight: 800;
    color: var(--color-foreground, #09090b);
    letter-spacing: -0.01em;
}
.kg-live-chat-option {
    display: flex;
    align-items: center;
    gap: 0.85rem;
    padding: 0.75rem 0.65rem;
    border-radius: 0.9rem;
    color: var(--color-foreground, #09090b);
    font-size: 0.85rem;
    font-weight: 700;
    opacity: 0;
    transform: translateX(-8px);
    transition: background-color 0.15s ease, transform 0.15s ease, opacity 0.2s ease, box-shadow 0.15s ease;
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
    box-shadow: 0 2px 10px -3px rgb(0 0 0 / 0.1);
}
.kg-live-chat-icon {
    display: grid;
    width: 2.5rem;
    height: 2.5rem;
    place-items: center;
    border-radius: 9999px;
    color: #fff;
    box-shadow: 0 4px 12px -2px rgb(0 0 0 / 0.4);
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
    #kg-live-chat { bottom: 5rem; }

    /* Match the two floating chat buttons (WhatsApp/Call toggle on the left,
       the agent avatar "Live Chat" button on the right) to the same, more
       compact size on mobile so they look like a matched pair. */
    #kg-live-chat-toggle {
        width: 3rem;
        height: 3rem;
        font-size: 1.3rem;
    }
    #kg-live-chat-toggle svg {
        width: 22px;
        height: 22px;
    }
    .agent-float {
        height: 3rem;
        width: 3rem;
    }
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

/* ── Customer Chat Panel ── */
#kg-customer-chat {
    position: fixed;
    right: 1rem;
    bottom: 1rem;
    z-index: 80;
    display: flex;
    flex-direction: column;
    width: min(360px, calc(100vw - 2rem));
    border: 1px solid #e5e7eb;
    border-radius: 1.1rem;
    background: #fff;
    box-shadow: 0 20px 50px rgb(0 0 0 / .25);
    overflow: hidden;
    opacity: 0;
    transform: translateY(16px) scale(.96);
    pointer-events: none;
    visibility: hidden;
    transform-origin: bottom right;
    transition: opacity .28s cubic-bezier(.16,1,.3,1), transform .28s cubic-bezier(.16,1,.3,1), visibility .28s;
}
#kg-customer-chat.is-visible {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
    visibility: visible;
}
#kg-customer-chat-header {
    padding: 1rem 1.1rem;
    background: linear-gradient(135deg, #24272c, #14161a);
    color: #fff;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .75rem;
}
#kg-customer-chat-header-info { display: flex; align-items: center; gap: .65rem; min-width: 0; }
#kg-customer-chat-avatar {
    position: relative;
    display: grid;
    place-items: center;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 9999px;
    background: rgba(255,255,255,.12);
    flex-shrink: 0;
}
#kg-customer-chat-status-dot {
    position: absolute;
    right: -1px;
    bottom: -1px;
    width: .6rem;
    height: .6rem;
    border-radius: 9999px;
    background: #22c55e;
    border: 2px solid #24272c;
}
#kg-customer-chat-header-text { min-width: 0; }
#kg-customer-chat-header-text strong { display: block; font-size: .85rem; font-weight: 700; line-height: 1.3; }
#kg-customer-chat-header-text small { display: block; font-size: .7rem; color: rgba(255,255,255,.65); line-height: 1.3; margin-top: 1px; }
#kg-close-customer-chat {
    display: grid;
    place-items: center;
    width: 1.85rem;
    height: 1.85rem;
    background: rgba(255,255,255,.1);
    border: 0;
    border-radius: 9999px;
    color: #fff;
    cursor: pointer;
    flex-shrink: 0;
    transition: background .15s;
}
#kg-close-customer-chat:hover { background: rgba(255,255,255,.2); }
#kg-customer-chat-messages { height: 280px; overflow-y: auto; padding: .9rem; background: #f8fafc; display: flex; flex-direction: column; }
.kg-chat-message { max-width: 80%; margin-bottom: .5rem; padding: .55rem .8rem; border-radius: .8rem; font-size: .82rem; line-height: 1.45; word-wrap: break-word; animation: kg-chat-msg-in .2s ease; }
.kg-chat-message.customer { margin-left: auto; background: #24272c; color: #fff; border-bottom-right-radius: .25rem; }
.kg-chat-message.agent { margin-right: auto; background: #fff; border: 1px solid #e5e7eb; color: #1f2937; border-bottom-left-radius: .25rem; }
@keyframes kg-chat-msg-in {
    from { opacity: 0; transform: translateY(6px); }
    to { opacity: 1; transform: translateY(0); }
}
#kg-customer-chat-form { display: flex; align-items: center; gap: .5rem; padding: .7rem; border-top: 1px solid #e5e7eb; background: #fff; }
#kg-customer-chat-form input { min-width: 0; flex: 1; border: 1px solid #e2e2e5; background: #f4f4f5; border-radius: 9999px; padding: .6rem 1rem; font-size: .82rem; outline: none; transition: border-color .15s, background .15s; }
#kg-customer-chat-form input:focus { border-color: #24272c; background: #fff; }
#kg-customer-chat-form button {
    display: grid;
    place-items: center;
    width: 2.4rem;
    height: 2.4rem;
    flex-shrink: 0;
    border: 0;
    border-radius: 9999px;
    background: #24272c;
    color: #fff;
    cursor: pointer;
    transition: transform .15s, background .15s;
}
#kg-customer-chat-form button:hover { background: #14161a; transform: scale(1.05); }
#kg-customer-chat-start { padding: 1rem; display: none; }
#kg-customer-chat-start.is-visible { display: block; }
#kg-customer-chat-start input { width: 100%; margin: .35rem 0; border: 1px solid #d1d5db; border-radius: .5rem; padding: .6rem; font-size: .8rem; box-sizing: border-box; }
#kg-customer-chat-start button { width: 100%; margin-top: .5rem; border: 0; border-radius: .5rem; padding: .65rem; background: #24272c; color: #fff; font-weight: 700; cursor: pointer; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
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
                        // The "Order on WhatsApp" button is now server-rendered directly on the
                        // product detail page (aligned in a grid with Add to Cart), so it is no
                        // longer injected here.
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
            whatsapp: '<svg width="29" height="29" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.571-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .105 5.36.103 11.943c0 2.105.549 4.16 1.595 5.973L0 24l6.335-1.652a11.882 11.882 0 005.71 1.447h.006c6.585 0 11.94-5.36 11.943-11.943a11.874 11.874 0 00-3.474-8.403"/></svg>',
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
    $liveChatWhatsapp = \App\Support\PhoneNumber::whatsapp($liveChatWhatsappNumber ?? '');
    $liveChatCall = \App\Support\PhoneNumber::tel($liveChatCallNumber ?? '');
    $showWhatsapp = ($liveChatWhatsappEnabled ?? '1') === '1' && $liveChatWhatsapp !== '';
    $showMessenger = ($liveChatMessengerEnabled ?? '0') === '1' && !empty($liveChatMessengerUrl);
    $showCall = ($liveChatCallEnabled ?? '1') === '1' && $liveChatCall !== '';
@endphp

@if (($liveChatEnabled ?? '1') === '1')
@if ($showWhatsapp || $showMessenger || $showCall)
    <aside id="kg-live-chat" aria-label="Live chat options" style="--kg-chat-pulse-color: {{ $liveChatWhatsappColor ?? '#25D366' }}">
        <div id="kg-live-chat-tooltip" class="kg-live-chat-tooltip" role="status">Chat With Us 👋</div>
        <div id="kg-live-chat-menu" role="menu">
            <p class="kg-live-chat-heading">How Can We Help? 💬</p>

            @if ($showWhatsapp)
                <a class="kg-live-chat-option" href="https://wa.me/{{ $liveChatWhatsapp }}?text={{ rawurlencode('Hello, I need help.') }}" target="_blank" rel="noopener noreferrer" role="menuitem">
                    <span class="kg-live-chat-icon" style="background:{{ $liveChatWhatsappColor ?? '#25D366' }}" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.571-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .105 5.36.103 11.943c0 2.105.549 4.16 1.595 5.973L0 24l6.335-1.652a11.882 11.882 0 005.71 1.447h.006c6.585 0 11.94-5.36 11.943-11.943a11.874 11.874 0 00-3.474-8.403"/></svg></span>
                    <span>Chat On WhatsApp</span>
                </a>
            @endif

            @if ($showMessenger)
                <a class="kg-live-chat-option" href="{{ $liveChatMessengerUrl }}" target="_blank" rel="noopener noreferrer" role="menuitem">
                    <span class="kg-live-chat-icon" style="background:{{ $liveChatMessengerColor ?? '#0084FF' }}" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.536 21.686a.5.5 0 0 0 .937-.024l6.5-19a.496.496 0 0 0-.635-.635l-19 6.5a.5.5 0 0 0-.024.937l7.93 3.18a2 2 0 0 1 1.112 1.11z"/><path d="m21.854 2.147-10.94 10.939"/></svg></span>
                    <span>Chat On Messenger</span>
                </a>
            @endif

            @if ($showCall)
                <a class="kg-live-chat-option" href="tel:{{ $liveChatCall }}" role="menuitem">
                    <span class="kg-live-chat-icon" style="background:{{ $liveChatCallColor ?? '#4f46e5' }}" aria-hidden="true"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .8 2.9a2 2 0 0 1-.4 2.1L8.2 10a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.4 1.9.7 2.9.8a2 2 0 0 1 1.5 1.8Z"/></svg></span>
                    <span>Call Us</span>
                </a>
            @endif
        </div>

        <button id="kg-live-chat-toggle" type="button" aria-label="Toggle live chat options" aria-expanded="false" aria-controls="kg-live-chat-menu" style="background:{{ $liveChatToggleColor ?? '#24272c' }}">
            <span id="kg-live-chat-toggle-icon" class="kg-live-chat-toggle-icon" aria-hidden="true"><svg width="29" height="29" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.372-.01-.571-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884M20.52 3.449C18.24 1.245 15.24 0 12.045 0 5.463 0 .105 5.36.103 11.943c0 2.105.549 4.16 1.595 5.973L0 24l6.335-1.652a11.882 11.882 0 005.71 1.447h.006c6.585 0 11.94-5.36 11.943-11.943a11.874 11.874 0 00-3.474-8.403"/></svg></span>
        </button>
    </aside>
@endif

    <!-- Right-side Floating Live Chat Support Widget Button -->
    <div id="kg-live-chat-floating-btn" class="fixed bottom-20 right-4 z-50 flex flex-col items-center sm:bottom-6">
        <button type="button" class="flex flex-col items-center transition-transform hover:scale-105 active:scale-95 cursor-pointer" aria-label="Open live chat" title="Need help?">
            <img src="/assets/support-agent-BWJyOWv2.png" alt="Live chat support agent" width="512" height="512" loading="lazy" class="agent-float h-20 w-20 select-none object-contain drop-shadow-lg sm:h-24 sm:w-24" />
            <span class="-mt-1 rounded-full bg-foreground px-2.5 py-1 text-[10px] font-semibold uppercase tracking-wider text-background shadow-sm">Live Chat</span>
        </button>
    </div>

    <section id="kg-customer-chat" aria-label="Customer chat">
        <div id="kg-customer-chat-header">
            <div id="kg-customer-chat-header-info">
                <span id="kg-customer-chat-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                    <span id="kg-customer-chat-status-dot" aria-hidden="true"></span>
                </span>
                <span id="kg-customer-chat-header-text">
                    <strong>{{ $siteName ?? 'Khan Gadget' }} Support</strong>
                    <small>We usually reply in a few minutes</small>
                </span>
            </div>
            <button type="button" id="kg-close-customer-chat" aria-label="Close chat">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>
        <div id="kg-customer-chat-start" class="is-visible">
            <p class="text-sm">Start a conversation</p>
            <input id="kg-chat-name" placeholder="Your name" maxlength="100">
            <input id="kg-chat-phone" placeholder="Phone (optional)" maxlength="30">
            <button type="button" id="kg-chat-start-button">Start Chat</button>
        </div>
        <div id="kg-customer-chat-body" style="display:none">
            <div id="kg-customer-chat-messages"></div>
            <form id="kg-customer-chat-form">
                <input id="kg-chat-input" placeholder="Write a message..." maxlength="2000" autocomplete="off">
                <button type="submit" aria-label="Send message">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m22 2-7 20-4-9-9-4Z"/><path d="M22 2 11 13"/></svg>
                </button>
            </form>
        </div>
    </section>
    <script>
    (function () {
        function initCustomerChat() {
            const box = document.getElementById('kg-customer-chat'),
                  start = document.getElementById('kg-customer-chat-start'),
                  body = document.getElementById('kg-customer-chat-body'),
                  list = document.getElementById('kg-customer-chat-messages');
            if (!box) return;
            let timer;

            function render(data) {
                list.innerHTML = (data.messages || []).map(m =>
                    '<div class="kg-chat-message ' + m.sender_type + '">' + String(m.body).replace(/[&<>]/g, s => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;' }[s])) + '</div>'
                ).join('');
                list.scrollTop = list.scrollHeight;
            }

            function load() {
                fetch('{{ route('chat.messages') }}').then(r => r.ok ? r.json() : null).then(d => {
                    if (d) { start.classList.remove('is-visible'); body.style.display = 'block'; render(d); }
                }).catch(() => {});
            }

            function openCustomerChat() {
                box.classList.add('is-visible');
                load();
                if (!timer) timer = setInterval(load, 4000);
            }
            window.kgOpenCustomerChat = openCustomerChat;
            document.querySelectorAll('[aria-label="Open live chat"], #kg-live-chat-floating-btn button').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    openCustomerChat();
                });
            });

            const closeBtn = document.getElementById('kg-close-customer-chat');
            if (closeBtn) closeBtn.addEventListener('click', () => box.classList.remove('is-visible'));

            const startBtn = document.getElementById('kg-chat-start-button');
            if (startBtn) startBtn.addEventListener('click', () => {
                let nameInput = document.getElementById('kg-chat-name');
                if (!nameInput.value.trim()) { nameInput.focus(); return; }
                fetch('{{ route('chat.start') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ name: nameInput.value, phone: document.getElementById('kg-chat-phone').value })
                }).then(r => r.ok ? r.json() : Promise.reject()).then(d => {
                    start.classList.remove('is-visible');
                    body.style.display = 'block';
                    render(d);
                    if (!timer) timer = setInterval(load, 4000);
                }).catch(() => {});
            });

            const chatForm = document.getElementById('kg-customer-chat-form');
            if (chatForm) chatForm.addEventListener('submit', e => {
                e.preventDefault();
                let input = document.getElementById('kg-chat-input');
                if (!input.value.trim()) return;
                fetch('{{ route('chat.messages.send') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ body: input.value })
                }).then(r => r.json()).then(d => { input.value = ''; render(d); });
            });
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCustomerChat);
        } else {
            initCustomerChat();
        }
    })();
    </script>
@endif
