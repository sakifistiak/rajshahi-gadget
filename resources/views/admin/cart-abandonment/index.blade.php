<x-app-layout>

    @if (session('success'))
        <div class="mb-5 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded text-xs font-semibold shadow-sm flex items-center gap-2">
            <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Settings Panel -->
    <div class="bg-white rounded-md border border-slate-200 shadow-sm mb-5">
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
            <h3 class="text-[13px] font-bold text-slate-800 flex items-center gap-2">
                <i data-lucide="settings" class="h-3.5 w-3.5 text-slate-400"></i>
                Abandoned Cart Settings
            </h3>
            <p class="text-[10px] text-slate-400 mt-0.5">Carts idle past the threshold below are flagged "Abandoned." SMS reminders require an SMS gateway to be configured — until then reminders are logged only, never actually sent.</p>
        </div>
        <form method="POST" action="{{ route('admin.cart-abandonment.settings') }}" class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
            @csrf
            <div class="md:col-span-2">
                <label class="flex items-center gap-2.5 text-xs font-bold text-slate-700 cursor-pointer select-none">
                    <input type="checkbox" name="cart_abandonment_enabled" value="1" {{ ($settings['cart_abandonment_enabled'] ?? '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                    Enable abandoned-cart tracking &amp; reminders
                </label>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Abandonment threshold (minutes)</label>
                <input type="number" min="5" step="1" name="cart_abandonment_threshold_minutes" value="{{ $settings['cart_abandonment_threshold_minutes'] }}" class="w-full px-3 py-2 rounded border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-blue-500">
                <p class="text-[10px] text-slate-400 mt-1">A cart with no activity for this long is flagged as abandoned.</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Resend cooldown (hours)</label>
                <input type="number" min="1" step="1" name="cart_abandonment_resend_cooldown_hours" value="{{ $settings['cart_abandonment_resend_cooldown_hours'] }}" class="w-full px-3 py-2 rounded border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-blue-500">
                <p class="text-[10px] text-slate-400 mt-1">Minimum gap before a reminder can be sent again to the same cart.</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">SMS reminder message</label>
                <textarea name="cart_abandonment_sms_template" rows="3" class="w-full px-3 py-2 rounded border border-slate-300 text-xs font-medium focus:ring-1 focus:ring-blue-500">{{ $settings['cart_abandonment_sms_template'] }}</textarea>
                <p class="text-[10px] text-slate-400 mt-1">Placeholders: <code class="bg-slate-100 px-1 rounded">{name}</code>, <code class="bg-slate-100 px-1 rounded">{items}</code>, <code class="bg-slate-100 px-1 rounded">{value}</code></p>
            </div>
            <div class="md:col-span-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold uppercase rounded shadow-sm transition-colors">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Status Filter Tabs -->
    <div class="bg-white rounded-sm border border-slate-200 shadow-sm p-1 flex overflow-x-auto gap-1 mb-5">
        @php $currentStatus = request('status', 'all'); @endphp

        <a href="{{ route('admin.cart-abandonment.index', array_merge(request()->query(), ['status' => 'all'])) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5 {{ $currentStatus === 'all' ? 'bg-blue-50 text-blue-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <span>All</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'all' ? 'bg-blue-100 text-blue-700' : 'bg-slate-100 text-slate-600' }}">{{ $statusCounts['all'] }}</span>
        </a>
        <a href="{{ route('admin.cart-abandonment.index', array_merge(request()->query(), ['status' => 'active'])) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5 {{ $currentStatus === 'active' ? 'bg-sky-50 text-sky-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <span>Active</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'active' ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-600' }}">{{ $statusCounts['active'] }}</span>
        </a>
        <a href="{{ route('admin.cart-abandonment.index', array_merge(request()->query(), ['status' => 'abandoned'])) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5 {{ $currentStatus === 'abandoned' ? 'bg-amber-50 text-amber-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <span>Abandoned</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'abandoned' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-600' }}">{{ $statusCounts['abandoned'] }}</span>
        </a>
        <a href="{{ route('admin.cart-abandonment.index', array_merge(request()->query(), ['status' => 'reminded'])) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5 {{ $currentStatus === 'reminded' ? 'bg-purple-50 text-purple-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <span>Reminded</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'reminded' ? 'bg-purple-100 text-purple-700' : 'bg-slate-100 text-slate-600' }}">{{ $statusCounts['reminded'] }}</span>
        </a>
        <a href="{{ route('admin.cart-abandonment.index', array_merge(request()->query(), ['status' => 'recovered'])) }}"
           class="px-3 py-1.5 text-xs font-bold rounded-sm transition-colors flex items-center gap-1.5 {{ $currentStatus === 'recovered' ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:text-slate-900 hover:bg-slate-50' }}">
            <span>Recovered</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $currentStatus === 'recovered' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $statusCounts['recovered'] }}</span>
        </a>
    </div>

    <!-- Table Panel -->
    <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3 bg-slate-50/50">
            <div>
                <h3 class="text-[13px] font-bold text-slate-800">Abandoned Carts</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Carts that were started but never completed at checkout.</p>
            </div>
            <form method="GET" action="{{ route('admin.cart-abandonment.index') }}" class="flex items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif
                <div class="relative">
                    <i data-lucide="search" class="absolute left-2.5 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, phone, email..."
                           class="pl-8 pr-3 py-1.5 border border-slate-200 rounded text-xs text-slate-700 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 w-64">
                </div>
                <button type="submit" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold uppercase rounded shadow-sm transition-colors">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.cart-abandonment.index', request()->only('status')) }}" class="px-2 py-1.5 border border-slate-200 text-slate-500 hover:bg-slate-100 text-xs rounded">Clear</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/20">
                    <tr>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Cart Value</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Last Activity</th>
                        <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($carts as $cart)
                        <tr class="hover:bg-slate-50/30 transition-colors">
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <div class="text-xs font-semibold text-slate-800">{{ $cart->customer_name ?: 'Anonymous' }}</div>
                                <div class="text-[9px] text-slate-400">{{ $cart->phone ?: ($cart->email ?: 'No contact info') }}</div>
                            </td>
                            <td class="px-6 py-3.5 text-xs text-slate-600 max-w-xs truncate">
                                {{ collect($cart->items)->pluck('name')->filter()->implode(', ') ?: '—' }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-xs font-bold text-slate-800">
                                ৳{{ number_format($cart->cart_value) }}
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                <div class="text-xs text-slate-600">{{ $cart->last_activity_at?->format('d M, Y') }}</div>
                                <div class="text-[9px] text-slate-400">{{ $cart->last_activity_at?->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap">
                                @if ($cart->status === 'active')
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-sky-50 text-sky-600 border border-sky-100">Active</span>
                                @elseif ($cart->status === 'abandoned')
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-amber-50 text-amber-600 border border-amber-100">Abandoned</span>
                                @elseif ($cart->status === 'reminded')
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-purple-50 text-purple-600 border border-purple-100">Reminded</span>
                                @elseif ($cart->status === 'recovered')
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-emerald-50 text-emerald-600 border border-emerald-100">Recovered</span>
                                @endif
                                @if ($cart->contacted_at)
                                    <span class="mt-1 px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-blue-50 text-blue-600 border border-blue-100">Contacted</span>
                                @endif
                            </td>
                            <td class="px-6 py-3.5 whitespace-nowrap text-right text-xs font-medium">
                                <div class="flex justify-end items-center gap-3">
                                    @if ($cart->phone)
                                        <a href="tel:{{ \App\Support\PhoneNumber::tel($cart->phone) }}" class="text-slate-600 hover:text-slate-900 font-semibold inline-flex items-center gap-1" title="Call">
                                            <i data-lucide="phone" class="h-3.5 w-3.5 text-slate-500"></i>
                                            Call
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.cart-abandonment.show', $cart) }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1">
                                        <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                                        View
                                    </a>
                                    @unless ($cart->contacted_at)
                                        <form action="{{ route('admin.cart-abandonment.contacted', $cart) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-emerald-600 hover:text-emerald-800 font-semibold inline-flex items-center gap-1">
                                                <i data-lucide="check" class="h-3.5 w-3.5"></i>
                                                Mark Contacted
                                            </button>
                                        </form>
                                    @endunless
                                    <form action="{{ route('admin.cart-abandonment.destroy', $cart) }}" method="POST" onsubmit="return confirm('Delete this record?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold inline-flex items-center gap-1">
                                            <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <i data-lucide="alert-triangle" class="h-8 w-8 text-slate-300 mb-2"></i>
                                    <p class="text-xs text-slate-400 font-semibold">No abandoned carts found</p>
                                    <p class="text-[10px] text-slate-300 mt-0.5">Carts left idle past the threshold will appear here.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($carts->hasPages())
            <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/20">
                {{ $carts->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
