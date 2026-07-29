<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HeroSlider;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_path' => 'required|string|max:500',
            'cta_link' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:100',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        HeroSlider::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $request->image_path,
            'cta_link' => $request->cta_link ?: '/shop',
            'cta_text' => $request->cta_text ?: 'Shop Now',
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->route('admin.sliders.index')->with('success', 'Hero Slider created successfully!');
    }

    public function edit(HeroSlider $slider): View
    {
        return view('admin.sliders.edit', compact('slider'));
    }

    public function update(Request $request, HeroSlider $slider): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_path' => 'required|string|max:500',
            'cta_link' => 'nullable|string|max:255',
            'cta_text' => 'nullable|string|max:100',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $slider->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $request->image_path,
            'cta_link' => $request->cta_link ?: '/shop',
            'cta_text' => $request->cta_text ?: 'Shop Now',
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

        return redirect()->route('admin.sliders.index')->with('success', 'Slider status updated successfully!');
    }

    public function destroy(HeroSlider $slider): RedirectResponse
    {
        $slider->delete();
        return redirect()->route('admin.sliders.index')->with('success', 'Hero Slider deleted successfully!');
    }
}
