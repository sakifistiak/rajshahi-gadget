<x-app-layout>
    <div class="space-y-6" x-data="analyticsDashboard()">
        <!-- Header & Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-lg bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600">
                        <i data-lucide="activity" class="h-5 w-5"></i>
                    </div>
                    <div>
                        <h1 class="text-xl font-black text-slate-900 tracking-tight">Visitor & Reach Analytics</h1>
                        <p class="text-xs text-slate-500 font-medium">Real-time live traffic, product views, and blog readership insights</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center flex-wrap gap-2.5">
                <!-- Live Pulse Indicator -->
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span>Live Auto-Sync: <span x-text="autoRefresh ? 'Active' : 'Paused'">Active</span></span>
                    <button @click="toggleAutoRefresh()" class="ml-1 text-[11px] underline font-bold hover:text-emerald-900" x-text="autoRefresh ? 'Pause' : 'Resume'"></button>
                </div>

                <!-- Period Selector -->
                <div class="inline-flex rounded-lg border border-slate-200 bg-slate-50/50 p-1 text-xs font-medium">
                    <a href="{{ route('admin.analytics.index', ['period' => 'today']) }}" 
                       class="px-2.5 py-1 rounded-md transition-colors {{ $period === 'today' ? 'bg-white font-bold text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        Today
                    </a>
                    <a href="{{ route('admin.analytics.index', ['period' => 'yesterday']) }}" 
                       class="px-2.5 py-1 rounded-md transition-colors {{ $period === 'yesterday' ? 'bg-white font-bold text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        Yesterday
                    </a>
                    <a href="{{ route('admin.analytics.index', ['period' => '7d']) }}" 
                       class="px-2.5 py-1 rounded-md transition-colors {{ $period === '7d' ? 'bg-white font-bold text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        7 Days
                    </a>
                    <a href="{{ route('admin.analytics.index', ['period' => '30d']) }}" 
                       class="px-2.5 py-1 rounded-md transition-colors {{ $period === '30d' ? 'bg-white font-bold text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        30 Days
                    </a>
                    <a href="{{ route('admin.analytics.index', ['period' => 'all']) }}" 
                       class="px-2.5 py-1 rounded-md transition-colors {{ $period === 'all' ? 'bg-white font-bold text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        All Time
                    </a>
                </div>
            </div>
        </div>

        <!-- Top KPI Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- 1. Live Online Now -->
            <div class="bg-gradient-to-br from-emerald-500 to-teal-700 rounded-xl p-4 text-white shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                <div class="absolute -right-3 -bottom-3 opacity-15">
                    <i data-lucide="radio" class="h-24 w-24"></i>
                </div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-100 flex items-center gap-1.5">
                        <span class="h-2 w-2 rounded-full bg-white animate-pulse"></span>
                        Live Online Now
                    </span>
                    <span class="text-[10px] font-semibold bg-emerald-400/30 px-2 py-0.5 rounded-full text-white">Active</span>
                </div>
                <div class="text-3xl font-extrabold tracking-tight" x-text="liveCount">{{ $liveCount }}</div>
                <div class="text-[11px] text-emerald-100 mt-1 font-medium">Browsing store right now</div>
            </div>

            <!-- 2. Total Store Visits -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Total Visits</span>
                    <span class="p-1.5 bg-blue-50 text-blue-600 rounded-lg border border-blue-100">
                        <i data-lucide="eye" class="h-4 w-4"></i>
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($totalVisits) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Pageviews in {{ $period == '7d' ? 'last 7 days' : ($period == '30d' ? 'last 30 days' : $period) }}</div>
            </div>

            <!-- 3. Unique Visitors -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Unique Visitors</span>
                    <span class="p-1.5 bg-indigo-50 text-indigo-600 rounded-lg border border-indigo-100">
                        <i data-lucide="users" class="h-4 w-4"></i>
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($uniqueVisitors) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Distinct customer devices</div>
            </div>

            <!-- 4. Product Views -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Product Reach</span>
                    <span class="p-1.5 bg-amber-50 text-amber-600 rounded-lg border border-amber-100">
                        <i data-lucide="package" class="h-4 w-4"></i>
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($productViews) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Product page views</div>
            </div>

            <!-- 5. Blog Views -->
            <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Blog Reach</span>
                    <span class="p-1.5 bg-rose-50 text-rose-600 rounded-lg border border-rose-100">
                        <i data-lucide="book-open" class="h-4 w-4"></i>
                    </span>
                </div>
                <div class="text-2xl font-extrabold text-slate-900">{{ number_format($blogViews) }}</div>
                <div class="text-[11px] text-slate-500 mt-1">Article reads & views</div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="border-b border-slate-200">
            <nav class="flex space-x-6">
                <button @click="activeTab = 'live'" 
                        :class="activeTab === 'live' ? 'border-emerald-500 text-emerald-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
                        class="py-3 px-1 border-b-2 text-sm flex items-center gap-2 transition-all">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Live Visitor Stream (<span x-text="liveCount">{{ $liveCount }}</span>)
                </button>

                <button @click="activeTab = 'products'" 
                        :class="activeTab === 'products' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
                        class="py-3 px-1 border-b-2 text-sm flex items-center gap-2 transition-all">
                    <i data-lucide="package" class="h-4 w-4"></i>
                    Product Reach ({{ $productReach->count() }})
                </button>

                <button @click="activeTab = 'blogs'" 
                        :class="activeTab === 'blogs' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
                        class="py-3 px-1 border-b-2 text-sm flex items-center gap-2 transition-all">
                    <i data-lucide="book-open" class="h-4 w-4"></i>
                    Blog Reach ({{ $blogReach->count() }})
                </button>

                <button @click="activeTab = 'sources'" 
                        :class="activeTab === 'sources' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300 font-medium'"
                        class="py-3 px-1 border-b-2 text-sm flex items-center gap-2 transition-all">
                    <i data-lucide="globe" class="h-4 w-4"></i>
                    Traffic Sources & Devices
                </button>
            </nav>
        </div>

        <!-- TAB 1: Live Visitor Stream -->
        <div x-show="activeTab === 'live'" class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                        <h3 class="text-sm font-bold text-slate-900">Current Online Visitors (Active past 3 minutes)</h3>
                    </div>
                    <span class="text-xs text-slate-400 font-medium">Last synced: <span x-text="lastUpdated">Just now</span></span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left">
                        <thead class="bg-slate-50/60 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Visitor Session</th>
                                <th class="px-4 py-3">Current Active Page</th>
                                <th class="px-4 py-3">Traffic Source</th>
                                <th class="px-4 py-3">Device / Browser</th>
                                <th class="px-4 py-3 text-right">Last Active</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            <template x-if="liveSessions.length === 0">
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                        <div class="flex flex-col items-center justify-center gap-1.5">
                                            <i data-lucide="radio" class="h-8 w-8 text-slate-300"></i>
                                            <p class="font-semibold text-slate-600">No active visitors right now</p>
                                            <p class="text-[11px]">When customers browse your storefront, they will appear here instantly.</p>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <template x-for="session in liveSessions" :key="session.id">
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-4 py-3 font-mono text-[11px] text-slate-600 font-semibold flex items-center gap-2">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        <span x-text="session.session_id"></span>
                                    </td>
                                    <td class="px-4 py-3 max-w-xs">
                                        <a :href="session.current_url" target="_blank" class="font-bold text-slate-900 hover:text-indigo-600 truncate block text-xs" x-text="session.current_title"></a>
                                        <span class="text-[10px] text-slate-400 truncate block" x-text="session.current_url"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold capitalize"
                                              :class="{
                                                  'bg-blue-50 text-blue-700 border border-blue-100': session.referrer_domain === 'facebook',
                                                  'bg-red-50 text-red-700 border border-red-100': session.referrer_domain === 'google' || session.referrer_domain === 'youtube',
                                                  'bg-pink-50 text-pink-700 border border-pink-100': session.referrer_domain === 'instagram',
                                                  'bg-emerald-50 text-emerald-700 border border-emerald-100': session.referrer_domain === 'whatsapp',
                                                  'bg-slate-100 text-slate-700': session.referrer_domain === 'direct' || !session.referrer_domain
                                              }"
                                              x-text="session.referrer_domain || 'Direct'">
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">
                                        <div class="flex items-center gap-1.5 font-medium">
                                            <span class="capitalize" x-text="session.device_type"></span> · 
                                            <span x-text="session.browser"></span>
                                        </div>
                                        <div class="text-[10px] text-slate-400" x-text="session.platform"></div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-medium text-slate-500" x-text="session.time_ago"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: Product Reach -->
        <div x-show="activeTab === 'products'" class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 mb-4 gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Most Viewed Products</h3>
                        <p class="text-xs text-slate-400">Ranked by total pageviews in selected period ({{ $period }})</p>
                    </div>
                    <span class="text-xs font-semibold text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-100">
                        {{ $productReach->count() }} Products Viewed
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left">
                        <thead class="bg-slate-50/60 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Rank</th>
                                <th class="px-4 py-3">Product</th>
                                <th class="px-4 py-3">Price & Stock</th>
                                <th class="px-4 py-3 text-center">Period Views</th>
                                <th class="px-4 py-3 text-center">Unique Viewers</th>
                                <th class="px-4 py-3 text-center">Traffic Share</th>
                                <th class="px-4 py-3 text-right">All-Time Views</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse ($productReach as $index => $item)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-4 py-3 font-extrabold text-slate-400">
                                        #{{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if ($item['thumbnail'])
                                                <img src="{{ $item['thumbnail'] }}" alt="{{ $item['name'] }}" class="h-10 w-10 object-cover rounded-lg border border-slate-200 flex-shrink-0">
                                            @else
                                                <div class="h-10 w-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 flex-shrink-0">
                                                    <i data-lucide="package" class="h-5 w-5"></i>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                @if ($item['product'])
                                                    <a href="{{ route('admin.products.edit', $item['product_id']) }}" class="font-bold text-slate-900 hover:text-indigo-600 line-clamp-1">
                                                        {{ $item['name'] }}
                                                    </a>
                                                    @if ($item['slug'])
                                                        <a href="{{ route('product', $item['slug']) }}" target="_blank" class="text-[10px] text-indigo-500 hover:underline inline-flex items-center gap-0.5">
                                                            View on Store <i data-lucide="external-link" class="h-2.5 w-2.5"></i>
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-slate-500">{{ $item['name'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($item['price'])
                                            <div class="font-bold text-slate-900">৳{{ number_format($item['price']) }}</div>
                                        @endif
                                        @if ($item['in_stock'])
                                            <span class="inline-block text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded">In Stock</span>
                                        @else
                                            <span class="inline-block text-[10px] font-semibold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="font-extrabold text-sm text-slate-900">{{ number_format($item['views_total']) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-600">
                                        {{ number_format($item['unique_visitors']) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-16 bg-slate-100 rounded-full h-2 overflow-hidden">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, $item['share_pct']) }}%"></div>
                                            </div>
                                            <span class="font-bold text-[11px] text-slate-700">{{ $item['share_pct'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-indigo-600">
                                        {{ number_format($item['all_time_views']) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                        <p class="font-semibold text-slate-600">No product views recorded yet for this period</p>
                                        <p class="text-[11px] mt-0.5">As visitors view products in your catalog, their reach will appear here.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 3: Blog Reach -->
        <div x-show="activeTab === 'blogs'" class="space-y-4">
            <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 mb-4 gap-2">
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">Most Read Blog Articles</h3>
                        <p class="text-xs text-slate-400">Ranked by readership and pageviews in selected period ({{ $period }})</p>
                    </div>
                    <span class="text-xs font-semibold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-md border border-rose-100">
                        {{ $blogReach->count() }} Articles Read
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left">
                        <thead class="bg-slate-50/60 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-3">Rank</th>
                                <th class="px-4 py-3">Blog Article</th>
                                <th class="px-4 py-3 text-center">Period Reads</th>
                                <th class="px-4 py-3 text-center">Unique Readers</th>
                                <th class="px-4 py-3 text-center">Audience Share</th>
                                <th class="px-4 py-3 text-right">All-Time Reads</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse ($blogReach as $index => $item)
                                <tr class="hover:bg-slate-50/60 transition-colors">
                                    <td class="px-4 py-3 font-extrabold text-slate-400">
                                        #{{ $index + 1 }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if ($item['image'])
                                                <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="h-10 w-14 object-cover rounded-lg border border-slate-200 flex-shrink-0">
                                            @else
                                                <div class="h-10 w-14 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 flex-shrink-0">
                                                    <i data-lucide="book-open" class="h-5 w-5"></i>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                @if ($item['blog'])
                                                    <a href="{{ route('admin.blog-posts.edit', $item['blog_id']) }}" class="font-bold text-slate-900 hover:text-rose-600 line-clamp-1">
                                                        {{ $item['title'] }}
                                                    </a>
                                                    @if ($item['slug'])
                                                        <a href="{{ route('blog', $item['slug']) }}" target="_blank" class="text-[10px] text-rose-500 hover:underline inline-flex items-center gap-0.5">
                                                            Read on Store <i data-lucide="external-link" class="h-2.5 w-2.5"></i>
                                                        </a>
                                                    @endif
                                                @else
                                                    <span class="text-slate-500">{{ $item['title'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="font-extrabold text-sm text-slate-900">{{ number_format($item['views_total']) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-bold text-slate-600">
                                        {{ number_format($item['unique_readers']) }}
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="w-16 bg-slate-100 rounded-full h-2 overflow-hidden">
                                                <div class="bg-rose-500 h-2 rounded-full" style="width: {{ min(100, $item['share_pct']) }}%"></div>
                                            </div>
                                            <span class="font-bold text-[11px] text-slate-700">{{ $item['share_pct'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono font-bold text-rose-600">
                                        {{ number_format($item['all_time_views']) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                        <p class="font-semibold text-slate-600">No blog reads recorded yet for this period</p>
                                        <p class="text-[11px] mt-0.5">When customers read blog posts, metrics will show here.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 4: Traffic Sources & Devices -->
        <div x-show="activeTab === 'sources'" class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Traffic Sources -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="share-2" class="h-4 w-4 text-indigo-600"></i>
                            <h3 class="text-sm font-bold text-slate-900">Traffic Acquisition Sources</h3>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Where visitors came from</span>
                    </div>

                    <div class="space-y-3">
                        @forelse ($trafficSources as $source)
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2.5 w-1/3">
                                    <span class="h-2 w-2 rounded-full {{ $source['domain'] === 'facebook' ? 'bg-blue-600' : ($source['domain'] === 'google' ? 'bg-red-500' : ($source['domain'] === 'instagram' ? 'bg-pink-500' : ($source['domain'] === 'whatsapp' ? 'bg-emerald-500' : 'bg-slate-400'))) }}"></span>
                                    <span class="font-bold text-slate-800">{{ $source['name'] }}</span>
                                </div>
                                <div class="flex-1 mx-4">
                                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ min(100, $source['share_pct']) }}%"></div>
                                    </div>
                                </div>
                                <div class="w-20 text-right">
                                    <span class="font-extrabold text-slate-900">{{ number_format($source['count']) }}</span>
                                    <span class="text-[10px] text-slate-400 ml-1">({{ $source['share_pct'] }}%)</span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 text-center py-4">No traffic source data available</p>
                        @endforelse
                    </div>
                </div>

                <!-- Device Breakdown -->
                <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-5">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="smartphone" class="h-4 w-4 text-blue-600"></i>
                            <h3 class="text-sm font-bold text-slate-900">Visitor Devices</h3>
                        </div>
                        <span class="text-xs text-slate-400 font-medium">Mobile vs Desktop</span>
                    </div>

                    <div class="grid grid-cols-3 gap-3 mb-5">
                        @php
                            $totalDevices = $deviceData->sum('count');
                            $mobileCount = $deviceData->firstWhere('device_type', 'mobile')?->count ?? 0;
                            $desktopCount = $deviceData->firstWhere('device_type', 'desktop')?->count ?? 0;
                            $tabletCount = $deviceData->firstWhere('device_type', 'tablet')?->count ?? 0;
                            $mobilePct = $totalDevices > 0 ? round(($mobileCount / $totalDevices) * 100, 1) : 0;
                            $desktopPct = $totalDevices > 0 ? round(($desktopCount / $totalDevices) * 100, 1) : 0;
                            $tabletPct = $totalDevices > 0 ? round(($tabletCount / $totalDevices) * 100, 1) : 0;
                        @endphp

                        <div class="p-3.5 bg-slate-50 rounded-xl text-center border border-slate-100">
                            <i data-lucide="smartphone" class="h-5 w-5 text-blue-600 mx-auto mb-1"></i>
                            <div class="text-xs font-bold text-slate-500">Mobile</div>
                            <div class="text-lg font-black text-slate-900 mt-0.5">{{ $mobilePct }}%</div>
                            <div class="text-[10px] text-slate-400">{{ number_format($mobileCount) }} visits</div>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-xl text-center border border-slate-100">
                            <i data-lucide="monitor" class="h-5 w-5 text-indigo-600 mx-auto mb-1"></i>
                            <div class="text-xs font-bold text-slate-500">Desktop</div>
                            <div class="text-lg font-black text-slate-900 mt-0.5">{{ $desktopPct }}%</div>
                            <div class="text-[10px] text-slate-400">{{ number_format($desktopCount) }} visits</div>
                        </div>

                        <div class="p-3.5 bg-slate-50 rounded-xl text-center border border-slate-100">
                            <i data-lucide="tablet" class="h-5 w-5 text-purple-600 mx-auto mb-1"></i>
                            <div class="text-xs font-bold text-slate-500">Tablet</div>
                            <div class="text-lg font-black text-slate-900 mt-0.5">{{ $tabletPct }}%</div>
                            <div class="text-[10px] text-slate-400">{{ number_format($tabletCount) }} visits</div>
                        </div>
                    </div>

                    <!-- Top Browsers -->
                    <div class="border-t border-slate-100 pt-3">
                        <h4 class="text-xs font-bold text-slate-600 mb-2">Top Browsers</h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($browserData as $b)
                                <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 rounded-md text-slate-700">
                                    {{ $b->browser }}: <strong class="text-slate-900">{{ number_format($b->count) }}</strong>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alpine.js Live Polling Script -->
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
                    }, 5000);
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
                        console.error('Analytics live sync error', e);
                    }
                }
            }
        }
    </script>
</x-app-layout>
