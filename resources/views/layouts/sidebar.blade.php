<!-- Left Sidebar for Desktop -->
<aside class="hidden lg:flex lg:w-64 lg:flex-col shrink-0 bg-[#ffffff] border-r border-[#e2e8f0]">
    
    <!-- User Profile Card -->
    <div class="p-3">
        <div class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="h-10 w-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs uppercase shrink-0">
                {{ substr(Auth::user()->name, 0, 2) }}
            </div>
            <div class="overflow-hidden">
                <h4 class="text-[12px] font-bold text-slate-800 truncate leading-tight">{{ Auth::user()->name }}</h4>
                <p class="text-[10px] text-slate-400 truncate mt-0.5 leading-tight">{{ Auth::user()->email }}</p>
            </div>
        </div>
    </div>

    <!-- Navigation List (Only Real System Features) -->
    <nav class="flex-1 px-2.5 pb-4 overflow-y-auto space-y-1">
        
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 pt-2 pb-1">Core</p>

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-[13px] font-medium transition-colors {{ request()->routeIs('dashboard') ? 'bg-[#f1f5f9] text-[#1e293b] font-semibold' : 'text-slate-600 hover:bg-[#f8fafc] hover:text-[#1e293b]' }}">
            <svg class="h-4 w-4 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
            </svg>
            {{ __('Dashboard') }}
        </a>

        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 pt-3 pb-1">E-Commerce</p>

        <!-- Products -->
        <a href="{{ route('admin.products.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-[13px] font-medium transition-colors {{ request()->routeIs('admin.products.*') ? 'bg-[#f1f5f9] text-[#1e293b] font-semibold' : 'text-slate-600 hover:bg-[#f8fafc] hover:text-[#1e293b]' }}">
            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.products.*') ? 'text-slate-800' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            {{ __('Products Catalog') }}
        </a>

        <!-- Categories -->
        <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded text-[13px] font-medium text-slate-600 hover:bg-[#f8fafc] hover:text-[#1e293b] transition-colors">
            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>
            {{ __('Categories') }}
        </a>

        <!-- Brands -->
        <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded text-[13px] font-medium text-slate-600 hover:bg-[#f8fafc] hover:text-[#1e293b] transition-colors">
            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v5m-4 0h4" />
            </svg>
            {{ __('Brands') }}
        </a>

        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider px-3 pt-3 pb-1">CMS & Content</p>

        <!-- Blogs -->
        <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded text-[13px] font-medium text-slate-600 hover:bg-[#f8fafc] hover:text-[#1e293b] transition-colors">
            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v1m2 4a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2h-3l-4 4v-4H5" />
            </svg>
            {{ __('Blog Articles') }}
        </a>

        <!-- Customer Feedback -->
        <a href="#" class="flex items-center gap-2.5 px-3 py-2 rounded text-[13px] font-medium text-slate-600 hover:bg-[#f8fafc] hover:text-[#1e293b] transition-colors">
            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
            </svg>
            {{ __('Customer Reviews') }}
        </a>

        <!-- Hero Sliders -->
        <a href="{{ route('admin.sliders.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-[13px] font-medium transition-colors {{ request()->routeIs('admin.sliders.*') ? 'bg-[#f1f5f9] text-[#1e293b] font-semibold' : 'text-slate-600 hover:bg-[#f8fafc] hover:text-[#1e293b]' }}">
            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.sliders.*') ? 'text-slate-800' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            {{ __('Hero Sliders') }}
        </a>

        <!-- Side Promo Banners -->
        <a href="{{ route('admin.promos.index') }}" class="flex items-center gap-2.5 px-3 py-2 rounded text-[13px] font-medium transition-colors {{ request()->routeIs('admin.promos.*') ? 'bg-[#f1f5f9] text-[#1e293b] font-semibold' : 'text-slate-600 hover:bg-[#f8fafc] hover:text-[#1e293b]' }}">
            <svg class="h-4 w-4 shrink-0 {{ request()->routeIs('admin.promos.*') ? 'text-slate-800' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
            </svg>
            {{ __('Side Promo Banners') }}
        </a>
    </nav>
</aside>
