<x-app-layout>
    <div class="p-6 w-full space-y-6">
        @if (session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded text-xs font-semibold shadow-sm flex items-center gap-2">
                <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
                {{ session('success') }}
            </div>
        @endif

        @php $status = $flashSale->statusLabel(); @endphp
        <div class="flex items-center justify-between bg-white p-5 rounded-sm border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="text-xl font-bold text-gray-900">{{ $flashSale->title }}</h1>
                    <p class="text-xs text-gray-500 mt-1">{{ $flashSale->starts_at->format('d M Y, h:i A') }} — {{ $flashSale->ends_at->format('d M Y, h:i A') }}</p>
                </div>
                <span class="px-2.5 py-1 text-[10px] font-extrabold rounded-full border uppercase {{ match($status) { 'Live' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'Upcoming' => 'bg-blue-50 text-blue-600 border-blue-100', 'Expired' => 'bg-slate-100 text-slate-500 border-slate-200', default => 'bg-rose-50 text-rose-600 border-rose-100' } }}">{{ $status }}</span>
            </div>
            <a href="{{ route('admin.flash-sales.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-sm transition-colors">
                ← Back to Flash Sales
            </a>
        </div>

        <!-- Campaign Settings -->
        <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-6">
            <h2 class="text-sm font-bold text-slate-900 mb-4">Campaign Settings</h2>
            <form action="{{ route('admin.flash-sales.update', $flashSale) }}" method="POST" class="space-y-6">
                @csrf @method('PUT')

                <div>
                    <label for="title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Campaign Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $flashSale->title) }}" required
                           class="w-full max-w-lg px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                    @error('title') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-2xl">
                    <div>
                        <label for="starts_at" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Starts At *</label>
                        <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at', $flashSale->starts_at->format('Y-m-d\TH:i')) }}" required
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        @error('starts_at') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="ends_at" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Ends At *</label>
                        <input type="datetime-local" name="ends_at" id="ends_at" value="{{ old('ends_at', $flashSale->ends_at->format('Y-m-d\TH:i')) }}" required
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        @error('ends_at') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $flashSale->is_active) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-xs font-bold text-gray-800 uppercase tracking-wider">Active</span>
                    </label>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-sm shadow-sm transition-all cursor-pointer">Save Settings</button>
                </div>
            </form>
        </div>

        <!-- Add Product -->
        <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-6"
             x-data="{
                discountType: 'fixed_price',
                productId: '',
                salePrice: '',
                percentOff: '',
                products: {{ $products->mapWithKeys(fn ($p) => [$p->id => $p->price])->toJson() }},
                get normalPrice() { return this.productId ? this.products[this.productId] : null; },
                get computedSalePrice() {
                    if (!this.normalPrice) return null;
                    if (this.discountType === 'fixed_price') return this.salePrice ? Number(this.salePrice) : null;
                    if (this.discountType === 'percent_off' && this.percentOff) return Math.round(this.normalPrice * (100 - this.percentOff) / 100);
                    return null;
                }
             }">
            <h2 class="text-sm font-bold text-slate-900 mb-4">Add a Product to This Sale</h2>
            <form action="{{ route('admin.flash-sales.products.store', $flashSale) }}" method="POST" class="grid grid-cols-1 md:grid-cols-6 gap-4 items-end">
                @csrf
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Product *</label>
                    <select name="product_id" x-model="productId" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">Select product</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (৳{{ number_format($product->price) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Normal Price</label>
                    <div class="w-full px-3.5 py-2.5 bg-slate-100 border border-slate-200 rounded-sm text-xs font-bold text-slate-700" x-text="normalPrice ? '৳' + normalPrice.toLocaleString('en-US') : '—'"></div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Discount Type *</label>
                    <select name="discount_type" x-model="discountType" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="fixed_price">Fixed Price</option>
                        <option value="percent_off">Percent Off</option>
                    </select>
                </div>
                <div x-show="discountType === 'fixed_price'">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Sale Price (৳)</label>
                    <input type="number" name="sale_price" x-model="salePrice" min="1" placeholder="e.g. 118000" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                </div>
                <div x-show="discountType === 'percent_off'" style="display:none">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Percent Off (%)</label>
                    <input type="number" name="percent_off" x-model="percentOff" min="1" max="90" placeholder="e.g. 15" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Stock Limit <span class="normal-case font-normal text-gray-400">(optional)</span></label>
                    <input type="number" name="stock_limit" min="1" placeholder="e.g. 50" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                </div>
                <div class="md:col-span-6" x-show="computedSalePrice" style="display:none">
                    <p class="text-xs font-semibold text-emerald-600">Customers will pay <span x-text="'৳' + (computedSalePrice ? computedSalePrice.toLocaleString('en-US') : '')"></span> instead of <span x-text="'৳' + (normalPrice ? normalPrice.toLocaleString('en-US') : '')"></span></p>
                </div>
                <div class="md:col-span-6">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-sm shadow-sm transition-all cursor-pointer flex items-center gap-1.5">
                        <i data-lucide="plus" class="h-3.5 w-3.5"></i> Add Product
                    </button>
                </div>
            </form>
            @error('product_id') <p class="text-xs text-rose-500 mt-3">{{ $message }}</p> @enderror
            @error('sale_price') <p class="text-xs text-rose-500 mt-3">{{ $message }}</p> @enderror
            @error('percent_off') <p class="text-xs text-rose-500 mt-3">{{ $message }}</p> @enderror
        </div>

        <!-- Products in Sale -->
        <div class="bg-white rounded-sm border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-900">Products in This Sale ({{ $flashSale->items->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50/20">
                        <tr>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Original</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Sale Price</th>
                            <th class="px-6 py-3 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stock</th>
                            <th class="px-6 py-3 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($flashSale->items as $item)
                            @php $product = $item->product; @endphp
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-6 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded bg-slate-100 overflow-hidden shrink-0">
                                            @if($product)
                                                <img src="{{ $product->primaryImage() }}" alt="" class="h-full w-full object-cover">
                                            @endif
                                        </div>
                                        <span class="text-xs font-semibold text-slate-800">{{ $product?->name ?? 'Product removed' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-500 line-through">{{ $product ? '৳' . number_format($product->price) : '—' }}</td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs font-bold text-emerald-600">
                                    @if($product)
                                        ৳{{ number_format($item->priceFor($product->price)) }}
                                        <span class="text-[10px] font-semibold text-slate-400">({{ $item->discount_type === 'percent_off' ? $item->percent_off . '% off' : 'fixed' }})</span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-xs text-slate-600">
                                    @if($item->stock_limit)
                                        {{ $item->sold_count }} / {{ $item->stock_limit }} sold
                                    @else
                                        Unlimited
                                    @endif
                                </td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-right text-xs font-medium">
                                    <details class="inline-block text-left">
                                        <summary class="cursor-pointer text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1 list-none">
                                            <i data-lucide="pencil" class="h-3.5 w-3.5"></i> Edit
                                        </summary>
                                        <form action="{{ route('admin.flash-sales.products.update', [$flashSale, $item]) }}" method="POST" class="mt-3 p-3 bg-slate-50 border border-slate-200 rounded-sm space-y-2 w-64" x-data="{ dt: '{{ $item->discount_type }}' }">
                                            @csrf @method('PATCH')
                                            <select name="discount_type" x-model="dt" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded text-xs">
                                                <option value="fixed_price">Fixed Price</option>
                                                <option value="percent_off">Percent Off</option>
                                            </select>
                                            <input x-show="dt === 'fixed_price'" type="number" name="sale_price" min="1" value="{{ $item->sale_price }}" placeholder="Sale price" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded text-xs">
                                            <input x-show="dt === 'percent_off'" style="display:none" type="number" name="percent_off" min="1" max="90" value="{{ $item->percent_off }}" placeholder="Percent off" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded text-xs">
                                            <input type="number" name="stock_limit" min="1" value="{{ $item->stock_limit }}" placeholder="Stock limit (optional)" class="w-full px-2 py-1.5 bg-white border border-slate-200 rounded text-xs">
                                            <button type="submit" class="w-full px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-bold rounded">Save</button>
                                        </form>
                                    </details>
                                    <form action="{{ route('admin.flash-sales.products.destroy', [$flashSale, $item]) }}" method="POST" onsubmit="return confirm('Remove this product from the sale?');" class="inline ml-3">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-semibold inline-flex items-center gap-1">
                                            <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <p class="text-xs text-slate-400 font-semibold">No products added yet</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
