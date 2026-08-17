<x-app-layout>
    <div class="w-full space-y-6" x-data="{
        showMediaModal: false,
        imagePath: '{{ old('image_path', $product->primaryImage()) }}',
        mediaFiles: [],
        loadingMedia: false,
        galleryTargetInput: null,
        galleryTargetPreview: null,
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
        openMediaModal(targetInputId = null, targetPreviewId = null) {
            this.galleryTargetInput = targetInputId;
            this.galleryTargetPreview = targetPreviewId;
            this.showMediaModal = true;
            this.fetchMedia();
        },
        selectMedia(url) {
            if (this.galleryTargetInput) {
                const input = document.getElementById(this.galleryTargetInput);
                if (input) input.value = url;
                if (this.galleryTargetPreview) {
                    const preview = document.getElementById(this.galleryTargetPreview);
                    if (preview) { preview.src = url; preview.classList.remove('hidden'); }
                    const placeholder = document.getElementById(this.galleryTargetPreview.replace('gallery_preview_', 'gallery_placeholder_'));
                    if (placeholder) placeholder.classList.add('hidden');
                }
                this.galleryTargetInput = null;
                this.galleryTargetPreview = null;
            } else {
                this.imagePath = url;
            }
            this.showMediaModal = false;
        },
        previewFile(event) {
            const file = event.target.files[0];
            if (file) { this.imagePath = URL.createObjectURL(file); }
        }
    }">
        <!-- Page Header -->
        <div class="flex items-center justify-between bg-white p-5 rounded-sm border border-slate-200 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Edit Product</h1>
                <p class="text-xs text-slate-500 mt-1">{{ $product->name }}</p>
            </div>
            <a href="{{ route('admin.products.index') }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-sm transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back to Products
            </a>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        <!-- Form Card -->
        <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-sm border border-slate-200 shadow-sm space-y-6">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Product Name <span class="text-rose-500">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name', $product->name) }}" required autofocus
                       class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Brand -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Brand <span class="text-rose-500">*</span></label>
                    <select id="brand_id" name="brand_id" required
                            class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all bg-white">
                        <option value="">Select Brand</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Category -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Category <span class="text-rose-500">*</span></label>
                    <select id="category_id" name="category_id" required
                            class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all bg-white">
                        <option value="">Select Category</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Condition -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Condition <span class="text-rose-500">*</span></label>
                    <select id="condition_id" name="condition_id" required
                            class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all bg-white">
                        <option value="">Select Condition</option>
                        @foreach ($conditions as $cond)
                            <option value="{{ $cond->id }}" {{ old('condition_id', $product->condition_id) == $cond->id ? 'selected' : '' }}>{{ $cond->label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Price -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Discount Price (BDT) <span class="text-rose-500">*</span></label>
                    <input id="price" type="number" name="price" value="{{ old('price', $product->price) }}" required
                           class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    <p class="mt-1 text-[11px] text-slate-400">The actual price customers pay.</p>
                </div>

                <!-- Compare Price -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Regular Price (BDT)</label>
                    <input id="compare_at_price" type="number" name="compare_at_price" value="{{ old('compare_at_price', $product->compare_at_price) }}"
                           class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all">
                    <p class="mt-1 text-[11px] text-slate-400">Optional — shown crossed out if higher than discount price.</p>
                </div>
            </div>

            <!-- Featured Image -->
            <div class="p-4 bg-slate-50 rounded-sm border border-slate-200">
                <div class="flex items-center justify-between mb-2">
                    <label class="block font-bold text-xs text-slate-800 uppercase tracking-wider">
                        {{ __('Featured Image') }}
                    </label>
                    <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-bold rounded-sm border border-blue-200">
                        {{ __('Main image on shop cards & product page') }}
                    </span>
                </div>
                <p class="text-[11px] text-slate-500 mb-3">Upload a new image or choose one already uploaded from the Media Library. Leave empty to keep the current image.</p>

                <input type="hidden" name="image_path" x-model="imagePath" />

                <div class="flex items-start gap-3">
                    <div class="h-20 w-20 shrink-0 rounded-sm border border-slate-200 bg-white overflow-hidden flex items-center justify-center" x-show="imagePath">
                        <img :src="imagePath" alt="Featured image preview" class="h-full w-full object-cover">
                    </div>
                    <div class="h-20 w-20 shrink-0 rounded-sm border border-dashed border-slate-300 bg-white flex items-center justify-center" x-show="!imagePath">
                        <i data-lucide="image" class="w-6 h-6 text-slate-300"></i>
                    </div>

                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <input type="file" name="image_file" accept="image/*" @change="previewFile"
                                   class="flex-1 block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-sm file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-slate-200 rounded-sm p-1 bg-white" />
                            <button type="button" @click="openMediaModal()"
                                    class="px-3 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-sm shadow-sm transition-all flex items-center gap-1.5 shrink-0">
                                <i data-lucide="folder-image" class="w-4 h-4 text-blue-400"></i>
                                <span class="hidden sm:inline">{{ __('Media Library') }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Gallery Images -->
            <div class="p-4 bg-slate-50 rounded-sm border border-slate-200">
                <div class="flex items-center justify-between mb-2">
                    <label class="block font-bold text-xs text-slate-800 uppercase tracking-wider">
                        {{ __('Gallery Images') }}
                    </label>
                    <button type="button" onclick="addGalleryRow()" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-sm border border-blue-200 transition-colors">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                        {{ __('+ Add Image') }}
                    </button>
                </div>
                <p class="text-[11px] text-slate-500 mb-3">Extra photos shown as thumbnails on the product page. Upload files or choose from the Media Library.</p>

                <div id="gallery-container" class="space-y-2">
                    <!-- JS will populate rows -->
                </div>
            </div>

            <!-- Highlighted Key Features (Bullet Points for Shop Product Cards) -->
            <div class="p-4 bg-slate-50 rounded-sm border border-slate-200">
                <div class="flex items-center justify-between mb-2">
                    <label class="block font-bold text-xs text-slate-800 uppercase tracking-wider">
                        {{ __('Highlighted Key Features (Bullet Points for Product Cards)') }}
                    </label>
                    <button type="button" onclick="addHighlightRow()" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-sm border border-blue-200 transition-colors">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                        {{ __('+ Add Feature') }}
                    </button>
                </div>
                <p class="text-[11px] text-slate-500 mb-3">Enter key specifications (e.g. "Apple M4 10-core", "16 GB RAM", "1 TB NVMe", "14.5\" FHD+ 120Hz"). These bullet points will show directly under the title on mobile & desktop shop cards. <strong>Tip:</strong> write as <strong>"Label: Value"</strong> (e.g. "RAM: 16 GB", "Display: 14.5\" FHD+ 120Hz") so it lines up as its own row on the <strong>/compare</strong> page — plain bullets without a colon still work, they just show as a simple checkmark feature there.</p>

                <div id="highlights-container" class="space-y-2">
                    <!-- JS will populate rows -->
                </div>
            </div>

            <!-- Specifications -->
            <div class="p-4 bg-slate-50 rounded-sm border border-slate-200">
                <div class="flex items-center justify-between mb-2">
                    <label class="block font-bold text-xs text-slate-800 uppercase tracking-wider">
                        {{ __('Specifications Table') }}
                    </label>
                    <button type="button" onclick="addSpecRow()" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-sm border border-blue-200 transition-colors">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                        {{ __('+ Add Spec') }}
                    </button>
                </div>
                <p class="text-[11px] text-slate-500 mb-3">Label/value pairs shown in the "Specifications" table on the product page (e.g. "Processor" / "Core Ultra 7 155H").</p>

                <div id="specs-container" class="space-y-2">
                    <!-- JS will populate rows -->
                </div>
            </div>

            <!-- Description -->
            @include('admin.products._description_editor')

            <!-- Stock Status -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">{{ __('Stock Status') }}</label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="in_stock" value="1" {{ old('in_stock', $product->in_stock ? '1' : '0') == '1' ? 'checked' : '' }}
                               class="border-slate-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                        <span class="text-xs font-semibold text-slate-700 select-none">{{ __('In Stock') }}</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="in_stock" value="0" {{ old('in_stock', $product->in_stock ? '1' : '0') == '0' ? 'checked' : '' }}
                               class="border-slate-300 text-blue-600 focus:ring-blue-500 h-4 w-4">
                        <span class="text-xs font-semibold text-slate-700 select-none">{{ __('Out of Stock') }}</span>
                    </label>
                </div>
            </div>

            <!-- New Arrival -->
            <div class="p-4 bg-amber-50 rounded-sm border border-amber-200 space-y-3">
                <div class="flex items-center gap-2">
                    <input id="is_new_arrival" type="checkbox" name="is_new_arrival" value="1" {{ old('is_new_arrival', $product->is_new_arrival) ? 'checked' : '' }}
                           class="rounded-sm border-slate-300 text-amber-600 focus:ring-amber-500 h-4 w-4">
                    <label for="is_new_arrival" class="text-xs font-bold text-slate-800 uppercase tracking-wider select-none cursor-pointer">{{ __('New Arrival Product (stock: TBA)') }}</label>
                </div>
                <p class="text-[11px] text-slate-500">Not physically in stock — sourced on order. Shows in the homepage "New Arrival" section, the status badge shows "TBA" instead of In Stock/Stock Out, and the storefront hides Buy Now / Add to Cart in favor of an "Order on WhatsApp" button only.</p>
                <div class="flex items-center gap-2 pt-2 border-t border-amber-200">
                    <input id="price_is_tba" type="checkbox" name="price_is_tba" value="1" {{ old('price_is_tba', $product->price_is_tba) ? 'checked' : '' }}
                           class="rounded-sm border-slate-300 text-amber-600 focus:ring-amber-500 h-4 w-4">
                    <label for="price_is_tba" class="text-xs font-semibold text-slate-700 select-none cursor-pointer">{{ __('Price is TBA (hide the price, show "TBA" instead)') }}</label>
                </div>
                <p class="text-[11px] text-slate-500">Independent of the checkbox above — the Price field below is still required and still used at checkout, this only controls whether the number is shown on the storefront.</p>
            </div>

            <div class="pt-4 border-t border-slate-100 flex justify-end gap-3">
                <a href="{{ route('admin.products.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-sm transition-all">
                    {{ __('Cancel') }}
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-sm shadow-sm transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    {{ __('Update Product') }}
                </button>
            </div>
        </form>

        <!-- Media Library Modal (shared by Featured Image + Gallery Images) -->
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

    <script>
        let galleryRowCounter = 0;

        function addGalleryRow(existingPath = '') {
            const idx = galleryRowCounter++;
            const container = document.getElementById('gallery-container');
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 gallery-row bg-white border border-slate-200 rounded-sm p-2';
            row.innerHTML = `
                <div class="h-12 w-12 shrink-0 rounded-sm border border-slate-200 bg-slate-100 overflow-hidden flex items-center justify-center">
                    <img id="gallery_preview_${idx}" src="${existingPath}" class="h-full w-full object-cover ${existingPath ? '' : 'hidden'}" onerror="this.classList.add('hidden')">
                    <i data-lucide="image" id="gallery_placeholder_${idx}" class="w-5 h-5 text-slate-300 ${existingPath ? 'hidden' : ''}"></i>
                </div>
                <input type="hidden" name="gallery_paths[]" id="gallery_path_${idx}" value="${existingPath}">
                <input type="file" name="gallery_files[]" accept="image/*" onchange="previewGalleryFile(event, ${idx})" class="flex-1 block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-sm file:border-0 file:text-[11px] file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-slate-200 rounded-sm p-1 bg-white" />
                <button type="button" @click="openMediaModal('gallery_path_${idx}', 'gallery_preview_${idx}')" title="Choose from Media Library" class="px-2.5 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-bold rounded-sm shadow-sm transition-all flex items-center gap-1 shrink-0">
                    <i data-lucide="folder-image" class="w-3.5 h-3.5 text-blue-400"></i>
                </button>
                <button type="button" onclick="this.closest('.gallery-row').remove()" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-sm hover:bg-rose-50 transition-colors shrink-0" title="Remove image">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            `;
            container.appendChild(row);
            if (window.lucide) lucide.createIcons();
            if (window.Alpine) window.Alpine.initTree(row);
        }

        function previewGalleryFile(event, idx) {
            const file = event.target.files[0];
            if (!file) return;
            const preview = document.getElementById('gallery_preview_' + idx);
            const placeholder = document.getElementById('gallery_placeholder_' + idx);
            if (preview) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            }
            if (placeholder) placeholder.classList.add('hidden');
        }

        function addHighlightRow(value = '') {
            const container = document.getElementById('highlights-container');
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 highlight-row';
            row.innerHTML = `
                <span class="h-1.5 w-1.5 rounded-full bg-slate-400 shrink-0 ml-1"></span>
                <input type="text" name="highlights[]" value="${value.replace(/"/g, '&quot;')}" placeholder="e.g. 16 GB RAM or Core Ultra 7 155H" class="border-slate-200 rounded-sm text-xs w-full py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none" />
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-sm hover:bg-rose-50 transition-colors" title="Remove feature">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            `;
            container.appendChild(row);
            if (window.lucide) lucide.createIcons();
        }

        function addSpecRow(label = '', value = '') {
            const container = document.getElementById('specs-container');
            const row = document.createElement('div');
            row.className = 'flex items-center gap-2 spec-row';
            row.innerHTML = `
                <input type="text" name="specs_label[]" value="${label.replace(/"/g, '&quot;')}" placeholder="e.g. Processor" class="border-slate-200 rounded-sm text-xs w-2/5 py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none" />
                <input type="text" name="specs_value[]" value="${value.replace(/"/g, '&quot;')}" placeholder="e.g. Core Ultra 7 155H" class="border-slate-200 rounded-sm text-xs w-full py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none" />
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-sm hover:bg-rose-50 transition-colors" title="Remove spec">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            `;
            container.appendChild(row);
            if (window.lucide) lucide.createIcons();
        }

        document.addEventListener('DOMContentLoaded', function() {
            const existingGalleryPaths = @json($product->images ? $product->images->where('is_primary', false)->pluck('image_path')->values() : []);
            const oldGalleryPaths = @json(old('gallery_paths', []));
            const galleryPaths = oldGalleryPaths.length > 0 ? oldGalleryPaths : existingGalleryPaths;
            galleryPaths.forEach(path => addGalleryRow(path));

            const existingHighlights = @json($product->highlights ? $product->highlights->pluck('text') : []);
            const oldHighlights = @json(old('highlights', []));
            const list = oldHighlights.length > 0 ? oldHighlights : existingHighlights;

            if (list.length > 0) {
                list.forEach(val => addHighlightRow(val));
            } else {
                addHighlightRow('');
                addHighlightRow('');
                addHighlightRow('');
                addHighlightRow('');
            }

            const existingSpecsLabel = @json($product->specs ? $product->specs->pluck('label') : []);
            const existingSpecsValue = @json($product->specs ? $product->specs->pluck('value') : []);
            const oldSpecsLabel = @json(old('specs_label', []));
            const oldSpecsValue = @json(old('specs_value', []));
            const specsLabel = oldSpecsLabel.length > 0 ? oldSpecsLabel : existingSpecsLabel;
            const specsValue = oldSpecsLabel.length > 0 ? oldSpecsValue : existingSpecsValue;
            if (specsLabel.length > 0) {
                specsLabel.forEach((label, i) => addSpecRow(label, specsValue[i] || ''));
            } else {
                addSpecRow('');
                addSpecRow('');
            }
        });
    </script>
</x-app-layout>
