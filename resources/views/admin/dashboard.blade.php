<x-app-layout>
    
    <!-- Top Row Statistics (Pixel-perfect clean cards for actual system metrics) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        <!-- Stat Card 1: Total Products -->
        <div class="bg-white rounded-md border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="p-2.5 bg-blue-50 text-blue-600 rounded border border-blue-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </span>
                <div>
                    <h4 class="text-[12px] font-semibold text-slate-500 leading-tight">Total Products</h4>
                    <p class="text-[16px] font-bold text-slate-800 mt-0.5">{{ $stats['total_products'] }} Items</p>
                </div>
            </div>
        </div>

        <!-- Stat Card 2: Stock Availability Ratio -->
        <div class="bg-white rounded-md border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="p-2.5 bg-emerald-50 text-emerald-600 rounded border border-emerald-100">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </span>
                    <div>
                        <h4 class="text-[12px] font-semibold text-slate-500 leading-tight">In-Stock Ratio</h4>
                    </div>
                </div>
                @php
                    $stockPercent = $stats['total_products'] > 0 ? round(($stats['in_stock'] / $stats['total_products']) * 100) : 0;
                @endphp
                <span class="text-xs font-bold text-slate-700">{{ $stats['in_stock'] }} / {{ $stats['total_products'] }}</span>
            </div>
            <!-- Progress Bar below -->
            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden mt-3">
                <div class="bg-emerald-500 h-full rounded-full" style="width: {{ $stockPercent }}%"></div>
            </div>
        </div>

        <!-- Stat Card 3: Categories & Brands -->
        <div class="bg-white rounded-md border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="p-2.5 bg-sky-50 text-sky-600 rounded border border-sky-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                </span>
                <div>
                    <h4 class="text-[12px] font-semibold text-slate-500 leading-tight">Categories & Brands</h4>
                    <p class="text-[16px] font-bold text-slate-800 mt-0.5">{{ $stats['total_categories'] }} Cat / {{ $stats['total_brands'] }} Brands</p>
                </div>
            </div>
        </div>

        <!-- Stat Card 4: Customer Reviews -->
        <div class="bg-white rounded-md border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="p-2.5 bg-amber-50 text-amber-600 rounded border border-amber-100">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 0 1-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </span>
                <div>
                    <h4 class="text-[12px] font-semibold text-slate-500 leading-tight">Reviews & Blogs</h4>
                    <p class="text-[16px] font-bold text-slate-800 mt-0.5">{{ $stats['total_feedbacks'] }} Reviews / {{ $stats['total_blogs'] }} Blogs</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid Section: Inventory Breakdown (Col-span 2) & Quick System Panel (Col-span 1) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
        
        <!-- Left Overview Panel (Col Span 2) -->
        <div class="lg:col-span-2 bg-white rounded-md border border-slate-200 shadow-sm p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                
                <!-- Column 1: Inventory & Stock Status -->
                <div class="space-y-4 pr-2">
                    <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                        <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3 class="text-[13px] font-bold text-slate-800">Inventory Status Overview</h3>
                    </div>
                    
                    <div class="space-y-4 pt-1">
                        <!-- In Stock -->
                        <div>
                            <div class="flex justify-between text-[11px] font-medium mb-1">
                                <span class="text-emerald-700 font-semibold">{{ $stats['in_stock'] }} In Stock</span>
                                <span class="text-slate-400">{{ $stockPercent }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full"><div class="bg-emerald-500 h-full rounded-full" style="width: {{ $stockPercent }}%"></div></div>
                        </div>
                        
                        <!-- Out of Stock -->
                        @php
                            $outPercent = $stats['total_products'] > 0 ? round(($stats['out_of_stock'] / $stats['total_products']) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-[11px] font-medium mb-1">
                                <span class="text-rose-600 font-semibold">{{ $stats['out_of_stock'] }} Out of Stock</span>
                                <span class="text-slate-400">{{ $outPercent }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full"><div class="bg-rose-500 h-full rounded-full" style="width: {{ $outPercent }}%"></div></div>
                        </div>

                        <!-- Total Products Count -->
                        <div>
                            <div class="flex justify-between text-[11px] font-medium mb-1">
                                <span class="text-slate-700 font-semibold">{{ $stats['total_products'] }} Total Catalog Items</span>
                                <span class="text-slate-400">100%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full"><div class="bg-blue-600 h-full rounded-full" style="width: 100%"></div></div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: System Content Metrics -->
                <div class="space-y-4 md:pl-5 pt-4 md:pt-0">
                    <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                        <svg class="h-4 w-4 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                        <h3 class="text-[13px] font-bold text-slate-800">Content Metrics</h3>
                    </div>
                    
                    <div class="space-y-4 pt-1">
                        <!-- Categories -->
                        <div>
                            <div class="flex justify-between text-[11px] font-medium mb-1">
                                <span class="text-slate-700 font-semibold">{{ $stats['total_categories'] }} Active Categories</span>
                                <span class="text-slate-400">Active</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full"><div class="bg-sky-500 h-full rounded-full" style="width: 100%"></div></div>
                        </div>

                        <!-- Brands -->
                        <div>
                            <div class="flex justify-between text-[11px] font-medium mb-1">
                                <span class="text-slate-700 font-semibold">{{ $stats['total_brands'] }} Registered Brands</span>
                                <span class="text-slate-400">Active</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full"><div class="bg-amber-500 h-full rounded-full" style="width: 100%"></div></div>
                        </div>

                        <!-- Hero Sliders -->
                        <div>
                            <div class="flex justify-between text-[11px] font-medium mb-1">
                                <span class="text-slate-700 font-semibold">{{ $stats['total_sliders'] }} Banner Banners</span>
                                <span class="text-slate-400">Active</span>
                            </div>
                            <div class="w-full bg-slate-100 h-1.5 rounded-full"><div class="bg-purple-500 h-full rounded-full" style="width: 100%"></div></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Side Panel: Quick Actions & Store Health -->
        <div class="space-y-5">
            <!-- Quick Actions Card -->
            <div class="bg-white rounded-md border border-slate-200 shadow-sm p-4">
                <div class="flex justify-between items-center pb-2 border-b border-slate-100 mb-3">
                    <div class="flex items-center gap-1.5">
                        <svg class="h-4.5 w-4.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        <h3 class="text-[12px] font-bold text-slate-700">Quick Actions</h3>
                    </div>
                </div>

                <div class="space-y-2">
                    <a href="{{ route('admin.products.create') }}" class="flex items-center justify-between p-2.5 bg-blue-50/60 hover:bg-blue-50 border border-blue-100 rounded text-xs font-semibold text-blue-700 transition-colors">
                        <span>+ Add New Product</span>
                        <svg class="h-3.5 w-3.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="flex items-center justify-between p-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded text-xs font-semibold text-slate-700 transition-colors">
                        <span>Manage Product Catalog</span>
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
                    <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between p-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded text-xs font-semibold text-slate-700 transition-colors">
                        <span>Visit Frontend Store</span>
                        <svg class="h-3.5 w-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- System Status Panel -->
            <div class="bg-white rounded-md border border-slate-200 shadow-sm p-4">
                <h3 class="text-[12px] font-bold text-slate-700 border-b border-slate-100 pb-2 mb-3">System & Server Status</h3>
                <div class="space-y-2 text-[11px]">
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Database Connection</span>
                        <span class="font-bold text-emerald-600 flex items-center gap-1">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Connected
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Database Name</span>
                        <span class="font-semibold text-slate-800">khanu</span>
                    </div>
                    <div class="flex items-center justify-between text-slate-600">
                        <span>Framework Version</span>
                        <span class="font-semibold text-slate-800">Laravel {{ app()->version() }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bottom Section: Recently Uploaded Products Table -->
    <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden mt-6">
        <div class="px-5 py-3 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="text-[13px] font-bold text-slate-800">{{ __('Recently Added Products') }}</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Latest 5 products fetched directly from your MySQL database.</p>
            </div>
            <a href="{{ route('admin.products.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">
                {{ __('Manage Catalog') }}
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/20">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Image') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Product Name') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Category') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Condition') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Price') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($recentProducts as $product)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-3 whitespace-nowrap">
                                <img src="{{ $product->primaryImage() }}" alt="{{ $product->name }}" class="h-8 w-8 object-cover rounded border border-slate-100">
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <div class="text-xs font-semibold text-slate-800">{{ $product->name }}</div>
                                <div class="text-[9px] text-slate-400">Brand: {{ $product->brand?->name }}</div>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-xs text-slate-600">
                                {{ $product->category?->name }}
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-slate-100 text-slate-600 border border-slate-200/50">
                                    {{ $product->condition?->short }}
                                </span>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap text-xs font-bold text-slate-800">
                                ৳{{ number_format($product->price) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-xs text-slate-400">
                                {{ __('No products found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
