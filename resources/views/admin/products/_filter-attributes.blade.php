@php
    $existingFilterValues = collect($productFilterValues ?? []);
    $oldFilterValues = old('filter_values', []);
@endphp

<div id="product-filter-attributes" class="p-4 bg-blue-50/50 rounded-sm border border-blue-200">
    <div class="flex items-center justify-between mb-1">
        <label class="block font-bold text-xs text-slate-800 uppercase tracking-wider">Product Filters</label>
        <span class="px-2 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-bold rounded-sm border border-blue-200">Used on Shop page</span>
    </div>
    <p class="text-[11px] text-slate-500 mb-3">Set the filter values for this product directly. These values are independent of the display specifications below.</p>

    @forelse ($filterAttributesByCategory as $categoryId => $attributes)
        <div class="filter-attribute-group hidden grid grid-cols-1 md:grid-cols-2 gap-4" data-category-id="{{ $categoryId }}">
            @foreach ($attributes as $attribute)
                @php
                    $oldValue = array_key_exists($attribute->id, $oldFilterValues)
                        ? $oldFilterValues[$attribute->id]
                        : ($existingFilterValues->get($attribute->id)?->text_value ?? $existingFilterValues->get($attribute->id)?->numeric_value);
                @endphp
                <div>
                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                        {{ $attribute->label }}{{ $attribute->unit ? ' (' . $attribute->unit . ')' : '' }}
                    </label>
                    @if ($attribute->type === 'select')
                        <select name="filter_values[{{ $attribute->id }}]" class="w-full text-xs px-3 py-2 rounded-sm border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                            <option value="">Select {{ $attribute->label }}</option>
                            @foreach ($attribute->optionList() as $option)
                                <option value="{{ $option }}" {{ (string) $oldValue === (string) $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                    @else
                        <input type="number" step="0.01" name="filter_values[{{ $attribute->id }}]" value="{{ $oldValue }}" placeholder="Enter {{ strtolower($attribute->label) }}"
                               class="w-full text-xs px-3 py-2 rounded-sm border border-slate-200 bg-white focus:border-blue-500 focus:ring-1 focus:ring-blue-500 outline-none">
                    @endif
                </div>
            @endforeach
        </div>
    @empty
        <p class="text-xs text-slate-500">No filter attributes are configured for any category yet.</p>
    @endforelse
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const category = document.getElementById('category_id');
    const groups = document.querySelectorAll('#product-filter-attributes .filter-attribute-group');
    if (!category || !groups.length) return;

    function updateFilterAttributes() {
        const selected = category.value;
        groups.forEach(group => {
            group.classList.toggle('hidden', group.dataset.categoryId !== selected);
        });
    }

    category.addEventListener('change', updateFilterAttributes);
    updateFilterAttributes();
});
</script>
