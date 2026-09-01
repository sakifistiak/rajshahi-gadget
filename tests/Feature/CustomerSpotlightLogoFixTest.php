<?php

namespace Tests\Feature;

use Tests\TestCase;

class CustomerSpotlightLogoFixTest extends TestCase
{
    /**
     * Regression guard for the "Khan Gadget" logo bug: the site-wide logo swap
     * in theme.js must only ever target structural selectors (the home-link
     * logo in header/footer, or .site-logo-img), never alt-text/content
     * matching — otherwise any card whose title/alt happens to contain the
     * site name gets silently replaced with the brand logo.
     */
    public function test_theme_js_logo_swap_does_not_match_on_alt_text(): void
    {
        $js = file_get_contents(base_path('public/assets/theme.js'));

        $this->assertStringContainsString(
            'header a[href="/"] img, footer a[href="/"] img, .site-logo-img',
            $js,
            'swapLogos() must only target the structural home-link/.site-logo-img selectors.'
        );
        $this->assertStringNotContainsString('[alt*=', $js);
    }

    /**
     * Regression guard for the related "Laptop" bug: several models/views used
     * a hardcoded laptop stock photo as the "no image uploaded" fallback,
     * which silently displayed an unrelated product photo instead of leaving
     * a neutral placeholder. All of those must now point at the neutral
     * placeholder asset instead.
     */
    public function test_missing_image_fallbacks_use_the_neutral_placeholder(): void
    {
        $this->assertFileExists(base_path('public/assets/no-image-placeholder.svg'));

        $files = [
            'app/Models/Product.php',
            'app/Http/Controllers/Admin/ProductController.php',
            'resources/views/pages/customer-spotlight.blade.php',
            'resources/views/pages/customer-feedback.blade.php',
            'resources/views/pages/philanthropic-work.blade.php',
            'resources/views/pages/thank-you.blade.php',
            'resources/views/partials/blog-cards.blade.php',
            'resources/views/admin/blog-posts/index.blade.php',
            'resources/views/admin/customer-spotlights/index.blade.php',
            'resources/views/admin/philanthropic-works/index.blade.php',
            'resources/views/admin/customer-feedbacks/index.blade.php',
        ];

        foreach ($files as $file) {
            $contents = file_get_contents(base_path($file));
            $this->assertStringNotContainsString(
                'laptop-ultrabook',
                $contents,
                "{$file} should no longer fall back to the hardcoded laptop stock photo."
            );
        }
    }
}
