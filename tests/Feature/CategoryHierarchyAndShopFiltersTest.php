<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\FilterAttribute;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryHierarchyAndShopFiltersTest extends TestCase
{
    use RefreshDatabase;

    private Category $laptops;

    private Category $gaming;

    private Category $chargers;

    private Condition $condition;

    protected function setUp(): void
    {
        parent::setUp();

        $this->laptops = Category::forceCreate(['slug' => 'laptops', 'name' => 'Laptops']);
        $this->gaming = Category::forceCreate(['slug' => 'gaming-series', 'name' => 'Gaming Series', 'parent_id' => $this->laptops->id]);
        $this->chargers = Category::forceCreate(['slug' => 'chargers', 'name' => 'Chargers']);
        $this->condition = Condition::create(['slug' => 'intact', 'label' => 'Intact', 'short' => 'Intact', 'tagline' => 'Sealed']);
    }

    private function admin(): User
    {
        return User::factory()->create()->forceFill(['is_admin' => true]);
    }

    private function product(string $slug, Category $category, string $brand = 'hp', array $filters = [], bool $inStock = true): Product
    {
        $product = Product::forceCreate([
            'slug' => $slug,
            'name' => 'Product '.$slug,
            'brand_id' => Brand::firstOrCreate(['slug' => $brand], ['name' => strtoupper($brand)])->id,
            'category_id' => $category->id,
            'condition_id' => $this->condition->id,
            'price' => 100000,
            'description' => 'A product.',
            'in_stock' => $inStock,
        ]);
        foreach ($filters as $attribute => $value) {
            $product->filterValues()->create(['filter_attribute_id' => $attribute, 'text_value' => $value]);
        }

        return $product;
    }

    private function processorFilter(Category $category): FilterAttribute
    {
        return FilterAttribute::create([
            'category_id' => $category->id, 'key' => 'processor', 'label' => 'Processor',
            'type' => 'select', 'options' => 'i3, i5, i7, 1st Gen', 'sort_order' => 0,
        ]);
    }

    private function productForm(array $category): array
    {
        return [
            'name' => 'Test Laptop', 'brand_id' => Brand::firstOrCreate(['slug' => 'hp'], ['name' => 'HP'])->id,
            'condition_id' => $this->condition->id, 'price' => 50000, 'description' => 'Laptop', 'stock_quantity' => 1,
            ...$category,
        ];
    }

    // Admin product form: main category required, sub category optional

    public function test_a_product_can_be_saved_with_only_a_main_category(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.products.store'), $this->productForm(['main_category_id' => $this->laptops->id, 'sub_category_id' => '']))
            ->assertSessionHasNoErrors();

        $this->assertSame($this->laptops->id, Product::where('name', 'Test Laptop')->value('category_id'));
    }

    public function test_a_picked_sub_category_is_saved_as_the_product_category(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.products.store'), $this->productForm(['main_category_id' => $this->laptops->id, 'sub_category_id' => $this->gaming->id]))
            ->assertSessionHasNoErrors();

        $this->assertSame($this->gaming->id, Product::where('name', 'Test Laptop')->value('category_id'));
    }

    public function test_a_sub_category_of_another_main_category_is_rejected(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.products.store'), $this->productForm(['main_category_id' => $this->chargers->id, 'sub_category_id' => $this->gaming->id]))
            ->assertSessionHasErrors('sub_category_id');

        $this->assertSame(0, Product::count());
    }

    public function test_a_sub_category_product_saves_filters_defined_on_its_main_category(): void
    {
        $processor = $this->processorFilter($this->laptops);

        $this->actingAs($this->admin())->post(route('admin.products.store'), $this->productForm([
            'main_category_id' => $this->laptops->id,
            'sub_category_id' => $this->gaming->id,
            'filter_values' => [$processor->id => 'i7'],
        ]))->assertSessionHasNoErrors();

        $this->assertSame('i7', Product::where('name', 'Test Laptop')->first()->filterValues()->value('text_value'));
    }

    public function test_the_edit_form_splits_the_saved_category_into_main_and_sub(): void
    {
        $product = $this->product('rog', $this->gaming);

        $html = $this->actingAs($this->admin())->get(route('admin.products.edit', $product))->assertOk()->getContent();

        $this->assertMatchesRegularExpression('/<option value="'.$this->laptops->id.'"\s+selected>Laptops/', $html);
        $this->assertMatchesRegularExpression('/<option value="'.$this->gaming->id.'" data-parent="'.$this->laptops->id.'"\s+selected>Gaming Series/', $html);
    }

    // Shop sidebar: options without products are hidden

    public function test_spec_options_without_products_are_hidden(): void
    {
        $processor = $this->processorFilter($this->laptops);
        $this->product('a', $this->laptops, 'hp', [$processor->id => 'i5']);
        $this->product('b', $this->gaming, 'hp', [$processor->id => 'i7']);
        $this->product('sold-out', $this->laptops, 'hp', [$processor->id => 'i3'], inStock: false);

        $this->get('/shop')->assertOk()
            ->assertSee('value="i5"', false)
            ->assertSee('value="i7"', false)
            ->assertDontSee('value="i3"', false)      // only on an out-of-stock product
            ->assertDontSee('value="1st Gen"', false); // on no product at all
    }

    public function test_a_spec_filter_with_no_matching_products_disappears_entirely(): void
    {
        $this->processorFilter($this->laptops);
        $this->product('a', $this->laptops);

        $this->get('/shop')->assertOk()->assertDontSee('spec_select[processor]', false);
    }

    public function test_options_follow_the_other_selections_but_not_their_own(): void
    {
        $processor = $this->processorFilter($this->laptops);
        $this->product('hp-i5', $this->laptops, 'hp', [$processor->id => 'i5']);
        $this->product('dell-i7', $this->laptops, 'dell', [$processor->id => 'i7']);

        // Picking HP hides i7 (no HP has it) ...
        $this->get('/shop?brand[]=hp')->assertSee('value="i5"', false)->assertDontSee('value="i7"', false);

        // ... but ticking i5 keeps i7 visible so both can be combined, and Dell drops out of Brand.
        $this->get('/shop?spec_select[processor][]=i5')
            ->assertSee('value="i7"', false)
            ->assertSee('value="hp"', false)
            ->assertDontSee('value="dell"', false);
    }

    public function test_a_ticked_option_stays_visible_even_when_nothing_matches(): void
    {
        $processor = $this->processorFilter($this->laptops);
        $this->product('hp-i5', $this->laptops, 'hp', [$processor->id => 'i5']);
        Brand::create(['slug' => 'dell', 'name' => 'Dell']);

        $this->get('/shop?brand[]=dell&spec_select[processor][]=i5')
            ->assertSee('value="dell"', false)
            ->assertSee('value="i5"', false);
    }

    public function test_empty_categories_brands_and_conditions_are_hidden(): void
    {
        Brand::create(['slug' => 'acer', 'name' => 'Acer']);
        Condition::create(['slug' => 'pre-owned', 'label' => 'Pre-Owned', 'short' => 'Used', 'tagline' => 'Used']);
        $this->product('rog', $this->gaming, 'asus');

        $this->get('/shop')->assertOk()
            ->assertSee('value="laptops"', false)        // its sub category has a product
            ->assertSee('value="gaming-series"', false)
            ->assertDontSee('value="chargers"', false)
            ->assertSee('value="asus"', false)
            ->assertDontSee('value="acer"', false)
            ->assertSee('value="intact"', false)
            ->assertDontSee('value="pre-owned"', false);
    }

    public function test_a_sub_category_page_shows_filters_defined_on_its_main_category(): void
    {
        $processor = $this->processorFilter($this->laptops);
        $this->product('rog', $this->gaming, 'asus', [$processor->id => 'i7']);

        $this->get('/shop/gaming-series')->assertOk()->assertSee('value="i7"', false);
    }

    // Migration: laptop-type main categories move under Laptops

    public function test_the_migration_nests_laptop_types_and_leaves_accessories(): void
    {
        Category::query()->delete();
        $names = ['MacBook Series', 'Mobile Workstation', 'Gaming Series', 'Premium Ultrabook', 'Chargers & Adapters', 'Laptop Bags', 'Gaming Mouse', 'Laptop Batteries'];
        foreach ($names as $i => $name) {
            Category::forceCreate(['slug' => str($name)->slug()->toString(), 'name' => $name, 'sort_order' => $i]);
        }

        (require database_path('migrations/2026_10_06_100000_nest_laptop_types_under_laptops.php'))->up();

        $laptops = Category::where('slug', 'laptops')->firstOrFail();
        $this->assertNull($laptops->parent_id);
        $this->assertEqualsCanonicalizing(
            ['MacBook Series', 'Mobile Workstation', 'Gaming Series', 'Premium Ultrabook'],
            Category::where('parent_id', $laptops->id)->pluck('name')->all(),
        );
        $this->assertSame(4, Category::whereNull('parent_id')->where('id', '!=', $laptops->id)->count());
    }

    public function test_the_migration_does_nothing_without_laptop_categories(): void
    {
        Category::query()->delete();
        Category::forceCreate(['slug' => 'chargers', 'name' => 'Chargers']);

        (require database_path('migrations/2026_10_06_100000_nest_laptop_types_under_laptops.php'))->up();

        $this->assertSame(['chargers'], Category::pluck('slug')->all());
    }
}
