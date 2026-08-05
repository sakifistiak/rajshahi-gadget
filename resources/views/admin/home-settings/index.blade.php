<x-app-layout>
<div class="w-full space-y-6" x-data="{
    title1: '{{ addslashes($settings['home_sec1_title']) }}',
    hl1: '{{ addslashes($settings['home_sec1_highlight']) }}',
    title2: '{{ addslashes($settings['home_sec2_title']) }}',
    hl2: '{{ addslashes($settings['home_sec2_highlight']) }}',
    title3: '{{ addslashes($settings['home_sec3_title']) }}',
    hl3: '{{ addslashes($settings['home_sec3_highlight']) }}',
    formatTitle(fullText, highlightWord) {
        if (!fullText) return '';
        if (!highlightWord || !fullText.toLowerCase().includes(highlightWord.toLowerCase())) {
            return fullText;
        }
        const regex = new RegExp('(' + highlightWord.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        return fullText.replace(regex, '<span class=&quot;text-blue-600 font-bold bg-blue-50 px-1 py-0.5 rounded&quot;>$1</span>');
    }
}">

    <!-- Header -->
    <div class="flex items-center justify-between p-5 bg-white rounded-sm border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Homepage Settings</h2>
            <p class="text-xs text-slate-500 mt-1">Control section visibility, product filters, and title styling for the homepage.</p>
        </div>
        <a href="{{ route('home') }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-lg transition-colors">
            <i data-lucide="external-link" class="w-4 h-4"></i>
            <span>View Homepage</span>
        </a>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-sm text-xs font-bold flex items-center gap-2">
            <i data-lucide="check-circle" class="h-4 w-4 text-emerald-600"></i>
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.home-settings.update') }}" method="POST" class="space-y-6">
        @csrf

        <!-- 1. Section Visibility Toggles -->
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">1. Section Visibility Toggles</h3>
                        <p class="text-xs text-slate-500">Toggle which homepage sections are visible or hidden.</p>
                    </div>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Hero Slider</span>
                    <input type="checkbox" name="home_hero_active" value="1" {{ $settings['home_hero_active'] == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Flash Deals / Offers</span>
                    <input type="checkbox" name="home_flash_active" value="1" {{ $settings['home_flash_active'] == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Product Section 1</span>
                    <input type="checkbox" name="home_sec1_active" value="1" {{ $settings['home_sec1_active'] == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Product Section 2</span>
                    <input type="checkbox" name="home_sec2_active" value="1" {{ $settings['home_sec2_active'] == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Product Section 3</span>
                    <input type="checkbox" name="home_sec3_active" value="1" {{ $settings['home_sec3_active'] == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Promo Banner Cards</span>
                    <input type="checkbox" name="home_promos_active" value="1" {{ $settings['home_promos_active'] == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Customer Testimonials</span>
                    <input type="checkbox" name="home_testimonials_active" value="1" {{ $settings['home_testimonials_active'] == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
            </div>
        </div>

        <!-- 2. Product Section 1 -->
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">2. Product Section 1</h3>
                        <p class="text-xs text-slate-500">Configure title, highlight word, category filter, and product limit.</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Section Title</label>
                        <input type="text" name="home_sec1_title" x-model="title1" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Brand new intact box">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Highlight Word</label>
                        <input type="text" name="home_sec1_highlight" x-model="hl1" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. intact box">
                    </div>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Live Title Preview:</span>
                    <h2 class="text-xl font-bold text-slate-900" x-html="formatTitle(title1, hl1)"></h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Category / Filter Source</label>
                        <select name="home_sec1_filter" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <optgroup label="Product Conditions">
                                <option value="cond_intact" {{ $settings['home_sec1_filter'] == 'cond_intact' ? 'selected' : '' }}>Brand New Intact Box</option>
                                <option value="cond_without-box" {{ $settings['home_sec1_filter'] == 'cond_without-box' ? 'selected' : '' }}>Brand New Without Box</option>
                                <option value="cond_pre-owned" {{ $settings['home_sec1_filter'] == 'cond_pre-owned' ? 'selected' : '' }}>Certified Pre-Owned</option>
                            </optgroup>
                            <optgroup label="Product Categories">
                                @foreach($categories as $cat)
                                    <option value="cat_{{ $cat->id }}" {{ $settings['home_sec1_filter'] == 'cat_'.$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="General">
                                <option value="all" {{ $settings['home_sec1_filter'] == 'all' ? 'selected' : '' }}>All Latest Products</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Product Limit</label>
                        <select name="home_sec1_limit" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="4" {{ $settings['home_sec1_limit'] == '4' ? 'selected' : '' }}>4 Items</option>
                            <option value="8" {{ $settings['home_sec1_limit'] == '8' ? 'selected' : '' }}>8 Items</option>
                            <option value="12" {{ $settings['home_sec1_limit'] == '12' ? 'selected' : '' }}>12 Items</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Product Section 2 -->
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 text-amber-600 rounded-lg">
                        <i data-lucide="package" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">3. Product Section 2</h3>
                        <p class="text-xs text-slate-500">Configure title, highlight word, category filter, and product limit.</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Section Title</label>
                        <input type="text" name="home_sec2_title" x-model="title2" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Brand new without box">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Highlight Word</label>
                        <input type="text" name="home_sec2_highlight" x-model="hl2" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. without box">
                    </div>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Live Title Preview:</span>
                    <h2 class="text-xl font-bold text-slate-900" x-html="formatTitle(title2, hl2)"></h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Category / Filter Source</label>
                        <select name="home_sec2_filter" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <optgroup label="Product Conditions">
                                <option value="cond_intact" {{ $settings['home_sec2_filter'] == 'cond_intact' ? 'selected' : '' }}>Brand New Intact Box</option>
                                <option value="cond_without-box" {{ $settings['home_sec2_filter'] == 'cond_without-box' ? 'selected' : '' }}>Brand New Without Box</option>
                                <option value="cond_pre-owned" {{ $settings['home_sec2_filter'] == 'cond_pre-owned' ? 'selected' : '' }}>Certified Pre-Owned</option>
                            </optgroup>
                            <optgroup label="Product Categories">
                                @foreach($categories as $cat)
                                    <option value="cat_{{ $cat->id }}" {{ $settings['home_sec2_filter'] == 'cat_'.$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="General">
                                <option value="all" {{ $settings['home_sec2_filter'] == 'all' ? 'selected' : '' }}>All Latest Products</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Product Limit</label>
                        <select name="home_sec2_limit" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="4" {{ $settings['home_sec2_limit'] == '4' ? 'selected' : '' }}>4 Items</option>
                            <option value="8" {{ $settings['home_sec2_limit'] == '8' ? 'selected' : '' }}>8 Items</option>
                            <option value="12" {{ $settings['home_sec2_limit'] == '12' ? 'selected' : '' }}>12 Items</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. Product Section 3 -->
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-100 text-purple-600 rounded-lg">
                        <i data-lucide="tag" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">4. Product Section 3</h3>
                        <p class="text-xs text-slate-500">Configure title, highlight word, category filter, and product limit.</p>
                    </div>
                </div>
            </div>
            <div class="p-6 space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Section Title</label>
                        <input type="text" name="home_sec3_title" x-model="title3" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Certified pre-owned">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Highlight Word</label>
                        <input type="text" name="home_sec3_highlight" x-model="hl3" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. pre-owned">
                    </div>
                </div>
                <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Live Title Preview:</span>
                    <h2 class="text-xl font-bold text-slate-900" x-html="formatTitle(title3, hl3)"></h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Category / Filter Source</label>
                        <select name="home_sec3_filter" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <optgroup label="Product Conditions">
                                <option value="cond_intact" {{ $settings['home_sec3_filter'] == 'cond_intact' ? 'selected' : '' }}>Brand New Intact Box</option>
                                <option value="cond_without-box" {{ $settings['home_sec3_filter'] == 'cond_without-box' ? 'selected' : '' }}>Brand New Without Box</option>
                                <option value="cond_pre-owned" {{ $settings['home_sec3_filter'] == 'cond_pre-owned' ? 'selected' : '' }}>Certified Pre-Owned</option>
                            </optgroup>
                            <optgroup label="Product Categories">
                                @foreach($categories as $cat)
                                    <option value="cat_{{ $cat->id }}" {{ $settings['home_sec3_filter'] == 'cat_'.$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="General">
                                <option value="all" {{ $settings['home_sec3_filter'] == 'all' ? 'selected' : '' }}>All Latest Products</option>
                            </optgroup>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Product Limit</label>
                        <select name="home_sec3_limit" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="4" {{ $settings['home_sec3_limit'] == '4' ? 'selected' : '' }}>4 Items</option>
                            <option value="8" {{ $settings['home_sec3_limit'] == '8' ? 'selected' : '' }}>8 Items</option>
                            <option value="12" {{ $settings['home_sec3_limit'] == '12' ? 'selected' : '' }}>12 Items</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-sm transition-all">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save Homepage Settings</span>
            </button>
        </div>
    </form>
</div>
</x-app-layout>
