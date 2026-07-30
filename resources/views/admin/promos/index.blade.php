<x-app-layout>
    <div class="p-6 space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Homepage Sliders & Banners</h1>
                <p class="text-xs text-gray-500 mt-1">Manage side promo cards next to main slider (1 active = Full Height, 2 active = Split Stacked)</p>
            </div>
            <a href="{{ route('admin.promos.create') }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-lg shadow-sm transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add Side Promo Banner
            </a>
        </div>

        <!-- Navigation Tabs -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-1 flex border-b border-gray-100">
            <a href="{{ route('admin.sliders.index') }}" 
               class="px-5 py-2.5 text-xs font-medium rounded-lg transition-all text-gray-500 hover:text-gray-900 hover:bg-gray-50 flex items-center gap-1.5">
                <i data-lucide="sliders" class="w-4 h-4"></i>
                Main Hero Slides
            </a>
            <a href="{{ route('admin.promos.index') }}" 
               class="px-5 py-2.5 text-xs font-bold rounded-lg transition-all bg-blue-50 text-blue-600 flex items-center gap-1.5">
                <i data-lucide="image" class="w-4 h-4"></i>
                Side Promo Banners
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

        <!-- Promos Table -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3.5">Image</th>
                            <th class="px-5 py-3.5">Title & Offer Text</th>
                            <th class="px-5 py-3.5">Target Link</th>
                            <th class="px-5 py-3.5 text-center">Order</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        @forelse($promos as $promo)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="w-20 h-14 rounded-lg overflow-hidden border border-gray-200 bg-gray-50 shrink-0">
                                        <img src="{{ $promo->image_path }}" alt="{{ $promo->title }}" class="w-full h-full object-contain" />
                                    </div>
                                </td>
                                <td class="px-5 py-4 max-w-xs">
                                    <div class="text-sm font-semibold text-gray-900 truncate">{{ $promo->title }}</div>
                                    <div class="text-xs text-gray-500 truncate mt-0.5">{{ $promo->subtitle ?? 'No description' }}</div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700 text-[11px] font-mono">
                                        {{ $promo->link }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full bg-gray-100 text-gray-800 font-bold text-[11px]">
                                        #{{ $promo->sort_order }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <form action="{{ route('admin.promos.toggle', $promo) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider cursor-pointer transition-all {{ $promo->is_active ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                                            {{ $promo->is_active ? 'Active' : 'Inactive' }}
                                        </button>
                                    </form>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.promos.edit', $promo) }}" 
                                           class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                           title="Edit Promo">
                                            <i data-lucide="edit-3" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('admin.promos.destroy', $promo) }}" method="POST" onsubmit="return confirm('Delete this promo banner?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer"
                                                    title="Delete Promo">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-8 text-center text-gray-400">
                                    No promo banners added. Click "Add Side Promo Banner" to create one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
