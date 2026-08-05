<x-app-layout>
    <div class="space-y-6 max-w-5xl mx-auto">
        <!-- Top Navigation Header -->
        <div class="flex items-center justify-between p-5 bg-white rounded-xl border border-slate-200 shadow-sm">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.pages.index') }}" class="p-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors">
                    <i data-lucide="arrow-left" class="h-5 w-5"></i>
                </a>
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Create New Custom Page</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Write custom content or paste raw HTML code to build your page</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.pages.store') }}" class="space-y-6" x-data="{ editorMode: 'html' }">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content Area (Left 2 cols) -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Title & Slug Card -->
                    <div class="bg-white rounded-xl border border-slate-200 p-6 shadow-xs space-y-4">
                        <div>
                            <label for="title" class="block text-xs font-bold text-slate-700 mb-1">Page Title <span class="text-red-500">*</span></label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required placeholder="e.g. Warranty Policy, Terms & Conditions, EMI Guide" class="w-full px-3.5 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-medium" />
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-xs font-bold text-slate-700 mb-1">URL Slug (Optional)</label>
                            <div class="flex items-center">
                                <span class="bg-slate-100 border border-r-0 border-slate-300 text-slate-500 text-xs px-3 py-2.5 rounded-l-lg font-mono">/p/</span>
                                <input type="text" name="slug" id="slug" value="{{ old('slug') }}" placeholder="auto-generated-from-title" class="w-full px-3.5 py-2.5 text-xs border border-slate-300 rounded-r-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono" />
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">Leave empty to automatically generate from title.</p>
                            @error('slug')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Page Content Editor Card -->
                    <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
                        <div class="p-4 border-b border-slate-200 bg-slate-50/80 flex items-center justify-between">
                            <label class="block text-xs font-bold text-slate-800 flex items-center gap-2">
                                <i data-lucide="code" class="h-4 w-4 text-blue-600"></i>
                                Page Content (HTML / Rich Text)
                            </label>
                            <!-- Switch Mode Tabs -->
                            <div class="flex items-center bg-slate-200/80 p-0.5 rounded-lg text-xs">
                                <button type="button" @click="editorMode = 'html'" :class="editorMode === 'html' ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 font-medium'" class="px-3 py-1 rounded-md transition-all">
                                    HTML / Source Code
                                </button>
                                <button type="button" @click="editorMode = 'preview'" :class="editorMode === 'preview' ? 'bg-white text-blue-600 font-bold shadow-xs' : 'text-slate-600 font-medium'" class="px-3 py-1 rounded-md transition-all">
                                    Live Preview
                                </button>
                            </div>
                        </div>

                        <div class="p-4">
                            <!-- HTML Code Mode -->
                            <div x-show="editorMode === 'html'">
                                <p class="text-xs text-slate-500 mb-2">You can write standard text or paste custom raw HTML code (headings, paragraphs, images, videos, styling, tables, etc.).</p>
                                <textarea name="content" id="content" rows="18" placeholder="<h1>Welcome to Khan Gadget</h1>&#10;<p>Write your HTML content here...</p>" class="w-full p-4 text-xs font-mono bg-slate-900 text-emerald-400 border border-slate-700 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 leading-relaxed tracking-wide shadow-inner" style="tab-size: 4;">{{ old('content') }}</textarea>
                            </div>

                            <!-- Live Preview Mode -->
                            <div x-show="editorMode === 'preview'" class="min-h-[350px] p-4 border border-slate-200 rounded-lg bg-white prose prose-slate max-w-none">
                                <div x-html="document.getElementById('content').value || '<p class=\'text-slate-400 italic text-xs\'>No content to preview yet. Switch to HTML mode to type or paste content.</p>'"></div>
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
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>

                        <div>
                            <label for="sort_order" class="block text-xs font-bold text-slate-700 mb-1">Sort Order</label>
                            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', 0) }}" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                            <p class="text-[11px] text-slate-400 mt-1">Lower numbers appear first in lists.</p>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs py-3 px-4 rounded-lg shadow-sm transition-all flex items-center justify-center gap-2">
                                <i data-lucide="check" class="h-4 w-4"></i>
                                <span>Save & Publish Page</span>
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
                            <input type="text" name="meta_title" id="meta_title" value="{{ old('meta_title') }}" placeholder="SEO title tag" class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                        </div>

                        <div>
                            <label for="meta_description" class="block text-xs font-bold text-slate-700 mb-1">Meta Description</label>
                            <textarea name="meta_description" id="meta_description" rows="3" placeholder="Brief page summary for search engine snippet..." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500">{{ old('meta_description') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
