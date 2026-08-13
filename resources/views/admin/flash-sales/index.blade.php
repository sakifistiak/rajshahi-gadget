<x-app-layout>
    <div class="p-6 w-full space-y-6">
        @if (session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded text-xs font-semibold shadow-sm flex items-center gap-2">
                <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center justify-between bg-white p-5 rounded-sm border border-gray-200 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Flash Sales</h1>
                <p class="text-xs text-gray-500 mt-1">Only one flash sale should be live at a time — the homepage shows whichever campaign is currently active.</p>
            </div>
            <a href="{{ route('admin.flash-sales.create') }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-sm shadow-sm transition-colors flex items-center gap-1.5">
                <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                New Flash Sale
            </a>
        </div>

        <div class="bg-white rounded-sm border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Title</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Starts</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ends</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Products</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($flashSales as $sale)
                            @php
                                $status = $sale->statusLabel();
                                $statusClass = match($status) {
                                    'Live' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
                                    'Upcoming' => 'bg-blue-50 text-blue-600 border-blue-100',
                                    'Expired' => 'bg-slate-100 text-slate-500 border-slate-200',
                                    default => 'bg-rose-50 text-rose-600 border-rose-100',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <a href="{{ route('admin.flash-sales.edit', $sale) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">{{ $sale->title }}</a>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap">
                                    <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded border uppercase {{ $statusClass }}">{{ $status }}</span>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-600">{{ $sale->starts_at->format('d M Y, h:i A') }}</td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-600">{{ $sale->ends_at->format('d M Y, h:i A') }}</td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs font-semibold text-slate-700">{{ $sale->items_count }} {{ Str::plural('product', $sale->items_count) }}</td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-right text-xs font-medium">
                                    <div class="flex justify-end items-center gap-3">
                                        <a href="{{ route('admin.flash-sales.edit', $sale) }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1">
                                            <i data-lucide="pencil" class="h-3.5 w-3.5"></i> Edit
                                        </a>
                                        <form action="{{ route('admin.flash-sales.destroy', $sale) }}" method="POST" onsubmit="return confirm('Delete this flash sale?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800 font-semibold inline-flex items-center gap-1">
                                                <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <i data-lucide="flame" class="h-8 w-8 text-slate-300 mb-2"></i>
                                        <p class="text-xs text-slate-400 font-semibold">No flash sales yet</p>
                                        <p class="text-[10px] text-slate-300 mt-0.5">Create one to feature discounted products on the homepage.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
