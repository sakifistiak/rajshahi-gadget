<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoBanner;
use App\Support\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class PromoBannerController extends Controller
{
    public function index(): View
    {
        $promos = PromoBanner::orderBy('sort_order', 'asc')->get();
        return view('admin.promos.index', compact('promos'));
    }

    public function create(): View
    {
        return view('admin.promos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image_path' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $imagePath = $request->image_path;

        if ($request->hasFile('image_file')) {
            $imagePath = ImageUploader::storeInPublic($request->file('image_file'), 'uploads');
        }

        if (empty($imagePath)) {
            return redirect()->back()->withInput()->withErrors(['image_path' => 'Please select or upload an image file.']);
        }

        PromoBanner::create([
            'title' => '',
            'subtitle' => null,
            'image_path' => $imagePath,
            'bg_color' => 'from-sky-100 to-sky-50',
            'link' => $request->link ?: '/shop',
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->route('admin.promos.index')->with('success', 'Side Promo Banner added successfully!');
    }

    public function edit(PromoBanner $promo): View
    {
        return view('admin.promos.edit', compact('promo'));
    }

    public function update(Request $request, PromoBanner $promo): RedirectResponse
    {
        $request->validate([
            'image_path' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $imagePath = $request->image_path ?: $promo->image_path;

        if ($request->hasFile('image_file')) {
            $imagePath = ImageUploader::storeInPublic($request->file('image_file'), 'uploads');
        }

        $promo->update([
            'title' => '',
            'image_path' => $imagePath,
            'link' => $request->link ?: '/shop',
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.promos.index')->with('success', 'Side Promo Banner updated successfully!');
    }

    public function toggle(PromoBanner $promo): RedirectResponse
    {
        $promo->update([
            'is_active' => !$promo->is_active
        ]);

        return redirect()->route('admin.promos.index')->with('success', 'Promo Banner status updated!');
    }

    public function destroy(PromoBanner $promo): RedirectResponse
    {
        $promo->delete();
        return redirect()->route('admin.promos.index')->with('success', 'Promo Banner deleted!');
    }
}
