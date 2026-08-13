<x-app-layout>
    <div class="p-6 w-full space-y-6">
        <div class="flex items-center justify-between bg-white p-5 rounded-sm border border-gray-200 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-gray-900">New Filter Attribute</h1>
                <p class="text-xs text-gray-500 mt-1">This will appear as a shop-page filter for products in the chosen category.</p>
            </div>
            <a href="{{ route('admin.filter-attributes.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-sm transition-colors">
                ← Back to Filter Attributes
            </a>
        </div>

        <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-6 max-w-2xl" x-data="{ type: 'range' }">
            <form action="{{ route('admin.filter-attributes.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Category *</label>
                    <select name="category_id" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">Select category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Label *</label>
                        <input type="text" name="label" value="{{ old('label') }}" required placeholder="e.g. RAM"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                        @error('label') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Filter Type *</label>
                        <select name="type" x-model="type" required class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                            <option value="range" {{ old('type', 'range') == 'range' ? 'selected' : '' }}>Range (min–max number, e.g. RAM, Storage, DPI)</option>
                            <option value="select" {{ old('type') == 'select' ? 'selected' : '' }}>Select (fixed list, e.g. Connection: Wired/Wireless)</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Reads From Spec Label(s)</label>
                    <input type="text" name="match_labels" value="{{ old('match_labels') }}" placeholder="e.g. RAM, Memory"
                           class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    <p class="text-[11px] text-slate-500 mt-1.5">The value auto-fills from whichever Specification the product already has under this label (comma-separated to match more than one, e.g. products using either "RAM" or "Memory"). Leave blank to match the attribute's own Label above.</p>
                </div>

                <div x-show="type === 'range'">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Unit <span class="normal-case font-normal text-gray-400">(optional)</span></label>
                    <input type="text" name="unit" value="{{ old('unit') }}" placeholder="e.g. GB"
                           class="w-full max-w-xs px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                </div>

                <div x-show="type === 'select'" style="display:none">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Options *</label>
                    <input type="text" name="options" value="{{ old('options') }}" placeholder="e.g. Wired, Wireless, Bluetooth"
                           class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                    <p class="text-[11px] text-slate-500 mt-1.5">Comma-separated list of the choices customers can filter by.</p>
                    @error('options') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Display Order</label>
                    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}"
                           class="w-full max-w-xs px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500" />
                </div>

                <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                    <a href="{{ route('admin.filter-attributes.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-sm transition-colors">Cancel</a>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-sm shadow-sm transition-all cursor-pointer">Create</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
