<x-app-layout>
    <div class="space-y-5" x-data="analyticsDashboard()">
        <!-- Header & Period Filter -->
        <div class="bg-white rounded-md border border-slate-200 shadow-sm p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-[14px] font-bold text-slate-800 flex items-center gap-2">
                    <i data-lucide="activity" class="h-4 w-4 text-blue-600"></i>
                    {{ __('Visitor & Reach Analytics') }}
                </h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Live store visitors, traffic sources, product engagement, and blog readership statistics.</p>
            </div>

            <div class="flex items-center flex-wrap gap-2">
                <!-- Live status & toggle -->
                <button @click="toggleAutoRefresh()" 
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded border text-[11px] font-semibold transition-colors"
                        :class="autoRefresh ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-50 text-slate-600 border-slate-200'">
                    <span class="h-2 w-2 rounded-full" :class="autoRefresh ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                    <span x-text="autoRefresh ? 'Live Syncing (4s)' : 'Sync Paused'"></span>
                </button>

                <!-- Period Selector -->
                <div class="inline-flex rounded border border-slate-200 bg-slate-50 p-0.5 text-xs">
                    @foreach(['today' => 'Today', 'yesterday' => 'Yesterday', '7d' => '7 Days', '30d' => '30 Days', 'all' => 'All Time'] as $pKey => $pLabel)
                        <a href="{{ route('admin.analytics.index', ['period' => $pKey]) }}" 
                           class="px-2.5 py-1 rounded text-[11px] font-bold transition-colors {{ $period === $pKey ? 'bg-white text-blue-600 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                            {{ $pLabel }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Top Statistics Cards (Standard Admin Dashboard Style) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Stat 1: Live Online -->
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-emerald-50 text-emerald-600 rounded-xl border border-emerald-100 flex items-center justify-center">
                        <i data-lucide="radio" class="h-5 w-5"></i>
                    </span>
                    <div>
                        <h4 class="text-[13px] font-bold text-slate-600 leading-tight">Live Online</h4>
                        <p class="text-[18px] font-extrabold text-slate-900 mt-0.5" x-text="liveCount + ' Active'">{{ $liveCount }} Active</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-100">&lt; 5 mins</span>
            </div>

            <!-- Stat 2: Total Visits -->
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-blue-50 text-blue-600 rounded-xl border border-blue-100 flex items-center justify-center">
                        <i data-lucide="eye" class="h-5 w-5"></i>
                    </span>
                    <div>
                        <h4 class="text-[13px] font-bold text-slate-600 leading-tight">Total Visits</h4>
                        <p class="text-[18px] font-extrabold text-slate-900 mt-0.5">{{ number_format($totalVisits) }}</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">{{ $periodLabel }}</span>
            </div>

            <!-- Stat 3: Unique Visitors -->
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-sky-50 text-sky-600 rounded-xl border border-sky-100 flex items-center justify-center">
                        <i data-lucide="users" class="h-5 w-5"></i>
                    </span>
                    <div>
                        <h4 class="text-[13px] font-bold text-slate-600 leading-tight">Unique Visitors</h4>
                        <p class="text-[18px] font-extrabold text-slate-900 mt-0.5">{{ number_format($uniqueVisitors) }}</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-sky-600 bg-sky-50 px-2 py-0.5 rounded border border-sky-100">Audience</span>
            </div>

            <!-- Stat 4: Product Views -->
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-amber-50 text-amber-600 rounded-xl border border-amber-100 flex items-center justify-center">
                        <i data-lucide="package" class="h-5 w-5"></i>
                    </span>
                    <div>
                        <h4 class="text-[13px] font-bold text-slate-600 leading-tight">Product Views</h4>
                        <p class="text-[18px] font-extrabold text-slate-900 mt-0.5">{{ number_format($productViews) }}</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded border border-amber-100">Products</span>
            </div>

            <!-- Stat 5: Blog Reads -->
            <div class="bg-white rounded-lg border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="p-2.5 bg-rose-50 text-rose-600 rounded-xl border border-rose-100 flex items-center justify-center">
                        <i data-lucide="file-text" class="h-5 w-5"></i>
                    </span>
                    <div>
                        <h4 class="text-[13px] font-bold text-slate-600 leading-tight">Blog Reads</h4>
                        <p class="text-[18px] font-extrabold text-slate-900 mt-0.5">{{ number_format($blogViews) }}</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded border border-rose-100">Articles</span>
            </div>
        </div>

        <!-- Section Filter Tabs (Standard Admin Tabs Style) -->
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm p-1 flex overflow-x-auto gap-1">
            <button @click="activeTab = 'live'" 
                    :class="activeTab === 'live' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'"
                    class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5">
                <i data-lucide="radio" class="h-3.5 w-3.5"></i>
                <span>Live Active Visitors</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-emerald-100 text-emerald-700" x-text="liveCount">{{ $liveCount }}</span>
            </button>

            <button @click="activeTab = 'products'" 
                    :class="activeTab === 'products' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'"
                    class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5">
                <i data-lucide="package" class="h-3.5 w-3.5"></i>
                <span>Product Reach</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-600">{{ $productReach->count() }}</span>
            </button>

            <button @click="activeTab = 'blogs'" 
                    :class="activeTab === 'blogs' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'"
                    class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5">
                <i data-lucide="file-text" class="h-3.5 w-3.5"></i>
                <span>Blog Reach</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-600">{{ $blogReach->count() }}</span>
            </button>

            <button @click="activeTab = 'cart-adds'"
                    :class="activeTab === 'cart-adds' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'"
                    class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5">
                <i data-lucide="shopping-cart" class="h-3.5 w-3.5"></i>
                <span>Cart Adds</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] bg-slate-100 text-slate-600">{{ $cartAdds->count() }}</span>
            </button>

            <button @click="activeTab = 'sources'" 
                    :class="activeTab === 'sources' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'"
                    class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5">
                <i data-lucide="globe" class="h-3.5 w-3.5"></i>
                <span>Traffic Sources &amp; Devices</span>
            </button>

            <button @click="activeTab = 'daily'" 
                    :class="activeTab === 'daily' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50'"
                    class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5">
                <i data-lucide="calendar" class="h-3.5 w-3.5"></i>
                <span>Daily History</span>
            </button>
        </div>

        <!-- TAB 1: Live Active Visitors Table -->
        <div x-show="activeTab === 'live'" class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-[13px] font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="radio" class="h-3.5 w-3.5 text-emerald-600"></i>
                        Live Active Visitors Stream
                    </h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Visitors active within the last 5 minutes. Real-time updates automatically.</p>
                </div>
                <div class="text-[11px] text-slate-500">
                    Last sync: <strong class="text-slate-700" x-text="lastUpdated">Just now</strong>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/20">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Visitor IP &amp; Session</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Current Page Being Browsed</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Traffic Source</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Device &amp; Browser</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">Time on Site</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Last Activity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-if="liveSessions.length === 0">
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-400">
                                    {{ __('No active visitors right now. When customers browse the store, they will appear here instantly.') }}
                                </td>
                            </tr>
                        </template>

                        <template x-for="session in liveSessions" :key="session.id">
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <div class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        <span x-text="session.ip_address"></span>
                                    </div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="'SID: ' + session.session_id"></div>
                                </td>
                                <td class="px-6 py-3.5">
                                    <a :href="session.current_url" target="_blank" class="text-xs font-semibold text-blue-600 hover:text-blue-800 line-clamp-1 block" x-text="session.current_title"></a>
                                    <span class="text-[10px] text-slate-400 truncate block mt-0.5" x-text="session.current_url"></span>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <span class="px-2 py-0.5 inline-flex text-[10px] leading-5 font-bold rounded bg-slate-100 text-slate-700 border border-slate-200/60" x-text="session.referrer_domain"></span>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <div class="text-xs font-semibold text-slate-800" x-text="session.device_type + ' · ' + session.browser"></div>
                                    <div class="text-[10px] text-slate-400" x-text="session.platform"></div>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-bold text-slate-700" x-text="session.duration"></td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-right text-xs font-medium text-slate-500" x-text="session.time_ago"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 2: Product Reach Table -->
        <div x-show="activeTab === 'products'" class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-[13px] font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="package" class="h-3.5 w-3.5 text-blue-600"></i>
                        Product Reach &amp; Engagement
                    </h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Top products ranked by pageviews during {{ $periodLabel }}.</p>
                </div>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded border border-blue-100">
                    {{ $productReach->count() }} Products Tracked
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/20">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider w-12">#</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Product') }}</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Price & Stock') }}</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Period Views') }}</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Unique Viewers') }}</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Share %') }}</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('All-Time Views') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($productReach as $index => $item)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs font-extrabold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $item['thumbnail'] }}" alt="{{ $item['name'] }}" class="h-9 w-9 object-cover rounded border border-slate-100 shadow-sm shrink-0">
                                        <div>
                                            @if ($item['product'])
                                                <a href="{{ route('admin.products.edit', $item['product_id']) }}" class="text-xs font-semibold text-slate-800 hover:text-blue-600 block">
                                                    {{ $item['name'] }}
                                                </a>
                                                <div class="text-[9px] text-slate-400">Category: {{ $item['category_name'] }}</div>
                                            @else
                                                <span class="text-xs font-semibold text-slate-800">{{ $item['name'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    @if ($item['price'])
                                        <div class="text-xs font-bold text-slate-800">৳{{ number_format($item['price']) }}</div>
                                    @endif
                                    @if ($item['in_stock'])
                                        <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-emerald-50 text-emerald-600 border border-emerald-100">In Stock</span>
                                    @else
                                        <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-rose-50 text-rose-600 border border-rose-100">Out of Stock</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-extrabold text-slate-900">
                                    {{ number_format($item['views_total']) }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-bold text-slate-600">
                                    {{ number_format($item['unique_visitors']) }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-bold text-blue-600">
                                    {{ $item['share_pct'] }}%
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-right">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded-full">
                                        <i data-lucide="eye" class="h-3 w-3 text-slate-400"></i>
                                        {{ number_format($item['all_time_views']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-xs text-slate-400">
                                    {{ __('No product views recorded for this period yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 3: Most Added to Cart Table -->
        <div x-show="activeTab === 'cart-adds'" class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-[13px] font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="shopping-cart" class="h-3.5 w-3.5 text-emerald-600"></i>
                        Most Added to Cart Products
                    </h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Products customers added to cart during {{ $periodLabel }}.</p>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded border border-emerald-100">
                    {{ $cartAdds->count() }} Products Tracked
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/20">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider w-12">#</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Product') }}</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Price & Stock') }}</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Cart Adds') }}</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Unique Carts') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($cartAdds as $index => $item)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs font-extrabold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $item['thumbnail'] ?? '/assets/no-image-placeholder.svg' }}" alt="{{ $item['name'] }}" class="h-9 w-9 object-cover rounded border border-slate-100 shadow-sm shrink-0">
                                        <div>
                                            @if ($item['product'])
                                                <a href="{{ route('admin.products.edit', $item['product_id']) }}" class="text-xs font-semibold text-slate-800 hover:text-blue-600 block">{{ $item['name'] }}</a>
                                            @else
                                                <span class="text-xs font-semibold text-slate-800">{{ $item['name'] }}</span>
                                            @endif
                                            <div class="text-[9px] text-slate-400">Category: {{ $item['category_name'] }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    @if ($item['price'])
                                        <div class="text-xs font-bold text-slate-800">৳{{ number_format($item['price']) }}</div>
                                    @endif
                                    @if ($item['in_stock'])
                                        <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-emerald-50 text-emerald-600 border border-emerald-100">In Stock</span>
                                    @else
                                        <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-rose-50 text-rose-600 border border-rose-100">Out of Stock</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-extrabold text-emerald-600">{{ number_format($item['adds_total']) }}</td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-bold text-slate-600">{{ number_format($item['unique_carts']) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-6 py-8 text-center text-xs text-slate-400">No cart additions recorded for this period yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 4: Blog Reach Table -->
        <div x-show="activeTab === 'blogs'" class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-[13px] font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="file-text" class="h-3.5 w-3.5 text-blue-600"></i>
                        Blog Reach &amp; Readership
                    </h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Top blog articles ranked by readership during {{ $periodLabel }}.</p>
                </div>
                <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2.5 py-1 rounded border border-blue-100">
                    {{ $blogReach->count() }} Articles Tracked
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/20">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider w-12">#</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Article Title') }}</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Published') }}</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Period Reads') }}</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Unique Readers') }}</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Share %') }}</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('All-Time Reads') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($blogReach as $index => $item)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs font-extrabold text-slate-400">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="h-9 w-12 object-cover rounded border border-slate-100 shadow-sm shrink-0">
                                        <div>
                                            @if ($item['blog'])
                                                <a href="{{ route('admin.blog-posts.edit', $item['blog_id']) }}" class="text-xs font-semibold text-slate-800 hover:text-blue-600 block">
                                                    {{ $item['title'] }}
                                                </a>
                                                <div class="text-[9px] text-slate-400">/blog/{{ $item['slug'] }}</div>
                                            @else
                                                <span class="text-xs font-semibold text-slate-800">{{ $item['title'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-500">
                                    {{ $item['created_at'] }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-extrabold text-slate-900">
                                    {{ number_format($item['views_total']) }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-bold text-slate-600">
                                    {{ number_format($item['unique_readers']) }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-bold text-blue-600">
                                    {{ $item['share_pct'] }}%
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-right">
                                    <span class="inline-flex items-center gap-1 text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded-full">
                                        <i data-lucide="eye" class="h-3 w-3 text-slate-400"></i>
                                        {{ number_format($item['all_time_views']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-xs text-slate-400">
                                    {{ __('No blog reads recorded for this period yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- TAB 4: Traffic Sources & Devices -->
        <div x-show="activeTab === 'sources'" class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Traffic Sources Panel -->
            <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-[13px] font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="globe" class="h-3.5 w-3.5 text-blue-600"></i>
                        Traffic Sources
                    </h3>
                    <span class="text-[10px] text-slate-400">Visits by referrer</span>
                </div>
                <div class="p-4 space-y-3">
                    @forelse ($trafficSources as $src)
                        <div class="flex items-center justify-between text-xs">
                            <div class="w-2/5 font-semibold text-slate-800 truncate flex items-center gap-2">
                                <span class="h-2 w-2 rounded-full bg-blue-600"></span>
                                <span>{{ $src['domain'] }}</span>
                            </div>
                            <div class="flex-1 mx-3">
                                <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min(100, $src['share_pct']) }}%"></div>
                                </div>
                            </div>
                            <div class="w-24 text-right">
                                <span class="font-extrabold text-slate-900">{{ number_format($src['count']) }}</span>
                                <span class="text-[10px] text-slate-400">({{ $src['share_pct'] }}%)</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">No traffic source data yet</p>
                    @endforelse
                </div>
            </div>

            <!-- Devices Panel -->
            <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                    <h3 class="text-[13px] font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="smartphone" class="h-3.5 w-3.5 text-blue-600"></i>
                        Device Breakdown
                    </h3>
                    <span class="text-[10px] text-slate-400">Mobile vs Desktop</span>
                </div>
                <div class="p-4">
                    @php
                        $totDev = max(1, $deviceData->sum('count'));
                    @endphp
                    <div class="grid grid-cols-3 gap-3 mb-4">
                        @foreach (['Mobile' => 'smartphone', 'Desktop' => 'monitor', 'Tablet' => 'tablet'] as $dType => $dIcon)
                            @php
                                $dCount = $deviceData->firstWhere('device_type', $dType)?->count ?? 0;
                                $dPct = round(($dCount / $totDev) * 100, 1);
                            @endphp
                            <div class="p-3 bg-slate-50 rounded border border-slate-100 text-center">
                                <i data-lucide="{{ $dIcon }}" class="h-5 w-5 text-blue-600 mx-auto mb-1"></i>
                                <div class="text-xs font-bold text-slate-600">{{ $dType }}</div>
                                <div class="text-[16px] font-extrabold text-slate-900 mt-0.5">{{ $dPct }}%</div>
                                <div class="text-[10px] text-slate-400">{{ number_format($dCount) }} visits</div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Top Browsers & Platforms -->
                    <div class="border-t border-slate-100 pt-3">
                        <h4 class="text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">Top Browsers &amp; Platforms</h4>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($browserData as $b)
                                <span class="text-xs font-semibold px-2 py-0.5 bg-slate-100 rounded text-slate-700">
                                    {{ $b->browser }}: <strong class="text-slate-900">{{ number_format($b->count) }}</strong>
                                </span>
                            @endforeach
                            @foreach ($platformData as $p)
                                <span class="text-xs font-semibold px-2 py-0.5 bg-blue-50 text-blue-700 rounded border border-blue-100">
                                    {{ $p->platform }}: <strong class="text-blue-900">{{ number_format($p->count) }}</strong>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 5: Daily History Table -->
        <div x-show="activeTab === 'daily'" class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <div>
                    <h3 class="text-[13px] font-bold text-slate-800 flex items-center gap-2">
                        <i data-lucide="calendar" class="h-3.5 w-3.5 text-blue-600"></i>
                        Daily Traffic &amp; Reach Breakdown
                    </h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Day-by-day traffic log for the selected period.</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/20">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Pageviews</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unique Visitors</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">Product Views</th>
                            <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">Blog Reads</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($dailyData as $day)
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs font-bold text-slate-800">
                                    {{ \Carbon\Carbon::parse($day->visit_date)->format('d M, Y (l)') }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-extrabold text-slate-900">
                                    {{ number_format($day->total_visits) }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-bold text-blue-600">
                                    {{ number_format($day->unique_visitors) }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-bold text-amber-600">
                                    {{ number_format($day->product_views) }}
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-bold text-rose-600">
                                    {{ number_format($day->blog_views) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-xs text-slate-400">
                                    {{ __('No daily history recorded yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Live Sync Polling Script -->
    <script>
        function analyticsDashboard() {
            return {
                activeTab: 'live',
                autoRefresh: true,
                liveCount: {{ $liveCount }},
                liveSessions: @json($liveSessionsFormatted),
                lastUpdated: 'Just now',
                timer: null,

                init() {
                    this.startPolling();
                },

                startPolling() {
                    if (this.timer) clearInterval(this.timer);
                    this.timer = setInterval(() => {
                        if (this.autoRefresh) {
                            this.fetchLiveStats();
                        }
                    }, 4000);
                },

                toggleAutoRefresh() {
                    this.autoRefresh = !this.autoRefresh;
                },

                async fetchLiveStats() {
                    try {
                        const res = await fetch('{{ route('admin.analytics.live-stats') }}');
                        if (res.ok) {
                            const data = await res.json();
                            this.liveCount = data.live_count;
                            this.liveSessions = data.sessions;
                            this.lastUpdated = data.updated_at;
                            if (window.lucide) {
                                window.lucide.createIcons();
                            }
                        }
                    } catch (e) {
                        console.error('Analytics sync failed', e);
                    }
                }
            }
        }
    </script>
</x-app-layout>
