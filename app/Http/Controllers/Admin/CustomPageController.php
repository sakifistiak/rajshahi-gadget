<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use App\Support\ImageUploader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CustomPageController extends Controller
{
    /**
     * Display a listing of the custom pages.
     */
    public function index(Request $request): View
    {
        $query = CustomPage::query();

        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%");
        }

        $pages = $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new custom page.
     */
    public function create(): View
    {
        return view('admin.pages.create');
    }

    /**
     * Store a newly created custom page in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:custom_pages,slug',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'show_title' => 'nullable|boolean',
            'location_name' => 'nullable|array',
            'location_name.*' => 'nullable|string|max:255',
            'location_address' => 'nullable|array',
            'location_address.*' => 'nullable|string',
            'location_phone' => 'nullable|array',
            'location_phone.*' => 'nullable|string|max:50',
            'location_map_link' => 'nullable|array',
            'location_map_link.*' => 'nullable|string|max:500',
            'location_details' => 'nullable|array',
            'location_details.*' => 'nullable|string',
            'location_image_path' => 'nullable|array',
            'location_image_path.*' => 'nullable|string|max:500',
            'location_image_file' => 'nullable|array',
            'location_image_file.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ], [], $this->locationAttributeNames($request));

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        $page = CustomPage::create([
            'title' => $request->title,
            'slug' => $slug,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'content' => $request->content,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
            'show_title' => $request->has('show_title') ? true : false,
        ]);

        $this->syncLocations($request, $page);

        return redirect()->route('admin.pages.index')->with('success', 'Custom page created successfully!');
    }

    /**
     * Show the form for editing the specified custom page.
     */
    public function edit(CustomPage $page): View
    {
        $page->load('locations');

        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified custom page in storage.
     */
    public function update(Request $request, CustomPage $page): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:custom_pages,slug,'.$page->id,
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
            'show_title' => 'nullable|boolean',
            'location_name' => 'nullable|array',
            'location_name.*' => 'nullable|string|max:255',
            'location_address' => 'nullable|array',
            'location_address.*' => 'nullable|string',
            'location_phone' => 'nullable|array',
            'location_phone.*' => 'nullable|string|max:50',
            'location_map_link' => 'nullable|array',
            'location_map_link.*' => 'nullable|string|max:500',
            'location_details' => 'nullable|array',
            'location_details.*' => 'nullable|string',
            'location_image_path' => 'nullable|array',
            'location_image_path.*' => 'nullable|string|max:500',
            'location_image_file' => 'nullable|array',
            'location_image_file.*' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ], [], $this->locationAttributeNames($request));

        $slug = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        $page->update([
            'title' => $request->title,
            'slug' => $slug,
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'content' => $request->content,
            'sort_order' => $request->sort_order ?? 0,
            'is_active' => $request->has('is_active') ? true : false,
            'show_title' => $request->has('show_title') ? true : false,
        ]);

        $this->syncLocations($request, $page);

        return redirect()->route('admin.pages.index')->with('success', 'Custom page updated successfully!');
    }

    /**
     * Remove the specified custom page from storage.
     */
    public function destroy(CustomPage $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Custom page deleted successfully!');
    }

    /**
     * Replace this page's store-location cards with the submitted rows.
     * Rows are parallel arrays keyed by the same index (name/address/phone/
     * map link/details/image), matching the highlights/gallery pattern used
     * on the product form. A row with no name is treated as blank and
     * skipped, so a stray empty row left in the repeater doesn't get saved.
     */
    private function syncLocations(Request $request, CustomPage $page): void
    {
        $page->locations()->delete();

        $names = $request->input('location_name', []);
        $imageFiles = $request->file('location_image_file', []);

        foreach ($names as $index => $name) {
            if (empty(trim((string) $name))) {
                continue;
            }

            $imagePath = $request->input("location_image_path.{$index}") ?: null;
            if (isset($imageFiles[$index]) && $imageFiles[$index] instanceof UploadedFile && $imageFiles[$index]->isValid()) {
                $imagePath = $this->storeUploadedImage($imageFiles[$index]);
            }

            $page->locations()->create([
                'image_path' => $imagePath,
                'name' => $name,
                'address' => $request->input("location_address.{$index}"),
                'phone' => $request->input("location_phone.{$index}"),
                'map_link' => $request->input("location_map_link.{$index}"),
                'details' => $request->input("location_details.{$index}"),
                'sort_order' => $index,
            ]);
        }
    }

    private function storeUploadedImage(UploadedFile $file): string
    {
        return ImageUploader::storeInPublic($file, 'uploads');
    }

    /**
     * Friendly names for the location repeater's array fields, so a
     * validation failure (e.g. an oversized photo) reads as "Location photo
     * #2 must not be greater than 10240 kilobytes" instead of the raw
     * "location_image_file.1" key — and, critically, so the failure is
     * legible at all once the create/edit forms show $errors->all().
     */
    private function locationAttributeNames(Request $request): array
    {
        $names = [];
        $count = count($request->input('location_name', []));

        for ($i = 0; $i < $count; $i++) {
            $n = $i + 1;
            $names["location_name.{$i}"] = "location #{$n} name";
            $names["location_address.{$i}"] = "location #{$n} address";
            $names["location_phone.{$i}"] = "location #{$n} phone";
            $names["location_map_link.{$i}"] = "location #{$n} map link";
            $names["location_details.{$i}"] = "location #{$n} details";
            $names["location_image_file.{$i}"] = "location #{$n} photo";
        }

        return $names;
    }
}
