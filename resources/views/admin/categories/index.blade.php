<x-app-layout>
    @if (session('success') || session('error'))
        <div class="mb-5 rounded border p-3 text-xs font-semibold {{ session('error') ? 'border-rose-200 bg-rose-50 text-rose-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">{{ session('error') ?: session('success') }}</div>
    @endif

    <div class="overflow-hidden rounded-md border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50/50 px-5 py-4">
            <div><h3 class="text-[13px] font-bold text-slate-800">Category List</h3><p class="mt-0.5 text-[10px] text-slate-400">Manage categories used in the product catalog.</p></div>
            <a href="{{ route('admin.categories.create') }}" class="inline-flex items-center gap-1.5 rounded bg-blue-600 px-3 py-1.5 text-[11px] font-bold uppercase text-white shadow-sm hover:bg-blue-700"><i data-lucide="plus" class="h-3.5 w-3.5"></i>Add Category</a>
        </div>
        <div class="overflow-x-auto"><table class="min-w-full divide-y divide-slate-100"><thead class="bg-slate-50/20"><tr><th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">Category</th><th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">Parent</th><th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">Products</th><th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">Order</th><th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-400">Actions</th></tr></thead><tbody class="divide-y divide-slate-100">
            @forelse ($categories as $category)
                <tr class="hover:bg-slate-50/40"><td class="px-5 py-3.5"><div class="flex items-center gap-3">@if($category->image)<img src="{{ $category->image }}" alt="" class="h-9 w-9 rounded border border-slate-200 object-cover">@else<span class="grid h-9 w-9 place-items-center rounded bg-slate-100 text-slate-400"><i data-lucide="layers" class="h-4 w-4"></i></span>@endif<div><p class="text-xs font-bold text-slate-800">{{ $category->name }}</p><p class="text-[10px] text-slate-400">/{{ $category->slug }}{{ $category->tagline ? ' · ' . $category->tagline : '' }}</p></div></div></td><td class="px-5 py-3.5 text-xs text-slate-600">{{ $category->parent?->name ?? '—' }}</td><td class="px-5 py-3.5 text-xs font-semibold text-slate-700">{{ $category->products_count }}</td><td class="px-5 py-3.5 text-xs text-slate-600">{{ $category->sort_order }}</td><td class="px-5 py-3.5 text-right"><div class="flex justify-end gap-3"><a href="{{ route('admin.categories.edit', $category) }}" class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 hover:text-blue-800"><i data-lucide="edit-3" class="h-3.5 w-3.5"></i>Edit</a><form action="{{ route('admin.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?');">@csrf @method('DELETE')<button class="inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-800"><i data-lucide="trash-2" class="h-3.5 w-3.5"></i>Delete</button></form></div></td></tr>
            @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-xs text-slate-400">No categories found.</td></tr>
            @endforelse
        </tbody></table></div>
        @if ($categories->hasPages())<div class="border-t border-slate-100 px-5 py-4">{{ $categories->links() }}</div>@endif
    </div>
</x-app-layout>
