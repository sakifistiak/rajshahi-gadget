<x-app-layout>
    <div class="w-full space-y-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-5 bg-white rounded-sm border border-slate-200 shadow-sm">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Site & Footer Settings</h2>
                <p class="text-xs text-slate-500 mt-1">Manage brand logos, general website info, contact details, social links, and footer copyright.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="p-4 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-sm text-xs font-bold flex items-center gap-2">
                <i data-lucide="check-circle" class="h-4 w-4 text-emerald-600"></i>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Card 1: Brand & Logo Management -->
            <div class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm space-y-5">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-800 font-bold text-xs uppercase tracking-wider">
                    <i data-lucide="image" class="h-4 w-4 text-blue-600"></i>
                    <span>1. Brand Logos (Light & Dark Mode)</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Light Mode Logo Upload -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">
                            Light Mode Logo (White Theme)
                        </label>
                        <div class="p-4 bg-slate-50 border border-dashed border-slate-300 rounded-sm flex flex-col items-center justify-center gap-3">
                            <img src="{{ $settings['logo_light'] }}" alt="Light Mode Logo Preview" class="h-12 w-auto object-contain bg-white p-2 border border-slate-200 rounded-sm">
                            <input type="file" name="logo_light_file" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        </div>
                        <p class="text-[11px] text-slate-400">Used on light backgrounds across the public website, header and footer.</p>
                    </div>

                    <!-- Dark Mode Logo Upload -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">
                            Dark Mode Logo (Dark Theme)
                        </label>
                        <div class="p-4 bg-slate-900 border border-dashed border-slate-700 rounded-sm flex flex-col items-center justify-center gap-3">
                            <img src="{{ $settings['logo_dark'] }}" alt="Dark Mode Logo Preview" class="h-12 w-auto object-contain bg-slate-800 p-2 border border-slate-700 rounded-sm">
                            <input type="file" name="logo_dark_file" accept="image/*" class="text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                        </div>
                        <p class="text-[11px] text-slate-400">Used when Dark Mode is enabled across the site.</p>
                    </div>
                </div>
            </div>

            <!-- Card 2: General Information -->
            <div class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-800 font-bold text-xs uppercase tracking-wider">
                    <i data-lucide="globe" class="h-4 w-4 text-blue-600"></i>
                    <span>2. General Website & Footer About</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Site Name</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Site Slogan / Tagline</label>
                        <input type="text" name="site_slogan" value="{{ old('site_slogan', $settings['site_slogan']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Footer Description / About Text</label>
                    <textarea name="site_description" rows="3" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">{{ old('site_description', $settings['site_description']) }}</textarea>
                </div>
            </div>

            <!-- Card 3: Contact & Address Details -->
            <div class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-800 font-bold text-xs uppercase tracking-wider">
                    <i data-lucide="phone-call" class="h-4 w-4 text-blue-600"></i>
                    <span>3. Contact & Store Location Info (Footer)</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Support Phone Number</label>
                        <input type="text" name="site_phone" value="{{ old('site_phone', $settings['site_phone']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            WhatsApp Number
                            <span class="text-slate-400 font-normal">(for single-product orders and live chat)</span>
                        </label>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $settings['whatsapp_number']) }}" inputmode="numeric" pattern="[0-9]*" placeholder="e.g. 8801700000001" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                        <p class="mt-1 text-[11px] text-slate-400">Use English digits only, including country code; do not add +, spaces, or dashes.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Support Email Address</label>
                        <input type="email" name="site_email" value="{{ old('site_email', $settings['site_email']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Store Physical Address</label>
                    <input type="text" name="site_address" value="{{ old('site_address', $settings['site_address']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Mobile Menu Contact Number
                            <span class="text-slate-400 font-normal">(Shown as call button in mobile hamburger menu)</span>
                        </label>
                        <input type="text" name="mobile_menu_contact" value="{{ old('mobile_menu_contact', $settings['mobile_menu_contact']) }}" placeholder="e.g. 01341-246152" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Mobile Menu Store Location Info Text
                            <span class="text-slate-400 font-normal">(Shown as &lt;p&gt; text in mobile hamburger menu)</span>
                        </label>
                        <input type="text" name="mobile_drawer_store_info" value="{{ old('mobile_drawer_store_info', $settings['mobile_drawer_store_info']) }}" placeholder="Enter store location text to show in mobile menu..." class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Card 4: Social Links & Copyright -->
            <div class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-800 font-bold text-xs uppercase tracking-wider">
                    <i data-lucide="share-2" class="h-4 w-4 text-blue-600"></i>
                    <span>4. Social Links & Copyright Text</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Facebook Page Link</label>
                        <input type="text" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Instagram Link</label>
                        <input type="text" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">WhatsApp Support Link or Number</label>
                        <input type="text" name="social_whatsapp" value="{{ old('social_whatsapp', $settings['social_whatsapp']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">YouTube Channel Link</label>
                        <input type="text" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Footer Copyright Text</label>
                    <input type="text" name="footer_copyright" value="{{ old('footer_copyright', $settings['footer_copyright']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <!-- Card 5: Footer Link Columns & Custom Page Links -->
            <div class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm space-y-5"
                 data-col1-active="{{ $settings['footer_col1_active'] }}"
                 data-col1-title="{{ $settings['footer_col1_title'] }}"
                 data-col1-links='{{ json_encode(json_decode($settings['footer_col1_links'] ?: '[]', true) ?: [], JSON_HEX_TAG - JSON_HEX_AMP - JSON_HEX_APOS - JSON_HEX_QUOT) }}'
                 data-col2-active="{{ $settings['footer_col2_active'] }}"
                 data-col2-title="{{ $settings['footer_col2_title'] }}"
                 data-col2-links='{{ json_encode(json_decode($settings['footer_col2_links'] ?: '[]', true) ?: [], JSON_HEX_TAG - JSON_HEX_AMP - JSON_HEX_APOS - JSON_HEX_QUOT) }}'
                 x-data="{
                     col1Active: false,
                     col1Title: '',
                     col1Links: [],
                     col2Active: false,
                     col2Title: '',
                     col2Links: [],
                     init() {
                         this.col1Active = this.$el.dataset.col1Active === '1';
                         this.col1Title = this.$el.dataset.col1Title || '';
                         const raw1 = JSON.parse(this.$el.dataset.col1Links || '[]');
                         this.col1Links = raw1.map((l, i) => ({ _id: Date.now() + Math.random() + i, label: l.label || '', url: l.url || '' }));

                         this.col2Active = this.$el.dataset.col2Active === '1';
                         this.col2Title = this.$el.dataset.col2Title || '';
                         const raw2 = JSON.parse(this.$el.dataset.col2Links || '[]');
                         this.col2Links = raw2.map((l, i) => ({ _id: Date.now() + Math.random() + i + 1000, label: l.label || '', url: l.url || '' }));
                     },
                     addCol1Link() {
                         this.col1Active = true;
                         this.col1Links.push({ _id: Date.now() + Math.random(), label: 'New Link', url: '/p/sample' });
                     },
                     removeCol1Link(idx) {
                         this.col1Links = this.col1Links.filter((_, i) => i !== idx);
                     },
                     addCol2Link() {
                         this.col2Active = true;
                         this.col2Links.push({ _id: Date.now() + Math.random(), label: 'New Link', url: '/p/sample' });
                     },
                     removeCol2Link(idx) {
                         this.col2Links = this.col2Links.filter((_, i) => i !== idx);
                     },
                     selectCustomPage(type, index, slug, title) {
                         if (type === 'col1') {
                             this.col1Links[index].url = '/p/' + slug;
                             if (!this.col1Links[index].label || this.col1Links[index].label === 'New Link') {
                                 this.col1Links[index].label = title;
                             }
                         } else {
                             this.col2Links[index].url = '/p/' + slug;
                             if (!this.col2Links[index].label || this.col2Links[index].label === 'New Link') {
                                 this.col2Links[index].label = title;
                             }
                         }
                     }
                 }">

                <!-- Hidden Inputs to submit JSON strings & Toggles -->
                <input type="hidden" name="footer_col1_title" :value="col1Title">
                <input type="hidden" name="footer_col1_links" :value="JSON.stringify(col1Links.map(l => ({ label: l.label, url: l.url })))">
                <input type="hidden" name="footer_col2_title" :value="col2Title">
                <input type="hidden" name="footer_col2_links" :value="JSON.stringify(col2Links.map(l => ({ label: l.label, url: l.url })))">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-2">
                    <div class="flex items-center gap-2 text-slate-800 font-bold text-xs uppercase tracking-wider">
                        <i data-lucide="link-2" class="h-4 w-4 text-blue-600"></i>
                        <span>5. Footer Link Columns & Custom Page Navigation (ফুটার নেভিগেশন ও লিংক সেটিং)</span>
                    </div>
                    <span class="text-[11px] text-slate-500">Customize or turn OFF Column 1 (SHOP) & Column 2 (EXPLORE)</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- COLUMN 1 (SHOP) -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-sm space-y-4">
                        <div class="flex items-center justify-between p-2 bg-white border border-slate-200 rounded-sm">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="footer_col1_active" value="1" x-model="col1Active" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-xs font-bold text-slate-800 uppercase">Show Column 1 in Footer</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded" :class="col1Active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'" x-text="col1Active ? 'Enabled (Visible)' : 'Disabled (Hidden)'"></span>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-800 uppercase">Column 1 Header Title</label>
                                <button type="button" @click.prevent="addCol1Link()" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded border border-blue-200 transition-all flex items-center gap-1 cursor-pointer">
                                    <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                                    <span>+ Add Link</span>
                                </button>
                            </div>
                            <input type="text" x-model="col1Title" placeholder="e.g. SHOP" class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-sm bg-white focus:ring-1 focus:ring-blue-500">

                            <div class="space-y-3 pt-2">
                                <template x-for="(link, index) in col1Links" :key="link._id">
                                    <div class="p-3 bg-white border border-slate-200 rounded-sm shadow-2xs space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[11px] font-bold text-slate-500" x-text="'Link #' + (index + 1)"></span>
                                            
                                            <!-- Custom Page Quick Select Dropdown -->
                                            <div class="flex items-center gap-2">
                                                <select @change="if ($event.target.value) { const parts = $event.target.value.split('|'); selectCustomPage('col1', index, parts[0], parts[1]); $event.target.value = ''; }" class="text-[11px] font-medium bg-blue-50/70 border border-blue-200 text-blue-700 px-2 py-1 rounded focus:outline-none cursor-pointer">
                                                    <option value="">-- Attach Custom Page --</option>
                                                    @foreach($customPages as $cp)
                                                        <option value="{{ $cp->slug }}|{{ addslashes($cp->title) }}">{{ $cp->title }} (/p/{{ $cp->slug }})</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" @click.prevent.stop="removeCol1Link(index)" class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2 py-1 rounded border border-red-200 transition-colors cursor-pointer" title="Delete Row">
                                                    <span>Remove</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase">Label / Text</label>
                                                <input type="text" x-model="link.label" placeholder="Link Display Text" class="w-full text-xs font-medium px-2.5 py-1.5 border border-slate-200 rounded focus:ring-1 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase">URL / Path</label>
                                                <input type="text" x-model="link.url" placeholder="e.g. /p/warranty-policy or /shop" class="w-full text-xs font-mono px-2.5 py-1.5 border border-slate-200 rounded focus:ring-1 focus:ring-blue-500">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="col1Links.length === 0" class="p-4 text-center text-xs text-slate-400 border border-dashed border-slate-300 rounded bg-white">
                                    No links in Column 1. Click "+ Add Link" above to add a link.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- COLUMN 2 (EXPLORE) -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-sm space-y-4">
                        <div class="flex items-center justify-between p-2 bg-white border border-slate-200 rounded-sm">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="footer_col2_active" value="1" x-model="col2Active" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-xs font-bold text-slate-800 uppercase">Show Column 2 in Footer</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded" :class="col2Active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'" x-text="col2Active ? 'Enabled (Visible)' : 'Disabled (Hidden)'"></span>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-800 uppercase">Column 2 Header Title</label>
                                <button type="button" @click.prevent="addCol2Link()" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded border border-blue-200 transition-all flex items-center gap-1 cursor-pointer">
                                    <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                                    <span>+ Add Link</span>
                                </button>
                            </div>
                            <input type="text" x-model="col2Title" placeholder="e.g. EXPLORE" class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-sm bg-white focus:ring-1 focus:ring-blue-500">

                            <div class="space-y-3 pt-2">
                                <template x-for="(link, index) in col2Links" :key="link._id">
                                    <div class="p-3 bg-white border border-slate-200 rounded-sm shadow-2xs space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[11px] font-bold text-slate-500" x-text="'Link #' + (index + 1)"></span>
                                            
                                            <!-- Custom Page Quick Select Dropdown -->
                                            <div class="flex items-center gap-2">
                                                <select @change="if ($event.target.value) { const parts = $event.target.value.split('|'); selectCustomPage('col2', index, parts[0], parts[1]); $event.target.value = ''; }" class="text-[11px] font-medium bg-blue-50/70 border border-blue-200 text-blue-700 px-2 py-1 rounded focus:outline-none cursor-pointer">
                                                    <option value="">-- Attach Custom Page --</option>
                                                    @foreach($customPages as $cp)
                                                        <option value="{{ $cp->slug }}|{{ addslashes($cp->title) }}">{{ $cp->title }} (/p/{{ $cp->slug }})</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" @click.prevent.stop="removeCol2Link(index)" class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2 py-1 rounded border border-red-200 transition-colors cursor-pointer" title="Delete Row">
                                                    <span>Remove</span>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase">Label / Text</label>
                                                <input type="text" x-model="link.label" placeholder="Link Display Text" class="w-full text-xs font-medium px-2.5 py-1.5 border border-slate-200 rounded focus:ring-1 focus:ring-blue-500">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-500 uppercase">URL / Path</label>
                                                <input type="text" x-model="link.url" placeholder="e.g. /p/warranty-policy or /blog" class="w-full text-xs font-mono px-2.5 py-1.5 border border-slate-200 rounded focus:ring-1 focus:ring-blue-500">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="col2Links.length === 0" class="p-4 text-center text-xs text-slate-400 border border-dashed border-slate-300 rounded bg-white">
                                    No links in Column 2. Click "+ Add Link" above to add a link.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-sm shadow-sm transition-all cursor-pointer">
                    <i data-lucide="save" class="h-4 w-4"></i>
                    <span>Save All Settings</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
