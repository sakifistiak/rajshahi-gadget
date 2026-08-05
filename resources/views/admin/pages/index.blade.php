<x-app-layout>
    <div class="space-y-6">
        <!-- Top Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-5 bg-white rounded-xl border border-slate-200 shadow-sm">
            <div>
                <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                    <i data-lucide="file-text" class="h-6 w-6 text-blue-600"></i>
                    Custom Pages Management
                </h2>
                <p class="text-xs text-slate-500 mt-1">Create and manage custom content pages (Terms, Privacy, Warranty, Landing Pages, etc.)</p>
            </div>
            <div>
                <a href="{{ route('admin.pages.create') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs px-4 py-2.5 rounded-lg shadow-sm transition-all">
                    <i data-lucide="plus-circle" class="h-4 w-4"></i>
                    <span>+ Create New Page</span>
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-lg text-xs font-bold flex items-center justify-between shadow-xs">
                <div class="flex items-center gap-2">
                    <i data-lucide="check-circle-2" class="h-5 w-5 text-emerald-600"></i>
                    <span>{{ session('success') }}</span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">
                    <i data-lucide="x" class="h-4 w-4"></i>
                </button>
            </div>
        @endif

        <!-- Card Container -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-xs overflow-hidden">
            <!-- Filter / Search Header -->
            <div class="p-4 border-b border-slate-200 bg-slate-50/50 flex flex-col sm:flex-row items-center justify-between gap-3">
                <form method="GET" action="{{ route('admin.pages.index') }}" class="relative flex-1 w-full sm:w-auto max-w-md">
                    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search pages by title or slug..." class="w-full pl-9 pr-4 py-2 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-white" />
                </form>

                <div class="text-xs text-slate-500 font-medium">
                    Total Pages: <span class="font-bold text-slate-800">{{ $pages->total() }}</span>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-100/70 border-b border-slate-200 text-[11px] font-bold text-slate-600 uppercase tracking-wider">
                            <th class="py-3 px-4">Title</th>
                            <th class="py-3 px-4">Slug / Public URL</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Order</th>
                            <th class="py-3 px-4 text-center">Created</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs">
                        @forelse ($pages as $page)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-4 font-bold text-slate-800">
                                    <a href="{{ route('admin.pages.edit', $page->id) }}" class="hover:text-blue-600 hover:underline">
                                        {{ $page->title }}
                                    </a>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 font-mono text-[11px]">
                                    <div class="flex items-center gap-1.5">
                                        <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded border border-slate-200">/p/{{ $page->slug }}</span>
                                        <a href="{{ route('pages.custom', $page->slug) }}" target="_blank" class="text-blue-600 hover:text-blue-800 p-1" title="View Public Page">
                                            <i data-lucide="external-link" class="h-3.5 w-3.5"></i>
                                        </a>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    @if ($page->is_active)
                                        <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Published
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 border border-slate-200 px-2.5 py-0.5 rounded-full text-[11px] font-semibold">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span> Draft
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center text-slate-500 font-medium">
                                    {{ $page->sort_order }}
                                </td>
                                <td class="py-3.5 px-4 text-center text-slate-500">
                                    {{ $page->created_at->format('M d, Y') }}
                                </td>
                                <td class="py-3.5 px-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.pages.edit', $page->id) }}" class="inline-flex items-center gap-1 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold px-2.5 py-1 rounded-md border border-blue-200 text-xs transition-colors" title="Edit Page">
                                            <i data-lucide="edit-3" class="h-3.5 w-3.5"></i>
                                            <span>Edit</span>
                                        </a>
                                        <form method="POST" action="{{ route('admin.pages.destroy', $page->id) }}" onsubmit="return confirm('Are you sure you want to delete this page?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1 bg-red-50 hover:bg-red-100 text-red-700 font-bold px-2 py-1 rounded-md border border-red-200 text-xs transition-colors" title="Delete Page">
                                                <i data-lucide="trash-2" class="h-3.5 w-3.5"></i>
                                                <span>Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <i data-lucide="file-x" class="h-10 w-10 mx-auto text-slate-300 mb-2"></i>
                                    <p class="font-medium text-slate-500 text-sm">No custom pages found.</p>
                                    <p class="text-xs text-slate-400 mt-1">Click "Create New Page" above to add your first page.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($pages->hasPages())
                <div class="p-4 border-t border-slate-200">
                    {{ $pages->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
