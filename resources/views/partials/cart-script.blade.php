<!-- Global Interactive Shopping Cart System - Premium Fly-to-Cart Animation -->
<style>
@keyframes kgFloatUp {
    0% { transform: translateY(0) scale(1); opacity: 1; }
    50% { transform: translateY(-25px) scale(1.15); opacity: 0.9; }
    100% { transform: translateY(-45px) scale(0.9); opacity: 0; }
}
@keyframes kgBadgeBounce {
    0% { transform: scale(1); }
    30% { transform: scale(1.8); }
    50% { transform: scale(0.8); }
    70% { transform: scale(1.3); }
    100% { transform: scale(1); }
}
@keyframes kgSuccessBannerIn {
    0% { transform: translateY(30px) scale(0.9); opacity: 0; }
    60% { transform: translateY(-6px) scale(1.02); opacity: 1; }
    100% { transform: translateY(0) scale(1); opacity: 1; }
}
@keyframes kgSuccessBannerOut {
    0% { transform: translateY(0) scale(1); opacity: 1; }
    100% { transform: translateY(30px) scale(0.9); opacity: 0; }
}
@keyframes kgCheckDraw {
    0% { stroke-dashoffset: 24; }
    100% { stroke-dashoffset: 0; }
}
@keyframes kgRipple {
    0% { transform: scale(0.6); opacity: 0.5; }
    100% { transform: scale(2.5); opacity: 0; }
}
.kg-animate-float {
    animation: kgFloatUp 0.85s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.kg-badge-bounce {
    animation: kgBadgeBounce 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}
.kg-success-banner-in {
    animation: kgSuccessBannerIn 0.45s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.kg-success-banner-out {
    animation: kgSuccessBannerOut 0.35s ease-out forwards;
}
.kg-check-draw {
    stroke-dasharray: 24;
    stroke-dashoffset: 24;
    animation: kgCheckDraw 0.4s 0.15s ease-out forwards;
}
.kg-ripple {
    animation: kgRipple 0.6s ease-out forwards;
}
</style>

<!-- Success Banner Container (BOTTOM CENTER) -->
<div id="kg-success-banner-container" class="fixed bottom-20 sm:bottom-8 left-0 right-0 z-[99999] flex justify-center pointer-events-none px-4"></div>

<script>
(function() {
    var CART_KEY = 'kg_shopping_cart';

    function getCart() {
        try { return JSON.parse(localStorage.getItem(CART_KEY) || '[]'); } catch(e) { return []; }
    }
    function saveCart(cart) {
        try { localStorage.setItem(CART_KEY, JSON.stringify(cart)); } catch(e) {}
    }
    function addToCart(product) {
        var cart = getCart();
        var existing = cart.find(function(item) { return item.slug === product.slug; });
        if (existing) { existing.quantity = (existing.quantity || 1) + 1; }
        else { cart.push({ slug: product.slug, name: product.name, price: product.price || 0, image: product.image || '', quantity: 1 }); }
        saveCart(cart);
    }
    function getCartCount() {
        return getCart().reduce(function(s, i) { return s + (i.quantity || 1); }, 0);
    }
    function formatBDT(n) {
        return '৳ ' + n.toLocaleString('en-IN');
    }

    // ── Badge (bigger, clearer, red with white border) ──
    function updateNavbarBadge(animate) {
        var count = getCartCount();
        document.querySelectorAll('a[href="/cart"], button[aria-label*="Cart"]').forEach(function(link) {
            if (link.closest('header') || link.closest('nav')) {
                var badge = link.querySelector('.kg-cart-badge');
                if (!badge) {
                    badge = document.createElement('span');
                    badge.className = 'kg-cart-badge';
                    badge.style.cssText = 'position:absolute;top:-6px;right:-6px;background:#ef4444;color:#fff;font-size:11px;font-weight:800;min-width:22px;height:22px;padding:0 6px;border-radius:11px;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 8px rgba(239,68,68,0.5);border:2.5px solid var(--background, #0f172a);pointer-events:none;line-height:1;';
                    link.style.position = 'relative';
                    link.appendChild(badge);
                }
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'flex';
                    if (animate) {
                        badge.classList.remove('kg-badge-bounce');
                        void badge.offsetWidth;
                        badge.classList.add('kg-badge-bounce');
                        setTimeout(function() { badge.classList.remove('kg-badge-bounce'); }, 600);
                    }
                } else {
                    badge.style.display = 'none';
                }
            }
        });
    }

    // ── Fly-to-Cart (curved arc using Web Animations API) ──
    function flyToCart(imgEl, callback) {
        var target = document.querySelector('header a[href="/cart"]') || document.querySelector('header button[aria-label*="Cart"]');
        if (!imgEl || !target) { if (callback) callback(); return; }

        var sr = imgEl.getBoundingClientRect();
        var tr = target.getBoundingClientRect();
        var flySize = Math.min(sr.width, sr.height, 120);
        var startX = sr.left + sr.width/2 - flySize/2;
        var startY = sr.top + sr.height/2 - flySize/2;
        var endX = tr.left + tr.width/2 - 18;
        var endY = tr.top + tr.height/2 - 18;
        var cpX = startX + (endX - startX) * 0.5;
        var cpY = Math.min(startY, endY) - 120;

        var flyer = document.createElement('div');
        flyer.style.cssText = 'position:fixed;z-index:999999;pointer-events:none;will-change:transform,opacity;border-radius:14px;overflow:hidden;box-shadow:0 8px 32px rgba(16,185,129,0.5),0 0 0 3px rgba(16,185,129,0.3);border:2px solid rgba(16,185,129,0.6);';
        flyer.style.left = startX+'px'; flyer.style.top = startY+'px';
        flyer.style.width = flySize+'px'; flyer.style.height = flySize+'px';
        var img = document.createElement('img');
        img.src = imgEl.src;
        img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
        flyer.appendChild(img);
        document.body.appendChild(flyer);

        var steps = 30, kf = [];
        for (var i = 0; i <= steps; i++) {
            var t = i/steps;
            var x = (1-t)*(1-t)*startX + 2*(1-t)*t*cpX + t*t*endX;
            var y = (1-t)*(1-t)*startY + 2*(1-t)*t*cpY + t*t*endY;
            var sc = 1 - t*0.72;
            var op = t < 0.7 ? 1 : 1 - ((t-0.7)/0.3)*0.4;
            kf.push({ left:x+'px', top:y+'px', width:(flySize*sc)+'px', height:(flySize*sc)+'px', opacity:op, borderRadius:(14+t*36)+'px', transform:'rotate('+(t*360)+'deg)' });
        }
        var anim = flyer.animate(kf, { duration:750, easing:'cubic-bezier(0.22,0.61,0.36,1)', fill:'forwards' });
        anim.onfinish = function() {
            // Ripple at cart icon
            var rr = target.getBoundingClientRect();
            var rip = document.createElement('div');
            rip.style.cssText = 'position:fixed;z-index:999998;pointer-events:none;border-radius:50%;border:2px solid rgba(16,185,129,0.6);';
            rip.style.left=(rr.left-5)+'px'; rip.style.top=(rr.top-5)+'px';
            rip.style.width=(rr.width+10)+'px'; rip.style.height=(rr.height+10)+'px';
            rip.classList.add('kg-ripple');
            document.body.appendChild(rip);
            setTimeout(function(){ rip.remove(); }, 600);
            flyer.remove();
            if (callback) callback();
        };
    }

    // ── Success Banner (BOTTOM CENTER, clickable, matching site UI) ──
    function showSuccessBanner(productName) {
        var c = document.getElementById('kg-success-banner-container');
        if (!c) return;
        c.innerHTML = '';
        var b = document.createElement('a');
        b.href = '/cart';
        b.className = 'pointer-events-auto kg-success-banner-in block no-underline';
        b.style.cssText = 'max-width:440px;width:100%;text-decoration:none;';
        b.innerHTML =
            '<div class="flex items-center gap-3.5 rounded-lg border border-border bg-card p-4 shadow-lg transition-all hover:border-primary/50 cursor-pointer">' +
                '<div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-500/15 text-emerald-500">' +
                    '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="kg-check-draw"><polyline points="20 6 9 17 4 12"></polyline></svg>' +
                '</div>' +
                '<div class="min-w-0 flex-1">' +
                    '<div class="text-xs font-semibold uppercase tracking-wider text-emerald-500">✓ Item Added Successfully</div>' +
                    '<div class="mt-0.5 text-sm font-medium text-foreground">' + (productName || 'Product') + '</div>' +
                '</div>' +
                '<span class="inline-flex shrink-0 items-center justify-center rounded-full bg-primary px-4 py-2 text-xs font-medium text-primary-foreground shadow hover:bg-primary/90 transition-colors">' +
                    'View Cart →' +
                '</span>' +
            '</div>';
        c.appendChild(b);
        setTimeout(function() {
            b.classList.remove('kg-success-banner-in');
            b.classList.add('kg-success-banner-out');
            setTimeout(function() { b.remove(); }, 350);
        }, 3500);
    }

    // ── Init ──
    function initCartButtons() {
        updateNavbarBadge(false);
        // Buy Now always starts a single-product checkout.  Product cards in some
        // legacy page templates are buttons, so resolve their product URL here.
        document.addEventListener('click', function(e) {
            var target = e.target.closest('.btn-buy-now, button, a');
            if (!target || target.closest('header, footer, nav')) return;
            if (target.matches('a[href*="wa.me"]')) return;
            var label = (target.textContent || '').trim().replace(/\s+/g, ' ');
            if (!target.classList.contains('btn-buy-now') && !/^Buy Now(?:\s|$)/i.test(label)) return;

            var checkoutLink = target.matches('a[href*="/checkout"]') ? target.getAttribute('href') : '';
            if (!checkoutLink) {
                var card = target.closest('article, .product-card, [data-tsd-source*="ProductCard"], .group');
                var productLink = card && card.querySelector('a[href*="/product/"]');
                var productPath = productLink && productLink.getAttribute('href');
                if (!productPath && /^\/product\//.test(location.pathname)) productPath = location.pathname;
                if (productPath) {
                    var slug = productPath.split('/product/')[1].split('?')[0];
                    checkoutLink = '/checkout?product=' + encodeURIComponent(slug);
                }
            }
            if (!checkoutLink) return;
            e.preventDefault();
            e.stopPropagation();
            location.href = checkoutLink;
        });
        document.addEventListener('click', function(e) {
            var btn = e.target.closest('button[aria-label*="cart" i], a[aria-label*="Cart" i], a[title*="Cart" i], .btn-add-to-cart');
            if (!btn) return;
            if (btn.closest('header') || btn.closest('footer') || btn.closest('nav')) return;

            e.preventDefault();
            e.stopPropagation();

            var card = btn.closest('article, .product-card, [data-tsd-source*="ProductCard"], div.group');
            var productName='Product', productPrice=0, productImage='', productSlug='product-'+Date.now(), imgEl=null;

            if (card) {
                // Prefer the title heading over the image link — the image is wrapped
                // in an a[href*="/product/"] that comes first in the DOM but has no
                // text, so querySelector('h3, ..., a[href*="/product/"]') would match
                // that empty anchor first and silently produce a blank cart item name.
                var t = card.querySelector('h3') || card.querySelector('.line-clamp-2') || card.querySelector('a[href*="/product/"]');
                if (t) {
                    var tName = t.textContent.trim();
                    if (tName) productName = tName;
                }
                var l = card.querySelector('a[href*="/product/"]');
                if (l) { var p = l.getAttribute('href').split('/product/'); if(p.length>1) productSlug=p[1].split('?')[0]; }
                
                // Extract price from the card. Only read the FIRST run of digits
                // from a currency-looking string and sanity-check the range —
                // never strip-and-concat every digit in an element (that turns a
                // title full of model numbers, or a "price + old price" span,
                // into an absurd number that then shows up in the cart).
                var priceFromText = function (text) {
                    var s = String(text || '').replace(/[,\s]/g, '');
                    if (s.indexOf('৳') === -1 && s.indexOf('$') === -1 && s.indexOf('Tk') === -1) return 0;
                    var m = s.match(/\d{2,9}/);
                    var n = m ? parseInt(m[0], 10) : 0;
                    return (n >= 100 && n <= 100000000) ? n : 0;
                };
                var priceContainer = card.querySelector('.mt-3.flex, [class*="items-baseline"]');
                if (priceContainer) {
                    var spans = priceContainer.querySelectorAll('span');
                    for (var i = 0; i < spans.length; i++) {
                        if (spans[i].textContent.includes('Save') || spans[i].classList.contains('line-through')) continue;
                        productPrice = priceFromText(spans[i].textContent);
                        if (productPrice) break;
                    }
                }
                if (!productPrice) {
                    var priceEls = card.querySelectorAll('.text-base, .font-semibold, .tabular-nums, [class*="price" i]');
                    for (var j = 0; j < priceEls.length; j++) {
                        productPrice = priceFromText(priceEls[j].textContent);
                        if (productPrice) break;
                    }
                }

                imgEl = card.querySelector('img');
                if (imgEl) productImage = imgEl.src;
            }

            addToCart({ slug: productSlug, name: productName, price: productPrice, image: productImage });

            // Button feedback
            var origHtml = btn.innerHTML, origClass = btn.className;
            btn.style.transition = 'all 0.25s ease';
            btn.classList.add('bg-emerald-600','text-white','border-emerald-600');
            btn.style.transform = 'scale(1.15)';
            btn.innerHTML = '<svg class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
            var pill = document.createElement('span');
            pill.className = 'absolute -top-6 left-1/2 -translate-x-1/2 bg-emerald-600 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-lg pointer-events-none z-50 kg-animate-float';
            pill.textContent = '+1';
            btn.style.position = 'relative';
            btn.appendChild(pill);
            setTimeout(function() { pill.remove(); btn.innerHTML=origHtml; btn.className=origClass; btn.style.transform=''; }, 1300);

            flyToCart(imgEl, function() {
                updateNavbarBadge(true);
                showSuccessBanner(productName);
            });
        });
    }

    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', initCartButtons);
    else initCartButtons();
})();
</script>
