<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerSpotlight;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class CustomerSpotlightController extends Controller
{
    public function index(Request $request): View
    {
        $query = CustomerSpotlight::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('product', 'like', "%{$search}%"));
        }

        $spotlights = $query->latest('date')->paginate(15)->withQueryString();

        return view('admin.customer-spotlights.index', compact('spotlights'));
    }

    public function create(): View
    {
        return view('admin.customer-spotlights.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $imagePath = $request->image_path;
        if ($request->hasFile('image_file')) {
            $imagePath = $this->storeUploadedImage($request->file('image_file'));
        }

        CustomerSpotlight::create([...$data, 'image' => $imagePath, 'date' => now()]);

        return redirect()->route('admin.customer-spotlights.index')->with('success', 'Customer spotlight added successfully!');
    }

    public function edit(CustomerSpotlight $customerSpotlight): View
    {
        return view('admin.customer-spotlights.edit', ['spotlight' => $customerSpotlight]);
    }

    public function update(Request $request, CustomerSpotlight $customerSpotlight): RedirectResponse
    {
        $data = $this->validated($request);

        $imagePath = $request->image_path;
        if ($request->hasFile('image_file')) {
            $imagePath = $this->storeUploadedImage($request->file('image_file'));
        }

        $customerSpotlight->update([...$data, 'image' => $imagePath]);

        return redirect()->route('admin.customer-spotlights.index')->with('success', 'Customer spotlight updated successfully!');
    }

    public function destroy(CustomerSpotlight $customerSpotlight): RedirectResponse
    {
        $customerSpotlight->delete();

        return redirect()->route('admin.customer-spotlights.index')->with('success', 'Customer spotlight deleted successfully!');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'product' => 'nullable|string|max:255',
            'quote' => 'nullable|string|max:1000',
            'image_path' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ]);

        unset($data['image_path'], $data['image_file']);

        return $data;
    }

    private function storeUploadedImage(UploadedFile $file): string
    {
        $filename = time().'_'.str_replace(' ', '_', preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $file->getClientOriginalName()));
        $targetDir = public_path('uploads');
        if (! File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }
        $file->move($targetDir, $filename);

        return '/uploads/'.$filename;
    }
}
