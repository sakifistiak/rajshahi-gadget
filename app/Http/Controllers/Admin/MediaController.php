<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function index(): View
    {
        $files = $this->getMediaFiles();
        return view('admin.media.index', compact('files'));
    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|image|mimes:jpeg,png,jpg,webp,gif,svg,avif|max:20480',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson() || $request->wantsJson() || $request->ajax() || $request->hasHeader('X-CSRF-TOKEN')) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('file') ?: 'Validation failed.',
                ], 422);
            }
            return redirect()->back()->withErrors($validator);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $url = ImageUploader::storeInPublic($file, 'uploads');
            $filename = basename($url);

            return response()->json([
                'success' => true,
                'url' => $url,
                'filename' => $filename,
                'message' => 'Image uploaded successfully!',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No file provided.'], 400);
    }

    public function list(): JsonResponse
    {
        $files = $this->getMediaFiles();
        return response()->json([
            'success' => true,
            'files' => $files,
        ]);
    }

    public function destroy(string $filename): RedirectResponse
    {
        // Sanitize filename to prevent directory traversal
        $filename = basename($filename);
        $path = public_path('uploads/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
            return redirect()->back()->with('success', 'File deleted successfully.');
        }

        return redirect()->back()->with('error', 'File not found or cannot be deleted.');
    }

    private function getMediaFiles(): array
    {
        $files = [];

        // 1. Scan public/uploads
        $uploadsPath = public_path('uploads');
        if (File::exists($uploadsPath)) {
            foreach (File::files($uploadsPath) as $file) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
                    $files[] = [
                        'name' => $file->getFilename(),
                        'url' => '/uploads/' . $file->getFilename(),
                        'size' => round($file->getSize() / 1024, 1) . ' KB',
                        'modified' => date('Y-m-d H:i', $file->getMTime()),
                        'type' => 'Uploads',
                    ];
                }
            }
        }

        // 2. Scan public/media
        $mediaPath = public_path('media');
        if (File::exists($mediaPath)) {
            foreach (File::files($mediaPath) as $file) {
                $ext = strtolower($file->getExtension());
                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'])) {
                    $files[] = [
                        'name' => $file->getFilename(),
                        'url' => '/media/' . $file->getFilename(),
                        'size' => round($file->getSize() / 1024, 1) . ' KB',
                        'modified' => date('Y-m-d H:i', $file->getMTime()),
                        'type' => 'Media',
                    ];
                }
            }
        }

        // Sort by modified date descending
        usort($files, function ($a, $b) {
            return strtotime($b['modified']) <=> strtotime($a['modified']);
        });

        return $files;
    }
}