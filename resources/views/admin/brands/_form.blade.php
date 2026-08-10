<div class="space-y-5" x-data="{
    showMediaModal: false,
    logoPath: '{{ old('logo_path', $brand->logo_path ?? '') }}',
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
        this.logoPath = url;
        this.showMediaModal = false;
    },
    previewFile(event) {
        const file = event.target.files[0];
        if (file) {
            this.logoPath = URL.createObjectURL(file);
        }
    }
}">
    <div><label class="mb-1 block text-xs font-bold text-slate-700">Brand Name <span class="text-rose-500">*</span></label><input type="text" name="name" value="{{ old('name', $brand->name ?? '') }}" required class="w-full rounded-sm border border-slate-200 px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500"></div>
    <div><label class="mb-1 block text-xs font-bold text-slate-700">Slug <span class="font-normal text-slate-400">(optional)</span></label><input type="text" name="slug" value="{{ old('slug', $brand->slug ?? '') }}" placeholder="Auto-generated from name" class="w-full rounded-sm border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"></div>

    <div>
        <label class="mb-1 block text-xs font-bold text-slate-700">Brand Logo</label>
        <input type="hidden" name="logo_path" x-model="logoPath" />

        <div class="flex items-center gap-2">
            <div class="flex-1">
                <input type="file" name="logo_file" accept="image/*" @change="previewFile"
                       class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-slate-200 rounded-sm p-1 bg-white" />
            </div>
            <button type="button" @click="showMediaModal = true; fetchMedia()"
                    title="Choose from Media Library"
                    class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-sm shadow-sm transition-all flex items-center gap-1.5 shrink-0 cursor-pointer h-[38px]">
                <i data-lucide="folder-image" class="w-4 h-4 text-blue-400"></i>
                <span class="hidden sm:inline">Media Library</span>
            </button>
        </div>

        <div x-show="logoPath" class="pt-3">
            <span class="block text-[11px] font-semibold text-slate-600 mb-1 font-mono">Preview:</span>
            <div class="h-16 w-16 rounded-sm border border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center p-2">
                <img :src="logoPath" alt="Logo preview" class="max-h-full max-w-full object-contain" />
            </div>
        </div>

        @error('logo_path') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
        @error('logo_file') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
    </div>

    <!-- Media Library Modal -->
    <div x-show="showMediaModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
        <div class="bg-white rounded-sm shadow-xl w-full max-w-4xl max-h-[85vh] flex flex-col overflow-hidden border border-slate-200" @click.outside="showMediaModal = false">
            <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="folder-image" class="w-4 h-4 text-blue-600"></i>
                    Select Logo from Media Library
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
