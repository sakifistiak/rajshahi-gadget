<nav class="bg-white border-b border-slate-200 h-14 shrink-0 flex items-center justify-between px-4 z-20">
    <!-- Left Section: Hamburger & Brand Name -->
    <div class="flex items-center gap-3">
        <!-- Toggle button for sidebar on mobile -->
        <button @click="sidebarOpen = !sidebarOpen" class="text-slate-500 hover:text-slate-700 focus:outline-none lg:hidden p-1 rounded hover:bg-slate-100">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 19h16" />
            </svg>
        </button>
        
        <!-- Logo / Brand Name (Matching Perfex style font size and weight) -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <span class="text-[17px] font-bold text-[#2563eb] tracking-tight">Khan Gadget</span>
        </a>
    </div>

    <!-- Center Section: Search Bar & Plus Button (Pill style) -->
    <div class="hidden md:flex items-center gap-2 flex-1 max-w-sm ml-8">
        <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="search" placeholder="Search..." class="block w-full pl-9 pr-4 py-1.5 bg-[#f1f5f9] border-none rounded-md text-[11px] text-slate-800 placeholder-slate-400 focus:bg-[#e2e8f0] focus:ring-0 focus:outline-none transition-colors h-7.5">
        </div>
        <!-- Circular Blue Plus Button -->
        <button class="h-7 w-7 rounded-full bg-[#2563eb] text-white flex items-center justify-center hover:bg-blue-700 transition-colors shadow-sm shrink-0 focus:outline-none">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
            </svg>
        </button>
    </div>

    <!-- Right Section: Actions -->
    <div class="flex items-center gap-3">
        <!-- View Store link -->
        <a href="{{ route('home') }}" target="_blank" class="text-[11px] font-medium text-slate-500 hover:text-[#2563eb] transition-colors hidden sm:block">
            {{ __('View Store') }}
        </a>

        <!-- Settings text link -->
        <button class="text-[11px] font-medium text-slate-500 hover:text-slate-800 focus:outline-none">
            Settings
        </button>

        <!-- System Icons (Thin Line icons) -->
        <div class="hidden sm:flex items-center gap-2 border-l border-slate-200 pl-3">
            <!-- Share Icon -->
            <button class="p-1 text-slate-400 hover:text-slate-600 rounded focus:outline-none">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 10.742l5.052-2.526M8.684 13.258l5.052 2.526M21 8a3 3 0 11-6 0 3 3 0 016 0zm-6 8a3 3 0 11-6 0 3 3 0 016 0zM10 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </button>
            <!-- Checkmark List Icon -->
            <button class="p-1 text-slate-400 hover:text-slate-600 rounded focus:outline-none">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                </svg>
            </button>
            <!-- Clock Icon -->
            <button class="p-1 text-slate-400 hover:text-slate-600 rounded focus:outline-none">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </button>
            <!-- Bell Notification Icon -->
            <button class="p-1 text-slate-400 hover:text-slate-600 rounded focus:outline-none relative">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span class="absolute top-0.5 right-0.5 block h-1.5 w-1.5 rounded-full bg-red-500 ring-2 ring-white"></span>
            </button>
        </div>

        <!-- Dashboard Options button -->
        <button class="hidden lg:flex items-center gap-1.5 px-2.5 py-1 text-[11px] font-medium text-slate-600 hover:text-slate-800 bg-[#e2e8f0]/60 hover:bg-[#e2e8f0] rounded border border-slate-300/40 focus:outline-none transition-colors">
            <svg class="h-3.5 w-3.5 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Dashboard Options
        </button>

        <!-- Profile / Logout Dropdown -->
        <div class="border-l border-slate-200 pl-3">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-800 focus:outline-none transition duration-150 ease-in-out">
                        <span>{{ Auth::user()->name }}</span>
                        <svg class="fill-current h-3.5 w-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </x-slot>

                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')" class="text-xs">
                        {{ __('My Profile') }}
                    </x-dropdown-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();"
                                class="text-xs text-red-600 hover:text-red-700">
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

    <div class="fixed inset-0 flex">
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
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Brand Logo -->
            <div class="flex h-14 shrink-0 items-center px-4 border-b border-slate-100 bg-slate-50/50">
                <span class="text-md font-bold text-blue-600 uppercase tracking-wide">Khan Gadget</span>
            </div>
            
            <!-- Mobile Sidebar Contents (Replicating sidebar links) -->
            <div class="p-4 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3 p-2 rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="h-10 w-10 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold text-sm uppercase shrink-0">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div class="overflow-hidden">
                        <h4 class="text-xs font-semibold text-slate-800 truncate">{{ Auth::user()->name }}</h4>
                        <p class="text-[10px] text-slate-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold {{ request()->routeIs('dashboard') ? 'bg-slate-100 text-blue-600' : 'text-slate-600' }}">
                    {{ __('Dashboard') }}
                </a>
                <a href="{{ route('admin.products.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-xs font-semibold {{ request()->routeIs('admin.products.*') ? 'bg-slate-100 text-blue-600' : 'text-slate-600' }}">
                    {{ __('Products') }}
                </a>
            </nav>
        </div>
    </div>
</div>
