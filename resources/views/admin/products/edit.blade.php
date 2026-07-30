<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Product: ') }} {{ $product->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700 p-6">
                <form action="{{ route('admin.products.update', $product) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Name -->
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Product Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $product->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Brand -->
                        <div>
                            <x-input-label for="brand_id" :value="__('Brand')" />
                            <select id="brand_id" name="brand_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                                <option value="">Select Brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('brand_id')" class="mt-2" />
                        </div>

                        <!-- Category -->
                        <div>
                            <x-input-label for="category_id" :value="__('Category')" />
                            <select id="category_id" name="category_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                                <option value="">Select Category</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                        </div>

                        <!-- Condition -->
                        <div>
                            <x-input-label for="condition_id" :value="__('Condition')" />
                            <select id="condition_id" name="condition_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                                <option value="">Select Condition</option>
                                @foreach ($conditions as $cond)
                                    <option value="{{ $cond->id }}" {{ old('condition_id', $product->condition_id) == $cond->id ? 'selected' : '' }}>{{ $cond->label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('condition_id')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Price -->
                        <div>
                            <x-input-label for="price" :value="__('Price (BDT)')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price', $product->price)" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Compare Price -->
                        <div>
                            <x-input-label for="compare_at_price" :value="__('Compare At Price (BDT)')" />
                            <x-text-input id="compare_at_price" class="block mt-1 w-full" type="number" name="compare_at_price" :value="old('compare_at_price', $product->compare_at_price)" />
                            <x-input-error :messages="$errors->get('compare_at_price')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Badge -->
                        <div>
                            <x-input-label for="badge" :value="__('Badge (e.g. Bestseller, Sale, New)')" />
                            <x-text-input id="badge" class="block mt-1 w-full" type="text" name="badge" :value="old('badge', $product->badge)" />
                            <x-input-error :messages="$errors->get('badge')" class="mt-2" />
                        </div>

                        <!-- Warranty -->
                        <div>
                            <x-input-label for="warranty" :value="__('Warranty')" />
                            <x-text-input id="warranty" class="block mt-1 w-full" type="text" name="warranty" :value="old('warranty', $product->warranty)" />
                            <x-input-error :messages="$errors->get('warranty')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Highlighted Key Features (Bullet Points for Shop Product Cards) -->
                    <div class="mb-6 p-4 bg-slate-50 dark:bg-gray-900/50 rounded-lg border border-slate-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-2">
                            <label class="block font-bold text-xs text-slate-800 dark:text-gray-200 uppercase tracking-wider">
                                {{ __('Highlighted Key Features (Bullet Points for Product Cards)') }}
                            </label>
                            <button type="button" onclick="addHighlightRow()" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded border border-blue-200 transition-colors">
                                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                                {{ __('+ Add Feature') }}
                            </button>
                        </div>
                        <p class="text-[11px] text-slate-500 mb-3">Enter key specifications (e.g. "Apple M4 10-core", "16 GB RAM", "1 TB NVMe", "14.5\" FHD+ 120Hz"). These bullet points will show directly under the title on mobile & desktop shop cards.</p>
                        
                        <div id="highlights-container" class="space-y-2">
                            <!-- JS will populate rows -->
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Full Description')" />
                        <textarea id="description" name="description" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full h-32" required>{{ old('description', $product->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- In Stock Checkbox -->
                    <div class="block mb-6">
                        <label for="in_stock" class="inline-flex items-center">
                            <input id="in_stock" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600" name="in_stock" value="1" {{ old('in_stock', $product->in_stock) ? 'checked' : '' }}>
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('In Stock') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline rounded-md me-4">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button class="ms-4">
                            {{ __('Update Product') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function addHighlightRow(value = '') {
            const container = document.getElementById('highlights-container');
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 highlight-row';
            row.innerHTML = `
                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 shrink-0 ml-1"></span>
                <input type="text" name="highlights[]" value="${value.replace(/"/g, '&quot;')}" placeholder="e.g. 16 GB RAM or Core Ultra 7 155H" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md text-xs w-full py-1.5 px-3 focus:ring-blue-500 focus:border-blue-500 shadow-sm" />
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1.5 rounded hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors" title="Remove feature">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            `;
            container.appendChild(row);
            if (window.lucide) lucide.createIcons();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const existingHighlights = @json($product->highlights ? $product->highlights->pluck('text') : []);
            const oldHighlights = @json(old('highlights', []));
            const list = oldHighlights.length > 0 ? oldHighlights : existingHighlights;

            if (list.length > 0) {
                list.forEach(val => addHighlightRow(val));
            } else {
                addHighlightRow('');
                addHighlightRow('');
                addHighlightRow('');
                addHighlightRow('');
            }
        });
    </script>
</x-app-layout>
