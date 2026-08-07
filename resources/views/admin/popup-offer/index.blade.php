<x-app-layout>
    <div class="p-6 w-full space-y-6" x-data="{
        showMediaModal: false,
        activeTarget: 'desktop',
        desktopImg: '{{ $settings['popup_offer_image'] ?? '' }}',
        mobileImg: '{{ $settings['popup_offer_image_mobile'] ?? '' }}',
        mediaFiles: [],
        loadingMedia: false,
        fetchMedia() {
            this.loadingMedia = true;
            fetch('{{ route('admin.media.list') }}')
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        this.mediaFiles = data.files;
                    }
                    this.loadingMedia = false;
                })
                .catch(() => this.loadingMedia = false);
        },
        selectMedia(url) {
            if (this.activeTarget === 'desktop') {
                this.desktopImg = url;
            } else {
                this.mobileImg = url;
            }
            this.showMediaModal = false;
        },
        previewFile(event, target) {
            const file = event.target.files[0];
            if (file) {
                if (target === 'desktop') {
                    this.desktopImg = URL.createObjectURL(file);
                } else {
                    this.mobileImg = URL.createObjectURL(file);
                }
            }
        }
    }">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                    <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                        <i data-lucide="layers" class="w-5 h-5"></i>
                    </div>
                    <span>Popup Offer Banner</span>
                </h1>
                <p class="text-xs text-slate-500 mt-1">Configure popup offer modal banner shown to website visitors (Apple Gadgets BD style).</p>
            </div>
        </div>

        <!-- Alert Banners -->
        @if (session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-md text-xs font-bold shadow-sm flex items-center gap-2.5">
                <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600 shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-md text-xs font-bold space-y-1 shadow-sm">
                @foreach($errors->all() as $error)
                    <div class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="h-4 w-4 text-rose-600 shrink-0"></i>
                        <span>{{ $error }}</span>
                    </div>
                @endforeach
            </div>
        @endif

        <form action="{{ route('admin.popup-offer.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Hidden image path fields bound to Alpine state -->
            <input type="hidden" name="popup_offer_image" :value="desktopImg" />
            <input type="hidden" name="popup_offer_image_mobile" :value="mobileImg" />

            <!-- Popup Offer Main Card -->
            <div class="bg-white rounded-md border border-slate-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-slate-900">Offer Modal Controls</h3>
                            <p class="text-xs text-slate-500">Enable or disable pop-up banner overlay on homepage.</p>
                        </div>
                    </div>
                    <label class="flex items-center gap-2.5 text-xs font-bold text-slate-700 cursor-pointer select-none">
                        <input type="checkbox" name="popup_offer_active" value="1" {{ ($settings['popup_offer_active'] ?? '0') == '1' ? 'checked' : '' }} class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500 border-slate-300">
                        <span>Enable Offer Popup</span>
                    </label>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Banner Images Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Desktop Banner Image -->
                        <div class="space-y-3 p-4 bg-slate-50/50 rounded-lg border border-slate-200">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                    Desktop Banner Image <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[10px] font-semibold text-slate-400">Desktop Viewport</span>
                            </div>

                            <input type="text" x-model="desktopImg" placeholder="Image URL e.g. /media/banner.jpg" class="w-full px-3 py-2 rounded border border-slate-300 text-xs font-medium focus:ring-1 focus:ring-blue-500">
                            
                            <!-- File Upload + Load From Gallery Button -->
                            <div class="flex items-center gap-2">
                                <div class="flex-1 min-w-0">
                                    <input type="file" name="popup_offer_image_file" accept="image/*" @change="previewFile($event, 'desktop')" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-slate-200 rounded p-1 bg-white">
                                </div>
                                
                                <button type="button" @click="activeTarget = 'desktop'; showMediaModal = true; fetchMedia()"
                                        title="Select Existing from Media Gallery"
                                        class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded shadow-sm transition-all flex items-center gap-1.5 shrink-0 cursor-pointer h-[34px]">
                                    <i data-lucide="folder-image" class="w-4 h-4 text-blue-400"></i>
                                    <span class="hidden sm:inline">Load from Gallery</span>
                                </button>
                            </div>
                            
                            <!-- Live Preview -->
                            <div x-show="desktopImg" class="mt-2 p-3 bg-white rounded border border-slate-200 shadow-sm">
                                <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Desktop Banner Live Preview:</span>
                                <img :src="desktopImg" alt="Desktop Banner" class="h-32 w-auto object-contain rounded border border-slate-200">
                            </div>
                        </div>

                        <!-- Mobile Banner Image -->
                        <div class="space-y-3 p-4 bg-slate-50/50 rounded-lg border border-slate-200">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">
                                    Mobile Banner Image <span class="text-slate-400 font-normal">(Optional)</span>
                                </label>
                                <span class="text-[10px] font-semibold text-slate-400">Mobile Viewport</span>
                            </div>

                            <input type="text" x-model="mobileImg" placeholder="Mobile Image URL e.g. /media/banner-mobile.jpg" class="w-full px-3 py-2 rounded border border-slate-300 text-xs font-medium focus:ring-1 focus:ring-blue-500">
                            
                            <!-- File Upload + Load From Gallery Button -->
                            <div class="flex items-center gap-2">
                                <div class="flex-1 min-w-0">
                                    <input type="file" name="popup_offer_image_mobile_file" accept="image/*" @change="previewFile($event, 'mobile')" class="w-full text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-slate-200 rounded p-1 bg-white">
                                </div>

                                <button type="button" @click="activeTarget = 'mobile'; showMediaModal = true; fetchMedia()"
                                        title="Select Existing from Media Gallery"
                                        class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded shadow-sm transition-all flex items-center gap-1.5 shrink-0 cursor-pointer h-[34px]">
                                    <i data-lucide="folder-image" class="w-4 h-4 text-blue-400"></i>
                                    <span class="hidden sm:inline">Load from Gallery</span>
                                </button>
                            </div>

                            <p class="text-[11px] text-slate-500 leading-relaxed">If left blank, Desktop Banner Image will automatically be used as fallback on mobile devices.</p>

                            <!-- Live Preview -->
                            <div x-show="mobileImg" class="mt-2 p-3 bg-white rounded border border-slate-200 shadow-sm">
                                <span class="text-[10px] font-bold text-slate-400 uppercase block mb-1">Mobile Banner Live Preview:</span>
                                <img :src="mobileImg" alt="Mobile Banner" class="h-32 w-auto object-contain rounded border border-slate-200">
                            </div>
                        </div>
                    </div>

                    <!-- Redirect, Frequency & Blur Controls -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4 pt-4 border-t border-slate-100">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Target Offer Link</label>
                            <input type="text" name="popup_offer_link" value="{{ $settings['popup_offer_link'] ?? '/shop' }}" placeholder="/shop or /product/slug" class="w-full px-3 py-2 rounded border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Target Window</label>
                            <select name="popup_offer_target" class="w-full px-3 py-2 rounded border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-blue-500">
                                <option value="_self" {{ ($settings['popup_offer_target'] ?? '_self') === '_self' ? 'selected' : '' }}>Same Tab (_self)</option>
                                <option value="_blank" {{ ($settings['popup_offer_target'] ?? '_self') === '_blank' ? 'selected' : '' }}>New Tab (_blank)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Display Frequency</label>
                            <select name="popup_offer_frequency" class="w-full px-3 py-2 rounded border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-blue-500">
                                <option value="session" {{ ($settings['popup_offer_frequency'] ?? 'session') === 'session' ? 'selected' : '' }}>Once per session</option>
                                <option value="daily" {{ ($settings['popup_offer_frequency'] ?? 'session') === 'daily' ? 'selected' : '' }}>Once per 24 hours</option>
                                <option value="always" {{ ($settings['popup_offer_frequency'] ?? 'session') === 'always' ? 'selected' : '' }}>Every refresh</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Backdrop Blur (px)</label>
                            <div class="flex items-center gap-2">
                                <input type="number" step="1" min="0" max="50" name="popup_offer_backdrop_blur"
                                       value="{{ $settings['popup_offer_backdrop_blur'] ?? '8' }}"
                                       placeholder="0–50"
                                       class="w-full px-3 py-2 rounded border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-blue-500">
                                <span class="text-xs text-slate-400 shrink-0 font-semibold">px</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">0 = no blur, 8 = default, max 50px</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Auto Open Delay (Sec)</label>
                            <input type="number" step="0.5" min="0" max="10" name="popup_offer_delay" value="{{ $settings['popup_offer_delay'] ?? '1' }}" class="w-full px-3 py-2 rounded border border-slate-300 text-xs font-semibold focus:ring-1 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <!-- Footer Save Button -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold uppercase rounded shadow-sm transition-colors flex items-center gap-2 cursor-pointer">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Save Popup Settings</span>
                    </button>
                </div>
            </div>
        </form>

        <!-- Media Library Modal (Choose Existing Image) -->
        <div x-show="showMediaModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-sm shadow-xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden border border-slate-200" @click.outside="showMediaModal = false">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="folder-image" class="w-4 h-4 text-blue-600"></i>
                        <span>Select Existing Image from Media Gallery</span>
                    </h3>
                    <button type="button" @click="showMediaModal = false" class="text-slate-400 hover:text-slate-700 text-sm font-bold cursor-pointer">✕</button>
                </div>

                <div class="p-4 overflow-y-auto flex-1">
                    <div x-show="loadingMedia" class="py-12 text-center text-slate-400 text-xs font-bold">
                        Loading media files...
                    </div>
                    <div x-show="!loadingMedia && mediaFiles.length === 0" class="py-12 text-center text-slate-400 text-xs">
                        No images found in media library.
                    </div>
                    <div x-show="!loadingMedia && mediaFiles.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">
                        <template x-for="file in mediaFiles" :key="file.url">
                            <div @click="selectMedia(file.url)" 
                                 class="group cursor-pointer rounded-sm border border-slate-200 bg-slate-50 overflow-hidden hover:border-blue-600 hover:shadow-md transition-all">
                                <div class="aspect-video w-full bg-slate-200 overflow-hidden relative">
                                    <img :src="file.url" :alt="file.name" class="w-full h-full object-cover group-hover:scale-105 transition-transform" />
                                </div>
                                <div class="p-2 bg-white text-[11px]">
                                    <p x-text="file.name" class="font-bold text-slate-800 truncate"></p>
                                    <p x-text="file.size" class="text-[10px] text-slate-400"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="p-3 border-t border-slate-100 bg-slate-50 flex justify-end">
                    <button type="button" @click="showMediaModal = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-sm transition-colors cursor-pointer">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
