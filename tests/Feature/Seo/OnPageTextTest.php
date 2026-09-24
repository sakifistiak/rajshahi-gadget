<?php

namespace Tests\Feature\Seo;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\CustomPage;
use App\Models\PhilanthropicWork;
use App\Models\Product;
use App\Models\ProductHighlight;
use App\Support\Seo;
use PHPUnit\Framework\Attributes\DataProvider;
use ReflectionClass;
use Tests\TestCase;

/**
 * SEO phase 3: titles, meta descriptions and text cleaning on App\Support\Seo. These use in-memory
 * models, so none of them touch the database.
 */
class OnPageTextTest extends TestCase
{
    private function product(array $attributes = [], string $conditionLabel = 'BRAND NEW INTACT BOX', array $highlights = [], ?string $brand = 'HP'): Product
    {
        $product = Product::make(array_merge([
            'slug' => 'hp-probook-430-g8',
            'name' => 'HP ProBook 430 G8 Core i7-1165G7 512GB SSD 13.3" Display Business Series Laptop',
            'price' => 45000,
            'in_stock' => true,
            'is_new_arrival' => false,
            'price_is_tba' => false,
        ], $attributes));

        $product->setRelation('brand', $brand ? new Brand(['name' => $brand]) : null);
        $product->setRelation('condition', $conditionLabel ? new Condition(['slug' => 'x', 'label' => $conditionLabel]) : null);
        $product->setRelation('highlights', collect(array_map(fn (string $text) => new ProductHighlight(['text' => $text]), $highlights)));

        return $product;
    }

    /** @return array<string, array{0: string, 1: string}> */
    public static function dirtyText(): array
    {
        return [
            'bold mathematical letters' => ['𝐇𝐏 𝐏𝐫𝐨𝐁𝐨𝐨𝐤 𝟒𝟑𝟎 𝐆𝟖', 'HP ProBook 430 G8'],
            'script and monospace letters' => ['𝓚𝓱𝓪𝓷 𝙶𝚊𝚍𝚐𝚎𝚝', 'Khan Gadget'],
            'small capitals' => ['Dɪsᴄᴏᴜɴᴛ Pʀɪcᴇ', 'Discount Price'],
            'double-encoded nbsp' => ['Core i7&amp;nbsp;512GB SSD', 'Core i7 512GB SSD'],
            'single-encoded nbsp' => ['Core i7&nbsp;512GB SSD', 'Core i7 512GB SSD'],
            'encoded ampersand and quote' => ['Dell &amp; HP 13.3&quot; laptop', 'Dell & HP 13.3" laptop'],
            'left-to-right mark' => ["HD\u{200E}MODEL", 'HDMODEL'],
            'zero width space and BOM' => ["a\u{200B}b\u{FEFF}c", 'abc'],
            'no-break space' => ["a\u{00A0}b", 'a b'],
            'line breaks and tabs' => ["Retailer ;\nServing\t&amp; Building", 'Retailer ; Serving & Building'],
            'markup' => ['<p>Fast <b>SSD</b></p>', 'Fast SSD'],
            'Bangla is untouched' => ['বাংলাদেশ কম্পিউটার সমিতি', 'বাংলাদেশ কম্পিউটার সমিতি'],
            'Bangla joiners are kept' => ["র\u{200D}্যাব", "র\u{200D}্যাব"],
        ];
    }

    #[DataProvider('dirtyText')]
    public function test_text_is_flattened_to_plain_single_line_text(string $input, string $expected): void
    {
        $this->assertSame($expected, Seo::text($input));
    }

    public function test_excerpt_cuts_at_a_word_boundary_and_keeps_paragraph_words_apart(): void
    {
        $html = '<p>Khan Gadget is a genuine wholesaler.</p><p>Serving trust since 2012 in the gadget industry across Bangladesh with many outlets.</p>';

        $excerpt = Seo::excerpt($html, 90);

        $this->assertLessThanOrEqual(90, mb_strlen($excerpt));
        $this->assertStringEndsWith('…', $excerpt);
        $this->assertStringStartsWith('Khan Gadget is a genuine wholesaler. Serving trust', $excerpt, 'Paragraphs must not run together.');
        $this->assertSame('Short text.', Seo::excerpt('<p>Short text.</p>', 90), 'Short text is returned whole, without an ellipsis.');
        $this->assertSame('', Seo::excerpt(null));
    }

    public function test_excerpt_works_on_bangla_text(): void
    {
        $bangla = 'খান গ্যাজেট: বাংলাদেশ কম্পিউটার সমিতি নিবন্ধিত, একযুগেরও বেশি পুরনো, ৪০০+ ব্র্যান্ড নিউ, ওপেন বক্স ও প্রি-ওন্ড মডেলের গেমিং ল্যাপটপ ও ম্যাকবুক সরবরাহকারী।';

        $excerpt = Seo::excerpt('<p>'.$bangla.'</p>', 60);

        $this->assertLessThanOrEqual(60, mb_strlen($excerpt));
        $this->assertStringStartsWith('খান গ্যাজেট: বাংলাদেশ', $excerpt);
    }

    public function test_fit_never_leaves_a_dangling_connector_or_punctuation(): void
    {
        $this->assertSame('Lenovo Legion 5 15AHP10 Ryzen 7 260 NVIDIA', Seo::fit('Lenovo Legion 5 15AHP10 Ryzen 7 260 NVIDIA RTX 5060 With 8GB', 44));
        $this->assertSame('Acer Predator Helios', Seo::fit('Acer Predator Helios - 300 Core i7', 22));
        $this->assertSame('Short', Seo::fit('Short', 60));
    }

    public function test_every_listing_page_description_is_unique_and_140_to_160_characters(): void
    {
        $descriptions = (new ReflectionClass(Seo::class))->getConstant('PAGE_DESCRIPTIONS');

        $this->assertCount(6, $descriptions);
        foreach ($descriptions as $key => $description) {
            $this->assertGreaterThanOrEqual(140, mb_strlen($description), $key);
            $this->assertLessThanOrEqual(160, mb_strlen($description), $key);
            $this->assertStringNotContainsString("\n", $description, $key);
            $this->assertSame($description, Seo::text($description), "$key must already be plain text.");
        }
        $this->assertSame($descriptions, array_unique($descriptions), 'No two pages may share a description.');
    }

    public function test_unlisted_pages_fall_back_to_the_cleaned_site_description(): void
    {
        $this->assertSame(
            'Genuine Wholesaler & Retailer ; Serving Trust',
            Seo::pageDescription('cart', "Genuine Wholesaler &amp; Retailer ;\nServing Trust"),
        );
        $this->assertNotSame(Seo::pageDescription('shop'), Seo::pageDescription('blog'));
    }

    public function test_home_title_carries_the_keyword_headline(): void
    {
        $this->assertSame('Khan Gadget — Genuine Imported Laptops & Gadgets in Bangladesh', Seo::homeTitle('Khan Gadget'));
        $this->assertSame('Acme — Genuine Imported Laptops & Gadgets in Bangladesh', Seo::homeTitle('Acme'));
    }

    public function test_long_product_titles_are_cut_to_about_sixty_characters(): void
    {
        $title = Seo::productTitle($this->product(), 'Khan Gadget');

        $this->assertLessThanOrEqual(60, mb_strlen($title));
        $this->assertStringEndsWith(' · Intact Box | Khan Gadget', $title);
        $this->assertStringStartsWith('HP ProBook 430 G8 Core i7', $title);
        $this->assertStringNotContainsString('Business Series', $title, 'The full spec string stays in the H1, not the title.');
    }

    public function test_short_product_names_are_kept_whole(): void
    {
        $title = Seo::productTitle($this->product(['name' => 'HP OMEN 16'], 'PRE-OWNED'), 'Khan Gadget');

        $this->assertSame('HP OMEN 16 · Pre-Owned | Khan Gadget', $title);
    }

    public function test_condition_lead_in_is_dropped_from_titles_and_case_is_normalised(): void
    {
        $intact = Seo::productTitle($this->product(['name' => 'HP OMEN 16'], 'Brand New Intact Box'), 'Khan Gadget');
        $withoutBox = Seo::productTitle($this->product(['name' => 'HP OMEN 16'], 'BRAND NEW WITHOUT BOX'), 'Khan Gadget');

        $this->assertSame('HP OMEN 16 · Intact Box | Khan Gadget', $intact);
        $this->assertSame('HP OMEN 16 · Without Box | Khan Gadget', $withoutBox);
    }

    public function test_product_title_starts_with_the_brand_when_the_name_omits_it(): void
    {
        $title = Seo::productTitle($this->product(['name' => 'ProBook 430 G8'], 'PRE-OWNED'), 'Khan Gadget');
        $already = Seo::productTitle($this->product(['name' => 'HP ProBook 430 G8'], 'PRE-OWNED'), 'Khan Gadget');

        $this->assertSame('HP ProBook 430 G8 · Pre-Owned | Khan Gadget', $title);
        $this->assertSame('HP ProBook 430 G8 · Pre-Owned | Khan Gadget', $already, 'The brand must not be doubled.');
    }

    public function test_product_title_without_a_condition_or_with_a_long_site_name_still_fits(): void
    {
        $noCondition = Seo::productTitle($this->product([], ''), 'Khan Gadget');
        $longSite = Seo::productTitle($this->product(), 'Khan Gadget Bangladesh Official Store');

        $this->assertLessThanOrEqual(60, mb_strlen($noCondition));
        $this->assertStringEndsWith(' | Khan Gadget', $noCondition);
        $this->assertStringNotContainsString(' · ', $noCondition);
        $this->assertStringNotContainsString(' · ', $longSite, 'A long site name drops the condition first.');
        $this->assertStringEndsWith(' | Khan Gadget Bangladesh Official Store', $longSite);
    }

    public function test_product_title_flattens_decorative_names(): void
    {
        $title = Seo::productTitle($this->product(['name' => '𝐇𝐏 𝐏𝐫𝐨𝐁𝐨𝐨𝐤 𝟒𝟑𝟎 𝐆𝟖'], 'PRE-OWNED'), 'Khan Gadget');

        $this->assertSame('HP ProBook 430 G8 · Pre-Owned | Khan Gadget', $title);
    }

    public function test_product_description_is_plain_short_and_built_from_structured_data(): void
    {
        $description = Seo::productDescription(
            $this->product([], 'BRAND NEW INTACT BOX', ['11th Gen Core i7', 'Up to 4.70GHz', '512GB SSD', 'Backlit keyboard']),
            'Khan Gadget',
        );

        $this->assertLessThanOrEqual(155, mb_strlen($description));
        $this->assertStringContainsString('HP ProBook 430 G8', $description);
        $this->assertStringContainsString('Brand New Intact Box.', $description);
        $this->assertStringContainsString('Price: ৳ 45,000.', $description);
        $this->assertStringContainsString('11th Gen Core i7.', $description);
        $this->assertStringNotContainsString('512GB SSD.', $description, 'A feature the name already says is skipped.');
        $this->assertSame($description, Seo::text($description), 'The description must already be plain text.');
    }

    public function test_product_description_ignores_the_pasted_marketing_text(): void
    {
        $product = $this->product(['description' => '𝐇𝐏 𝐏𝐫𝐨𝐁𝐨𝐨𝐤 𝐆𝟖&amp;nbsp;𝐌𝐎𝐃𝐄𝐋: Dɪsᴄᴏᴜɴᴛ Pʀɪcᴇ']);

        $description = Seo::productDescription($product, 'Khan Gadget');

        $this->assertStringNotContainsString('MODEL', $description);
        $this->assertStringNotContainsString('nbsp', $description);
        $this->assertDoesNotMatchRegularExpression('/[\x{1D400}-\x{1D7FF}\x{1D00}-\x{1D2B}]/u', $description);
    }

    /** @return array<string, array{0: array<string, mixed>, 1: bool}> */
    public static function priceVisibility(): array
    {
        return [
            'in stock shows the price' => [['in_stock' => true], true],
            'out of stock hides it, like the page' => [['in_stock' => false], false],
            'pre-order with a price shows it' => [['is_new_arrival' => true, 'in_stock' => false], true],
            'pre-order with a TBA price hides it' => [['is_new_arrival' => true, 'price_is_tba' => true], false],
            'no price hides it' => [['price' => 0], false],
        ];
    }

    #[DataProvider('priceVisibility')]
    public function test_product_description_states_a_price_only_when_the_page_shows_one(array $attributes, bool $shown): void
    {
        $description = Seo::productDescription($this->product($attributes), 'Khan Gadget');

        $this->assertSame($shown, str_contains($description, '৳'));
    }

    public function test_product_description_stays_within_the_limit_however_long_the_inputs(): void
    {
        $long = str_repeat('Ryzen 9 8940HX NVIDIA RTX 5070TI ', 12);
        $description = Seo::productDescription(
            $this->product(['name' => $long], 'BRAND NEW INTACT BOX', [$long, 'Short feature']),
            'Khan Gadget',
        );

        $this->assertLessThanOrEqual(155, mb_strlen($description));
        $this->assertStringEndsWith('.', $description);
    }

    public function test_thin_products_get_a_short_site_tail_and_full_ones_do_not_overflow(): void
    {
        $thin = Seo::productDescription($this->product(['name' => 'HP OMEN 16'], 'PRE-OWNED'), 'Khan Gadget');

        $this->assertSame('HP OMEN 16. Pre-Owned. Price: ৳ 45,000. Buy at Khan Gadget, Bangladesh.', $thin);
    }

    public function test_category_and_shop_text(): void
    {
        $category = Category::make(['slug' => 'gaming-laptops', 'name' => 'Gaming Laptops']);

        $this->assertSame('Gaming Laptops | Khan Gadget', Seo::categoryTitle($category, 'Khan Gadget'));
        $this->assertSame('Gaming Laptops | Khan Gadget', Seo::shopTitle($category, 'Khan Gadget'));
        $this->assertSame('Shop | Khan Gadget', Seo::shopTitle(null, 'Khan Gadget'));

        $description = Seo::categoryDescription($category, 'Khan Gadget');
        $this->assertStringContainsString('Gaming Laptops', $description);
        $this->assertLessThanOrEqual(160, mb_strlen($description));
        $this->assertSame($description, Seo::shopDescription($category, 'Khan Gadget'));
        $this->assertSame(Seo::pageDescription('shop'), Seo::shopDescription(null, 'Khan Gadget'));
        $this->assertNotSame(Seo::shopDescription(null, 'Khan Gadget'), $description);
    }

    public function test_the_sr_only_class_behind_the_hidden_cms_heading_is_a_real_visually_hidden_rule(): void
    {
        // The CMS template links the static storefront stylesheet directly. If .sr-only ever went
        // missing from it, the "hidden" heading would silently become a visible, unstyled one.
        $template = file_get_contents(resource_path('views/pages/custom.blade.php'));
        $this->assertSame(1, preg_match('#/assets/(styles-[A-Za-z0-9_-]+\.css)#', $template, $file), 'The CMS template must link the storefront stylesheet.');

        $css = file_get_contents(public_path('assets/'.$file[1]));

        $this->assertMatchesRegularExpression('/\.sr-only\{[^}]*clip-path:inset\(50%\)[^}]*width:1px[^}]*height:1px[^}]*position:absolute[^}]*overflow:hidden[^}]*\}/', $css);
        $this->assertDoesNotMatchRegularExpression('/\.sr-only\{[^}]*(?:display:none|visibility:hidden)/', $css, 'sr-only must not be a display:none style hide.');
    }

    public function test_cms_page_description_prefers_the_editor_then_the_content(): void
    {
        $own = CustomPage::make(['title' => 'About', 'meta_description' => "  Our\nown &amp; edited text  ", 'content' => '<p>Ignored body</p>']);
        $fromBody = CustomPage::make(['title' => 'About', 'meta_description' => null, 'content' => '<p>Registered with the Bangladesh Computer Samity.</p><p>Trusted since 2012.</p>']);
        $empty = CustomPage::make(['title' => 'Empty', 'meta_description' => null, 'content' => '<p><br></p>']);

        $this->assertSame('Our own & edited text', Seo::customPageDescription($own));
        $this->assertSame('Registered with the Bangladesh Computer Samity. Trusted since 2012.', Seo::customPageDescription($fromBody));
        $this->assertSame('', Seo::customPageDescription($empty));
    }

    public function test_philanthropic_work_description_uses_its_text_or_falls_back_to_its_title(): void
    {
        $withText = PhilanthropicWork::make(['title' => 'Food drive', 'content' => '<p>We fed 500 families in Rajshahi.</p>']);
        $withoutText = PhilanthropicWork::make(['title' => 'Food drive', 'content' => null]);

        $this->assertSame('We fed 500 families in Rajshahi.', Seo::philanthropicWorkDescription($withText, 'Khan Gadget'));
        $this->assertSame('Food drive: philanthropic work supported by Khan Gadget in Bangladesh.', Seo::philanthropicWorkDescription($withoutText, 'Khan Gadget'));
    }
}
