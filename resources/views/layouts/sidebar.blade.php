<!-- Left Sidebar for Desktop -->
<aside class="hidden lg:flex lg:w-64 lg:flex-col shrink-0 bg-white border-r border-slate-200"
       x-data="{ 
           openSection: '{{ request()->routeIs('admin.products.*') ? 'ecommerce' : (request()->routeIs('admin.sliders.*') || request()->routeIs('admin.promos.*') || request()->routeIs('admin.media.*') ? 'home_settings' : (request()->routeIs('admin.customers.*') ? 'customer' : 'ecommerce')) }}' 
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
                <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="shopping-cart" class="h-3.5 w-3.5 text-slate-400"></i>
                    <span>Orders</span>
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
                <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="layers" class="h-3.5 w-3.5 text-slate-400"></i>
                    <span>Categories</span>
                </a>
                <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="award" class="h-3.5 w-3.5 text-slate-400"></i>
                    <span>Brands</span>
                </a>
                <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="truck" class="h-3.5 w-3.5 text-slate-400"></i>
                    <span>Shipping Options</span>
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
                <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="file-text" class="h-3.5 w-3.5 text-slate-400"></i>
                    <span>Blog Posts</span>
                </a>
                <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="sparkles" class="h-3.5 w-3.5 text-slate-400"></i>
                    <span>Customer Spotlights</span>
                </a>
                <a href="#" class="flex items-center gap-2.5 px-3 py-1.5 rounded-md text-[13px] font-semibold text-slate-700 hover:text-blue-600 hover:bg-slate-50 transition-colors">
                    <i data-lucide="heart" class="h-3.5 w-3.5 text-slate-400"></i>
                    <span>Philanthropic Work</span>
                </a>
            </div>
        </div>

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

    </nav>
</aside>
