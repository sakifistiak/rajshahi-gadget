<x-app-layout>

    <div class="mb-5 flex items-center justify-between">
        <div>
            <a href="{{ route('admin.cart-abandonment.index') }}" class="text-xs font-semibold text-slate-500 hover:text-blue-600 inline-flex items-center gap-1">
                <i data-lucide="arrow-left" class="h-3.5 w-3.5"></i>
                Back to Abandoned Carts
            </a>
            <h1 class="text-lg font-bold text-slate-900 mt-1">{{ $cart->customer_name ?: 'Anonymous Cart' }}</h1>
        </div>
        @if ($cart->status === 'abandoned')
            <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded bg-amber-50 text-amber-600 border border-amber-100">Abandoned</span>
        @elseif ($cart->status === 'reminded')
            <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded bg-purple-50 text-purple-600 border border-purple-100">Reminded</span>
        @elseif ($cart->status === 'recovered')
            <span class="px-2.5 py-1 inline-flex text-[10px] leading-5 font-bold rounded bg-emerald-50 text-emerald-600 border border-emerald-100">Recovered</span>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="md:col-span-2 bg-white rounded-md border border-slate-200 shadow-sm">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-[13px] font-bold text-slate-800">Cart Items</h3>
            </div>
            <div class="divide-y divide-slate-100">
                @forelse ($cart->items ?? [] as $item)
                    <div class="px-5 py-3 flex items-center gap-3">
                        @if (!empty($item['image']))
                            <img src="{{ $item['image'] }}" alt="" class="h-12 w-12 rounded object-cover bg-slate-100 shrink-0">
                        @endif
                        <div class="min-w-0 flex-1">
                            <div class="text-xs font-semibold text-slate-800 truncate">{{ $item['name'] ?? $item['slug'] }}</div>
                            <div class="text-[10px] text-slate-400">Qty: {{ $item['quantity'] ?? 1 }}</div>
                        </div>
                        @if (isset($item['price']))
                            <div class="text-xs font-bold text-slate-700">৳{{ number_format($item['price'] * ($item['quantity'] ?? 1)) }}</div>
                        @endif
                    </div>
                @empty
                    <p class="px-5 py-6 text-xs text-slate-400 text-center">No items captured.</p>
                @endforelse
            </div>
            <div class="px-5 py-3 border-t border-slate-100 flex justify-between items-center bg-slate-50/50">
                <span class="text-xs font-bold text-slate-700">Cart Value</span>
                <span class="text-sm font-bold text-slate-900">৳{{ number_format($cart->cart_value) }}</span>
            </div>
        </div>

        <div class="space-y-5">
            <div class="bg-white rounded-md border border-slate-200 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-[13px] font-bold text-slate-800">Contact Info</h3>
                </div>
                <div class="p-5 space-y-3 text-xs">
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Name</div>
                        <div class="text-slate-700 font-semibold">{{ $cart->customer_name ?: '—' }}</div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Phone</div>
                        <div class="text-slate-700 font-semibold">
                            @if ($cart->phone)
                                <a href="tel:{{ \App\Support\PhoneNumber::tel($cart->phone) }}" class="text-blue-600 hover:underline">{{ $cart->phone }}</a>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Email</div>
                        <div class="text-slate-700 font-semibold">{{ $cart->email ?: '—' }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-md border border-slate-200 shadow-sm">
                <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                    <h3 class="text-[13px] font-bold text-slate-800">Timeline</h3>
                </div>
                <div class="p-5 space-y-3 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Last activity</span>
                        <span class="font-semibold text-slate-700">{{ $cart->last_activity_at?->format('d M, Y h:i A') ?? '—' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Reminded at</span>
                        <span class="font-semibold text-slate-700">{{ $cart->reminded_at?->format('d M, Y h:i A') ?? 'Not sent yet' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Contacted at</span>
                        <span class="font-semibold text-slate-700">{{ $cart->contacted_at?->format('d M, Y h:i A') ?? '—' }}</span>
                    </div>
                    @if ($cart->order)
                        <div class="flex justify-between pt-2 border-t border-slate-100">
                            <span class="text-slate-500">Recovered as order</span>
                            <a href="{{ route('admin.orders.show', $cart->order) }}" class="font-bold text-emerald-600 hover:underline">{{ $cart->order->order_number }}</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="flex flex-col gap-2">
                @unless ($cart->contacted_at)
                    <form action="{{ route('admin.cart-abandonment.contacted', $cart) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold uppercase rounded shadow-sm transition-colors">
                            Mark Contacted
                        </button>
                    </form>
                @endunless
                <form action="{{ route('admin.cart-abandonment.destroy', $cart) }}" method="POST" onsubmit="return confirm('Delete this record?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 text-[11px] font-bold uppercase rounded transition-colors">
                        Delete Record
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
