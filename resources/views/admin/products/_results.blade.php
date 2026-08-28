<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-slate-100">
        <thead class="bg-slate-50/20">
            <tr>
                <th class="px-6 py-3 text-left w-10">
                    <input type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" :checked="allSelected" @change="toggleAll($event.target.checked)">
                </th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Image') }}</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Product Name') }}</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Category') }}</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Condition') }}</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Price') }}</th>
                <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Stock') }}</th>
                <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($products as $product)
                <tr class="hover:bg-slate-50/30 transition-colors">
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        <input type="checkbox" value="{{ $product->id }}" x-model.number="selected" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        <img src="{{ $product->primaryImage() }}" alt="{{ $product->name }}" class="h-8 w-8 object-cover rounded border border-slate-100 shadow-sm">
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        <div class="text-xs font-semibold text-slate-800">{{ $product->name }}</div>
                        <div class="text-[9px] text-slate-400">Brand: {{ $product->brand?->name }}</div>
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-600">
                        {{ $product->category?->name }}
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-slate-100 text-slate-600 border border-slate-200/50">
                            {{ $product->condition?->short }}
                        </span>
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        <div class="text-xs font-bold text-slate-800">৳{{ number_format($product->price) }}</div>
                        @if ($product->compare_at_price)
                            <div class="text-[9px] text-slate-400 line-through">৳{{ number_format($product->compare_at_price) }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap">
                        @if ($product->in_stock)
                            <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-emerald-50 text-emerald-600 border border-emerald-100">
                                {{ __('In Stock') }}
                            </span>
                        @else
                            <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded bg-rose-50 text-rose-600 border border-rose-100">
                                {{ __('Out of Stock') }}
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-3.5 whitespace-nowrap text-right text-xs font-medium">
                        <div class="flex justify-end items-center gap-3">
                            <a href="{{ route('admin.products.edit', ['product' => $product, 'return' => request()->fullUrl()]) }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1">
                                <i data-lucide="edit-3" class="h-3.5 w-3.5"></i>
                                {{ __('Edit') }}
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="return" value="{{ request()->fullUrl() }}">
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
                    <td colspan="8" class="px-6 py-4 text-center text-xs text-slate-400">
                        @if (request('q') || request('category') || request('condition') || request('brand') || request('stock'))
                            {{ __('No products match your search/filters') }}
                        @else
                            {{ __('No products found') }}
                        @endif
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

@if ($products->hasPages())
    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/20">
        {{ $products->links() }}
    </div>
@endif
