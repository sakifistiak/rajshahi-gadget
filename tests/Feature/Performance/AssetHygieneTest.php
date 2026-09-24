<?php

namespace Tests\Feature\Performance;

use App\Support\SectionTitleStyle;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

/**
 * SEO phase 4: fonts, dev-only attributes and the icon library. None of these touch the database.
 */
class AssetHygieneTest extends TestCase
{
    private const INTER = 'family=Inter:wght@400;500;600;700';

    public function test_the_default_fonts_url_is_exactly_the_old_inter_only_request(): void
    {
        $url = SectionTitleStyle::googleFontsUrl([null, ['base' => ['font' => 'inherit'], 'highlight' => ['font' => 'inherit']]]);

        $this->assertSame('https://fonts.googleapis.com/css2?'.self::INTER.'&display=swap', $url);
    }

    public function test_only_the_section_title_fonts_a_page_uses_are_requested(): void
    {
        $url = SectionTitleStyle::googleFontsUrl([
            ['base' => ['font' => 'poppins'], 'highlight' => ['font' => 'oswald']],
            ['highlight' => ['font' => 'poppins']],
            null,
        ]);

        $this->assertSame('https://fonts.googleapis.com/css2?'.self::INTER.'&family=Oswald:wght@700&family=Poppins:wght@700&display=swap', $url);
        $this->assertStringNotContainsString('Playfair', $url);
        $this->assertStringNotContainsString('Roboto', $url);
    }

    public function test_choosing_inter_for_a_title_does_not_repeat_the_inter_family(): void
    {
        $url = SectionTitleStyle::googleFontsUrl([['highlight' => ['font' => 'inter'], 'base' => ['font' => 'inter']]]);

        $this->assertSame(1, substr_count($url, 'family=Inter'));
    }

    public function test_unknown_or_hostile_font_keys_are_ignored(): void
    {
        $url = SectionTitleStyle::googleFontsUrl([['base' => ['font' => 'x&family=Evil'], 'highlight' => ['font' => ['array']]]]);

        $this->assertSame('https://fonts.googleapis.com/css2?'.self::INTER.'&display=swap', $url);
    }

    public function test_the_url_is_one_request_that_lists_families_in_a_stable_order(): void
    {
        $first = SectionTitleStyle::googleFontsUrl([['base' => ['font' => 'roboto-slab'], 'highlight' => ['font' => 'playfair']]]);
        $second = SectionTitleStyle::googleFontsUrl([['base' => ['font' => 'playfair'], 'highlight' => ['font' => 'roboto-slab']]]);

        $this->assertSame($first, $second, 'The same fonts must always give the same URL, however they are assigned.');
        $this->assertSame(1, substr_count($first, 'fonts.googleapis.com/css2'));
    }

    public function test_the_full_list_is_still_available_for_callers_that_want_every_font(): void
    {
        $url = SectionTitleStyle::googleFontsUrl();

        foreach (['Inter', 'Oswald', 'Playfair+Display', 'Poppins', 'Roboto+Slab'] as $family) {
            $this->assertStringContainsString('family='.$family, $url);
        }
    }

    public function test_dev_only_source_attributes_are_stripped_from_compiled_views(): void
    {
        $compiled = Blade::compileString(
            '<html lang="en" data-tsd-source="/src/routes/__root.tsx:133:5"><head data-tsd-source="/src/routes/__root.tsx:134:7">'
            .'<a href="{{ $url }}" data-tsd-source="/src/components/site/Navbar.tsx:14:5" class="x">Home</a></head></html>'
        );

        $this->assertStringNotContainsString('data-tsd-source', $compiled);
        $this->assertStringContainsString('<html lang="en"><head>', $compiled);
        $this->assertStringContainsString('class="x">Home</a>', $compiled);
        $this->assertStringContainsString('$url', $compiled, 'Blade expressions in the same tag must survive.');
    }

    public function test_the_attribute_the_stylesheet_selects_on_is_kept(): void
    {
        $compiled = Blade::compileString(
            '<div data-tsd-source="/src/components/site/ProductCard.tsx:12:3" class="card"><span data-tsd-source="/src/components/site/Navbar.tsx:1:1">x</span></div>'
        );

        $this->assertStringContainsString('data-tsd-source="/src/components/site/ProductCard.tsx:12:3"', $compiled);
        $this->assertStringNotContainsString('Navbar.tsx', $compiled);
    }

    public function test_attributes_inside_verbatim_blocks_are_stripped_too(): void
    {
        $compiled = Blade::compileString('@verbatim<div data-tsd-source="/src/x.tsx:1:1">{{ literal }}</div>@endverbatim');

        $this->assertStringNotContainsString('data-tsd-source', $compiled);
        $this->assertStringContainsString('{{ literal }}', $compiled);
    }

    public function test_other_text_that_merely_mentions_the_attribute_is_left_alone(): void
    {
        $compiled = Blade::compileString('<p>The data-tsd-source attribute is removed.</p><div data-other="x" data-tsd-source-map="keep">y</div>');

        $this->assertStringContainsString('The data-tsd-source attribute is removed.', $compiled);
        $this->assertStringContainsString('data-tsd-source-map="keep"', $compiled);
    }

    public function test_stripping_is_fast_on_the_huge_single_line_markup_the_templates_use(): void
    {
        $line = str_repeat('<div class="flex items-center gap-2" data-tsd-source="/src/components/site/Navbar.tsx:14:5"><span>text</span></div>', 3000);

        $started = microtime(true);
        $compiled = Blade::compileString($line);
        $elapsed = microtime(true) - $started;

        $this->assertStringNotContainsString('data-tsd-source', $compiled);
        $this->assertLessThan(2.0, $elapsed, 'Compiling a 300 KB line must not blow up.');
    }

    public function test_the_icon_library_is_vendored_pinned_and_the_partial_points_at_it(): void
    {
        $partial = file_get_contents(resource_path('views/partials/lucide-script.blade.php'));
        $this->assertSame(1, preg_match('#<script src="(/assets/vendor/lucide-([0-9.]+)\.min\.js)" defer></script>#', $partial, $match), 'One deferred, versioned script tag.');

        $file = public_path(ltrim($match[1], '/'));
        $this->assertFileExists($file);
        $source = file_get_contents($file);
        $this->assertStringContainsString('@license lucide v'.$match[2].' - ISC', $source, 'The file must be the version its name claims.');
        $this->assertStringContainsString('createIcons', $source);
    }

    public function test_no_view_loads_an_unpinned_icon_library_or_a_second_copy(): void
    {
        $offenders = [];
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator(resource_path('views')));
        foreach ($iterator as $file) {
            if (! $file->isFile() || ! str_ends_with($file->getFilename(), '.blade.php')) {
                continue;
            }
            if ($file->getFilename() === 'lucide-script.blade.php') {
                continue; // the partial itself, whose comment explains what it replaced
            }
            $source = file_get_contents($file->getPathname());
            if (preg_match('#lucide(-static)?@latest#', $source) || preg_match('#<script[^>]+lucide[^>]*>#i', $source)) {
                $offenders[] = $file->getFilename();
            }
        }

        $this->assertSame([], $offenders, 'Every page must get lucide through partials/lucide-script.');
    }
}
