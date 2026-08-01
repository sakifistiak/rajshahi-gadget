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
                        <label class="block text-xs font-bold text-slate-700 mb-1">Support Email Address</label>
                        <input type="email" name="site_email" value="{{ old('site_email', $settings['site_email']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Store Physical Address</label>
                        <input type="text" name="site_address" value="{{ old('site_address', $settings['site_address']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Opening Business Hours (Footer)</label>
                        <input type="text" name="site_business_hours" value="{{ old('site_business_hours', $settings['site_business_hours']) }}" placeholder="e.g. Sat – Thu · 10:00 AM – 9:00 PM" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">
                        Mobile Menu Contact Number
                        <span class="text-slate-400 font-normal">(Shown as call button in mobile hamburger menu)</span>
                    </label>
                    <input type="text" name="mobile_menu_contact" value="{{ old('mobile_menu_contact', $settings['mobile_menu_contact']) }}" placeholder="e.g. 01341-246152" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-sm focus:ring-1 focus:ring-blue-500 focus:outline-none">
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
