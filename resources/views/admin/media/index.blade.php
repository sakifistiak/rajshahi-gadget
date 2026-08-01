<x-app-layout>
    <div class="p-6 space-y-6" x-data="{ 
        search: '',
        selectedUrl: '',
        copied: false,
        copyUrl(url) {
            navigator.clipboard.writeText(url);
            this.selectedUrl = url;
            this.copied = true;
            setTimeout(() => this.copied = false, 2000);
        }
    }">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="folder-image" class="w-6 h-6 text-blue-600"></i>
                    Media Library & Assets Manager
                </h1>
                <p class="text-xs text-gray-500 mt-1">Upload new images or reuse previously uploaded media across sliders, banners, and products</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.sliders.index') }}" 
                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition-colors flex items-center gap-1.5">
                    <i data-lucide="sliders" class="w-4 h-4"></i>
                    Home Sliders
                </a>
            </div>
        </div>

        <!-- Top Navigation Sub-menu (Side/Tab Menu) -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-1 flex border-b border-gray-100 overflow-x-auto">
            <a href="{{ route('admin.sliders.index') }}" 
               class="px-5 py-2.5 text-xs font-semibold rounded-lg transition-all text-gray-500 hover:text-gray-900 hover:bg-gray-50 flex items-center gap-1.5 whitespace-nowrap">
                <i data-lucide="sliders" class="w-4 h-4"></i>
                Home Slider
            </a>
            <a href="{{ route('admin.promos.index') }}" 
               class="px-5 py-2.5 text-xs font-semibold rounded-lg transition-all text-gray-500 hover:text-gray-900 hover:bg-gray-50 flex items-center gap-1.5 whitespace-nowrap">
                <i data-lucide="image" class="w-4 h-4"></i>
                Side Promo Banners
            </a>
            <a href="{{ route('admin.media.index') }}" 
               class="px-5 py-2.5 text-xs font-bold rounded-lg transition-all bg-blue-50 text-blue-600 flex items-center gap-1.5 whitespace-nowrap">
                <i data-lucide="folder-image" class="w-4 h-4"></i>
                Media Library
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-lg flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                    {{ session('success') }}
                </span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">Close</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-lg flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                    {{ session('error') }}
                </span>
                <button type="button" onclick="this.parentElement.remove()" class="text-rose-600 hover:text-rose-900">Close</button>
            </div>
        @endif

        <!-- Direct File Upload Box -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <h2 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-3 flex items-center gap-2">
                <i data-lucide="upload-cloud" class="w-4 h-4 text-blue-600"></i>
                Upload New Image File (আপলোড করুন)
            </h2>
            <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" class="flex flex-col sm:flex-row items-center gap-4">
                @csrf
                <input type="file" name="file" accept="image/*" required
                       class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer border border-gray-200 rounded-lg p-1 bg-gray-50" />
                <button type="submit" 
                        class="w-full sm:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition-all flex items-center justify-center gap-2 shrink-0 cursor-pointer">
                    <i data-lucide="upload" class="w-4 h-4"></i>
                    Upload File
                </button>
            </form>
        </div>

        <!-- Media Items List -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
                <div>
                    <h3 class="text-sm font-bold text-gray-900">All Saved Media Images ({{ count($files) }})</h3>
                    <p class="text-xs text-gray-500">Click any image to copy its URL to use in sliders or banners</p>
                </div>
                <div class="w-full sm:w-64">
                    <input type="text" x-model="search" placeholder="Search by filename..." 
                           class="w-full px-3 py-1.5 bg-gray-50 border border-gray-200 rounded-lg text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                </div>
            </div>

            <!-- Toast notification for copy -->
            <div x-show="copied" x-transition class="p-3 bg-blue-600 text-white text-xs font-bold rounded-lg shadow-md flex items-center justify-between">
                <span>✓ Image URL copied to clipboard: <span x-text="selectedUrl" class="underline"></span></span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                @forelse($files as $file)
                    <div class="group relative bg-gray-50 rounded-lg border border-gray-200 overflow-hidden hover:border-blue-400 hover:shadow-md transition-all flex flex-col justify-between"
                         x-show="!search || '{{ strtolower($file['name']) }}'.includes(search.toLowerCase())">
                        <div class="aspect-video w-full overflow-hidden bg-gray-200 relative cursor-pointer"
                             @click="copyUrl('{{ $file['url'] }}')">
                            <img src="{{ $file['url'] }}" alt="{{ $file['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <span class="px-2.5 py-1 bg-white text-gray-900 text-[10px] font-bold rounded shadow">Copy URL</span>
                            </div>
                        </div>
                        <div class="p-2.5 bg-white text-[11px] border-t border-gray-100 flex items-center justify-between gap-1">
                            <div class="overflow-hidden min-w-0">
                                <p class="font-bold text-gray-800 truncate" title="{{ $file['name'] }}">{{ $file['name'] }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $file['size'] }} • {{ $file['type'] }}</p>
                            </div>
                            @if($file['type'] === 'Uploads')
                                <form action="{{ route('admin.media.destroy', $file['name']) }}" method="POST" onsubmit="return confirm('Delete this file permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-gray-400 hover:text-rose-600 rounded transition-colors" title="Delete">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-400 text-xs">
                        No images found in media library. Upload an image above!
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
