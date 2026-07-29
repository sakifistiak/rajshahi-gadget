<x-app-layout>
    <div class="p-6 max-w-4xl mx-auto space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Add Side Promo Banner</h1>
                <p class="text-xs text-gray-500 mt-1">Add a promo card displayed next to the main slider</p>
            </div>
            <a href="{{ route('admin.promos.index') }}" 
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors">
                ← Back to Promos
            </a>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <form action="{{ route('admin.promos.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Headline / Title *</label>
                        <input type="text" name="title" id="title" required value="{{ old('title') }}"
                               placeholder="e.g. Up to ৳ 9,500 OFF or Mega Discount"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        @error('title') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Subtitle -->
                    <div>
                        <label for="subtitle" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Offer Subtitle</label>
                        <input type="text" name="subtitle" id="subtitle" value="{{ old('subtitle') }}"
                               placeholder="e.g. on Apple Watch Series 10 · Ends 25 July"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        @error('subtitle') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Image Path -->
                    <div class="md:col-span-2">
                        <label for="image_path" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Image URL / Path *</label>
                        <input type="text" name="image_path" id="image_path" required value="{{ old('image_path', '/assets/cat-wearables-WxrTS6bM.jpg') }}"
                               placeholder="e.g. /assets/cat-wearables-WxrTS6bM.jpg"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono" />
                        @error('image_path') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Background Gradient -->
                    <div>
                        <label for="bg_color" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Background Style</label>
                        <select name="bg_color" id="bg_color" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                            <option value="from-sky-100 to-sky-50">Soft Sky Blue</option>
                            <option value="from-orange-200 to-orange-100">Warm Orange</option>
                            <option value="from-emerald-100 to-emerald-50">Mint Emerald</option>
                            <option value="from-purple-100 to-purple-50">Royal Purple</option>
                            <option value="from-rose-100 to-rose-50">Rose Red</option>
                        </select>
                    </div>

                    <!-- Link -->
                    <div>
                        <label for="link" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Target Link</label>
                        <input type="text" name="link" id="link" value="{{ old('link', '/shop') }}"
                               placeholder="e.g. /shop?condition=intact"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                    </div>

                    <!-- Sort Order -->
                    <div>
                        <label for="sort_order" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Sort Order</label>
                        <input type="number" name="sort_order" id="sort_order" min="0" value="{{ old('sort_order', 1) }}"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
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
                    <a href="{{ route('admin.promos.index') }}" 
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
