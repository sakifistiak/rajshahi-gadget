<x-app-layout>
    <div class="p-6 w-full space-y-6">
        <div class="flex items-center justify-between bg-white p-5 rounded-sm border border-gray-200 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-gray-900">New Flash Sale</h1>
                <p class="text-xs text-gray-500 mt-1">Set the campaign window first — you'll add products on the next screen.</p>
            </div>
            <a href="{{ route('admin.flash-sales.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-sm transition-colors">
                ← Back to Flash Sales
            </a>
        </div>

        <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-6 max-w-2xl">
            <form action="{{ route('admin.flash-sales.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="title" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Campaign Title *</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                           placeholder="e.g. Eid Flash Sale"
                           class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                    @error('title') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="starts_at" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Starts At *</label>
                        <input type="datetime-local" name="starts_at" id="starts_at" value="{{ old('starts_at', now()->format('Y-m-d\TH:i')) }}" required
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        @error('starts_at') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="ends_at" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Ends At *</label>
                        <input type="datetime-local" name="ends_at" id="ends_at" value="{{ old('ends_at', now()->addDay()->format('Y-m-d\TH:i')) }}" required
                               class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-sm text-xs focus:bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all" />
                        @error('ends_at') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                        <span class="ml-3 text-xs font-bold text-gray-800 uppercase tracking-wider">Active</span>
                    </label>

                    <div class="flex gap-3">
                        <a href="{{ route('admin.flash-sales.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-sm transition-colors">Cancel</a>
                        <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-sm shadow-sm transition-all cursor-pointer">Create & Add Products</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
