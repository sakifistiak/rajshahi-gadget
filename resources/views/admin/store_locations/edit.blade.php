<x-app-layout>
    <div class="w-full space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between bg-white p-5 rounded-sm border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Edit Store Location</h1>
                <p class="text-xs text-gray-500 mt-1">Update outlet details for {{ $storeLocation->name }}</p>
            </div>
            <a href="{{ route('admin.store-locations.index') }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-sm transition-all">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Back to Locations
            </a>
        </div>

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold rounded-sm space-y-1">
                @foreach($errors->all() as $error)
                    <p class="flex items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600 shrink-0"></i>
                        {{ $error }}
                    </p>
                @endforeach
            </div>
        @endif

        <!-- Form Card -->
        <form action="{{ route('admin.store-locations.update', $storeLocation) }}" method="POST" class="bg-white p-6 rounded-sm border border-gray-100 shadow-sm space-y-5">
            @csrf
            @method('PUT')

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Store Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $storeLocation->name) }}" required
                       class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all" />
            </div>

            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Physical Address <span class="text-rose-500">*</span></label>
                <div id="quill-editor-address" class="bg-white rounded-sm text-xs border border-gray-200" style="min-height: 120px;">{!! old('address', $storeLocation->address) !!}</div>
                <textarea name="address" id="address-hidden" class="hidden">{{ old('address', $storeLocation->address) }}</textarea>
            </div>

            <!-- Include Quill.js CDN & Init -->
            <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
            <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var quill = new Quill('#quill-editor-address', {
                        theme: 'snow',
                        placeholder: 'Enter physical address...',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                                [{ 'color': [] }, { 'background': [] }],
                                ['clean']
                            ]
                        }
                    });

                    var hiddenInput = document.getElementById('address-hidden');
                    quill.on('text-change', function() {
                        hiddenInput.value = quill.root.innerHTML;
                    });
                    
                    var form = hiddenInput.closest('form');
                    form.addEventListener('submit', function() {
                        hiddenInput.value = quill.root.innerHTML;
                    });
                });
            </script>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Contact Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $storeLocation->phone) }}"
                           class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all" />
                    <div class="flex items-center gap-4 text-xs font-semibold text-gray-700">
                        <span class="text-gray-400 font-normal">Link opens:</span>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" name="phone_link_type" value="tel" {{ old('phone_link_type', $storeLocation->phone_link_type ?? 'tel') === 'tel' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                            Phone dialer
                        </label>
                        <label class="flex items-center gap-1.5 cursor-pointer">
                            <input type="radio" name="phone_link_type" value="whatsapp" {{ old('phone_link_type', $storeLocation->phone_link_type ?? 'tel') === 'whatsapp' ? 'checked' : '' }} class="text-blue-600 focus:ring-blue-500">
                            WhatsApp
                        </label>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider">Display Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $storeLocation->sort_order) }}" required
                           class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-gray-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all" />
                </div>
            </div>

            <div class="pt-2 flex items-center gap-2">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $storeLocation->is_active) ? 'checked' : '' }}
                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 h-4 w-4" />
                <label for="is_active" class="text-xs font-semibold text-gray-700 select-none cursor-pointer">
                    Active (display in site footer)
                </label>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('admin.store-locations.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-sm transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-sm shadow-sm transition-all flex items-center gap-2">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Update Store Location
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
