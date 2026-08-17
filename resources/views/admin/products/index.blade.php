<x-app-layout>

    @if (session('success'))
        <div class="mb-5 p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded text-xs font-semibold shadow-sm flex items-center gap-2">
            <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-5 p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded text-xs font-semibold shadow-sm flex items-center gap-2">
            <i data-lucide="alert-circle" class="h-4 w-4 text-rose-600"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Product Catalog Panel -->
    <div x-data="{ selected: [], get allSelected() { return {{ $products->count() }} > 0 && this.selected.length === {{ $products->count() }}; } }" class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">

        <!-- Header Controls -->
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div>
                <h3 class="text-[13px] font-bold text-slate-800">{{ __('Catalog List') }}</h3>
                <p class="text-[10px] text-slate-400 mt-0.5">Manage details of all products uploaded to the catalog.</p>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.products.bulk-destroy') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete the selected products?');">
                    @csrf
                    @method('DELETE')
                    <template x-for="id in selected" :key="id">
                        <input type="hidden" name="ids[]" :value="id">
                    </template>
                    <button type="submit" x-show="selected.length > 0" x-cloak :disabled="selected.length === 0" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-[11px] font-bold uppercase rounded shadow-sm transition-colors">
                        <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                        <span x-text="`{{ __('Delete Selected') }} (${selected.length})`"></span>
                    </button>
                </form>
                <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold uppercase rounded shadow-sm transition-colors">
                    <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                    {{ __('Add New Product') }}
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/20">
                    <tr>
                        <th class="px-6 py-3 text-left w-10">
                            <input type="checkbox" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500" :checked="allSelected" @change="selected = $event.target.checked ? [{{ $products->pluck('id')->implode(',') }}] : []">
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
                                    <a href="{{ route('admin.products.edit', $product) }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1">
                                        <i data-lucide="edit-3" class="h-3.5 w-3.5"></i>
                                        {{ __('Edit') }}
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" class="inline">
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
                            <td colspan="8" class="px-6 py-4 text-center text-xs text-slate-400">
                                {{ __('No products found') }}
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
    </div>
</x-app-layout>
