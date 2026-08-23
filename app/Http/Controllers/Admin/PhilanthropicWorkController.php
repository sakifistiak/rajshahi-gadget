<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PhilanthropicWork;
use App\Support\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class PhilanthropicWorkController extends Controller
{
    public function index(Request $request): View
    {
        $query = PhilanthropicWork::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('place', 'like', "%{$search}%"));
        }

        $works = $query->latest('date')->paginate(15)->withQueryString();

        return view('admin.philanthropic-works.index', compact('works'));
    }

    public function create(): View
    {
        return view('admin.philanthropic-works.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $slug = Str::slug($request->slug ?: $request->title);
        $count = PhilanthropicWork::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $imagePath = $request->image_path;
        if ($request->hasFile('image_file')) {
            $imagePath = $this->storeUploadedImage($request->file('image_file'));
        }

        PhilanthropicWork::create([...$data, 'slug' => $slug, 'image' => $imagePath]);

        return redirect()->route('admin.philanthropic-works.index')->with('success', 'Philanthropic work added successfully!');
    }

    public function edit(PhilanthropicWork $philanthropicWork): View
    {
        return view('admin.philanthropic-works.edit', ['work' => $philanthropicWork]);
    }

    public function update(Request $request, PhilanthropicWork $philanthropicWork): RedirectResponse
    {
        $data = $this->validated($request, $philanthropicWork);

        $slug = $request->slug ? Str::slug($request->slug) : $philanthropicWork->slug;

        $imagePath = $request->image_path;
        if ($request->hasFile('image_file')) {
            $imagePath = $this->storeUploadedImage($request->file('image_file'));
        }

        $philanthropicWork->update([...$data, 'slug' => $slug, 'image' => $imagePath]);

        return redirect()->route('admin.philanthropic-works.index')->with('success', 'Philanthropic work updated successfully!');
    }

    public function destroy(PhilanthropicWork $philanthropicWork): RedirectResponse
    {
        $philanthropicWork->delete();

        return redirect()->route('admin.philanthropic-works.index')->with('success', 'Philanthropic work deleted successfully!');
    }

    private function validated(Request $request, ?PhilanthropicWork $work = null): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:philanthropic_works,slug,' . ($work->id ?? 'NULL'),
            'place' => 'required|string|max:255',
            'summary' => 'required|string|max:500',
            'content' => 'nullable|string',
            'date' => 'required|date',
            'image_path' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ]);

        unset($data['slug'], $data['image_path'], $data['image_file']);

        return $data;
    }

    private function storeUploadedImage(UploadedFile $file): string
    {
        return ImageUploader::storeInPublic($file, 'uploads');
    }
}
