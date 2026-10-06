@php
    // Products store one category_id: the sub category when one is picked,
    // otherwise the main category. Split it back into the two dropdowns here.
    $currentCategory = $categories->firstWhere('id', (int) ($selectedCategoryId ?? 0));
    $selectedMainId = old('main_category_id', $currentCategory ? ($currentCategory->parent_id ?? $currentCategory->id) : null);
    $selectedSubId = old('sub_category_id', $currentCategory && $currentCategory->parent_id ? $currentCategory->id : null);
@endphp

<!-- Main Category -->
<div>
    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Main Category <span class="text-rose-500">*</span></label>
    <select id="main_category_id" name="main_category_id" required
            class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all bg-white">
        <option value="">Select Main Category</option>
        @foreach ($categories->whereNull('parent_id') as $cat)
            <option value="{{ $cat->id }}" {{ (string) $selectedMainId === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    @error('main_category_id')<p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>@enderror
</div>

<!-- Sub Category (optional) -->
<div>
    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Sub Category <span class="text-slate-400 normal-case font-semibold">(optional)</span></label>
    <select id="sub_category_id" name="sub_category_id"
            class="w-full text-xs px-3.5 py-2.5 rounded-sm border border-slate-200 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none transition-all bg-white disabled:bg-slate-50 disabled:text-slate-400">
        <option value="">No sub category</option>
        @foreach ($categories->whereNotNull('parent_id') as $cat)
            <option value="{{ $cat->id }}" data-parent="{{ $cat->parent_id }}" {{ (string) $selectedSubId === (string) $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
        @endforeach
    </select>
    @error('sub_category_id')<p class="mt-1 text-[11px] text-rose-600">{{ $message }}</p>@enderror
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const main = document.getElementById('main_category_id');
    const sub = document.getElementById('sub_category_id');
    if (!main || !sub) return;

    // Only list the sub categories of the chosen main category.
    function refreshSubOptions() {
        let available = 0;
        sub.querySelectorAll('option[data-parent]').forEach(function (option) {
            const belongs = option.dataset.parent === main.value;
            option.hidden = !belongs;
            option.disabled = !belongs;
            if (belongs) available++;
            if (!belongs && option.selected) sub.value = '';
        });
        sub.disabled = available === 0;
        sub.dispatchEvent(new Event('change'));
    }

    main.addEventListener('change', refreshSubOptions);
    refreshSubOptions();
});
</script>
