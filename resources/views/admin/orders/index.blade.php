<x-app-layout>

    @if (session('success'))
        <div class="mb-5 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded text-xs font-semibold shadow-sm flex items-center gap-2">
            <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Status Filter Tabs -->
    <div class="bg-white rounded-sm border border-slate-200 shadow-sm p-1 flex overflow-x-auto gap-1 mb-5">
        @php
            $currentStatus = request('status', 'all');
            $routeName = $routeName ?? 'admin.orders.index';
        @endphp

        <a href="{{ route($routeName, array_merge(request()->query(), ['status' => 'all'])) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5 {{ $currentStatus === 'all' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <span>All</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'all' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' }}">{{ $statusCounts['all'] }}</span>
        </a>

        <a href="{{ route($routeName, array_merge(request()->query(), ['status' => 'pending'])) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5 {{ $currentStatus === 'pending' ? 'bg-amber-50 text-amber-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <span>Pending</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">{{ $statusCounts['pending'] }}</span>
        </a>

        <a href="{{ route($routeName, array_merge(request()->query(), ['status' => 'processing'])) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5 {{ $currentStatus === 'processing' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <span>Processing</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'processing' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' }}">{{ $statusCounts['processing'] }}</span>
        </a>

        <a href="{{ route($routeName, array_merge(request()->query(), ['status' => 'shipped'])) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5 {{ $currentStatus === 'shipped' ? 'bg-purple-50 text-purple-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <span>Shipped</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'shipped' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' }}">{{ $statusCounts['shipped'] }}</span>
        </a>

        <a href="{{ route($routeName, array_merge(request()->query(), ['status' => 'delivered'])) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5 {{ $currentStatus === 'delivered' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <span>Delivered</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'delivered' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $statusCounts['delivered'] }}</span>
        </a>

        <a href="{{ route($routeName, array_merge(request()->query(), ['status' => 'cancelled'])) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5 {{ $currentStatus === 'cancelled' ? 'bg-rose-50 text-rose-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <span>Cancelled</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'cancelled' ? 'bg-rose-100 text-rose-700' : 'bg-slate-100 text-slate-600' }}">{{ $statusCounts['cancelled'] }}</span>
        </a>
    </div>

    <!-- Orders Table Panel -->
    <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
        
        <!-- Header Controls -->
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h3 class="text-[13px] font-bold text-slate-800">{{ __($pageTitle ?? 'Order Management') }}</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">{{ $pageSubtitle ?? 'Track and manage customer orders, update status and view details.' }}</p>
            </div>

            <!-- Search Form -->
            <form method="GET" action="{{ route($routeName) }}" class="flex items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative">
                    <i data-lucide="search" class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search order #, customer, phone..."
                           class="pl-8 pr-3 py-1.5 border border-slate-200 rounded text-xs text-slate-700 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 w-64">
                </div>
                <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold uppercase rounded shadow-sm transition-colors flex items-center gap-1">
                    {{ __('Search') }}
                </button>
                @if(request('search'))
                    <a href="{{ route($routeName, request()->only('status')) }}" class="px-2 py-1.5 border border-slate-200 text-slate-500 hover:bg-slate-100 text-xs rounded">Clear</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/20">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Order #') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Customer') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Area') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Items') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Total') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Payment') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Date') }}</th>
                        <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($orders as $order)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">
                                    {{ $order->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <div class="text-xs font-semibold text-slate-800">{{ $order->customer_name }}</div>
                                <div class="text-[9px] text-slate-400">{{ $order->phone }}</div>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                @if($order->delivery_method === 'store_pickup')
                                    Pickup
                                @else
                                    {{ $order->delivery_area === 'inside_dhaka' ? 'Inside Dhaka' : ($order->delivery_area === 'outside_dhaka' ? 'Outside Dhaka' : '—') }}
                                @endif
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-xs font-semibold text-slate-700">
                                {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <div class="text-xs font-bold text-slate-800">৳{{ number_format($order->total) }}</div>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-slate-100 text-slate-600 border border-slate-200/50 uppercase">
                                    {{ $order->payment_method }}
                                </span>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if ($order->status === 'pending')
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-amber-50 text-amber-600 border border-amber-100">
                                        Pending
                                    </span>
                                @elseif ($order->status === 'processing')
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-blue-50 text-blue-600 border border-blue-100">
                                        Processing
                                    </span>
                                @elseif ($order->status === 'shipped')
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-purple-50 text-purple-600 border border-purple-100">
                                        Shipped
                                    </span>
                                @elseif ($order->status === 'delivered')
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-emerald-50 text-emerald-600 border border-emerald-100">
                                        Delivered
                                    </span>
                                @elseif ($order->status === 'cancelled')
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-rose-50 text-rose-600 border border-rose-100">
                                        Cancelled
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-slate-100 text-slate-600 border border-slate-200/50">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <div class="text-xs text-slate-600">{{ $order->created_at->format('d M, Y') }}</div>
                                <div class="text-[9px] text-slate-400">{{ $order->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-right text-xs font-medium">
                                <div class="flex justify-end items-center gap-3">
                                    <button type="button" onclick="directDownloadInvoice('{{ route('admin.orders.invoice', $order) }}?download=1', this)" class="text-slate-600 hover:text-slate-900 font-semibold inline-flex items-center gap-1 cursor-pointer" title="Download Invoice">
                                        <i data-lucide="download" class="h-3.5 w-3.5 text-slate-500"></i>
                                        {{ __('Invoice') }}
                                    </button>
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1">
                                        <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                                        {{ __('View') }}
                                    </a>
                                    <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold inline-flex items-center gap-1">
                                            <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="shopping-cart" class="h-8 w-8 text-slate-300 mb-2"></i>
                                    <p class="text-xs text-slate-400 font-semibold">{{ __('No orders found') }}</p>
                                    <p class="text-[10px] text-slate-300 mt-0.5">Orders will appear here once customers checkout.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if ($orders->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/20">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <script>
        function directDownloadInvoice(url, btn) {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="inline-block animate-spin mr-1">⌛</span> <span class="text-[10px]">Downloading...</span>';
            btn.disabled = true;

            const iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.left = '-9999px';
            iframe.style.top = '0';
            iframe.style.width = '800px';
            iframe.style.height = '1200px';
            iframe.style.opacity = '0';
            iframe.style.pointerEvents = 'none';
            iframe.src = url;
            document.body.appendChild(iframe);

            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                if (typeof lucide !== 'undefined') lucide.createIcons();
                setTimeout(() => iframe.remove(), 10000);
            }, 3000);
        }
    </script>
</x-app-layout>
