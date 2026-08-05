<div class="grid gap-5 md:grid-cols-2">
    <div>
        <label class="mb-1 block text-xs font-bold text-slate-700">Category Name <span class="text-rose-500">*</span></label>
        <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required class="w-full rounded-sm border border-slate-200 px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>
    <div>
        <label class="mb-1 block text-xs font-bold text-slate-700">Slug <span class="font-normal text-slate-400">(optional)</span></label>
        <input type="text" name="slug" value="{{ old('slug', $category->slug ?? '') }}" placeholder="Auto-generated from name" class="w-full rounded-sm border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>
    <div>
        <label class="mb-1 block text-xs font-bold text-slate-700">Parent Category</label>
        <select name="parent_id" class="w-full rounded-sm border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
            <option value="">None (main category)</option>
            @foreach ($parents as $parent)
                <option value="{{ $parent->id }}" {{ (string) old('parent_id', $category->parent_id ?? '') === (string) $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="mb-1 block text-xs font-bold text-slate-700">Display Order</label>
        <input type="number" min="0" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="w-full rounded-sm border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-xs font-bold text-slate-700">Tagline</label>
        <input type="text" name="tagline" value="{{ old('tagline', $category->tagline ?? '') }}" placeholder="Short category description" class="w-full rounded-sm border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>
    <div class="md:col-span-2">
        <label class="mb-1 block text-xs font-bold text-slate-700">Image URL</label>
        <input type="text" name="image" value="{{ old('image', $category->image ?? '') }}" placeholder="/media/category.jpg or https://..." class="w-full rounded-sm border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
    </div>
</div>
