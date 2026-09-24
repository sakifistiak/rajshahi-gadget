<?php

namespace Tests\Feature\Performance;

use App\Models\Product;
use App\Models\ProductImage;
use App\Support\OgImage;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * SEO phase 4: og:image / twitter:image derivatives. These build real images with GD in a temporary
 * public folder and never touch the database or the real public/ directory.
 */
class OgImageTest extends TestCase
{
    private static string $root;

    private static string $public;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        self::$root = sys_get_temp_dir().DIRECTORY_SEPARATOR.'og-test-'.bin2hex(random_bytes(4));
        self::$public = self::$root.DIRECTORY_SEPARATOR.'public';
        mkdir(self::$public.DIRECTORY_SEPARATOR.'media', 0777, true);
        mkdir(self::$public.DIRECTORY_SEPARATOR.'uploads', 0777, true);

        // A "1.5 MB style" upload: a gradient with noise, saved as PNG. Made once for the whole class.
        $image = imagecreatetruecolor(1600, 900);
        mt_srand(7);
        for ($y = 0; $y < 900; $y++) {
            for ($x = 0; $x < 1600; $x++) {
                $noise = mt_rand(-16, 16);
                imagesetpixel($image, $x, $y, imagecolorallocate(
                    $image,
                    max(0, min(255, (int) ($x / 1600 * 255) + $noise)),
                    max(0, min(255, (int) ($y / 900 * 255) + $noise)),
                    max(0, min(255, 140 + $noise)),
                ));
            }
        }
        imagepng($image, self::$public.'/media/noisy.png', 1);
    }

    public static function tearDownAfterClass(): void
    {
        self::removeDirectory(self::$root);

        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->app->usePublicPath(self::$public);
        config(['app.canonical_url' => 'https://www.khangadget.com']);
        self::removeDirectory(self::$public.'/uploads/og');
        @unlink(self::$root.'/outside.png');
    }

    private static function removeDirectory(string $directory): void
    {
        if (is_file($directory)) {
            @unlink($directory);

            return;
        }
        if (! is_dir($directory)) {
            return;
        }
        foreach (scandir($directory) as $entry) {
            if ($entry !== '.' && $entry !== '..') {
                self::removeDirectory($directory.DIRECTORY_SEPARATOR.$entry);
            }
        }
        @rmdir($directory);
    }

    private function solid(string $name, int $width, int $height, array $rgb, bool $transparent = false): string
    {
        $image = imagecreatetruecolor($width, $height);
        if ($transparent) {
            imagesavealpha($image, true);
            imagealphablending($image, false);
            imagefill($image, 0, 0, imagecolorallocatealpha($image, 0, 0, 0, 127));
            imagealphablending($image, true);
            imagefilledellipse($image, (int) ($width / 2), (int) ($height / 2), (int) ($width / 2), (int) ($height / 2), imagecolorallocate($image, ...$rgb));
        } else {
            imagefill($image, 0, 0, imagecolorallocate($image, ...$rgb));
        }
        $path = self::$public.'/media/'.$name;
        imagepng($image, $path);

        return '/media/'.$name;
    }

    /** The derivative file behind a URL returned by OgImage. */
    private function fileFor(string $url): string
    {
        return self::$public.str_replace('/', DIRECTORY_SEPARATOR, parse_url($url, PHP_URL_PATH));
    }

    /** @return array{0: int, 1: int, 2: int} r, g, b of one pixel of a derivative. */
    private function pixel(string $file, int $x, int $y): array
    {
        $image = imagecreatefromjpeg($file);
        $rgb = imagecolorat($image, $x, $y);

        return [($rgb >> 16) & 0xFF, ($rgb >> 8) & 0xFF, $rgb & 0xFF];
    }

    public function test_a_large_upload_becomes_a_1200x630_jpeg_under_200_kb(): void
    {
        $sourceSize = filesize(self::$public.'/media/noisy.png');
        $this->assertGreaterThan(1000000, $sourceSize, 'The fixture must be a heavy upload, like the real share image.');

        $url = OgImage::url('/media/noisy.png');

        $this->assertMatchesRegularExpression('#^https://www\.khangadget\.com/uploads/og/og-[0-9a-f]{40}\.jpg$#', $url);
        $file = $this->fileFor($url);
        $this->assertFileExists($file);
        $info = getimagesize($file);
        $this->assertSame([OgImage::WIDTH, OgImage::HEIGHT, IMAGETYPE_JPEG], [$info[0], $info[1], $info[2]]);
        $this->assertLessThan(200000, filesize($file));
        $this->assertLessThan($sourceSize / 4, filesize($file));
    }

    public function test_the_derivative_is_reused_not_regenerated(): void
    {
        $first = OgImage::url('/media/noisy.png');
        $file = $this->fileFor($first);
        $modified = filemtime($file);
        clearstatcache();

        $second = OgImage::url('/media/noisy.png');

        $this->assertSame($first, $second);
        $this->assertSame($modified, filemtime($file));
        $this->assertCount(1, glob(self::$public.'/uploads/og/*.jpg'));
    }

    public function test_replacing_the_source_gives_a_new_url(): void
    {
        $path = $this->solid('replace.png', 800, 800, [200, 30, 30]);
        $before = OgImage::url($path);

        $this->solid('replace.png', 900, 900, [30, 30, 200]);
        clearstatcache();
        $after = OgImage::url($path);

        $this->assertNotSame($before, $after, 'A changed image must never keep serving the old resized copy.');
        $this->assertFileExists($this->fileFor($after));
    }

    public function test_transparent_logos_are_flattened_onto_white(): void
    {
        $path = $this->solid('logo.png', 800, 400, [20, 20, 20], transparent: true);

        $file = $this->fileFor(OgImage::url($path));

        [$r, $g, $b] = $this->pixel($file, 4, 4);
        $this->assertGreaterThan(240, min($r, $g, $b), 'Transparent corners must be white, not black.');
        [$r, $g, $b] = $this->pixel($file, 600, 315);
        $this->assertLessThan(60, max($r, $g, $b), 'The logo itself must survive.');
    }

    public function test_a_source_shaped_like_the_card_fills_it(): void
    {
        $file = $this->fileFor(OgImage::url($this->solid('wide.png', 1200, 630, [220, 20, 20])));

        [$r, $g, $b] = $this->pixel($file, 2, 2);
        $this->assertGreaterThan(190, $r);
        $this->assertLessThan(70, $g + $b, 'A card-shaped source must fill the whole card, with no white bars.');
    }

    public function test_other_shapes_are_fitted_whole_and_centred_never_cropped(): void
    {
        $file = $this->fileFor(OgImage::url($this->solid('square.png', 900, 900, [220, 20, 20])));

        [$r, $g, $b] = $this->pixel($file, 60, 300);
        $this->assertGreaterThan(240, min($r, $g, $b), 'A square photo leaves white margins on the sides.');
        [$r, $g, $b] = $this->pixel($file, 600, 315);
        $this->assertGreaterThan(190, $r);
        $this->assertLessThan(70, $g + $b);
        [$r, $g, $b] = $this->pixel($file, 600, 4);
        $this->assertGreaterThan(190, $r, 'The full height of the photo must be visible.');
    }

    public function test_sources_that_cannot_be_resized_keep_their_original_url(): void
    {
        file_put_contents(self::$public.'/media/logo.svg', '<svg xmlns="http://www.w3.org/2000/svg"/>');
        file_put_contents(self::$public.'/media/broken.png', 'this is not an image');

        $this->assertSame('https://www.khangadget.com/media/logo.svg', OgImage::url('/media/logo.svg'));
        $this->assertSame('https://www.khangadget.com/media/broken.png', OgImage::url('/media/broken.png'));
        $this->assertSame('https://www.khangadget.com/media/missing.png', OgImage::url('/media/missing.png'));
        $this->assertSame('https://cdn.example.com/a.jpg', OgImage::url('https://cdn.example.com/a.jpg'));
        $this->assertSame([], glob(self::$public.'/uploads/og/*.jpg') ?: [], 'No derivative may be created for any of them.');
    }

    public function test_a_corrupt_image_is_not_decoded_again_on_every_request(): void
    {
        file_put_contents(self::$public.'/media/broken.png', 'this is not an image');

        OgImage::url('/media/broken.png');

        $this->assertCount(1, glob(self::$public.'/uploads/og/*.fail'), 'The failure is remembered.');
        $this->assertSame('https://www.khangadget.com/media/broken.png', OgImage::url('/media/broken.png'));
    }

    public function test_it_never_reads_files_outside_the_public_folder(): void
    {
        copy(self::$public.'/media/noisy.png', self::$root.'/outside.png');

        $this->assertSame('https://www.khangadget.com/../outside.png', OgImage::url('/../outside.png'));
        $this->assertStringNotContainsString('/uploads/og/', OgImage::url('/media/../../outside.png'));
        $this->assertStringNotContainsString('/uploads/og/', OgImage::url('/%2e%2e/outside.png'));
        $this->assertSame([], glob(self::$public.'/uploads/og/*.jpg') ?: []);
    }

    public function test_an_unwritable_output_folder_falls_back_to_the_original_without_an_error(): void
    {
        file_put_contents(self::$public.'/uploads/og', 'a file where the folder should be');

        $url = OgImage::url('/media/noisy.png');

        $this->assertSame('https://www.khangadget.com/media/noisy.png', $url);
    }

    public function test_a_derivative_is_never_resized_again(): void
    {
        $derived = OgImage::url('/media/noisy.png');
        $path = parse_url($derived, PHP_URL_PATH);

        $this->assertSame($derived, OgImage::url($path));
        $this->assertCount(1, glob(self::$public.'/uploads/og/*.jpg'));
    }

    public function test_the_site_image_defaults_to_the_logo_lockup_and_follows_the_admins_choice(): void
    {
        $this->solid('b3ca13-kg-lockup-v2.png', 1000, 400, [10, 10, 10]);
        $this->solid('share_image_1.png', 1200, 630, [10, 90, 10]);

        $default = OgImage::site(null);

        $this->assertStringContainsString('/uploads/og/og-', $default);
        $this->assertSame($default, OgImage::site('   '), 'A blank setting also means the default.');
        $chosen = OgImage::site('/media/share_image_1.png');
        $this->assertStringContainsString('/uploads/og/og-', $chosen);
        $this->assertNotSame($default, $chosen);
    }

    public function test_urls_use_the_canonical_origin_when_one_is_configured(): void
    {
        $request = Request::create('http://some-other-host.test/page');

        $this->assertMatchesRegularExpression('#^https://www\.khangadget\.com/uploads/og/og-[0-9a-f]{40}\.jpg$#', OgImage::url('/media/noisy.png', $request));
        $this->assertSame('https://www.khangadget.com/media/missing.png', OgImage::url('/media/missing.png', $request));
    }

    public function test_urls_follow_the_request_origin_when_no_canonical_url_is_configured(): void
    {
        config(['app.canonical_url' => null]);
        $request = Request::create('http://shop.test/page');

        $this->assertMatchesRegularExpression('#^http://shop\.test/uploads/og/og-[0-9a-f]{40}\.jpg$#', OgImage::url('/media/noisy.png', $request));
    }

    public function test_a_products_share_image_is_its_primary_photo_resized(): void
    {
        $this->solid('front.png', 900, 900, [200, 20, 20]);
        $this->solid('side.png', 900, 900, [20, 20, 200]);
        $product = Product::make(['slug' => 'p', 'name' => 'P']);
        $product->setRelation('images', collect([
            new ProductImage(['image_path' => '/media/side.png', 'is_primary' => false, 'sort_order' => 1]),
            new ProductImage(['image_path' => '/media/front.png', 'is_primary' => true, 'sort_order' => 2]),
        ]));

        $this->assertSame(OgImage::url('/media/front.png'), OgImage::forProduct($product));
    }

    public function test_a_product_without_photos_uses_the_site_image(): void
    {
        $this->solid('b3ca13-kg-lockup-v2.png', 1000, 400, [10, 10, 10]);
        $product = Product::make(['slug' => 'p', 'name' => 'P']);
        $product->setRelation('images', collect());

        $this->assertSame(OgImage::site(null), OgImage::forProduct($product));
    }
}
