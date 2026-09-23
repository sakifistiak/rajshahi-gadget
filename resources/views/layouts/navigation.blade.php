{{-- Alpine scope for the mobile sidebar drawer. `contents` keeps the <nav> a direct flex child of <body>,
     so the layout is unchanged. Escape closes the drawer. --}}
<div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="contents">
<nav class="bg-white border-b border-slate-200 h-14 shrink-0 flex items-center justify-between px-4 z-20">
    <!-- Left Section: Mobile Menu Toggle & Official Site Logo -->
    <div class="flex items-center gap-3">
        <!-- Toggle button for sidebar on mobile -->
        <button type="button" @click="sidebarOpen = !sidebarOpen" aria-label="Open menu" :aria-expanded="sidebarOpen.toString()" class="text-slate-500 hover:text-slate-700 focus:outline-none lg:hidden p-1 rounded-md hover:bg-slate-100">
            <i data-lucide="menu" class="h-5 w-5"></i>
        </button>
        
        <!-- Site Logo Image -->
        <a href="{{ route('dashboard') }}" class="flex items-center py-1">
            <img src="{{ \App\Models\SiteSetting::getValue('logo_light', '/media/b3ca13-kg-lockup-v2.png') }}" alt="{{ \App\Models\SiteSetting::getValue('site_name', 'Khan Gadget') }}" class="h-8 w-auto object-contain" style="max-height:32px;width:auto;" />
        </a>
    </div>

    <!-- Right Section: Clean Actions (View Store & User Profile Dropdown) -->
    <div class="flex items-center gap-3">
        <!-- View Store Button -->
        <a href="{{ route('home') }}" target="_blank" class="text-xs font-semibold text-slate-600 hover:text-blue-600 bg-slate-100 hover:bg-slate-200/70 px-3 py-1.5 rounded-md transition-colors flex items-center gap-1.5">
            <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
            <span>View Store</span>
        </a>

        <!-- Profile / Logout Dropdown -->
        <div class="border-l border-slate-200 pl-3">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center gap-2 text-xs font-semibold text-slate-700 hover:text-slate-900 bg-slate-50 hover:bg-slate-100 border border-slate-200/80 px-3 py-1.5 rounded-md focus:outline-none transition duration-150 ease-in-out">
                        <div class="h-5 w-5 rounded-full bg-blue-600 text-white flex items-center justify-center text-[10px] font-bold uppercase">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                        <span>{{ Auth::user()->name }}</span>
                        <i data-lucide="chevron-down" class="h-3.5 w-3.5 text-slate-400"></i>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')" class="text-xs flex items-center gap-2">
                        <i data-lucide="user" class="h-3.5 w-3.5 text-slate-400"></i>
                        {{ __('My Profile') }}
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();"
                                class="text-xs text-red-600 hover:text-red-700 flex items-center gap-2">
                            <i data-lucide="log-out" class="h-3.5 w-3.5 text-red-500"></i>
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>
    </div>
</nav>

<!-- Mobile Sidebar Drawer -->
<div x-show="sidebarOpen" class="relative z-50 lg:hidden" role="dialog" aria-modal="true" style="display: none;">
    <!-- Backdrop overlay -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"
         @click="sidebarOpen = false"></div>

    {{-- This full-screen row sits above the backdrop, so it receives the outside taps; .self closes the drawer only
         when the empty area (not the panel or its children) is tapped. --}}
    <div class="fixed inset-0 flex" @click.self="sidebarOpen = false">
        <!-- Sidebar slide panel -->
        <div x-show="sidebarOpen"
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="relative flex w-full max-w-[260px] flex-1 flex-col bg-white border-r border-slate-200">
            
            <!-- Close button -->
            <div class="absolute right-[-3rem] top-0 pt-4 pl-2">
                <button type="button" class="text-white focus:outline-none" @click="sidebarOpen = false">
                    <i data-lucide="x" class="h-6 w-6"></i>
                </button>
            </div>

            <!-- Mobile Brand Logo -->
            <div class="flex h-14 shrink-0 items-center px-4 border-b border-slate-100 bg-slate-50/50">
                <img src="{{ \App\Models\SiteSetting::getValue('logo_light', '/media/b3ca13-kg-lockup-v2.png') }}" alt="{{ \App\Models\SiteSetting::getValue('site_name', 'Khan Gadget') }}" class="h-7 w-auto object-contain" />
            </div>
            
            <!-- Full admin menu (same as desktop sidebar) -->
            @include('layouts.sidebar-menu')
        </div>
    </div>
</div>
</div>
