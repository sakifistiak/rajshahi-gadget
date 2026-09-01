<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Product;
use App\Support\ImageUploader;
use App\Support\ProductFilterSync;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['category', 'condition', 'brand']);

        if ($search = trim((string) $request->input('q'))) {
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('stock')) {
            if ($request->input('stock') === 'in_stock') {
                $query->where('in_stock', true);
            } elseif ($request->input('stock') === 'out_of_stock') {
                $query->where('in_stock', false);
            }
        }

        if ($request->filled('category')) {
            $categoryId = $request->input('category');
            $query->where(function ($q) use ($categoryId) {
                if (is_numeric($categoryId)) {
                    $q->where('category_id', $categoryId);
                } else {
                    $q->whereHas('category', fn ($c) => $c->where('slug', $categoryId)->orWhere('name', $categoryId));
                }
            });
        }

        if ($request->filled('condition')) {
            $conditionId = $request->input('condition');
            $query->where(function ($q) use ($conditionId) {
                if (is_numeric($conditionId)) {
                    $q->where('condition_id', $conditionId);
                } else {
                    $q->whereHas('condition', fn ($c) => $c->where('slug', $conditionId)->orWhere('label', $conditionId)->orWhere('short', $conditionId));
                }
            });
        }

        if ($request->filled('brand')) {
            $brandId = $request->input('brand');
            $query->where(function ($q) use ($brandId) {
                if (is_numeric($brandId)) {
                    $q->where('brand_id', $brandId);
                } else {
                    $q->whereHas('brand', fn ($b) => $b->where('slug', $brandId)->orWhere('name', $brandId));
                }
            });
        }

        $products = $query->latest()->paginate(15)->withQueryString();

        // Live search / filter / pagination requests only need the table markup.
        if ($request->ajax()) {
            return view('admin.products._results', compact('products'));
        }

        $categories = Category::orderBy('name')->get();
        $conditions = Condition::orderBy('label')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'conditions', 'brands'));
    }

    /**
     * Only allow redirecting back to a URL that belongs to this admin product list,
     * so a tampered ?return= value can't bounce the user off-site.
     */
    private function safeReturnUrl(?string $return): ?string
    {
        if (! $return) {
            return null;
        }

        return str_starts_with($return, route('admin.products.index')) ? $return : null;
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
            'description' => 'required|string',
            'in_stock' => 'boolean',
            'is_new_arrival' => 'boolean',
            'price_is_tba' => 'boolean',
            'highlights' => 'nullable|array',
            'specs_label' => 'nullable|array',
            'specs_value' => 'nullable|array',
            'image_path' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
            'gallery_paths' => 'nullable|array',
            'gallery_paths.*' => 'nullable|string|max:500',
            'gallery_files' => 'nullable|array',
            'gallery_files.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ]);

        $slug = Str::slug($request->name);
        $count = Product::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-'.($count + 1);
        }

        $product = Product::create([
            'name' => $request->name,
            'slug' => $slug,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'condition_id' => $request->condition_id,
            'price' => $request->price,
            'compare_at_price' => $request->compare_at_price,
            'description' => $request->description,
            'in_stock' => $request->input('in_stock', '1') === '1',
            'is_new_arrival' => $request->has('is_new_arrival'),
            'price_is_tba' => $request->has('price_is_tba'),
            'rating' => 4.5, // default for new
            'reviews_count' => 0,
        ]);

        // Add featured (primary) image
        $imagePath = $request->image_path;
        if ($request->hasFile('image_file')) {
            $imagePath = $this->storeUploadedImage($request->file('image_file'));
        }
        if ($imagePath) {
            $product->images()->create([
                'image_path' => $imagePath,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        // Add gallery images
        $galleryFiles = $request->file('gallery_files', []);
        foreach ($request->input('gallery_paths', []) as $index => $path) {
            $finalPath = $path;
            if (isset($galleryFiles[$index]) && $galleryFiles[$index] instanceof UploadedFile && $galleryFiles[$index]->isValid()) {
                $finalPath = $this->storeUploadedImage($galleryFiles[$index]);
            }
            if (! empty($finalPath)) {
                $product->images()->create([
                    'image_path' => $finalPath,
                    'is_primary' => false,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        // Add highlights
        if ($request->has('highlights')) {
            foreach (array_filter($request->highlights) as $index => $text) {
                $product->highlights()->create([
                    'text' => $text,
                    'sort_order' => $index,
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
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        ProductFilterSync::syncProduct($product);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully!');
    }

    public function edit(Product $product): View
    {
        $categories = Category::all();
        $conditions = Condition::all();
        $brands = Brand::all();
        $product->load(['highlights', 'specs', 'images', 'filterValues']);

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
            'description' => 'required|string',
            'in_stock' => 'boolean',
            'is_new_arrival' => 'boolean',
            'price_is_tba' => 'boolean',
            'highlights' => 'nullable|array',
            'specs_label' => 'nullable|array',
            'specs_value' => 'nullable|array',
            'image_path' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
            'gallery_paths' => 'nullable|array',
            'gallery_paths.*' => 'nullable|string|max:500',
            'gallery_files' => 'nullable|array',
            'gallery_files.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ]);

        $product->update([
            'name' => $request->name,
            'brand_id' => $request->brand_id,
            'category_id' => $request->category_id,
            'condition_id' => $request->condition_id,
            'price' => $request->price,
            'compare_at_price' => $request->compare_at_price,
            'description' => $request->description,
            'in_stock' => $request->input('in_stock', '1') === '1',
            'is_new_arrival' => $request->has('is_new_arrival'),
            'price_is_tba' => $request->has('price_is_tba'),
        ]);

        // Sync highlights (delete old and insert new)
        $product->highlights()->delete();
        if ($request->has('highlights')) {
            foreach (array_filter($request->highlights) as $index => $text) {
                $product->highlights()->create([
                    'text' => $text,
                    'sort_order' => $index,
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
                        'sort_order' => $index,
                    ]);
                }
            }
        }

        // Update featured (primary) image
        $imagePath = $request->image_path;
        if ($request->hasFile('image_file')) {
            $imagePath = $this->storeUploadedImage($request->file('image_file'));
        }
        $primaryImage = $product->images()->where('is_primary', true)->first();
        if ($primaryImage) {
            if ($imagePath) {
                $primaryImage->update(['image_path' => $imagePath]);
            }
        } elseif ($imagePath) {
            $product->images()->create([
                'image_path' => $imagePath,
                'is_primary' => true,
                'sort_order' => 0,
            ]);
        }

        // Sync gallery images (delete old non-primary images and insert new)
        $product->images()->where('is_primary', false)->delete();
        $galleryFiles = $request->file('gallery_files', []);
        foreach ($request->input('gallery_paths', []) as $index => $path) {
            $finalPath = $path;
            if (isset($galleryFiles[$index]) && $galleryFiles[$index] instanceof UploadedFile && $galleryFiles[$index]->isValid()) {
                $finalPath = $this->storeUploadedImage($galleryFiles[$index]);
            }
            if (! empty($finalPath)) {
                $product->images()->create([
                    'image_path' => $finalPath,
                    'is_primary' => false,
                    'sort_order' => $index + 1,
                ]);
            }
        }

        ProductFilterSync::syncProduct($product);

        $target = $this->safeReturnUrl($request->input('return')) ?? route('admin.products.index');

        return redirect($target)->with('success', 'Product updated successfully!');
    }

    public function destroy(Request $request, Product $product): RedirectResponse
    {
        $target = $this->safeReturnUrl($request->input('return')) ?? route('admin.products.index');

        if ($product->orderItems()->exists()) {
            return redirect($target)->with('error', "Cannot delete \"{$product->name}\" because it has existing orders.");
        }

        $product->delete();

        return redirect($target)->with('success', 'Product deleted successfully!');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
        ]);

        $target = $this->safeReturnUrl($request->input('return')) ?? route('admin.products.index');

        $products = Product::whereIn('id', $request->input('ids'))->withCount('orderItems')->get();
        $deletable = $products->where('order_items_count', 0);
        $blocked = $products->where('order_items_count', '>', 0);

        Product::whereIn('id', $deletable->pluck('id'))->delete();

        if ($blocked->isEmpty()) {
            return redirect($target)->with('success', 'Selected products deleted successfully!');
        }

        $message = $deletable->count() > 0
            ? "{$deletable->count()} product(s) deleted. "
            : '';
        $message .= 'Could not delete '.$blocked->pluck('name')->implode(', ').' because they have existing orders.';

        return redirect($target)->with($deletable->count() > 0 ? 'success' : 'error', $message);
    }

    private function storeUploadedImage(UploadedFile $file): string
    {
        return ImageUploader::storeInPublic($file, 'uploads');
    }
}
