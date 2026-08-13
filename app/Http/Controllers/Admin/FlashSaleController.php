<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FlashSaleController extends Controller
{
    public function index(): View
    {
        $flashSales = FlashSale::withCount('items')->orderByDesc('starts_at')->get();

        return view('admin.flash-sales.index', compact('flashSales'));
    }

    public function create(): View
    {
        return view('admin.flash-sales.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->has('is_active');

        $flashSale = FlashSale::create($data);

        return redirect()->route('admin.flash-sales.edit', $flashSale)->with('success', 'Flash sale created. Now add products to it.');
    }

    public function edit(FlashSale $flashSale): View
    {
        $flashSale->load(['items.product.images']);
        $products = Product::orderBy('name')->get(['id', 'name', 'price']);

        return view('admin.flash-sales.edit', compact('flashSale', 'products'));
    }

    public function update(Request $request, FlashSale $flashSale): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        $data['is_active'] = $request->has('is_active');

        $flashSale->update($data);

        return redirect()->route('admin.flash-sales.edit', $flashSale)->with('success', 'Flash sale updated.');
    }

    public function destroy(FlashSale $flashSale): RedirectResponse
    {
        $flashSale->delete();

        return redirect()->route('admin.flash-sales.index')->with('success', 'Flash sale deleted.');
    }

    public function addProduct(Request $request, FlashSale $flashSale): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => [
                'required',
                'exists:products,id',
                Rule::unique('flash_sale_products')->where('flash_sale_id', $flashSale->id),
            ],
            'discount_type' => ['required', 'in:fixed_price,percent_off'],
            'sale_price' => ['required_if:discount_type,fixed_price', 'nullable', 'integer', 'min:1'],
            'percent_off' => ['required_if:discount_type,percent_off', 'nullable', 'integer', 'min:1', 'max:90'],
            'stock_limit' => ['nullable', 'integer', 'min:1'],
        ]);

        $flashSale->items()->create([
            'product_id' => $data['product_id'],
            'discount_type' => $data['discount_type'],
            'sale_price' => $data['discount_type'] === 'fixed_price' ? $data['sale_price'] : null,
            'percent_off' => $data['discount_type'] === 'percent_off' ? $data['percent_off'] : null,
            'stock_limit' => $data['stock_limit'] !== '' ? ($data['stock_limit'] ?? null) : null,
            'sort_order' => $flashSale->items()->max('sort_order') + 1,
        ]);

        return redirect()->route('admin.flash-sales.edit', $flashSale)->with('success', 'Product added to the flash sale.');
    }

    public function updateProduct(Request $request, FlashSale $flashSale, FlashSaleProduct $flashSaleProduct): RedirectResponse
    {
        abort_unless($flashSaleProduct->flash_sale_id === $flashSale->id, 404);

        $data = $request->validate([
            'discount_type' => ['required', 'in:fixed_price,percent_off'],
            'sale_price' => ['required_if:discount_type,fixed_price', 'nullable', 'integer', 'min:1'],
            'percent_off' => ['required_if:discount_type,percent_off', 'nullable', 'integer', 'min:1', 'max:90'],
            'stock_limit' => ['nullable', 'integer', 'min:1'],
        ]);

        $flashSaleProduct->update([
            'discount_type' => $data['discount_type'],
            'sale_price' => $data['discount_type'] === 'fixed_price' ? $data['sale_price'] : null,
            'percent_off' => $data['discount_type'] === 'percent_off' ? $data['percent_off'] : null,
            'stock_limit' => $data['stock_limit'] !== '' ? ($data['stock_limit'] ?? null) : null,
        ]);

        return redirect()->route('admin.flash-sales.edit', $flashSale)->with('success', 'Product updated.');
    }

    public function removeProduct(FlashSale $flashSale, FlashSaleProduct $flashSaleProduct): RedirectResponse
    {
        abort_unless($flashSaleProduct->flash_sale_id === $flashSale->id, 404);

        $flashSaleProduct->delete();

        return redirect()->route('admin.flash-sales.edit', $flashSale)->with('success', 'Product removed from the flash sale.');
    }
}
