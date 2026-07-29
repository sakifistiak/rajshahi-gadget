<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(): View
    {
        $products = Product::with(['category', 'condition', 'brand'])->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::all();
        $conditions = Condition::all();
        $brands = Brand::all();
        return view('admin.products.create', compact('categories', 'conditions', 'brands'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'condition_id' => 'required|exists:conditions,id',
            'price' => 'required|integer|min:0',
            'compare_at_price' => 'nullable|integer|min:0',
            'badge' => 'nullable|string|max:50',
            'description' => 'required|string',
            'warranty' => 'nullable|string|max:255',
            'in_stock' => 'boolean',
            'highlights' => 'nullable|array',
            'specs_label' => 'nullable|array',
            'specs_value' => 'nullable|array',
            'image_url' => 'nullable|string',
        ]);

        $slug = Str::slug($request->name);
        $count = Product::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'condition_id' => $request->condition_id,
            'price' => $request->price,
            'compare_at_price' => $request->compare_at_price,
            'badge' => $request->badge,
            'description' => $request->description,
            'warranty' => $request->warranty,
            'in_stock' => $request->has('in_stock') ? (bool)$request->in_stock : true,
            'rating' => 4.5, // default for new
            'reviews_count' => 0,
        ]);

        // Add primary image if provided, otherwise default
        $imagePath = $request->image_url ?: '/assets/laptop-ultrabook-C5nU_6_f.jpg';
        $product->images()->create([
            'image_path' => $imagePath,
            'is_primary' => true,
            'sort_order' => 0
        ]);

        // Add highlights
        if ($request->has('highlights')) {
            foreach (array_filter($request->highlights) as $index => $text) {
                $product->highlights()->create([
                    'text' => $text,
                    'sort_order' => $index
                ]);
            }
        }

        // Add specs
        if ($request->has('specs_label') && $request->has('specs_value')) {
            foreach ($request->specs_label as $index => $label) {
                $val = $request->specs_value[$index] ?? null;
                if ($label && $val) {
                    $product->specs()->create([
                        'label' => $label,
                        'value' => $val,
                        'sort_order' => $index
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product): View
    {
        $categories = Category::all();
        $conditions = Condition::all();
        $brands = Brand::all();
        $product->load(['highlights', 'specs', 'images']);
        return view('admin.products.edit', compact('product', 'categories', 'conditions', 'brands'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'condition_id' => 'required|exists:conditions,id',
            'price' => 'required|integer|min:0',
            'compare_at_price' => 'nullable|integer|min:0',
            'badge' => 'nullable|string|max:50',
            'description' => 'required|string',
            'warranty' => 'nullable|string|max:255',
            'in_stock' => 'boolean',
            'highlights' => 'nullable|array',
            'specs_label' => 'nullable|array',
            'specs_value' => 'nullable|array',
        ]);

        $product->update([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'condition_id' => $request->condition_id,
            'price' => $request->price,
            'compare_at_price' => $request->compare_at_price,
            'badge' => $request->badge,
            'description' => $request->description,
            'warranty' => $request->warranty,
            'in_stock' => $request->has('in_stock'),
        ]);

        // Sync highlights (delete old and insert new)
        $product->highlights()->delete();
        if ($request->has('highlights')) {
            foreach (array_filter($request->highlights) as $index => $text) {
                $product->highlights()->create([
                    'text' => $text,
                    'sort_order' => $index
                ]);
            }
        }

        // Sync specs
        $product->specs()->delete();
        if ($request->has('specs_label') && $request->has('specs_value')) {
            foreach ($request->specs_label as $index => $label) {
                $val = $request->specs_value[$index] ?? null;
                if ($label && $val) {
                    $product->specs()->create([
                        'label' => $label,
                        'value' => $val,
                        'sort_order' => $index
                    ]);
                }
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully!');
    }
}
