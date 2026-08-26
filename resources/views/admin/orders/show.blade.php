<x-app-layout>

    @if (session('success'))
        <div class="mb-5 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded text-xs font-semibold shadow-sm flex items-center gap-2">
            <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Header Panel -->
    <div class="bg-white rounded-md border border-slate-200 shadow-sm p-5 mb-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="p-2.5 bg-blue-50 text-blue-600 rounded-xl border border-blue-100 flex items-center justify-center">
                <i data-lucide="shopping-cart" class="h-5 w-5"></i>
            </span>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-base font-bold text-slate-900">Order #{{ $order->order_number }}</h1>
                    @if ($order->status === 'pending')
                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-amber-50 text-amber-600 border border-amber-100">Pending</span>
                    @elseif ($order->status === 'processing')
                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-blue-50 text-blue-600 border border-blue-100">Processing</span>
                    @elseif ($order->status === 'shipped')
                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-purple-50 text-purple-600 border border-purple-100">Shipped</span>
                    @elseif ($order->status === 'delivered')
                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-emerald-50 text-emerald-600 border border-emerald-100">Delivered</span>
                    @elseif ($order->status === 'cancelled')
                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-rose-50 text-rose-600 border border-rose-100">Cancelled</span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="rounded bg-blue-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-blue-700 inline-flex items-center gap-1.5 transition-colors shadow-sm">
                <i data-lucide="printer" class="h-3.5 w-3.5"></i>
                Invoice / Print
            </a>
            <a href="{{ route('admin.orders.index') }}" class="rounded border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 inline-flex items-center gap-1.5 transition-colors">
                <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
                Back to Orders
            </a>
            <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="rounded bg-rose-50 border border-rose-200 px-3 py-2 text-xs font-bold text-rose-600 hover:bg-rose-100 inline-flex items-center gap-1.5 transition-colors">
                    <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <!-- 2-Column Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-5">
        
        <!-- Left: Customer & Delivery Details (2 cols) -->
        <div class="lg:col-span-2 space-y-5">
            
            <!-- Customer Details Card -->
            <div class="bg-white rounded-md border border-slate-200 shadow-sm p-5">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 mb-4">
                    <i data-lucide="user" class="h-4.5 w-4.5 text-blue-600"></i>
                    <h3 class="text-sm font-bold text-slate-900">Customer & Shipping Information</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Customer Name</span>
                        <span class="text-xs font-semibold text-slate-800 mt-1 block">{{ $order->customer_name }}</span>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Phone Number</span>
                        <span class="text-xs font-semibold text-slate-800 mt-1 block">{{ $order->phone }}</span>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Email Address</span>
                        <span class="text-xs font-semibold text-slate-800 mt-1 block">{{ $order->email ?: 'N/A' }}</span>
                    </div>

                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Delivery Method</span>
                        <span class="text-xs font-semibold mt-1 inline-flex items-center gap-1 px-2 py-0.5 rounded {{ $order->delivery_method === 'store_pickup' ? 'bg-indigo-50 text-indigo-600 border border-indigo-100' : 'bg-slate-100 text-slate-700 border border-slate-200' }}">
                            {{ $order->delivery_method === 'store_pickup' ? 'Store Pickup' : 'Home Delivery' }}
                        </span>
                    </div>

                    @if($order->delivery_method === 'store_pickup')
                        <div class="sm:col-span-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pickup Outlet</span>
                            @if($order->storeLocation)
                                <span class="text-xs font-semibold text-slate-800 mt-1 block">{{ $order->storeLocation->name }}</span>
                                <div class="text-xs text-slate-600 mt-0.5 leading-relaxed">{!! $order->storeLocation->address !!}</div>
                            @else
                                <span class="text-xs text-slate-400 mt-1 block">Outlet no longer available</span>
                            @endif
                        </div>
                    @else
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Division</span>
                            <span class="text-xs font-semibold text-slate-800 mt-1 block">{{ $order->division }}</span>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">District</span>
                            <span class="text-xs font-semibold text-slate-800 mt-1 block">{{ $order->district }}</span>
                        </div>

                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Upazila / Thana</span>
                            <span class="text-xs font-semibold text-slate-800 mt-1 block">{{ $order->upazila }}</span>
                        </div>

                        @if($order->union_area)
                            <div>
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Union / Area</span>
                                <span class="text-xs font-semibold text-slate-800 mt-1 block">{{ $order->union_area }}</span>
                            </div>
                        @endif

                        <div class="sm:col-span-2">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Delivery Address</span>
                            <span class="text-xs font-semibold text-slate-800 mt-1 block leading-relaxed">{{ $order->address }}</span>
                        </div>
                    @endif

                    @if($order->note)
                        <div class="sm:col-span-2 p-3 bg-amber-50/60 border border-amber-100 rounded-md">
                            <span class="text-[10px] font-bold text-amber-700 uppercase tracking-wider block mb-1">Customer Note</span>
                            <p class="text-xs text-slate-700 leading-relaxed">{{ $order->note }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Status Update & Payment Summary (1 col) -->
        <div class="space-y-5">
            
            <!-- Update Status Card -->
            <div class="bg-white rounded-md border border-slate-200 shadow-sm p-5">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 mb-4">
                    <i data-lucide="refresh-cw" class="h-4.5 w-4.5 text-blue-600"></i>
                    <h3 class="text-sm font-bold text-slate-900">Update Order Status</h3>
                </div>

                <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PATCH')
                    
                    <div>
                        <label class="text-xs font-bold text-slate-700 block mb-1">Current Status</label>
                        <select name="status" class="w-full rounded-sm border border-slate-200 px-3 py-2 text-xs font-semibold focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold uppercase rounded py-2 shadow-sm transition-colors flex items-center justify-center gap-1.5">
                        <i data-lucide="save" class="h-3.5 w-3.5"></i>
                        Update Status
                    </button>
                </form>
            </div>

            <!-- Payment Summary Card -->
            <div class="bg-white rounded-md border border-slate-200 shadow-sm p-5">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 mb-4">
                    <i data-lucide="credit-card" class="h-4.5 w-4.5 text-blue-600"></i>
                    <h3 class="text-sm font-bold text-slate-900">Payment Summary</h3>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex justify-between text-slate-600">
                        <span>Payment Method:</span>
                        <span class="font-bold text-slate-800 uppercase">{{ $order->payment_method }}</span>
                    </div>

                    <div class="flex justify-between text-slate-600">
                        <span>Subtotal:</span>
                        <span class="font-semibold text-slate-800">৳{{ number_format($order->subtotal) }}</span>
                    </div>

                    <div class="flex justify-between text-slate-600">
                        <span>Shipping Fee:</span>
                        <span class="font-semibold text-slate-800">৳{{ number_format($order->shipping_fee) }}</span>
                    </div>

                    <div class="border-t border-slate-100 pt-2.5 flex justify-between items-center">
                        <span class="font-bold text-slate-900">Total Amount:</span>
                        <span class="text-base font-extrabold text-blue-600">৳{{ number_format($order->total) }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Order Items Panel -->
    <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
            <i data-lucide="package" class="h-4.5 w-4.5 text-blue-600"></i>
            <h3 class="text-[13px] font-bold text-slate-800">Ordered Products ({{ $order->items->count() }})</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/20">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-center text-[10px] font-bold text-slate-400 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Line Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach ($order->items as $item)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <a href="{{ route('product', $item->product_slug) }}" target="_blank" class="text-xs font-bold text-slate-800 hover:text-blue-600">
                                    {{ $item->product_name }}
                                </a>
                                <div class="text-[9px] text-slate-400">Slug: {{ $item->product_slug }}</div>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-xs font-semibold text-slate-700">
                                ৳{{ number_format($item->unit_price) }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-center text-xs font-bold text-slate-800">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-right text-xs font-bold text-slate-900">
                                ৳{{ number_format($item->line_total) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-50/50 border-t border-slate-200">
                    <tr>
                        <td colspan="3" class="px-6 py-2.5 text-right text-xs font-bold text-slate-600">Subtotal:</td>
                        <td class="px-6 py-2.5 text-right text-xs font-bold text-slate-900">৳{{ number_format($order->subtotal) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-2.5 text-right text-xs font-bold text-slate-600">Shipping Fee:</td>
                        <td class="px-6 py-2.5 text-right text-xs font-bold text-slate-900">৳{{ number_format($order->shipping_fee) }}</td>
                    </tr>
                    <tr class="border-t border-slate-200">
                        <td colspan="3" class="px-6 py-3 text-right text-sm font-bold text-slate-900">Grand Total:</td>
                        <td class="px-6 py-3 text-right text-base font-extrabold text-blue-600">৳{{ number_format($order->total) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</x-app-layout>
