<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add New Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700 p-6">
                <form action="{{ route('admin.products.store') }}" method="POST">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Product Name')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <!-- Brand -->
                        <div>
                            <x-input-label for="brand_id" :value="__('Brand')" />
                            <select id="brand_id" name="brand_id" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full" required>
                                <option value="">Select Brand</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
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
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
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
                                    <option value="{{ $cond->id }}" {{ old('condition_id') == $cond->id ? 'selected' : '' }}>{{ $cond->label }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('condition_id')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Price -->
                        <div>
                            <x-input-label for="price" :value="__('Price (BDT)')" />
                            <x-text-input id="price" class="block mt-1 w-full" type="number" name="price" :value="old('price')" required />
                            <x-input-error :messages="$errors->get('price')" class="mt-2" />
                        </div>

                        <!-- Compare Price -->
                        <div>
                            <x-input-label for="compare_at_price" :value="__('Compare At Price (BDT)')" />
                            <x-text-input id="compare_at_price" class="block mt-1 w-full" type="number" name="compare_at_price" :value="old('compare_at_price')" />
                            <x-input-error :messages="$errors->get('compare_at_price')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Badge -->
                        <div>
                            <x-input-label for="badge" :value="__('Badge (e.g. Bestseller, Sale, New)')" />
                            <x-text-input id="badge" class="block mt-1 w-full" type="text" name="badge" :value="old('badge')" />
                            <x-input-error :messages="$errors->get('badge')" class="mt-2" />
                        </div>

                        <!-- Warranty -->
                        <div>
                            <x-input-label for="warranty" :value="__('Warranty')" />
                            <x-text-input id="warranty" class="block mt-1 w-full" type="text" name="warranty" :value="old('warranty')" placeholder="e.g. 1 year service warranty" />
                            <x-input-error :messages="$errors->get('warranty')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Image URL -->
                    <div class="mb-4">
                        <x-input-label for="image_url" :value="__('Primary Image URL')" />
                        <x-text-input id="image_url" class="block mt-1 w-full" type="text" name="image_url" :value="old('image_url')" placeholder="/assets/laptop-ultrabook-C5nU_6_f.jpg" />
                        <span class="text-xs text-gray-400 mt-1 block">Leave empty to use default laptop placeholder image.</span>
                        <x-input-error :messages="$errors->get('image_url')" class="mt-2" />
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <x-input-label for="description" :value="__('Description')" />
                        <textarea id="description" name="description" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block mt-1 w-full h-32" required>{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <!-- In Stock Checkbox -->
                    <div class="block mb-6">
                        <label for="in_stock" class="inline-flex items-center">
                            <input id="in_stock" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600" name="in_stock" value="1" {{ old('in_stock', '1') == '1' ? 'checked' : '' }}>
                            <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('In Stock') }}</span>
                        </label>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('admin.products.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 underline rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 me-4">
                            {{ __('Cancel') }}
                        </a>
                        <x-primary-button class="ms-4">
                            {{ __('Save Product') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
