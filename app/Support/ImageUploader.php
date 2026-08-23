<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/**
 * Re-encodes every admin-uploaded raster image (PNG/JPG/GIF/BMP) as WebP
 * before it's saved, so uploads are compressed automatically without an
 * admin having to convert files themselves. SVGs, ICOs and animated GIFs are
 * saved unchanged — WebP conversion isn't meaningful for a vector logo, a
 * favicon needs broad .ico support, and GD's WebP encoder only captures a
 * single static frame, which would silently kill a GIF's animation.
 */
class ImageUploader
{
    private const QUALITY = 82;

    /**
     * Save an uploaded image into a public/ subdirectory (mirrors the old
     * UploadedFile::move() call sites). Returns the public URL path.
     */
    public static function storeInPublic(UploadedFile $file, string $directory): string
    {
        $result = self::process($file);
        $filename = self::filename($file, $result['extension']);

        $targetDir = public_path(trim($directory, '/'));
        if (! File::exists($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        file_put_contents($targetDir.'/'.$filename, $result['contents']);

        return '/'.trim($directory, '/').'/'.$filename;
    }

    /**
     * Save an uploaded image via a Storage disk (mirrors the old
     * UploadedFile::store() call sites). Returns the disk-relative path.
     */
    public static function storeOnDisk(UploadedFile $file, string $directory, string $disk = 'public'): string
    {
        $result = self::process($file);
        $filename = self::filename($file, $result['extension']);
        $path = trim($directory, '/').'/'.$filename;

        Storage::disk($disk)->put($path, $result['contents']);

        return $path;
    }

    /**
     * @return array{contents: string, extension: string}
     */
    private static function process(UploadedFile $file): array
    {
        $original = file_get_contents($file->getRealPath());
        $extension = strtolower($file->getClientOriginalExtension());

        if (! function_exists('imagewebp') || in_array($extension, ['svg', 'ico'], true) || self::isAnimatedGif($original, $extension)) {
            return ['contents' => $original, 'extension' => $extension];
        }

        $image = @imagecreatefromstring($original);
        if (! $image) {
            return ['contents' => $original, 'extension' => $extension];
        }

        // Preserve transparency (PNG/GIF) instead of it turning solid black.
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        ob_start();
        imagewebp($image, null, self::QUALITY);
        $webp = ob_get_clean();
        imagedestroy($image);

        if (! $webp) {
            return ['contents' => $original, 'extension' => $extension];
        }

        return ['contents' => $webp, 'extension' => 'webp'];
    }

    private static function isAnimatedGif(string $contents, string $extension): bool
    {
        if ($extension !== 'gif') {
            return false;
        }

        // A GIF is animated once it has more than one Graphic Control Extension block.
        return substr_count($contents, "\x00\x21\xF9\x04") > 1;
    }

    private static function filename(UploadedFile $file, string $extension): string
    {
        $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $base = preg_replace('/[^A-Za-z0-9\-_]/', '', str_replace(' ', '_', $base));

        return time().'_'.$base.'.'.$extension;
    }
}
