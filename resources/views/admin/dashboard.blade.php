<x-app-layout>
    
    <!-- Top Row Statistics (Font sizes: 13px title, 18px count bold) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
        
        <!-- Stat Card 1: Orders Today -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="p-2.5 bg-blue-50 text-blue-600 rounded-xl border border-blue-100 flex items-center justify-center">
                    <i data-lucide="shopping-cart" class="h-5 w-5"></i>
                </span>
                <div>
                    <h4 class="text-[13px] font-bold text-slate-600 leading-tight">Orders Today</h4>
                    <p class="text-[18px] font-extrabold text-slate-900 mt-0.5">12 Orders</p>
                </div>
            </div>
        </div>

        <!-- Stat Card 2: Total Products -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100 flex items-center justify-center">
                    <i data-lucide="package" class="h-5 w-5"></i>
                </span>
                <div>
                    <h4 class="text-[13px] font-bold text-slate-600 leading-tight">Total Products</h4>
                    <p class="text-[18px] font-extrabold text-slate-900 mt-0.5">{{ $stats['total_products'] }} Items</p>
                </div>
            </div>
        </div>

        <!-- Stat Card 3: In-Stock Ratio -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span class="p-2.5 bg-sky-50 text-sky-600 rounded-xl border border-sky-100 flex items-center justify-center">
                        <i data-lucide="check-circle-2" class="h-5 w-5"></i>
                    </span>
                    <div>
                        <h4 class="text-[13px] font-bold text-slate-600 leading-tight">In-Stock Ratio</h4>
                    </div>
                </div>
                @php
                    $stockPercent = $stats['total_products'] > 0 ? round(($stats['in_stock'] / $stats['total_products']) * 100) : 0;
                @endphp
                <span class="text-xs font-extrabold text-slate-800">{{ $stats['in_stock'] }} / {{ $stats['total_products'] }}</span>
            </div>
            <!-- Progress Bar -->
            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden mt-3">
                <div class="bg-sky-500 h-full rounded-full" style="width: {{ $stockPercent }}%"></div>
            </div>
        </div>

        <!-- Stat Card 4: Categories & Brands -->
        <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="p-2.5 bg-purple-50 text-purple-600 rounded-xl border border-purple-100 flex items-center justify-center">
                    <i data-lucide="layers" class="h-5 w-5"></i>
                </span>
                <div>
                    <h4 class="text-[13px] font-bold text-slate-600 leading-tight">Categories & Brands</h4>
                    <p class="text-[18px] font-extrabold text-slate-900 mt-0.5">{{ $stats['total_categories'] }} Cat / {{ $stats['total_brands'] }} Brands</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Overview Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">
        
        <!-- Left Overview Panel -->
        <div class="lg:col-span-2 bg-white rounded-lg border border-slate-200 shadow-sm p-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 divide-y md:divide-y-0 md:divide-x divide-slate-100">
                
                <!-- Column 1: Stock Overview -->
                <div class="space-y-4 pr-2">
                    <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                        <i data-lucide="box" class="h-4.5 w-4.5 text-blue-600"></i>
                        <h3 class="text-sm font-bold text-slate-900">Inventory Status Overview</h3>
                    </div>
                    
                    <div class="space-y-4 pt-1">
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-emerald-700 font-extrabold">{{ $stats['in_stock'] }} In Stock</span>
                                <span class="text-slate-500">{{ $stockPercent }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full"><div class="bg-emerald-500 h-full rounded-full" style="width: {{ $stockPercent }}%"></div></div>
                        </div>
                        
                        @php
                            $outPercent = $stats['total_products'] > 0 ? round(($stats['out_of_stock'] / $stats['total_products']) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-rose-600 font-extrabold">{{ $stats['out_of_stock'] }} Out of Stock</span>
                                <span class="text-slate-500">{{ $outPercent }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full"><div class="bg-rose-500 h-full rounded-full" style="width: {{ $outPercent }}%"></div></div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-slate-800 font-extrabold">{{ $stats['total_products'] }} Total Catalog Items</span>
                                <span class="text-slate-500">100%</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full"><div class="bg-blue-600 h-full rounded-full" style="width: 100%"></div></div>
                        </div>
                    </div>
                </div>

                <!-- Column 2: System Content Metrics -->
                <div class="space-y-4 md:pl-5 pt-4 md:pt-0">
                    <div class="flex items-center gap-2 pb-2 border-b border-slate-100">
                        <i data-lucide="pie-chart" class="h-4.5 w-4.5 text-sky-600"></i>
                        <h3 class="text-sm font-bold text-slate-900">Content Metrics</h3>
                    </div>
                    
                    <div class="space-y-4 pt-1">
                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-slate-800 font-bold">{{ $stats['total_categories'] }} Active Categories</span>
                                <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-sky-50 text-sky-700 border border-sky-200">Active</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full"><div class="bg-sky-500 h-full rounded-full" style="width: 100%"></div></div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-slate-800 font-bold">{{ $stats['total_brands'] }} Registered Brands</span>
                                <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-amber-50 text-amber-700 border border-amber-200">Active</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full"><div class="bg-amber-500 h-full rounded-full" style="width: 100%"></div></div>
                        </div>

                        <div>
                            <div class="flex justify-between text-xs font-bold mb-1">
                                <span class="text-slate-800 font-bold">{{ $stats['total_sliders'] }} Banner Banners</span>
                                <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-purple-50 text-purple-700 border border-purple-200">Active</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full"><div class="bg-purple-500 h-full rounded-full" style="width: 100%"></div></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right Side Panel: Quick Actions -->
        <div class="space-y-5">
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-4">
                <div class="flex justify-between items-center pb-2 border-b border-slate-100 mb-3">
                    <div class="flex items-center gap-1.5">
                        <i data-lucide="zap" class="h-4.5 w-4.5 text-amber-500"></i>
                        <h3 class="text-sm font-bold text-slate-800">Quick Actions</h3>
                    </div>
                </div>

                <div class="space-y-2">
                    <a href="{{ route('admin.products.create') }}" class="flex items-center justify-between p-3 bg-blue-50/60 hover:bg-blue-50 border border-blue-100 rounded-md text-[13px] font-bold text-blue-700 transition-colors">
                        <span class="flex items-center gap-2">
                            <i data-lucide="plus-circle" class="h-4 w-4 text-blue-600"></i>
                            Add New Product
                        </span>
                        <i data-lucide="chevron-right" class="h-4 w-4 text-blue-500"></i>
                    </a>
                    <a href="{{ route('admin.customers.index') }}" class="flex items-center justify-between p-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-md text-[13px] font-bold text-slate-800 transition-colors">
                        <span class="flex items-center gap-2">
                            <i data-lucide="user-plus" class="h-4 w-4 text-slate-600"></i>
                            Customer Management
                        </span>
                        <i data-lucide="chevron-right" class="h-4 w-4 text-slate-400"></i>
                    </a>
                    <a href="{{ route('home') }}" target="_blank" class="flex items-center justify-between p-3 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-md text-[13px] font-bold text-slate-800 transition-colors">
                        <span class="flex items-center gap-2">
                            <i data-lucide="external-link" class="h-4 w-4 text-slate-600"></i>
                            Visit Frontend Store
                        </span>
                        <i data-lucide="chevron-right" class="h-4 w-4 text-slate-400"></i>
                    </a>
                </div>
            </div>
        </div>

    </div>

</x-app-layout>
