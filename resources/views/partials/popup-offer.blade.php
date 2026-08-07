@if(!empty($popupOfferSettings['active']) && !empty($popupOfferSettings['image']))
    @php
        $blurPx    = max(0, min(50, (int) ($popupOfferSettings['backdrop_blur'] ?? 8)));
        $delay     = max(0, (float) ($popupOfferSettings['delay'] ?? 1));
        $frequency = $popupOfferSettings['frequency'] ?? 'session';
        $link      = $popupOfferSettings['link'] ?: '/shop';
        $target    = $popupOfferSettings['target'] ?: '_self';
        $image     = $popupOfferSettings['image'];
        $imageMob  = $popupOfferSettings['image_mobile'] ?? '';
    @endphp

    {{-- Popup Backdrop --}}
    <div id="kg-popup-backdrop"
         onclick="kgClosePopup()"
         style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:2147483647;background:rgba(0,0,0,0.78);backdrop-filter:blur({{ $blurPx }}px);-webkit-backdrop-filter:blur({{ $blurPx }}px);align-items:center;justify-content:center;padding:1.5rem;">

        {{-- Card --}}
        <div onclick="event.stopPropagation()"
             style="position:relative;max-width:42rem;width:100%;border-radius:0.75rem;overflow:visible;box-shadow:0 25px 60px rgba(0,0,0,0.6);">

            {{-- ✕ Close Button --}}
            <button onclick="kgClosePopup()"
                    type="button"
                    aria-label="Close popup"
                    style="position:absolute;top:-0.75rem;right:-0.75rem;z-index:9999;width:2.25rem;height:2.25rem;border-radius:9999px;background:white;color:#1e293b;border:2px solid #e2e8f0;box-shadow:0 4px 14px rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;cursor:pointer;padding:0;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="3"
                     stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 6 6 18"/><path d="m6 6 12 12"/>
                </svg>
            </button>

            {{-- Image wrapper --}}
            <div style="border-radius:0.75rem;overflow:hidden;">
                <a href="{{ $link }}" target="{{ $target }}" style="display:block;">
                    @if(!empty($imageMob))
                        <picture>
                            <source media="(max-width:639px)" srcset="{{ $imageMob }}">
                            <img src="{{ $image }}" alt="Special Offer" loading="eager"
                                 style="width:100%;height:auto;max-height:85vh;object-fit:contain;display:block;">
                        </picture>
                    @else
                        <img src="{{ $image }}" alt="Special Offer" loading="eager"
                             style="width:100%;height:auto;max-height:85vh;object-fit:contain;display:block;">
                    @endif
                </a>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var FREQ  = '{{ $frequency }}';
            var DELAY = {{ $delay * 1000 }};
            var SK    = 'kg_popup_dismissed';
            var LK    = 'kg_popup_dismissed_until';

            function shouldShow() {
                if (FREQ === 'always') return true;
                if (FREQ === 'session') return sessionStorage.getItem(SK) !== '1';
                if (FREQ === 'daily') {
                    var until = localStorage.getItem(LK);
                    return !until || Date.now() >= parseInt(until, 10);
                }
                return false;
            }

            window.kgClosePopup = function () {
                var el = document.getElementById('kg-popup-backdrop');
                if (!el) return;
                el.style.opacity = '0';
                el.style.transition = 'opacity 0.2s ease';
                setTimeout(function () { el.style.display = 'none'; }, 200);

                if (FREQ === 'session') sessionStorage.setItem(SK, '1');
                else if (FREQ === 'daily') localStorage.setItem(LK, (Date.now() + 86400000).toString());
            };

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') kgClosePopup();
            });

            if (shouldShow()) {
                setTimeout(function () {
                    var el = document.getElementById('kg-popup-backdrop');
                    if (!el) return;
                    el.style.display  = 'flex';
                    el.style.opacity  = '0';
                    el.style.transition = 'opacity 0.3s ease';
                    requestAnimationFrame(function () {
                        requestAnimationFrame(function () {
                            el.style.opacity = '1';
                        });
                    });
                }, DELAY);
            }
        })();
    </script>
@endif
