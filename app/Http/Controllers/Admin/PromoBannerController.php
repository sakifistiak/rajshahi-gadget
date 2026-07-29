<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PromoBanner;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_path' => 'required|string|max:500',
            'bg_color' => 'nullable|string|max:100',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        PromoBanner::create([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $request->image_path,
            'bg_color' => $request->bg_color ?: 'from-sky-100 to-sky-50',
            'link' => $request->link ?: '/shop',
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? (bool)$request->is_active : true,
        ]);

        return redirect()->route('admin.promos.index')->with('success', 'Promo Banner created successfully!');
    }

    public function edit(PromoBanner $promo): View
    {
        return view('admin.promos.edit', compact('promo'));
    }

    public function update(Request $request, PromoBanner $promo): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'image_path' => 'required|string|max:500',
            'bg_color' => 'nullable|string|max:100',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ]);

        $promo->update([
            'title' => $request->title,
            'subtitle' => $request->subtitle,
            'image_path' => $request->image_path,
            'bg_color' => $request->bg_color ?: 'from-sky-100 to-sky-50',
            'link' => $request->link ?: '/shop',
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.promos.index')->with('success', 'Promo Banner updated successfully!');
    }

    public function toggle(PromoBanner $promo): RedirectResponse
    {
        $promo->update([
            'is_active' => !$promo->is_active
        ]);

        return redirect()->route('admin.promos.index')->with('success', 'Promo Banner status updated successfully!');
    }

    public function destroy(PromoBanner $promo): RedirectResponse
    {
        $promo->delete();
        return redirect()->route('admin.promos.index')->with('success', 'Promo Banner deleted successfully!');
    }
}
