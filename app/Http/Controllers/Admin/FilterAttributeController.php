<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\FilterAttribute;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FilterAttributeController extends Controller
{
    public function index(): View
    {
        $categories = Category::with(['filterAttributes' => fn ($q) => $q->orderBy('sort_order')])
            ->orderBy('name')
            ->get();

        return view('admin.filter-attributes.index', compact('categories'));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.filter-attributes.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['key'] = $this->uniqueKey($data['category_id'], $data['label']);

        FilterAttribute::create($data);

        return redirect()->route('admin.filter-attributes.index')->with('success', 'Filter attribute added. You can set its value from each product add/edit page.');
    }

    public function edit(FilterAttribute $filterAttribute): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.filter-attributes.edit', compact('filterAttribute', 'categories'));
    }

    public function update(Request $request, FilterAttribute $filterAttribute): RedirectResponse
    {
        $data = $this->validated($request);
        if ($data['category_id'] != $filterAttribute->category_id || $data['label'] !== $filterAttribute->label) {
            $data['key'] = $this->uniqueKey($data['category_id'], $data['label'], $filterAttribute->id);
        }

        $filterAttribute->update($data);

        return redirect()->route('admin.filter-attributes.index')->with('success', 'Filter attribute updated. Product values remain manually controlled.');
    }

    public function destroy(FilterAttribute $filterAttribute): RedirectResponse
    {
        $filterAttribute->delete();
        return redirect()->route('admin.filter-attributes.index')->with('success', 'Filter attribute deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'label' => ['required', 'string', 'max:60'],
            'unit' => ['nullable', 'string', 'max:20'],
            'type' => ['required', Rule::in(['range', 'select'])],
            'options' => ['required_if:type,select', 'nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($data['type'] === 'range') {
            $data['options'] = null;
        }
        $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }

    private function uniqueKey(int $categoryId, string $label, ?int $ignoreId = null): string
    {
        $base = Str::slug($label, '_');
        $key = $base;
        $i = 1;
        while (
            FilterAttribute::where('category_id', $categoryId)
                ->where('key', $key)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $key = $base . '_' . (++$i);
        }

        return $key;
    }
}
