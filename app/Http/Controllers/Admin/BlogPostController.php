<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
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

        $slug = Str::slug($request->slug ?: $request->title);
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

        $slug = $request->slug ? Str::slug($request->slug) : $blogPost->slug;

        $imagePath = $request->featured_image_path;
        if ($request->hasFile('featured_image_file')) {
            $imagePath = $this->storeUploadedImage($request->file('featured_image_file'));
        }

        $blogPost->update([
            ...$data,
            'slug' => $slug,
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
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . ($blogPost->id ?? 'NULL'),
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'category_tag' => 'required|string|max:100',
            'read_minutes' => 'nullable|integer|min:1|max:60',
            'author_name' => 'required|string|max:100',
            'author_role' => 'nullable|string|max:100',
            'published_at' => 'nullable|date',
            'featured_image_path' => 'nullable|string|max:500',
            'featured_image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
            'is_featured' => 'nullable|boolean',
        ]);

        unset($data['featured_image_path'], $data['featured_image_file']);
        $data['read_minutes'] = $data['read_minutes'] ?? 5;
        $data['is_featured'] = $request->boolean('is_featured');

        return $data;
    }

    private function storeUploadedImage(UploadedFile $file): string
    {
        $filename = time() . '_' . str_replace(' ', '_', preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $file->getClientOriginalName()));
        $targetDir = public_path('uploads');
        if (!File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }
        $file->move($targetDir, $filename);

        return '/uploads/' . $filename;
    }
}
