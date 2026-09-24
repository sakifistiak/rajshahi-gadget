<?php

namespace App\Support;

use App\Models\Product;
use Illuminate\Http\Request;
use Throwable;

/**
 * Share images for og:image and twitter:image (SEO phase 4).
 *
 * The site share image and product photos are uploaded at whatever size the admin had, often a
 * megabyte or more, and social platforms download them on every share. This makes a 1200x630 JPEG
 * derivative under 200 KB the first time one is asked for, keeps it in public/uploads/og (git-ignored,
 * next to the other runtime uploads) and returns its URL. The file name contains a hash of the source
 * path, size and modification time, so replacing an image produces a new URL and the old file is simply
 * never referenced again.
 *
 * Nothing here can break a page: when the source is remote, missing, an SVG, corrupt, outside public/, or
 * the folder is not writable, the original image URL is returned exactly as before.
 */
class OgImage
{
    public const WIDTH = 1200;

    public const HEIGHT = 630;

    private const DIRECTORY = 'uploads/og';

    private const DEFAULT_SITE_IMAGE = '/media/b3ca13-kg-lockup-v2.png';

    /** Comfortably under the 200 KB target, so a slightly different encoder still lands below it. */
    private const MAX_BYTES = 190000;

    /** JPEG qualities tried in order until the file is small enough. */
    private const QUALITIES = [85, 78, 70, 62, 55, 48, 40];

    /** Refuse to decode anything bigger (about 24 megapixels), which could exhaust PHP's memory. */
    private const MAX_SOURCE_PIXELS = 24000000;

    /** Small photos are enlarged at most this much; beyond that they would only look blurry. */
    private const MAX_UPSCALE = 3.0;

    /** A source within this fraction of the card's 1.91:1 shape fills the card; anything else is fitted whole. */
    private const COVER_TOLERANCE = 0.06;

    /** Absolute URL of the site-wide share image: the admin's upload, or the default logo lockup. */
    public static function site(?string $configured, ?Request $request = null): string
    {
        return static::url(($configured !== null && trim($configured) !== '') ? $configured : self::DEFAULT_SITE_IMAGE, $request);
    }

    /**
     * Absolute URL of a product's share image: its primary photo, resized. A product without any photo
     * falls back to the site share image. Uses the already-loaded images relation, so no extra queries.
     */
    public static function forProduct(Product $product, ?Request $request = null): string
    {
        $path = $product->images->sortByDesc('is_primary')->first()?->image_path;

        return $path ? static::url($path, $request) : static::site(null, $request);
    }

    /** Absolute URL of the resized version of a site-relative image path, or of the original when it cannot be resized. */
    public static function url(string $path, ?Request $request = null): string
    {
        $original = (string) Seo::absoluteUrl($path, $request);

        try {
            $file = static::localFile($path);
            $derived = $file !== null ? static::derivative($file) : null;
        } catch (Throwable $e) {
            report($e);

            return $original;
        }

        return $derived !== null ? (string) Seo::absoluteUrl($derived, $request) : $original;
    }

    /** The image's file inside public/, or null for a remote URL, a missing file, or anything outside public/. */
    private static function localFile(string $path): ?string
    {
        if (preg_match('#^https?://#i', $path)) {
            return null;
        }

        $relative = ltrim(rawurldecode((string) (parse_url($path, PHP_URL_PATH) ?: $path)), '/');
        $root = realpath(public_path());
        $file = realpath(public_path($relative));

        if ($root === false || $file === false || ! is_file($file) || ! str_starts_with($file, $root.DIRECTORY_SEPARATOR)) {
            return null;
        }

        // Never resize a derivative again.
        return str_starts_with($file, $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, self::DIRECTORY).DIRECTORY_SEPARATOR) ? null : $file;
    }

    /** Public path ("/uploads/og/og-<hash>.jpg") of the derivative, creating it when needed. */
    private static function derivative(string $file): ?string
    {
        $key = sha1($file.'|'.filemtime($file).'|'.filesize($file).'|'.self::WIDTH.'x'.self::HEIGHT.'|v1');
        $publicPath = '/'.self::DIRECTORY.'/og-'.$key.'.jpg';
        $directory = public_path(self::DIRECTORY);
        $target = $directory.'/og-'.$key.'.jpg';
        $failed = $directory.'/og-'.$key.'.fail';

        if (is_file($target)) {
            return $publicPath;
        }
        // A source that could not be resized once will not be decoded again on every request.
        if (is_file($failed)) {
            return null;
        }
        if (! is_dir($directory) && ! @mkdir($directory, 0755, true) && ! is_dir($directory)) {
            return null;
        }
        if (! is_writable($directory) || ! function_exists('imagecreatetruecolor')) {
            return null;
        }

        $jpeg = static::render($file);
        if ($jpeg === null) {
            @touch($failed);

            return null;
        }

        // Write to a temporary name first, so a crawler can never fetch a half-written image.
        $temporary = $directory.'/.tmp-'.bin2hex(random_bytes(6));
        if (file_put_contents($temporary, $jpeg) === false) {
            return null;
        }
        if (! @rename($temporary, $target)) {
            @unlink($temporary);

            return is_file($target) ? $publicPath : null;
        }

        return $publicPath;
    }

    /** JPEG bytes of the 1200x630 card for the given image file, or null when it cannot be decoded. */
    private static function render(string $file): ?string
    {
        $info = getimagesize($file);
        if ($info === false || $info[0] < 1 || $info[1] < 1 || $info[0] * $info[1] > self::MAX_SOURCE_PIXELS) {
            return null;
        }

        [$width, $height, $type] = $info;
        $source = match ($type) {
            IMAGETYPE_PNG => imagecreatefrompng($file),
            IMAGETYPE_JPEG => imagecreatefromjpeg($file),
            IMAGETYPE_WEBP => imagecreatefromwebp($file),
            IMAGETYPE_GIF => imagecreatefromgif($file),
            default => false,
        };
        if ($source === false) {
            return null;
        }

        // A white card, so transparent logos and product cut-outs do not turn black in JPEG.
        $card = imagecreatetruecolor(self::WIDTH, self::HEIGHT);
        imagefill($card, 0, 0, imagecolorallocate($card, 255, 255, 255));

        $cardRatio = self::WIDTH / self::HEIGHT;
        if (abs(($width / $height) / $cardRatio - 1) <= self::COVER_TOLERANCE) {
            // Nearly the card's shape: fill it, trimming a sliver from the edges.
            $scale = max(self::WIDTH / $width, self::HEIGHT / $height);
            $sourceWidth = self::WIDTH / $scale;
            $sourceHeight = self::HEIGHT / $scale;
            imagecopyresampled($card, $source, 0, 0, (int) (($width - $sourceWidth) / 2), (int) (($height - $sourceHeight) / 2), self::WIDTH, self::HEIGHT, (int) round($sourceWidth), (int) round($sourceHeight));
        } else {
            // Any other shape (a square product photo, a tall poster): fit it whole and centred, never cropped.
            $scale = min(self::WIDTH / $width, self::HEIGHT / $height, self::MAX_UPSCALE);
            $drawWidth = max(1, (int) round($width * $scale));
            $drawHeight = max(1, (int) round($height * $scale));
            imagecopyresampled($card, $source, (int) ((self::WIDTH - $drawWidth) / 2), (int) ((self::HEIGHT - $drawHeight) / 2), 0, 0, $drawWidth, $drawHeight, $width, $height);
        }

        imageinterlace($card, true);

        $jpeg = null;
        foreach (self::QUALITIES as $quality) {
            ob_start();
            imagejpeg($card, null, $quality);
            $jpeg = (string) ob_get_clean();

            if (strlen($jpeg) <= self::MAX_BYTES) {
                break;
            }
        }

        return $jpeg !== '' ? $jpeg : null;
    }
}
