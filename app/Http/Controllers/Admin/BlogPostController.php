<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Support\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class BlogPostController extends Controller
{
    public function index(Request $request): View
    {
        $query = BlogPost::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('title', 'like', "%{$search}%")->orWhere('slug', 'like', "%{$search}%"));
        }

        $posts = $query->latest('published_at')->paginate(15)->withQueryString();

        return view('admin.blog-posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.blog-posts.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $slug = Str::slug($data['title']);
        $count = BlogPost::where('slug', 'like', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $imagePath = $request->featured_image_path;
        if ($request->hasFile('featured_image_file')) {
            $imagePath = $this->storeUploadedImage($request->file('featured_image_file'));
        }

        BlogPost::create([
            ...$data,
            'slug' => $slug,
            'featured_image' => $imagePath,
            'published_at' => now(),
        ]);

        return redirect()->route('admin.blog-posts.index')->with('success', 'Blog post created successfully!');
    }

    public function edit(BlogPost $blogPost): View
    {
        return view('admin.blog-posts.edit', ['post' => $blogPost]);
    }

    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $data = $this->validated($request, $blogPost);

        $imagePath = $request->featured_image_path;
        if ($request->hasFile('featured_image_file')) {
            $imagePath = $this->storeUploadedImage($request->file('featured_image_file'));
        }

        $blogPost->update([
            ...$data,
            'featured_image' => $imagePath,
        ]);

        return redirect()->route('admin.blog-posts.index')->with('success', 'Blog post updated successfully!');
    }

    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')->with('success', 'Blog post deleted successfully!');
    }

    private function validated(Request $request, ?BlogPost $blogPost = null): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image_path' => 'nullable|string|max:500',
            'featured_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ]);

        unset($data['featured_image_path'], $data['featured_image_file']);

        return $data;
    }

    private function storeUploadedImage(UploadedFile $file): string
    {
        return ImageUploader::storeInPublic($file, 'uploads');
    }
}
