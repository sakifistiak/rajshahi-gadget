<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreLocation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class StoreLocationController extends Controller
{
    public function index(): View
    {
        $locations = StoreLocation::orderBy('sort_order', 'asc')->get();
        return view('admin.store_locations.index', compact('locations'));
    }

    public function create(): View
    {
        return view('admin.store_locations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:100',
            'sort_order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        StoreLocation::create([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.store-locations.index')->with('success', 'Store Location created successfully!');
    }

    public function edit(StoreLocation $storeLocation): View
    {
        return view('admin.store_locations.edit', compact('storeLocation'));
    }

    public function update(Request $request, StoreLocation $storeLocation): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'phone' => 'nullable|string|max:100',
            'sort_order' => 'required|integer',
            'is_active' => 'boolean',
        ]);

        $storeLocation->update([
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'sort_order' => $request->input('sort_order', 0),
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.store-locations.index')->with('success', 'Store Location updated successfully!');
    }

    public function destroy(StoreLocation $storeLocation): RedirectResponse
    {
        $storeLocation->delete();
        return redirect()->route('admin.store-locations.index')->with('success', 'Store Location deleted successfully!');
    }
}
