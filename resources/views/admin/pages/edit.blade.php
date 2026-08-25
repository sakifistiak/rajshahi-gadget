<x-app-layout>
    <!-- Include Quill.js CDN for Rich Text Visual Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <div class="space-y-6 w-full">
        <!-- Top Navigation Header -->
        <div class="flex items-center justify-between p-5 bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pages.index') }}" class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                    <i data-lucide="arrow-left" class="h-5 w-5"></i>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Edit Custom Page: {{ $page->title }}</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Update content, HTML formatting or SEO meta tags</p>
                </div>
            </div>
            <div>
                <a href="{{ route('pages.custom', $page->slug) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-2 rounded-lg transition-colors border border-blue-200">
                    <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                    <span>View Public Page</span>
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl">
                <p class="text-xs font-bold text-rose-700 mb-1.5">Please fix the following before saving:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li class="text-xs text-rose-600">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.pages.update', $page->id) }}" enctype="multipart/form-data" class="space-y-6" x-data="customPageEditor()">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content Area (Left 2 cols) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title & Slug Card -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
                        <div>
                            <label for="title" class="block text-xs font-bold text-slate-700 mb-1">Page Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required placeholder="e.g. Warranty Policy" class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 font-medium" />
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-xs font-bold text-slate-700 mb-1">URL Slug</label>
                            <div class="flex items-center">
                                <span class="bg-slate-100 border border-r-0 border-slate-300 text-slate-500 text-xs px-3 py-2.5 rounded-l-lg font-mono">/page/</span>
                                <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 font-mono" />
                            </div>
                            @error('slug')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Page Content Editor Card (3 Modes: Visual, HTML, Preview) -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                        <div class="p-4 border-b border-slate-200 bg-slate-50/80 flex items-center justify-between flex-wrap gap-2">
                            <label class="block text-xs font-bold text-slate-800 flex items-center gap-2">
                                <i data-lucide="edit-3" class="h-4 w-4 text-blue-600"></i>
                                Page Content
                            </label>
                            <!-- 3 Mode Switch Tabs -->
                            <div class="flex items-center bg-slate-200/80 p-0.5 rounded-lg text-xs font-medium">
                                <button type="button" 
                                        @click="setMode('visual')" 
                                        :class="editorMode === 'visual' ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-1.5 rounded-md transition-all flex items-center gap-1.5">
                                    <i data-lucide="type" class="h-3.5 w-3.5"></i>
                                    Visual Editor
                                </button>
                                <button type="button" 
                                        @click="setMode('html')" 
                                        :class="editorMode === 'html' ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-1.5 rounded-md transition-all flex items-center gap-1.5">
                                    <i data-lucide="code" class="h-3.5 w-3.5"></i>
                                    HTML / Source Code
                                </button>
                                <button type="button" 
                                        @click="setMode('preview')" 
                                        :class="editorMode === 'preview' ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 hover:text-slate-900'" 
                                        class="px-3 py-1.5 rounded-md transition-all flex items-center gap-1.5">
                                    <i data-lucide="eye" class="h-3.5 w-3.5"></i>
                                    Live Preview
                                </button>
                            </div>
                        </div>

                        <div class="p-4">
                            <!-- 1. Visual Editor Mode (Quill WYSIWYG) -->
                            <div x-show="editorMode === 'visual'" class="space-y-2">
                                <p class="text-xs text-slate-500">Format text visually with bold, italic, headings, lists, colors, alignment, links, etc.</p>
                                <div class="bg-white border border-slate-300 rounded-lg overflow-hidden">
                                    <div id="quill-editor" style="min-height: 280px; font-size: 14px;"></div>
                                </div>
                            </div>

                            <!-- 2. HTML Source Code Mode -->
                            <div x-show="editorMode === 'html'" class="space-y-2">
                                <p class="text-xs text-slate-500">Write standard text or edit raw HTML code directly.</p>
                                <textarea name="content" 
                                          id="content" 
                                          x-ref="htmlTextarea"
                                          @input="syncFromHtml()"
                                          rows="18" 
                                          placeholder="<h1>Welcome</h1>..." 
                                          class="w-full p-4 text-xs font-mono bg-slate-900 text-emerald-400 border border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 leading-relaxed tracking-wide shadow-inner" 
                                          style="tab-size: 4;">{{ old('content', $page->content) }}</textarea>
                            </div>

                            <!-- 3. Live Preview Mode -->
                            <div x-show="editorMode === 'preview'" class="min-h-[350px] p-5 border border-slate-200 rounded-lg bg-white prose prose-slate max-w-none">
                                <div x-html="previewHtml || '<p class=\'text-slate-400 italic text-xs\'>No content to preview.</p>'"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Controls (Right 1 col) -->
                <div class="space-y-6">
                    <!-- Publishing & Status Card -->
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-4">
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2">Publishing Settings</h3>

                        <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-lg">
                            <div>
                                <span class="block text-xs font-bold text-slate-800">Publish Status</span>
                                <span class="text-[11px] text-slate-500">Make this page active on site</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $page->is_active) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-lg">
                            <div>
                                <span class="block text-xs font-bold text-slate-800">Show Page Title</span>
                                <span class="text-[11px] text-slate-500">Display the title & "Last updated" date at the top of the page</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="show_title" value="1" {{ old('show_title', $page->show_title) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-xs font-bold text-slate-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $page->sort_order) }}" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3 px-4 rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
                                <i data-lucide="save" class="h-4 w-4"></i>
                                <span>Update Page Changes</span>
                            </button>
                        </div>
                    </div>

                    <!-- SEO Meta Data Card -->
                    <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-xs space-y-4">
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider border-b border-slate-100 pb-2 flex items-center gap-1.5">
                            <i data-lucide="globe" class="h-3.5 w-3.5 text-blue-600"></i>
                            <span>SEO Meta Data</span>
                        </h3>

                        <div>
                            <label for="meta_title" class="block text-xs font-bold text-slate-700 mb-1">Meta Title</label>
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title', $page->meta_title) }}" placeholder="SEO title tag" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label for="meta_description" class="block text-xs font-bold text-slate-700 mb-1">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="3" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('meta_description', $page->meta_description) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Store Locations / Branches (optional card grid shown on this page) -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3 gap-3">
                    <div>
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider flex items-center gap-1.5">
                            <i data-lucide="map-pin" class="h-3.5 w-3.5 text-blue-600"></i>
                            Store Locations
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-1">Optional. Add outlet/branch cards (photo, address, phone, map link) — they render as a card grid on this page, e.g. a "Store Location" section on your Contact Us page.</p>
                    </div>
                    <button type="button" onclick="addLocationRow()" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-2.5 py-1 rounded-sm border border-blue-200 transition-colors shrink-0">
                        <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                        Add Location
                    </button>
                </div>

                <div id="locations-container" class="space-y-3">
                    <!-- Rows injected by JavaScript below -->
                </div>
            </div>
        </form>
    </div>

    @php
        // Precomputed here (instead of inline in the @json(...) call below)
        // because Blade's @json directive naively explode(',')s its raw
        // argument string to separate the value/options/depth params — an
        // inline array literal with several 'key' => $value, pairs has far
        // more than 2 commas, so the directive silently truncates it and
        // emits an unclosed '[' PHP parse error.
        $locationsForJs = $page->locations->map(fn ($l) => [
            'name' => $l->name,
            'address' => $l->address,
            'phone' => $l->phone,
            'map_link' => $l->map_link,
            'details' => $l->details,
            'image_path' => $l->image_path,
        ]);
    @endphp

    <script>
        function customPageEditor() {
            return {
                editorMode: 'visual',
                previewHtml: '',
                quill: null,
                init() {
                    this.$nextTick(() => {
                        const hiddenTextarea = this.$refs.htmlTextarea;
                        const editorElem = document.getElementById('quill-editor');

                        if (editorElem && typeof Quill !== 'undefined') {
                            this.quill = new Quill('#quill-editor', {
                                theme: 'snow',
                                placeholder: 'Write and format your page content here...',
                                modules: {
                                    toolbar: [
                                        [{ 'header': [1, 2, 3, 4, false] }],
                                        ['bold', 'italic', 'underline', 'strike'],
                                        [{ 'color': [] }, { 'background': [] }],
                                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                        [{ 'align': [] }],
                                        ['link', 'blockquote', 'code-block'],
                                        ['clean']
                                    ]
                                }
                            });

                            if (hiddenTextarea && hiddenTextarea.value) {
                                this.quill.root.innerHTML = hiddenTextarea.value;
                            }
                            this.previewHtml = hiddenTextarea ? hiddenTextarea.value : '';

                            this.quill.on('text-change', () => {
                                if (hiddenTextarea) {
                                    hiddenTextarea.value = this.quill.root.innerHTML;
                                }
                                this.previewHtml = this.quill.root.innerHTML;
                            });
                        }
                    });
                },
                setMode(mode) {
                    const hiddenTextarea = this.$refs.htmlTextarea;
                    if (this.editorMode === 'html' && mode !== 'html') {
                        if (this.quill && hiddenTextarea) {
                            this.quill.root.innerHTML = hiddenTextarea.value;
                        }
                    } else if (this.quill && hiddenTextarea) {
                        hiddenTextarea.value = this.quill.root.innerHTML;
                    }

                    if (hiddenTextarea) {
                        this.previewHtml = hiddenTextarea.value;
                    }
                    this.editorMode = mode;
                },
                syncFromHtml() {
                    const hiddenTextarea = this.$refs.htmlTextarea;
                    if (hiddenTextarea) {
                        this.previewHtml = hiddenTextarea.value;
                        if (this.quill) {
                            this.quill.root.innerHTML = hiddenTextarea.value;
                        }
                    }
                }
            }
        }

        let locationRowCounter = 0;

        function escLocationValue(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function addLocationRow(data = {}) {
            const idx = locationRowCounter++;
            const container = document.getElementById('locations-container');
            const image = data.image_path || '';
            const row = document.createElement('div');
            row.className = 'location-row bg-white border border-slate-200 rounded-lg p-4 space-y-3';
            row.innerHTML = `
                <div class="flex items-start justify-between gap-2">
                    <div class="flex items-start gap-3 flex-1">
                        <div class="h-16 w-16 shrink-0 rounded-lg border border-slate-200 bg-slate-100 overflow-hidden flex items-center justify-center">
                            <img id="location_preview_${idx}" src="${escLocationValue(image)}" class="h-full w-full object-cover ${image ? '' : 'hidden'}" onerror="this.classList.add('hidden')">
                            <i data-lucide="image" id="location_placeholder_${idx}" class="w-6 h-6 text-slate-300 ${image ? 'hidden' : ''}"></i>
                        </div>
                        <div class="flex-1 space-y-2">
                            <input type="hidden" name="location_image_path[]" id="location_path_${idx}" value="${escLocationValue(image)}">
                            <input type="file" name="location_image_file[]" accept="image/*" onchange="previewLocationFile(event, ${idx})" class="block w-full text-[11px] text-slate-500 file:mr-2 file:py-1.5 file:px-2.5 file:rounded-sm file:border-0 file:text-[11px] file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer border border-slate-200 rounded-sm p-1 bg-white" />
                            <p class="text-[10px] text-slate-400">Photo shown at the top of this location's card.</p>
                        </div>
                    </div>
                    <button type="button" onclick="this.closest('.location-row').remove()" class="text-rose-500 hover:text-rose-700 p-1.5 rounded-sm hover:bg-rose-50 transition-colors shrink-0" title="Remove location">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Branch Name</label>
                        <input type="text" name="location_name[]" value="${escLocationValue(data.name)}" placeholder="e.g. Dhanmondi Branch" class="border-slate-200 rounded-sm text-xs w-full py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none" />
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Phone</label>
                        <input type="text" name="location_phone[]" value="${escLocationValue(data.phone)}" placeholder="e.g. 01700000000" class="border-slate-200 rounded-sm text-xs w-full py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none" />
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Address</label>
                    <textarea name="location_address[]" rows="2" placeholder="Full address" class="border-slate-200 rounded-sm text-xs w-full py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">${escLocationValue(data.address)}</textarea>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Google Maps Link (powers the "Shop Map" button)</label>
                    <input type="text" name="location_map_link[]" value="${escLocationValue(data.map_link)}" placeholder="https://maps.google.com/..." class="border-slate-200 rounded-sm text-xs w-full py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none" />
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Details (revealed by the "Show Details" button)</label>
                    <textarea name="location_details[]" rows="2" placeholder="Opening hours, floor, landmark, etc." class="border-slate-200 rounded-sm text-xs w-full py-1.5 px-3 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">${escLocationValue(data.details)}</textarea>
                </div>
            `;
            container.appendChild(row);
            if (window.lucide) lucide.createIcons();
        }

        function previewLocationFile(event, idx) {
            const file = event.target.files[0];
            if (!file) return;
            const preview = document.getElementById('location_preview_' + idx);
            const placeholder = document.getElementById('location_placeholder_' + idx);
            if (preview) {
                preview.src = URL.createObjectURL(file);
                preview.classList.remove('hidden');
            }
            if (placeholder) placeholder.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const oldNames = @json(old('location_name', null));
            let rows;

            if (oldNames) {
                const oldAddresses = @json(old('location_address', []));
                const oldPhones = @json(old('location_phone', []));
                const oldMapLinks = @json(old('location_map_link', []));
                const oldDetails = @json(old('location_details', []));
                const oldImagePaths = @json(old('location_image_path', []));

                rows = oldNames.map((name, i) => ({
                    name: name,
                    address: oldAddresses[i] || '',
                    phone: oldPhones[i] || '',
                    map_link: oldMapLinks[i] || '',
                    details: oldDetails[i] || '',
                    image_path: oldImagePaths[i] || ''
                }));
            } else {
                rows = @json($locationsForJs);
            }

            rows.forEach(row => addLocationRow(row));
        });
    </script>
</x-app-layout>
