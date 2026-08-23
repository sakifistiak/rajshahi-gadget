<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use App\Support\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class HeroSliderController extends Controller
{
    public function index(): View
    {
        $sliders = HeroSlider::orderBy('sort_order', 'asc')->get();
        return view('admin.sliders.index', compact('sliders'));
    }

    public function create(): View
    {
        return view('admin.sliders.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'image_path' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
            'cta_link' => 'nullable|string|max:255',
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

        HeroSlider::create([
            'title' => null,
            'subtitle' => null,
            'image_path' => $imagePath,
            'cta_link' => $request->cta_link ?: '/shop',
            'cta_text' => null,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'Hero Slider added successfully!');
    }

    public function edit(HeroSlider $slider): View
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, HeroSlider $slider): RedirectResponse
    {
        $request->validate([
            'image_path' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
            'cta_link' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $imagePath = $request->image_path ?: $slider->image_path;

        if ($request->hasFile('image_file')) {
            $imagePath = ImageUploader::storeInPublic($request->file('image_file'), 'uploads');
        }

        $slider->update([
            'image_path' => $imagePath,
            'cta_link' => $request->cta_link ?: '/shop',
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'Hero Slider updated successfully!');
    }

    public function toggle(HeroSlider $slider): RedirectResponse
    {
        $slider->update([
            'is_active' => !$slider->is_active
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'Slider status updated!');
    }

    public function destroy(HeroSlider $slider): RedirectResponse
    {
        $slider->delete();
        return redirect()->route('admin.sliders.index')->with('success', 'Hero Slider deleted!');
    }
}
