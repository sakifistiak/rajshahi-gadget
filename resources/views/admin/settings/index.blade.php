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
            {{-- Identifies old-input as belonging to THIS form, so a validation failure on some
                 other admin page (e.g. Home Settings' popup-offer image check) doesn't cause the
                 footer column fields below to be wrongly re-derived from unrelated stale old(). --}}
            <input type="hidden" name="_settings_form" value="1">

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

                <!-- Favicon Upload -->
                <div class="space-y-2 pt-1">
                    <label class="block text-xs font-bold text-slate-700">
                        Favicon (Browser Tab Icon)
                    </label>
                    <div class="p-4 bg-slate-50 border border-dashed border-slate-300 rounded-sm flex items-center gap-4">
                        <img src="{{ $settings['site_favicon'] }}" alt="Favicon Preview" class="h-8 w-8 object-contain bg-white p-1 border border-slate-200 rounded-sm shrink-0">
                        <input type="file" name="favicon_file" accept="image/png,image/x-icon,image/svg+xml,image/webp,.ico" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    </div>
                    <p class="text-[11px] text-slate-400">Square image shown in the browser tab. PNG or ICO recommended, 32×32px or larger.</p>
                </div>

                <!-- Social Share Image Upload -->
                <div class="space-y-2 pt-1">
                    <label class="block text-xs font-bold text-slate-700">
                        Social Share Image (Link Preview)
                    </label>
                    <div class="p-4 bg-slate-50 border border-dashed border-slate-300 rounded-sm flex items-center gap-4">
                        <img src="{{ $settings['site_share_image'] }}" alt="Social Share Image Preview" class="h-16 w-28 object-cover bg-white p-1 border border-slate-200 rounded-sm shrink-0">
                        <input type="file" name="share_image_file" accept="image/png,image/jpeg,image/webp" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    </div>
                    <p class="text-[11px] text-slate-400">Shown as the preview image when a site link is shared on Facebook, WhatsApp, Messenger, etc. 1200×630px recommended.</p>
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
                        <div class="mt-2 flex items-center gap-4 text-xs font-semibold text-slate-700">
                            <span class="text-slate-400 font-normal">Footer link opens:</span>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="footer_phone_link_type" value="tel" {{ old('footer_phone_link_type', $settings['footer_phone_link_type']) === 'tel' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                                Phone dialer
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="radio" name="footer_phone_link_type" value="whatsapp" {{ old('footer_phone_link_type', $settings['footer_phone_link_type']) === 'whatsapp' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                                WhatsApp
                            </label>
                        </div>
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
                        <input type="email" id="site_email_main" name="site_email" value="{{ old('site_email', $settings['site_email']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
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

            <!-- Card 4: Footer Social & Contact Icons -->
            <div class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-800 font-bold text-xs uppercase tracking-wider">
                    <i data-lucide="share-2" class="h-4 w-4 text-blue-600"></i>
                    <span>4. Footer Social &amp; Contact Icons</span>
                </div>
                <p class="text-[11px] text-slate-400 -mt-2">These links power the row of official icon buttons shown under the footer's contact info.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Facebook Page Link</label>
                        <input type="text" name="social_facebook" value="{{ old('social_facebook', $settings['social_facebook']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">YouTube Channel Link</label>
                        <input type="text" name="social_youtube" value="{{ old('social_youtube', $settings['social_youtube']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Bikroy Shop Link</label>
                        <input type="text" name="social_instagram" value="{{ old('social_instagram', $settings['social_instagram']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">BD Stall Link</label>
                        <input type="text" name="social_whatsapp" value="{{ old('social_whatsapp', $settings['social_whatsapp']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Daraz Shop Link</label>
                        <input type="text" name="social_daraz" value="{{ old('social_daraz', $settings['social_daraz']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Email Icon Address
                            <span class="text-slate-400 font-normal">(synced with Support Email Address in Card 3)</span>
                        </label>
                        <input type="email" id="site_email_footer" value="{{ old('site_email', $settings['site_email']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Footer Copyright Text</label>
                    <input type="text" name="footer_copyright" value="{{ old('footer_copyright', $settings['footer_copyright']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <!-- Card 4b: Typography — Site Fonts -->
            <div class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-800 font-bold text-xs uppercase tracking-wider">
                    <i data-lucide="type" class="h-4 w-4 text-blue-600"></i>
                    <span>4b. Site Fonts (English &amp; Bangla)</span>
                </div>
                <p class="text-[11px] text-slate-400 -mt-2">Selected fonts apply site-wide. English text uses the English font; Bangla text automatically uses the Bangla font.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">English Font</label>
                        <select name="site_font_english" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            @foreach(['Inter', 'Poppins'] as $font)
                                <option value="{{ $font }}" {{ old('site_font_english', $settings['site_font_english']) === $font ? 'selected' : '' }}>{{ $font }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Bangla Font</label>
                        <select name="site_font_bangla" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                            @foreach(['Noto Serif Bengali', 'Hind Siliguri', 'Tiro Bangla', 'Anek Bangla'] as $font)
                                <option value="{{ $font }}" {{ old('site_font_bangla', $settings['site_font_bangla']) === $font ? 'selected' : '' }}>{{ $font }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Card 5: Footer Link Columns & Custom Page Links -->
            @php
                // If a PREVIOUS submission of this whole form failed validation on some
                // unrelated field (e.g. whatsapp_number), Laravel flashes old input and
                // redisplays this page. Without checking old() here, these data-* attributes
                // would silently fall back to the last-saved DB value — discarding whatever
                // the admin had just changed in this card (checkbox toggles included, since
                // an unchecked box is simply absent from old input rather than "false").
                $hasOldInput = session()->hasOldInput('_settings_form');
                $col1ActiveVal = $hasOldInput ? (old('footer_col1_active') ? '1' : '0') : $settings['footer_col1_active'];
                $col2ActiveVal = $hasOldInput ? (old('footer_col2_active') ? '1' : '0') : $settings['footer_col2_active'];
                $col3ActiveVal = $hasOldInput ? (old('footer_col3_active') ? '1' : '0') : $settings['footer_col3_active'];
            @endphp
            <div class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm space-y-5"
                 data-col1-active="{{ $col1ActiveVal }}"
                 data-col1-title="{{ old('footer_col1_title', $settings['footer_col1_title']) }}"
                 data-col1-links='{{ json_encode(json_decode(old('footer_col1_links', $settings['footer_col1_links']) ?: '[]', true) ?: [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }}'
                 data-col2-active="{{ $col2ActiveVal }}"
                 data-col2-title="{{ old('footer_col2_title', $settings['footer_col2_title']) }}"
                 data-col2-links='{{ json_encode(json_decode(old('footer_col2_links', $settings['footer_col2_links']) ?: '[]', true) ?: [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }}'
                 data-col3-active="{{ $col3ActiveVal }}"
                 data-col3-title="{{ old('footer_col3_title', $settings['footer_col3_title']) }}"
                 data-col3-links='{{ json_encode(json_decode(old('footer_col3_links', $settings['footer_col3_links']) ?: '[]', true) ?: [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }}'
                 x-data="{
                     col1Active: false,
                     col1Title: '',
                     col1Links: [],
                     col2Active: false,
                     col2Title: '',
                     col2Links: [],
                     col3Active: false,
                     col3Title: '',
                     col3Links: [],
                     init() {
                         this.col1Active = this.$el.dataset.col1Active === '1';
                         this.col1Title = this.$el.dataset.col1Title || '';
                         const raw1 = JSON.parse(this.$el.dataset.col1Links || '[]');
                         this.col1Links = raw1.map((l, i) => ({ _id: Date.now() + Math.random() + i, label: l.label || '', url: l.url || '' }));

                         this.col2Active = this.$el.dataset.col2Active === '1';
                         this.col2Title = this.$el.dataset.col2Title || '';
                         const raw2 = JSON.parse(this.$el.dataset.col2Links || '[]');
                         this.col2Links = raw2.map((l, i) => ({ _id: Date.now() + Math.random() + i + 1000, label: l.label || '', url: l.url || '' }));

                         this.col3Active = this.$el.dataset.col3Active === '1';
                         this.col3Title = this.$el.dataset.col3Title || '';
                         const raw3 = JSON.parse(this.$el.dataset.col3Links || '[]');
                         this.col3Links = raw3.map((l, i) => ({ _id: Date.now() + Math.random() + i + 2000, label: l.label || '', url: l.url || '' }));
                     },
                     addCol1Link() {
                         this.col1Active = true;
                         this.col1Links.push({ _id: Date.now() + Math.random(), label: 'New Link', url: '/page/sample' });
                     },
                     removeCol1Link(idx) {
                         this.col1Links = this.col1Links.filter((_, i) => i !== idx);
                     },
                     addCol2Link() {
                         this.col2Active = true;
                         this.col2Links.push({ _id: Date.now() + Math.random(), label: 'New Link', url: '/page/sample' });
                     },
                     removeCol2Link(idx) {
                         this.col2Links = this.col2Links.filter((_, i) => i !== idx);
                     },
                     addCol3Link() {
                         this.col3Active = true;
                         this.col3Links.push({ _id: Date.now() + Math.random(), label: 'New Link', url: '/page/sample' });
                     },
                     removeCol3Link(idx) {
                         this.col3Links = this.col3Links.filter((_, i) => i !== idx);
                     },
                     selectCustomPage(type, index, slug, title) {
                         var target = type === 'col1' ? this.col1Links : (type === 'col2' ? this.col2Links : this.col3Links);
                         target[index].url = '/page/' + slug;
                         if (!target[index].label || target[index].label === 'New Link') {
                             target[index].label = title;
                         }
                     }
                 }">

                <!-- Hidden Inputs to submit JSON strings & Toggles -->
                <input type="hidden" name="footer_col1_title" :value="col1Title">
                <input type="hidden" name="footer_col1_links" :value="JSON.stringify(col1Links.map(l => ({ label: l.label, url: l.url })))">
                <input type="hidden" name="footer_col2_title" :value="col2Title">
                <input type="hidden" name="footer_col2_links" :value="JSON.stringify(col2Links.map(l => ({ label: l.label, url: l.url })))">
                <input type="hidden" name="footer_col3_title" :value="col3Title">
                <input type="hidden" name="footer_col3_links" :value="JSON.stringify(col3Links.map(l => ({ label: l.label, url: l.url })))">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-2">
                    <div class="flex items-center gap-2 text-slate-800 font-bold text-xs uppercase tracking-wider">
                        <i data-lucide="link-2" class="h-4 w-4 text-blue-600"></i>
                        <span>5. Footer Link Columns & Custom Page Navigation (ফুটার নেভিগেশন ও লিংক সেটিং)</span>
                    </div>
                    <span class="text-[11px] text-slate-500">Turn Column 1, 2 & 3 on/off independently — the site info column on the left always stays put, and active columns automatically space themselves out (e.g. only one active column sits at the far right; two active columns sit middle + right).</span>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                                                        <option value="{{ $cp->slug }}|{{ addslashes($cp->title) }}">{{ $cp->title }} (/page/{{ $cp->slug }})</option>
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
                                                <input type="text" x-model="link.url" placeholder="e.g. /page/warranty-policy or /shop" class="w-full text-xs font-mono px-2.5 py-1.5 border border-slate-200 rounded focus:ring-1 focus:ring-blue-500">
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
                                                        <option value="{{ $cp->slug }}|{{ addslashes($cp->title) }}">{{ $cp->title }} (/page/{{ $cp->slug }})</option>
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
                                                <input type="text" x-model="link.url" placeholder="e.g. /page/warranty-policy or /blog" class="w-full text-xs font-mono px-2.5 py-1.5 border border-slate-200 rounded focus:ring-1 focus:ring-blue-500">
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

                    <!-- COLUMN 3 -->
                    <div class="p-4 bg-slate-50 border border-slate-200 rounded-sm space-y-4">
                        <div class="flex items-center justify-between p-2 bg-white border border-slate-200 rounded-sm">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="footer_col3_active" value="1" x-model="col3Active" class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-xs font-bold text-slate-800 uppercase">Show Column 3 in Footer</span>
                            </label>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded" :class="col3Active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200'" x-text="col3Active ? 'Enabled (Visible)' : 'Disabled (Hidden)'"></span>
                        </div>

                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-bold text-slate-800 uppercase">Column 3 Header Title</label>
                                <button type="button" @click.prevent="addCol3Link()" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded border border-blue-200 transition-all flex items-center gap-1 cursor-pointer">
                                    <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                                    <span>+ Add Link</span>
                                </button>
                            </div>
                            <input type="text" x-model="col3Title" placeholder="e.g. MORE" class="w-full text-xs font-bold px-3 py-2 border border-slate-300 rounded-sm bg-white focus:ring-1 focus:ring-blue-500">

                            <div class="space-y-3 pt-2">
                                <template x-for="(link, index) in col3Links" :key="link._id">
                                    <div class="p-3 bg-white border border-slate-200 rounded-sm shadow-2xs space-y-2">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="text-[11px] font-bold text-slate-500" x-text="'Link #' + (index + 1)"></span>

                                            <!-- Custom Page Quick Select Dropdown -->
                                            <div class="flex items-center gap-2">
                                                <select @change="if ($event.target.value) { const parts = $event.target.value.split('|'); selectCustomPage('col3', index, parts[0], parts[1]); $event.target.value = ''; }" class="text-[11px] font-medium bg-blue-50/70 border border-blue-200 text-blue-700 px-2 py-1 rounded focus:outline-none cursor-pointer">
                                                    <option value="">-- Attach Custom Page --</option>
                                                    @foreach($customPages as $cp)
                                                        <option value="{{ $cp->slug }}|{{ addslashes($cp->title) }}">{{ $cp->title }} (/page/{{ $cp->slug }})</option>
                                                    @endforeach
                                                </select>
                                                <button type="button" @click.prevent.stop="removeCol3Link(index)" class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2 py-1 rounded border border-red-200 transition-colors cursor-pointer" title="Delete Row">
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
                                                <input type="text" x-model="link.url" placeholder="e.g. /page/warranty-policy" class="w-full text-xs font-mono px-2.5 py-1.5 border border-slate-200 rounded focus:ring-1 focus:ring-blue-500">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                                <div x-show="col3Links.length === 0" class="p-4 text-center text-xs text-slate-400 border border-dashed border-slate-300 rounded bg-white">
                                    No links in Column 3. Click "+ Add Link" above to add a link.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 6: Mobile Slide Menu Info Links -->
            <div class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm space-y-5"
                 data-mobile-drawer-links='{{ json_encode(json_decode(old('mobile_drawer_info_links', $settings['mobile_drawer_info_links']) ?: '[]', true) ?: [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }}'
                 data-icon-svgs='{{ json_encode(array_map(fn ($i) => $i['svg'], $drawerIcons), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) }}'
                 x-data="{
                     mobileDrawerLinks: [],
                     iconSvgs: {},
                     init() {
                         this.iconSvgs = JSON.parse(this.$el.dataset.iconSvgs || '{}');
                         const raw = JSON.parse(this.$el.dataset.mobileDrawerLinks || '[]');
                         this.mobileDrawerLinks = raw.map((l, i) => ({ _id: Date.now() + Math.random() + i, label: l.label || '', url: l.url || '', icon: l.icon || 'link' }));
                     },
                     drawerIconPreviewSvg(icon) {
                         var inner = this.iconSvgs[icon] || this.iconSvgs['link'] || '';
                         return '<svg xmlns=&quot;http://www.w3.org/2000/svg&quot; width=&quot;16&quot; height=&quot;16&quot; viewBox=&quot;0 0 24 24&quot; fill=&quot;none&quot; stroke=&quot;currentColor&quot; stroke-width=&quot;2&quot; stroke-linecap=&quot;round&quot; stroke-linejoin=&quot;round&quot;>' + inner + '</svg>';
                     },
                     addMobileDrawerLink() {
                         this.mobileDrawerLinks.push({ _id: Date.now() + Math.random(), label: 'New Link', url: '/page/sample', icon: 'link' });
                     },
                     removeMobileDrawerLink(idx) {
                         this.mobileDrawerLinks = this.mobileDrawerLinks.filter((_, i) => i !== idx);
                     },
                     selectMobileDrawerCustomPage(index, slug, title) {
                         this.mobileDrawerLinks[index].url = '/page/' + slug;
                         if (!this.mobileDrawerLinks[index].label || this.mobileDrawerLinks[index].label === 'New Link') {
                             this.mobileDrawerLinks[index].label = title;
                         }
                     }
                 }">

                <input type="hidden" name="mobile_drawer_info_links" :value="JSON.stringify(mobileDrawerLinks.map(l => ({ label: l.label, url: l.url, icon: l.icon })))">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-100 gap-2">
                    <div class="flex items-center gap-2 text-slate-800 font-bold text-xs uppercase tracking-wider">
                        <i data-lucide="menu" class="h-4 w-4 text-blue-600"></i>
                        <span>6. Mobile Slide Menu Info Links (মোবাইল মেনুর "About Us / Contact / ..." লিংক সেটিং)</span>
                    </div>
                    <span class="text-[11px] text-slate-500">Controls the "About Us / Contact / Privacy &amp; Policy / ..." links shown near the bottom of the mobile (hamburger) slide-out menu. Add, remove, reorder or repoint them independently of the footer.</span>
                </div>

                <div class="p-4 bg-slate-50 border border-slate-200 rounded-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-800 uppercase">Menu Links</label>
                        <button type="button" @click.prevent="addMobileDrawerLink()" class="text-xs font-bold text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded border border-blue-200 transition-all flex items-center gap-1 cursor-pointer">
                            <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                            <span>+ Add Link</span>
                        </button>
                    </div>

                    <div class="space-y-3 pt-2">
                        <template x-for="(link, index) in mobileDrawerLinks" :key="link._id">
                            <div class="p-3 bg-white border border-slate-200 rounded-sm shadow-2xs space-y-2">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-[11px] font-bold text-slate-500" x-text="'Link #' + (index + 1)"></span>

                                    <!-- Custom Page Quick Select Dropdown -->
                                    <div class="flex items-center gap-2">
                                        <select @change="if ($event.target.value) { const parts = $event.target.value.split('|'); selectMobileDrawerCustomPage(index, parts[0], parts[1]); $event.target.value = ''; }" class="text-[11px] font-medium bg-blue-50/70 border border-blue-200 text-blue-700 px-2 py-1 rounded focus:outline-none cursor-pointer">
                                            <option value="">-- Attach Custom Page --</option>
                                            @foreach($customPages as $cp)
                                                <option value="{{ $cp->slug }}|{{ addslashes($cp->title) }}">{{ $cp->title }} (/page/{{ $cp->slug }})</option>
                                            @endforeach
                                        </select>
                                        <button type="button" @click.prevent.stop="removeMobileDrawerLink(index)" class="inline-flex items-center gap-1 text-[11px] font-bold text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-2 py-1 rounded border border-red-200 transition-colors cursor-pointer" title="Delete Row">
                                            <span>Remove</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-[auto_1fr_1fr] gap-2">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Icon</label>
                                        <div class="flex items-center gap-1.5">
                                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded border border-slate-200 bg-slate-50 text-slate-600" x-html="drawerIconPreviewSvg(link.icon)"></span>
                                            <select x-model="link.icon" class="w-full text-xs font-medium px-2 py-1.5 border border-slate-200 rounded focus:ring-1 focus:ring-blue-500">
                                                @foreach($drawerIcons as $iconKey => $iconMeta)
                                                    <option value="{{ $iconKey }}">{{ $iconMeta['label'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">Label / Text</label>
                                        <input type="text" x-model="link.label" placeholder="Link Display Text" class="w-full text-xs font-medium px-2.5 py-1.5 border border-slate-200 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-500 uppercase">URL / Path</label>
                                        <input type="text" x-model="link.url" placeholder="e.g. /about or /page/warranty-policy" class="w-full text-xs font-mono px-2.5 py-1.5 border border-slate-200 rounded focus:ring-1 focus:ring-blue-500">
                                    </div>
                                </div>
                            </div>
                        </template>
                        <div x-show="mobileDrawerLinks.length === 0" class="p-4 text-center text-xs text-slate-400 border border-dashed border-slate-300 rounded bg-white">
                            No links configured. Click "+ Add Link" above to add one.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 7: Shipping & Delivery Fees -->
            <div class="bg-white rounded-sm border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-800 font-bold text-xs uppercase tracking-wider">
                    <i data-lucide="truck" class="h-4 w-4 text-blue-600"></i>
                    <span>7. Shipping & Delivery Fees</span>
                </div>
                <p class="text-[11px] text-slate-400 -mt-2">Charged only when a customer picks Home Delivery at checkout — determined automatically by their selected district (Store/Outlet Pickup is always free).</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Inside Dhaka Delivery Fee (৳)</label>
                        <input type="number" min="0" step="1" name="shipping_fee_inside_dhaka" value="{{ old('shipping_fee_inside_dhaka', $settings['shipping_fee_inside_dhaka']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                        <p class="mt-1 text-[11px] text-slate-400">Applied when the customer's District is "Dhaka".</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Outside Dhaka Delivery Fee (৳)</label>
                        <input type="number" min="0" step="1" name="shipping_fee_outside_dhaka" value="{{ old('shipping_fee_outside_dhaka', $settings['shipping_fee_outside_dhaka']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                        <p class="mt-1 text-[11px] text-slate-400">Applied for any other district.</p>
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

    <script>
        // Card 3 "Support Email Address" and Card 4 "Email Icon Address" show the same
        // setting (site_email). Only the Card 3 input has a name= attribute and is submitted;
        // this keeps the Card 4 preview field in sync so it never silently overwrites it.
        (function () {
            var main = document.getElementById('site_email_main');
            var footer = document.getElementById('site_email_footer');
            if (!main || !footer) return;
            main.addEventListener('input', function () { footer.value = main.value; });
            footer.addEventListener('input', function () { main.value = footer.value; });
        })();
    </script>
</x-app-layout>
