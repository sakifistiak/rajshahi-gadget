(function() {
    // 1. Initial theme application before DOM loads to avoid flash
    const savedTheme = localStorage.getItem('theme');
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    function swapLogos() {
        var isDark = document.documentElement.classList.contains('dark');
        var lightLogo = window.__SITE_LOGO_LIGHT || document.body?.dataset?.logoLight || '/media/b3ca13-kg-lockup-v2.png';
        var darkLogo = window.__SITE_LOGO_DARK || document.body?.dataset?.logoDark || '/media/logo_dark_1786184552.png';
        var siteName = window.__SITE_NAME || 'Khan Gadget';
        var logoUrl = isDark ? darkLogo : lightLogo;

        if (!logoUrl) return;

        var fullLogoUrl = logoUrl.startsWith('http') ? logoUrl : new URL(logoUrl, window.location.origin).href;

        document.querySelectorAll('img[alt*="Khan Gadget"], img[alt*="' + siteName + '"], header a[href="/"] img, .site-logo-img, header img').forEach(function(img) {
            if (img.src !== fullLogoUrl && img.src !== logoUrl) {
                img.src = logoUrl;
            }
        });
    }

    function updateIcons() {
        const isDark = document.documentElement.classList.contains('dark');
        const buttons = document.querySelectorAll('button[aria-label*="dark mode"], button[aria-label*="light mode"], button[aria-label*="theme"], .theme-toggle-btn');
        buttons.forEach(btn => {
            btn.setAttribute('aria-label', isDark ? 'Switch to light mode' : 'Switch to dark mode');
            btn.innerHTML = isDark 
                ? `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sun text-amber-400 h-5 w-5"><circle cx="12" cy="12" r="4"></circle><path d="M12 2v2"></path><path d="M12 20v2"></path><path d="m4.93 4.93 1.41 1.41"></path><path d="m17.66 17.66 1.41 1.41"></path><path d="M2 12h2"></path><path d="M20 12h2"></path><path d="m6.34 17.66-1.41 1.41"></path><path d="m19.07 4.93-1.41 1.41"></path></svg>`
                : `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-moon h-5 w-5"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"></path></svg>`;
        });
        swapLogos();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initThemeToggle);
    } else {
        initThemeToggle();
    }

    function initThemeToggle() {
        updateIcons();
        swapLogos();
        var observer = new MutationObserver(swapLogos);
        observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('button[aria-label*="dark mode"], button[aria-label*="light mode"], button[aria-label*="theme"], .theme-toggle-btn');
            if (btn) {
                e.preventDefault();
                document.documentElement.classList.toggle('dark');
                const isDark = document.documentElement.classList.contains('dark');
                localStorage.setItem('theme', isDark ? 'dark' : 'light');
                updateIcons();
                swapLogos();
            }
        });
    }
})();
