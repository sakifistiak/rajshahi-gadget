<x-app-layout>
    <div class="p-6 w-full space-y-6" x-data="{ 
        showMediaModal: false,
        imagePath: '{{ old('image_path', $slider->image_path) }}',
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
            this.imagePath = url;
            this.showMediaModal = false;
        },
        previewFile(event) {
            const file = event.target.files[0];
            if (file) {
                this.imagePath = URL.createObjectURL(file);
            }
        }
    }">
        <!-- Page Header -->
        <div class="flex items-center justify-between bg-white p-5 rounded-sm border border-gray-200 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Edit Home Slider</h1>
                <p class="text-xs text-gray-500 mt-1">Update slider image, click URL or active status</p>
            </div>
            <a href="{{ route('admin.sliders.index') }}" 
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-sm transition-colors">
                ← Back to Sliders
            </a>
        </div>

        <!-- Full Width Form Card -->
        <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-6">
            <form action="{{ route('admin.sliders.update', $slider) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Hidden Image Path Field -->
                <input type="hidden" name="image_path" x-model="imagePath" />

                <!-- Image Selection Block -->
                <div class="space-y-4 bg-slate-50 p-5 rounded-sm border border-slate-200">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <label class="block text-xs font-bold text-slate-800 uppercase tracking-wider">1. Select or Upload Slider Image *</label>
                        <span class="px-2.5 py-1 bg-blue-100 text-blue-800 text-[11px] font-bold rounded-sm border border-blue-200">
                            Recommended Size: 1500 × 800 px (15:8)
                        </span>
                    </div>
                    
                    <!-- File Upload + Media Library Icon Button in 1 Line -->
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                            <input type="file" name="image_file" accept="image/*" @change="previewFile"
                                   class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-slate-200 rounded-sm p-1 bg-white" />
                        </div>
                        
                        <button type="button" @click="showMediaModal = true; fetchMedia()"
                                title="Choose from Media Library"
                                class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-sm shadow-sm transition-all flex items-center gap-1.5 shrink-0 cursor-pointer h-[38px]">
                            <i data-lucide="folder-image" class="w-4 h-4 text-blue-400"></i>
                            <span class="hidden sm:inline">Media Library</span>
                        </button>
                    </div>

                    <!-- Instant Live Preview (Matches 1500 x 800 px ratio) -->
                    <div x-show="imagePath" class="pt-2">
                        <span class="block text-[11px] font-semibold text-slate-600 mb-1 font-mono">Live Preview (Exact 1500 × 800 px Ratio):</span>
                        <div class="w-full max-w-xl aspect-[15/8] rounded-sm border border-slate-300 bg-slate-100 overflow-hidden shadow-sm">
                            <img :src="imagePath" alt="Preview" class="w-full h-full object-cover" />
                        </div>
                    </div>

                    @error('image_path') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Click Target Link & Sort Order -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cta_link" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">2. Click Target Link (Where image redirects)</label>
                        <input type="text" name="cta_link" id="cta_link" value="{{ old('cta_link', $slider->cta_link) }}"
                               placeholder="e.g. /shop or /product/iphone-16-pro-max"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        <p class="text-[11px] text-gray-400 mt-1">Users will be navigated to this link when clicking the slider image</p>
                        @error('cta_link') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sort_order" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">3. Display Order</label>
                        <input type="number" name="sort_order" id="sort_order" min="0" value="{{ old('sort_order', $slider->sort_order) }}"
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        @error('sort_order') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Active Status Toggle Button -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $slider->is_active ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-xs font-bold text-gray-800 uppercase tracking-wider">Active Slider</span>
                    </label>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.sliders.index') }}" 
                           class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-sm transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-sm shadow-sm transition-all cursor-pointer">
                            Update Slider
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Media Library Modal -->
        <div x-show="showMediaModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
            <div class="bg-white rounded-sm shadow-xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden border border-slate-200" @click.outside="showMediaModal = false">
                <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="folder-image" class="w-4 h-4 text-blue-600"></i>
                        Select Image from Media Library
                    </h3>
                    <button type="button" @click="showMediaModal = false" class="text-slate-400 hover:text-slate-700 text-sm font-bold">✕</button>
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
                    <button type="button" @click="showMediaModal = false" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold rounded-sm transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
