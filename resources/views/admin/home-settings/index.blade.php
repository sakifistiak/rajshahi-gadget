<x-app-layout>
<div class="w-full space-y-6" x-data="{
    sections: {{ json_encode($sectionsList) }},
    addSection() {
        this.sections.push({
            id: 'sec_' + Date.now(),
            title: 'New Product Section',
            highlight: 'Product',
            filter: 'all',
            limit: '4',
            active: true
        });
    },
    removeSection(index) {
        if (confirm('Are you sure you want to remove this section?')) {
            this.sections.splice(index, 1);
        }
    },
    formatTitle(fullText, highlightWord) {
        if (!fullText) return '';
        if (!highlightWord || !fullText.toLowerCase().includes(highlightWord.toLowerCase())) {
            return fullText;
        }
        const escapedHl = highlightWord.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        const regex = new RegExp('(' + escapedHl + ')', 'gi');
        return fullText.replace(regex, '<span style=&quot;color: #2563eb; background-color: rgba(37, 99, 235, 0.12); padding: 2px 8px; border-radius: 6px; display: inline-block; font-weight: 700;&quot;>$1</span>');
    }
}">

    <!-- Header -->
    <div class="flex items-center justify-between p-5 bg-white rounded-sm border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-900">Homepage Settings</h2>
            <p class="text-xs text-slate-500 mt-1">Control section visibility, create custom category sections, and customize title styling for the homepage.</p>
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

    @if($errors->any())
        <div class="p-4 bg-rose-50 text-rose-800 border border-rose-200 rounded-sm text-xs font-bold space-y-1">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="h-4 w-4 text-rose-600"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <form action="{{ route('admin.home-settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <input type="hidden" name="home_sections_json" :value="JSON.stringify(sections)">

        <!-- 1. Section Visibility Toggles -->
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <i data-lucide="eye" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">1. Global Section Toggles</h3>
                        <p class="text-xs text-slate-500">Toggle static homepage components like hero slider and testimonials.</p>
                    </div>
                </div>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Hero Slider</span>
                    <input type="checkbox" name="home_hero_active" value="1" {{ ($settings['home_hero_active'] ?? '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Flash Deals / Offers</span>
                    <input type="checkbox" name="home_flash_active" value="1" {{ ($settings['home_flash_active'] ?? '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
                <label class="flex items-center justify-between p-4 rounded-lg border border-slate-200 hover:border-blue-300 bg-slate-50/30 cursor-pointer transition-all">
                    <span class="text-sm font-bold text-slate-800">Customer Testimonials</span>
                    <input type="checkbox" name="home_testimonials_active" value="1" {{ ($settings['home_testimonials_active'] ?? '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                </label>
            </div>
        </div>

        <!-- 2. Notice Ticker Announcements -->
        <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-100 text-amber-600 rounded-lg">
                        <i data-lucide="megaphone" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900">2. Notice Ticker Announcements</h3>
                        <p class="text-xs text-slate-500">Manage marquee announcements shown on the homepage.</p>
                    </div>
                </div>
                <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer select-none">
                    <input type="checkbox" name="home_ticker_active" value="1" {{ ($settings['home_ticker_active'] ?? '1') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                    <span>Enable Notice Ticker</span>
                </label>
            </div>
            <div class="p-6">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Notice Items (One announcement per line)</label>
                <textarea name="home_ticker_text" rows="5" class="w-full px-4 py-3 rounded-lg border border-slate-300 text-xs font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 leading-relaxed" placeholder="Type announcements here, one per line...">{{ $settings['home_ticker_text'] ?? '' }}</textarea>
                <p class="text-[11px] text-slate-400 mt-2">Each line will be displayed as a scrolling announcement in the marquee bar.</p>
            </div>
        </div>



        <!-- 2. Dynamic Product Sections List -->
        <div class="space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Dynamic Product Sections</h3>
                    <p class="text-xs text-slate-500">Add, remove, or reorder custom category sections for your homepage.</p>
                </div>
                <button type="button" @click="addSection()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-lg transition-all shadow-sm">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Add New Product Section</span>
                </button>
            </div>

            <!-- Loop over sections -->
            <template x-for="(sec, index) in sections" :key="sec.id">
                <div class="bg-white rounded-sm border border-slate-200 shadow-sm overflow-hidden transition-all">
                    <div class="p-4 border-b border-slate-100 bg-slate-50/70 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-extrabold text-xs" x-text="index + 1"></span>
                            <span class="text-sm font-bold text-slate-800" x-text="sec.title || 'Product Section'"></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <!-- Toggle Active -->
                            <label class="flex items-center gap-2 text-xs font-semibold text-slate-600 cursor-pointer select-none">
                                <input type="checkbox" x-model="sec.active" class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                                <span>Show Section</span>
                            </label>
                            <!-- Delete Button -->
                            <button type="button" @click="removeSection(index)" class="p-1.5 text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-lg transition-colors" title="Delete Section">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Section Title</label>
                                <input type="text" x-model="sec.title" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Intact Box Laptops">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Highlight Word</label>
                                <input type="text" x-model="sec.highlight" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="e.g. Laptops">
                            </div>
                        </div>

                        <!-- Live Title Preview -->
                        <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Live Title Preview:</span>
                            <h2 class="text-xl font-bold text-slate-900" x-html="formatTitle(sec.title, sec.highlight)"></h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Category / Filter Source</label>
                                <select x-model="sec.filter" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <optgroup label="Product Conditions">
                                        <option value="cond_intact">Brand New Intact Box</option>
                                        <option value="cond_without-box">Brand New Without Box</option>
                                        <option value="cond_pre-owned">Certified Pre-Owned</option>
                                    </optgroup>
                                    <optgroup label="Product Categories">
                                        @foreach($categories as $cat)
                                            <option value="cat_{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </optgroup>
                                    <optgroup label="General">
                                        <option value="all">All Latest Products</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Product Limit</label>
                                <select x-model="sec.limit" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 text-sm font-semibold focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="4">4 Items</option>
                                    <option value="8">8 Items</option>
                                    <option value="12">12 Items</option>
                                    <option value="16">16 Items</option>
                                    <option value="20">20 Items</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Bottom Add Section Button -->
            <div class="flex justify-center pt-2">
                <button type="button" @click="addSection()" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-dashed border-slate-300 hover:border-blue-500 text-slate-700 hover:text-blue-600 font-bold text-xs rounded-xl transition-all w-full justify-center bg-white shadow-xs">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Add Another Product Section</span>
                </button>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end pt-4 border-t border-slate-200">
            <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm rounded-xl shadow-sm transition-all">
                <i data-lucide="save" class="w-4 h-4"></i>
                <span>Save Homepage Settings</span>
            </button>
        </div>
    </form>
</div>
</x-app-layout>
