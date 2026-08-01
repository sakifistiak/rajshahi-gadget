<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,webp,gif,svg|max:10240',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . str_replace(' ', '_', preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $file->getClientOriginalName()));
            
            $targetDir = public_path('uploads');
            if (!File::exists($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }

            $file->move($targetDir, $filename);
            $url = '/uploads/' . $filename;

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'url' => $url,
                    'filename' => $filename,
                    'message' => 'Image uploaded successfully!',
                ]);
            }

            return redirect()->back()->with('success', 'Image uploaded successfully: ' . $url);
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['success' => false, 'message' => 'No file provided.'], 400);
        }

        return redirect()->back()->with('error', 'No file provided.');
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
