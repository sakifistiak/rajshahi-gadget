<x-app-layout>
    <div class="w-full space-y-6">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 rounded-sm border border-gray-100 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Store Locations</h1>
                <p class="text-xs text-gray-500 mt-1">Manage physical outlet locations, addresses, and phone numbers shown in the site footer</p>
            </div>
            <a href="{{ route('admin.store-locations.create') }}" 
               class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold rounded-sm shadow-sm transition-all">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add New Store Location
            </a>
        </div>

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold rounded-sm flex items-center justify-between">
                <span class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                    {{ session('success') }}
                </span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900">Close</button>
            </div>
        @endif

        <!-- Locations Table -->
        <div class="bg-white rounded-sm border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-gray-50/80 border-b border-gray-100 text-[11px] font-bold uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-5 py-3.5">Store Name</th>
                            <th class="px-5 py-3.5">Physical Address</th>
                            <th class="px-5 py-3.5">Contact Phone</th>
                            <th class="px-5 py-3.5 text-center">Sort Order</th>
                            <th class="px-5 py-3.5 text-center">Status</th>
                            <th class="px-5 py-3.5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-medium">
                        @forelse($locations as $loc)
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="px-5 py-3.5 font-bold text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="map-pin" class="w-4 h-4 text-blue-600 shrink-0"></i>
                                        <span>{{ $loc->name }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-gray-700">
                                    {{ strip_tags($loc->address) }}
                                </td>
                                <td class="px-5 py-3.5 text-gray-700">
                                    {{ $loc->phone ?? 'N/A' }}
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-semibold bg-gray-100 text-gray-700">
                                        {{ $loc->sort_order }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-center">
                                    @if($loc->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600 border border-gray-200">
                                            Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.store-locations.edit', $loc) }}" 
                                           class="p-1.5 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
                                           title="Edit Store Location">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <form action="{{ route('admin.store-locations.destroy', $loc) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this store location?');"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="p-1.5 text-gray-500 hover:text-rose-600 hover:bg-rose-50 rounded transition-colors"
                                                    title="Delete Store Location">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center text-gray-400">
                                    <i data-lucide="map-pin-off" class="w-8 h-8 mx-auto mb-2 opacity-50"></i>
                                    No store locations found. Click "Add New Store Location" to add one.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
