<!-- Left Sidebar for Desktop -->
<aside class="hidden lg:flex lg:w-64 lg:flex-col shrink-0 bg-white border-r border-slate-200"
       x-data="{ openSection: '{{ request()->routeIs('admin.orders.*') || request()->routeIs('admin.products.*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.brands.*') || request()->routeIs('admin.flash-sales.*') || request()->routeIs('admin.filter-attributes.*') || request()->routeIs('admin.store-locations.*') ? 'ecommerce' : (request()->routeIs('admin.sliders.*') || request()->routeIs('admin.popup-offer.*') || request()->routeIs('admin.promos.*') || request()->routeIs('admin.media.*') || request()->routeIs('admin.home-settings.*') ? 'home_settings' : (request()->routeIs('admin.customers.*') ? 'customer' : (request()->routeIs('admin.live-chat.*') || request()->routeIs('admin.live-chat-settings.*') ? 'live_chat' : (request()->routeIs('admin.blog-posts.*') || request()->routeIs('admin.customer-spotlights.*') || request()->routeIs('admin.customer-feedbacks.*') || request()->routeIs('admin.philanthropic-works.*') ? 'blog' : (request()->routeIs('admin.settings.*') ? 'settings' : 'ecommerce')))))  }}'
        }">
 
     <!-- User Profile Card -->
     <div class="p-3">
         <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 bg-white shadow-sm">
             <div class="h-10 w-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs uppercase shrink-0">
                 {{ substr(Auth::user()->name, 0, 2) }}
             </div>
             <div class="overflow-hidden">
                 <h4 class="text-[13px] font-bold text-slate-900 truncate leading-tight">{{ Auth::user()->name }}</h4>
                 <p class="text-[11px] font-medium text-slate-500 truncate mt-0.5 leading-tight">{{ Auth::user()->email }}</p>
             </div>
         </div>
     </div>

     <!-- Navigation List (Font size: 13.5px, slightly bolder font weight) -->
     <nav class="flex-1 px-3 pb-6 overflow-y-auto space-y-1 text-[13.5px]">
         @if(Auth::user()->is_live_chat_agent && !Auth::user()->is_admin)
             <a href="{{ route('admin.live-chat.index') }}" class="flex items-center justify-between gap-2.5 rounded-md bg-blue-50 px-3 py-2.5 font-bold text-blue-600">
                 <span class="flex items-center gap-2.5"><i data-lucide="messages-square" class="h-4.5 w-4.5"></i><span>Live Chat</span></span>
                 <span id="live-chat-unread-badge" class="hidden rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-bold text-white"></span>
             </a>
         @else

         <!-- 1. Dashboard -->
         <a href="{{ route('dashboard') }}" 
            class="flex items-center gap-2.5 px-3 py-2.5 rounded-md font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-800 hover:bg-slate-50 hover:text-blue-600' }}">
             <i data-lucide="layout-dashboard" class="h-4.5 w-4.5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-slate-500' }}"></i>
             <span>Dashboard</span>
         </a>


         <!-- 2. Ecommerce -->
         <div class="pt-1">
             <button @click="openSection = (openSection === 'ecommerce' ? '' : 'ecommerce')" 
                     class="w-full flex items-center justify-between px-3 py-2.5 rounded-md font-bold text-slate-800 hover:bg-slate-50 hover:text-blue-600 transition-all">
                 <span class="flex items-center gap-2.5">
                     <i data-lucide="shopping-bag" class="h-4.5 w-4.5 text-slate-500"></i>
                     <span>Ecommerce</span>
                 </span>
                 <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200" :class="openSection === 'ecommerce' ? 'rotate-180 text-blue-600' : ''"></i>
             </button>
             <div x-show="openSection === 'ecommerce'" x-collapse class="pl-8 pr-2 py-1 space-y-1">
                 <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.orders.index') || request()->routeIs('admin.orders.show') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                     <i data-lucide="shopping-cart" class="h-3.5 w-3.5 {{ request()->routeIs('admin.orders.index') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                     <span>Orders</span>
                 </a>
                <a href="{{ route('admin.flash-sales.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.flash-sales.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="flame" class="h-3.5 w-3.5 {{ request()->routeIs('admin.flash-sales.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Flash Sales</span>
                </a>
                <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="credit-card" class="h-3.5 w-3.5 text-slate-400"></i>
                    <span>Payments</span>
                </a>
                <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="file-text" class="h-3.5 w-3.5 text-slate-400"></i>
                    <span>Invoices</span>
                </a>
                <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="rotate-ccw" class="h-3.5 w-3.5 text-slate-400"></i>
                    <span>Returns</span>
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="package" class="h-3.5 w-3.5 {{ request()->routeIs('admin.products.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Products</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.categories.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="layers" class="h-3.5 w-3.5 {{ request()->routeIs('admin.categories.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Categories</span>
                </a>
                <a href="{{ route('admin.brands.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.brands.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="award" class="h-3.5 w-3.5 {{ request()->routeIs('admin.brands.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Brands</span>
                </a>
                <a href="{{ route('admin.filter-attributes.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.filter-attributes.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="sliders-horizontal" class="h-3.5 w-3.5 {{ request()->routeIs('admin.filter-attributes.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Filter Attributes</span>
                </a>
                <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="truck" class="h-3.5 w-3.5 text-slate-400"></i>
                    <span>Shipping Options</span>
                </a>
                <a href="{{ route('admin.store-locations.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.store-locations.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="map-pin" class="h-3.5 w-3.5 {{ request()->routeIs('admin.store-locations.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Store Locations</span>
                </a>
            </div>
        </div>

        <!-- 3. Homepage Settings -->
        <div class="pt-1">
            <button @click="openSection = (openSection === 'home_settings' ? '' : 'home_settings')" 
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-md font-bold text-slate-800 hover:bg-slate-50 hover:text-blue-600 transition-all">
                <span class="flex items-center gap-2.5">
                    <i data-lucide="layout-template" class="h-4.5 w-4.5 text-slate-500"></i>
                    <span>Homepage Settings</span>
                </span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200" :class="openSection === 'home_settings' ? 'rotate-180 text-blue-600' : ''"></i>
            </button>
            <div x-show="openSection === 'home_settings'" x-collapse class="pl-8 pr-2 py-1 space-y-1">
                <a href="{{ route('admin.sliders.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ (request()->routeIs('admin.sliders.*') || request()->routeIs('admin.promos.*') || request()->routeIs('admin.media.*')) ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="sliders" class="h-3.5 w-3.5 {{ (request()->routeIs('admin.sliders.*') || request()->routeIs('admin.promos.*') || request()->routeIs('admin.media.*')) ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Home Slider</span>
                </a>
                <a href="{{ route('admin.popup-offer.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.popup-offer.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="layers" class="h-3.5 w-3.5 {{ request()->routeIs('admin.popup-offer.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Offer Popup Banner</span>
                </a>
                <a href="{{ route('admin.home-settings.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.home-settings.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="sliders-horizontal" class="h-3.5 w-3.5 {{ request()->routeIs('admin.home-settings.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Home Settings</span>
                </a>
            </div>
        </div>

        <!-- Live Chat -->
        <div class="pt-1">
            <button @click="openSection = (openSection === 'live_chat' ? '' : 'live_chat')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-md font-bold text-slate-800 hover:bg-slate-50 hover:text-blue-600 transition-all">
                <span class="flex items-center gap-2.5">
                    <i data-lucide="messages-square" class="h-4.5 w-4.5 text-slate-500"></i>
                    <span>Live Chat</span>
                </span>
                <span class="flex items-center gap-1.5">
                    <span id="live-chat-unread-badge" class="hidden rounded-full bg-rose-600 px-1.5 py-0.5 text-[10px] font-bold text-white"></span>
                    <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200" :class="openSection === 'live_chat' ? 'rotate-180 text-blue-600' : ''"></i>
                </span>
            </button>
            <div x-show="openSection === 'live_chat'" x-collapse class="pl-8 pr-2 py-1 space-y-1">
                <a href="{{ route('admin.live-chat.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.live-chat.index') || request()->routeIs('admin.live-chat.show') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="inbox" class="h-3.5 w-3.5 {{ request()->routeIs('admin.live-chat.index') || request()->routeIs('admin.live-chat.show') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Inbox</span>
                </a>
                <a href="{{ route('admin.live-chat.agents.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.live-chat.agents.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="users" class="h-3.5 w-3.5 {{ request()->routeIs('admin.live-chat.agents.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Manage Agents</span>
                </a>
                <a href="{{ route('admin.live-chat-settings.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.live-chat-settings.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="sliders" class="h-3.5 w-3.5 {{ request()->routeIs('admin.live-chat-settings.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Live Chat Settings</span>
                </a>
            </div>
        </div>

        <!-- 3. Customer -->
        <div class="pt-1">
            <button @click="openSection = (openSection === 'customer' ? '' : 'customer')"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-md font-bold text-slate-800 hover:bg-slate-50 hover:text-blue-600 transition-all">
                <span class="flex items-center gap-2.5">
                    <i data-lucide="users" class="h-4.5 w-4.5 text-slate-500"></i>
                    <span>Customer</span>
                </span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200" :class="openSection === 'customer' ? 'rotate-180 text-blue-600' : ''"></i>
            </button>
            <div x-show="openSection === 'customer'" x-collapse class="pl-8 pr-2 py-1 space-y-1">
                <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.customers.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="user-check" class="h-3.5 w-3.5 {{ request()->routeIs('admin.customers.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Customer List</span>
                </a>
            </div>
        </div>

        <!-- 4. Blog -->
        <div class="pt-1">
            <button @click="openSection = (openSection === 'blog' ? '' : 'blog')" 
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-md font-bold text-slate-800 hover:bg-slate-50 hover:text-blue-600 transition-all">
                <span class="flex items-center gap-2.5">
                    <i data-lucide="newspaper" class="h-4.5 w-4.5 text-slate-500"></i>
                    <span>Blog</span>
                </span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200" :class="openSection === 'blog' ? 'rotate-180 text-blue-600' : ''"></i>
            </button>
            <div x-show="openSection === 'blog'" x-collapse class="pl-8 pr-2 py-1 space-y-1">
                <a href="{{ route('admin.blog-posts.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.blog-posts.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="file-text" class="h-3.5 w-3.5 {{ request()->routeIs('admin.blog-posts.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Blog Posts</span>
                </a>
                <a href="{{ route('admin.customer-spotlights.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.customer-spotlights.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="sparkles" class="h-3.5 w-3.5 {{ request()->routeIs('admin.customer-spotlights.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Customer Spotlights</span>
                </a>
                <a href="{{ route('admin.customer-feedbacks.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.customer-feedbacks.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="message-square-heart" class="h-3.5 w-3.5 {{ request()->routeIs('admin.customer-feedbacks.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Customer Feedback</span>
                </a>
                <a href="{{ route('admin.philanthropic-works.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.philanthropic-works.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="heart" class="h-3.5 w-3.5 {{ request()->routeIs('admin.philanthropic-works.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Philanthropic Work</span>
                </a>
            </div>
        </div>

        <!-- Custom Pages -->
        <a href="{{ route('admin.pages.index') }}" 
           class="flex items-center gap-2.5 px-3 py-2.5 rounded-md font-bold transition-all {{ request()->routeIs('admin.pages.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-800 hover:bg-slate-50 hover:text-blue-600' }}">
            <i data-lucide="file-text" class="h-4.5 w-4.5 shrink-0 {{ request()->routeIs('admin.pages.*') ? 'text-blue-600' : 'text-slate-500' }}"></i>
            <span>Custom Pages</span>
        </a>

        <!-- 5. Settings -->
        <div class="pt-1">
            <button @click="openSection = (openSection === 'settings' ? '' : 'settings')" 
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-md font-bold text-slate-800 hover:bg-slate-50 hover:text-blue-600 transition-all">
                <span class="flex items-center gap-2.5">
                    <i data-lucide="settings" class="h-4.5 w-4.5 text-slate-500"></i>
                    <span>Settings</span>
                </span>
                <i data-lucide="chevron-down" class="h-4 w-4 text-slate-400 transition-transform duration-200" :class="openSection === 'settings' ? 'rotate-180 text-blue-600' : ''"></i>
            </button>
            <div x-show="openSection === 'settings'" x-collapse class="pl-8 pr-2 py-1 space-y-1">
                <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-bold transition-colors {{ request()->routeIs('admin.settings.*') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:text-blue-600 hover:bg-slate-50' }}">
                    <i data-lucide="sliders" class="h-3.5 w-3.5 {{ request()->routeIs('admin.settings.*') ? 'text-blue-600' : 'text-slate-400' }}"></i>
                    <span>Site Settings</span>
                </a>
                <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="shield-check" class="h-3.5 w-3.5 text-slate-400"></i>
                    <span>Admin Users & Roles</span>
                </a>
            </div>
        </div>

        @endif
    </nav>
</aside>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var badge = document.getElementById('live-chat-unread-badge');
    if (!badge) return;

    // Ring an alert tone on a loop while there's unread customer messages,
    // so an admin/agent notices even if they're not looking at the screen.
    // Stops the moment they open the conversation (unread count hits 0).
    var Ctx = window.AudioContext || window.webkitAudioContext;
    var audioCtx = Ctx ? new Ctx() : null;
    var soundTimer = null;
    var pendingChime = false;

    function unlockAudio() {
        if (audioCtx && audioCtx.state === 'suspended') {
            audioCtx.resume().then(function () {
                if (pendingChime) { pendingChime = false; playChime(); }
            });
        }
    }
    ['click', 'keydown', 'touchstart', 'scroll', 'mousemove'].forEach(function (evt) {
        document.addEventListener(evt, unlockAudio, { passive: true });
    });

    function playChime() {
        if (!audioCtx) return;
        if (audioCtx.state === 'suspended') {
            pendingChime = true;
            unlockAudio();
            return;
        }
        var now = audioCtx.currentTime;
        // Louder "ding-dong" doorbell-style two-note ring (Tawk.to-like), played
        // twice back-to-back per tick so it reads as one persistent, unmissable ring.
        [0, 0.55].forEach(function (ringOffset) {
            [[988, 0], [740, 0.16]].forEach(function (pair) {
                var freq = pair[0], delay = pair[1];
                var osc = audioCtx.createOscillator();
                var gain = audioCtx.createGain();
                osc.type = 'triangle';
                osc.frequency.value = freq;
                var t = now + ringOffset + delay;
                gain.gain.setValueAtTime(0.0001, t);
                gain.gain.exponentialRampToValueAtTime(0.6, t + 0.02);
                gain.gain.exponentialRampToValueAtTime(0.0001, t + 0.45);
                osc.connect(gain).connect(audioCtx.destination);
                osc.start(t);
                osc.stop(t + 0.5);
            });
        });
    }

    function poll() {
        fetch('{{ route('admin.live-chat.unread-count') }}').then(function (r) { return r.ok ? r.json() : null; }).then(function (d) {
            if (!d) return;
            if (d.count > 0) {
                badge.textContent = d.count > 99 ? '99+' : d.count;
                badge.classList.remove('hidden');
                if (!soundTimer) {
                    playChime();
                    soundTimer = setInterval(playChime, 2000);
                }
            } else {
                badge.classList.add('hidden');
                if (soundTimer) { clearInterval(soundTimer); soundTimer = null; }
            }
        }).catch(function () {});
    }
    poll();
    setInterval(poll, 5000);
});
</script>
