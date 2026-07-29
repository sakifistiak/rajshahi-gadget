<x-app-layout>
    <div class="p-6 max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Add New Hero Banner</h1>
                <p class="text-xs text-gray-500 mt-1">Add a new homepage banner slide with custom image, link and title</p>
            </div>
            <a href="{{ route('admin.sliders.index') }}" 
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors">
                ← Back to Banners
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <form action="{{ route('admin.sliders.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Banner Title *</label>
                        <input type="text" name="title" id="title" required value="{{ old('title') }}"
                               placeholder="e.g. Samsung Galaxy S26 Ultra"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        @error('title') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Subtitle -->
                    <div>
                        <label for="subtitle" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Subtitle / Offer</label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                               placeholder="e.g. Pre-order now with 0% EMI"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        @error('subtitle') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Image Path -->
                    <div class="md:col-span-2">
                        <label for="image_path" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Image URL / Path *</label>
                        <input type="text" name="image_path" id="image_path" required value="{{ old('image_path', '/media/6767eb-hero-s26-ultra.png') }}"
                               placeholder="e.g. /media/6767eb-hero-s26-ultra.png or https://domain.com/image.png"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono" />
                        <p class="text-[11px] text-gray-400 mt-1">Recommended resolution: 1500×800 px</p>
                        @error('image_path') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- CTA Link -->
                    <div>
                        <label for="cta_link" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Target Link</label>
                        <input type="text" name="cta_link" id="cta_link" value="{{ old('cta_link', '/shop') }}"
                               placeholder="e.g. /shop or /product/macbook-pro-14-m4-intact"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        @error('cta_link') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- CTA Text -->
                    <div>
                        <label for="cta_text" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Button Text</label>
                        <input type="text" name="cta_text" id="cta_text" value="{{ old('cta_text', 'Shop Now') }}"
                               placeholder="e.g. Shop Now, Explore"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        @error('cta_text') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Display Order (Sort)</label>
                        <input type="number" name="sort_order" id="sort_order" min="0" value="{{ old('sort_order', 1) }}"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        @error('sort_order') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Active Toggle -->
                    <div class="flex items-center pt-6">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-xs font-bold text-gray-700 uppercase tracking-wider">Active Banner</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end gap-3 border-t border-gray-100 pt-6">
                    <a href="{{ route('admin.sliders.index') }}" 
                       class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all cursor-pointer">
                        Save Banner
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
