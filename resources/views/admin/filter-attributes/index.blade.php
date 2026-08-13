<x-app-layout>
    <div class="p-6 w-full space-y-6">
        @if (session('success'))
            <div class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded text-xs font-semibold shadow-sm flex items-center gap-2">
                <i data-lucide="check-circle-2" class="h-4 w-4 text-emerald-600"></i>
                {{ session('success') }}
            </div>
        @endif

        <div class="flex items-center justify-between bg-white p-5 rounded-sm border border-gray-200 shadow-sm">
            <div>
                <h1 class="text-xl font-bold text-gray-900">Filter Attributes</h1>
                <p class="text-xs text-gray-500 mt-1">Define which spec filters (RAM, Storage, DPI, Connection...) appear on the shop page for each category. A category with no attributes here shows no spec filters.</p>
            </div>
            <a href="{{ route('admin.filter-attributes.create') }}"
               class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-sm shadow-sm transition-colors flex items-center gap-1.5">
                <i data-lucide="plus" class="h-3.5 w-3.5"></i>
                New Filter Attribute
            </a>
        </div>

        <div class="space-y-5">
            @forelse ($categories as $category)
                @if ($category->filterAttributes->count())
                    <div class="bg-white rounded-sm border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="text-sm font-bold text-slate-900">{{ $category->name }}</h3>
                        </div>
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead class="bg-slate-50/20">
                                <tr>
                                    <th class="px-6 py-2.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Label</th>
                                    <th class="px-6 py-2.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Type</th>
                                    <th class="px-6 py-2.5 text-left text-[10px] font-bold text-slate-400 uppercase tracking-wider">Unit / Options</th>
                                    <th class="px-6 py-2.5 text-right text-[10px] font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($category->filterAttributes as $attr)
                                    <tr class="hover:bg-slate-50/30 transition-colors">
                                        <td class="px-6 py-3 text-xs font-semibold text-slate-800">{{ $attr->label }}</td>
                                        <td class="px-6 py-3 whitespace-nowrap">
                                            <span class="px-2 py-0.5 inline-flex text-[9px] leading-5 font-bold rounded uppercase {{ $attr->type === 'range' ? 'bg-blue-50 text-blue-600 border border-blue-100' : 'bg-indigo-50 text-indigo-600 border border-indigo-100' }}">{{ $attr->type }}</span>
                                        </td>
                                        <td class="px-6 py-3 text-xs text-slate-600">
                                            {{ $attr->type === 'range' ? ($attr->unit ?: '—') : implode(', ', $attr->optionList()) }}
                                        </td>
                                        <td class="px-6 py-3 whitespace-nowrap text-right text-xs font-medium">
                                            <div class="flex justify-end items-center gap-3">
                                                <a href="{{ route('admin.filter-attributes.edit', $attr) }}" class="text-blue-600 hover:text-blue-800 font-semibold inline-flex items-center gap-1">
                                                    <i data-lucide="pencil" class="h-3.5 w-3.5"></i> Edit
                                                </a>
                                                <form action="{{ route('admin.filter-attributes.destroy', $attr) }}" method="POST" onsubmit="return confirm('Delete this filter attribute? Any saved product values for it will also be removed.');" class="inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800 font-semibold inline-flex items-center gap-1">
                                                        <i data-lucide="trash-2" class="h-3.5 w-3.5"></i> Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            @empty
            @endforelse

            @if ($categories->every(fn ($c) => $c->filterAttributes->isEmpty()))
                <div class="bg-white rounded-sm border border-gray-200 shadow-sm p-12 text-center">
                    <i data-lucide="sliders-horizontal" class="h-8 w-8 text-slate-300 mb-2 mx-auto"></i>
                    <p class="text-xs text-slate-400 font-semibold">No filter attributes defined yet</p>
                    <p class="text-[10px] text-slate-300 mt-0.5">Add one to start showing spec filters on the shop page.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
