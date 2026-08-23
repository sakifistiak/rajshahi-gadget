<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerFeedback;
use App\Support\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\View\View;
use Illuminate\Support\Facades\File;

class CustomerFeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $query = CustomerFeedback::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")->orWhere('message', 'like', "%{$search}%"));
        }

        $feedbacks = $query->latest('date')->paginate(15)->withQueryString();

        return view('admin.customer-feedbacks.index', compact('feedbacks'));
    }

    public function create(): View
    {
        return view('admin.customer-feedbacks.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);

        $imagePath = $request->image_path;
        if ($request->hasFile('image_file')) {
            $imagePath = $this->storeUploadedImage($request->file('image_file'));
        }

        CustomerFeedback::create([...$data, 'image' => $imagePath]);

        return redirect()->route('admin.customer-feedbacks.index')->with('success', 'Customer feedback added successfully!');
    }

    public function edit(CustomerFeedback $customerFeedback): View
    {
        return view('admin.customer-feedbacks.edit', ['feedback' => $customerFeedback]);
    }

    public function update(Request $request, CustomerFeedback $customerFeedback): RedirectResponse
    {
        $data = $this->validated($request);

        $imagePath = $request->image_path;
        if ($request->hasFile('image_file')) {
            $imagePath = $this->storeUploadedImage($request->file('image_file'));
        }

        $customerFeedback->update([...$data, 'image' => $imagePath]);

        return redirect()->route('admin.customer-feedbacks.index')->with('success', 'Customer feedback updated successfully!');
    }

    public function destroy(CustomerFeedback $customerFeedback): RedirectResponse
    {
        $customerFeedback->delete();

        return redirect()->route('admin.customer-feedbacks.index')->with('success', 'Customer feedback deleted successfully!');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'rating' => 'required|numeric|min:1|max:5',
            'message' => 'required|string|max:1000',
            'date' => 'required|date',
            'image_path' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ]);

        unset($data['image_path'], $data['image_file']);

        return $data;
    }

    private function storeUploadedImage(UploadedFile $file): string
    {
        return ImageUploader::storeInPublic($file, 'uploads');
    }
}
