<x-app-layout>
    <div class="max-w-4xl mx-auto space-y-6">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-200">
            <div>
                <h2 class="text-xl font-bold text-slate-800">Site Settings</h2>
                <p class="text-xs text-slate-500 mt-0.5">Manage brand identity, site logos (Light/Dark mode), and company contact info.</p>
            </div>
        </div>

        @if (session('success'))
            <div class="p-3 bg-emerald-50 text-emerald-800 border border-emerald-200 rounded-lg text-xs font-semibold flex items-center gap-2">
                <i data-lucide="check-circle" class="h-4 w-4 text-emerald-600"></i>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Card 1: Brand & Logo Management -->
            <div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm space-y-5">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <i data-lucide="image" class="h-4 w-4 text-blue-600"></i>
                    <span>Brand Logos (Light & Dark Mode)</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Light Mode Logo Upload -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">
                            Light Mode Logo (White Theme)
                        </label>
                        <div class="p-4 bg-slate-50 border border-dashed border-slate-300 rounded-lg flex flex-col items-center justify-center gap-3">
                            <img src="{{ $settings['logo_light'] }}" alt="Light Mode Logo Preview" class="h-12 w-auto object-contain bg-white p-2 border border-slate-200 rounded">
                            <input type="file" name="logo_light_file" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                        </div>
                        <p class="text-[11px] text-slate-400">Used on light backgrounds across the public website and topbar.</p>
                    </div>

                    <!-- Dark Mode Logo Upload -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold text-slate-700">
                            Dark Mode Logo (Dark Theme)
                        </label>
                        <div class="p-4 bg-slate-900 border border-dashed border-slate-700 rounded-lg flex flex-col items-center justify-center gap-3">
                            <img src="{{ $settings['logo_dark'] }}" alt="Dark Mode Logo Preview" class="h-12 w-auto object-contain bg-slate-800 p-2 border border-slate-700 rounded">
                            <input type="file" name="logo_dark_file" accept="image/*" class="text-xs text-slate-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-slate-200 hover:file:bg-slate-700 cursor-pointer">
                        </div>
                        <p class="text-[11px] text-slate-400">Used when Dark Mode is enabled across the site.</p>
                    </div>

                </div>
            </div>

            <!-- Card 2: General Information -->
            <div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <i data-lucide="globe" class="h-4 w-4 text-blue-600"></i>
                    <span>General Website Information</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Site Name</label>
                        <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Site Slogan / Tagline</label>
                        <input type="text" name="site_slogan" value="{{ old('site_slogan', $settings['site_slogan']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Site Meta Description</label>
                    <textarea name="site_description" rows="3" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">{{ old('site_description', $settings['site_description']) }}</textarea>
                </div>
            </div>

            <!-- Card 3: Contact Details -->
            <div class="bg-white rounded-lg border border-slate-200 p-5 shadow-sm space-y-4">
                <div class="flex items-center gap-2 pb-3 border-b border-slate-100 text-slate-800 font-bold text-sm">
                    <i data-lucide="phone-call" class="h-4 w-4 text-blue-600"></i>
                    <span>Contact Information</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Support Phone</label>
                        <input type="text" name="site_phone" value="{{ old('site_phone', $settings['site_phone']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Support Email</label>
                        <input type="email" name="site_email" value="{{ old('site_email', $settings['site_email']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Physical Store Address</label>
                    <input type="text" name="site_address" value="{{ old('site_address', $settings['site_address']) }}" class="w-full text-xs font-semibold px-3 py-2 border border-slate-200 rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-2">
                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-slate-900 hover:bg-black text-white text-xs font-bold rounded-md shadow transition-colors cursor-pointer">
                    <i data-lucide="save" class="h-4 w-4"></i>
                    <span>Save Settings</span>
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
