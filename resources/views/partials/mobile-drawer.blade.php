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

/* Footer strip */
#kg-mobile-footer {
    margin-top: auto;
    padding: 16px 20px;
    border-top: 1px solid var(--color-border, #e5e7eb);
    font-size: 11px;
    color: var(--color-muted-foreground, #71717a);
}
html.dark #kg-mobile-footer { border-top-color: #1e2330; }
</style>

<!-- Mobile Drawer HTML -->
<div id="kg-mobile-drawer" role="dialog" aria-modal="true" aria-label="Main menu">
    <div id="kg-mobile-backdrop"></div>
    <div id="kg-mobile-panel">
        <!-- Header -->
        <div id="kg-mobile-header">
            <img id="kg-drawer-logo"
                 src="/media/b3ca13-kg-lockup-v2.png"
                 alt="Khan Gadget"
                 style="height:36px;width:auto;object-fit:contain;">
            <button id="kg-mobile-close" aria-label="Close menu">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>
        </div>

        <!-- Nav sections -->
        <div class="kg-drawer-section">
            <p class="kg-drawer-section-title">Shop</p>

            <!-- Laptop group -->
            <div class="kg-drawer-group">
                <button class="kg-drawer-group-btn" data-target="submenu-laptop">
                    <span style="display:flex;align-items:center;gap:10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 16V7a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v9m14 0H4m16 0 1.28 2.55a1 1 0 0 1-.9 1.45H3.62a1 1 0 0 1-.9-1.45L4 16"/>
                        </svg>
                        Laptop
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
                </button>
                <div class="kg-drawer-submenu open" id="submenu-laptop">
                    <a href="/shop?condition=intact" class="kg-drawer-subitem">
                        <span class="kg-drawer-subitem-dot"></span>
                        BRAND NEW INTACT BOX
                    </a>
                    <a href="/shop?condition=without-box" class="kg-drawer-subitem">
                        <span class="kg-drawer-subitem-dot"></span>
                        BRAND NEW WITHOUT BOX
                    </a>
                    <a href="/shop?condition=pre-owned" class="kg-drawer-subitem">
                        <span class="kg-drawer-subitem-dot"></span>
                        PRE-OWNED
                    </a>
                </div>
            </div>

            <!-- Brands group -->
            <div class="kg-drawer-group">
                <button class="kg-drawer-group-btn" data-target="submenu-brands">
                    <span style="display:flex;align-items:center;gap:10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2"
                             stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"/>
                            <path d="M7 7h.01"/>
                        </svg>
                        Brands
                    </span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6"/>
                    </svg>
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

        <!-- Quick links -->
        <div class="kg-drawer-section" style="padding:0;">
            <a href="/" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M15 21v-8a1 1 0 0 0-1-1h-4a1 1 0 0 0-1 1v8"/>
                    <path d="M3 10a2 2 0 0 1 .709-1.528l7-6a2 2 0 0 1 2.582 0l7 6A2 2 0 0 1 21 10v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
                Home
            </a>
            <a href="/shop" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <rect width="7" height="7" x="3" y="3" rx="1"/>
                    <rect width="7" height="7" x="14" y="3" rx="1"/>
                    <rect width="7" height="7" x="14" y="14" rx="1"/>
                    <rect width="7" height="7" x="3" y="14" rx="1"/>
                </svg>
                All Products
            </a>
            <a href="/customer-feedback" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"/>
                </svg>
                Customer Feedback
            </a>
            <a href="/about" class="kg-drawer-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2"
                     stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <path d="M12 16v-4"/><path d="M12 8h.01"/>
                </svg>
                About Us
            </a>
        </div>

        <!-- Footer -->
        <div id="kg-mobile-footer">
            Sat – Thu &nbsp;·&nbsp; 10:00 AM – 9:00 PM
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



