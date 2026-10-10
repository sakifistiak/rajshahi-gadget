<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConditionBadgeTest extends TestCase
{
    use RefreshDatabase;

    private Condition $intact;

    private Condition $preOwned;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::forceCreate(['slug' => 'laptops', 'name' => 'Laptops']);
        $this->intact = Condition::create([
            'slug' => 'intact', 'label' => 'BRAND NEW INTACT BOX', 'short' => 'INT', 'tagline' => 'Sealed',
            'badge_active' => true, 'badge_text' => 'BRAND NEW', 'badge_color' => '#16a34a',
        ]);
        $this->preOwned = Condition::create([
            'slug' => 'pre-owned', 'label' => 'PRE-OWNED', 'short' => 'PRE', 'tagline' => 'Used',
            'badge_active' => false, 'badge_text' => 'PRE-OWNED', 'badge_color' => '#d97706',
        ]);
    }

    private function admin(): User
    {
        return User::factory()->create()->forceFill(['is_admin' => true]);
    }

    private function product(string $slug, Condition $condition, array $attributes = []): Product
    {
        return Product::forceCreate([
            'slug' => $slug,
            'name' => 'Product '.$slug,
            'brand_id' => Brand::firstOrCreate(['slug' => 'hp'], ['name' => 'HP'])->id,
            'category_id' => $this->category->id,
            'condition_id' => $condition->id,
            'price' => 100000,
            'description' => 'A product.',
            'in_stock' => true,
            ...$attributes,
        ]);
    }

    public function test_the_card_shows_the_badge_of_the_product_condition(): void
    {
        $this->product('sealed-laptop', $this->intact);
        $this->product('used-laptop', $this->preOwned);

        $html = $this->get('/shop')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'product-card-condition-badge'), 'Only the active condition shows a badge.');
        $this->assertStringContainsString('BRAND NEW', $html);
    }

    public function test_a_product_can_hide_its_condition_badge(): void
    {
        $this->product('sealed-laptop', $this->intact, ['hide_condition_badge' => true]);

        $this->get('/shop')->assertOk()->assertDontSee('product-card-condition-badge');
    }

    public function test_admin_changes_to_a_condition_badge_show_on_the_storefront(): void
    {
        $this->product('sealed-laptop', $this->intact);
        $this->get('/shop')->assertSee('BRAND NEW');

        $this->actingAs($this->admin())->post(route('admin.condition-badges.update'), [
            'badges' => [
                $this->intact->id => ['badge_active' => '1', 'badge_text' => 'SEALED NEW', 'badge_color' => '#2563EB'],
                $this->preOwned->id => ['badge_text' => 'PRE-OWNED', 'badge_color' => '#d97706'],
            ],
        ])->assertRedirect(route('admin.condition-badges.index'));

        $this->assertSame('#2563eb', $this->intact->fresh()->badge_color);
        $this->assertFalse($this->preOwned->fresh()->badge_active);
        $this->get('/shop')->assertSee('SEALED NEW')->assertSee('background-color: #2563eb', false);
    }

    public function test_admin_rejects_an_invalid_badge_colour(): void
    {
        $this->actingAs($this->admin())->post(route('admin.condition-badges.update'), [
            'badges' => [$this->intact->id => ['badge_text' => 'X', 'badge_color' => 'red;display:none']],
        ])->assertSessionHasErrors();

        $this->assertSame('#16a34a', $this->intact->fresh()->badge_color);
    }

    public function test_the_admin_screens_render_the_badge_fields(): void
    {
        $product = $this->product('sealed-laptop', $this->intact, ['badge' => 'Hot Deal']);
        $admin = $this->admin();

        $this->actingAs($admin)->get(route('admin.condition-badges.index'))->assertOk()->assertSee('BRAND NEW INTACT BOX');
        $this->actingAs($admin)->get(route('admin.products.create'))->assertOk()->assertSee('name="hide_condition_badge"', false);
        $this->actingAs($admin)->get(route('admin.products.edit', $product))->assertOk()->assertSee('value="Hot Deal"', false);
    }

    public function test_the_product_form_saves_the_custom_badge_and_the_opt_out(): void
    {
        $product = $this->product('sealed-laptop', $this->intact);

        $this->actingAs($this->admin())->put(route('admin.products.update', $product), [
            'name' => $product->name, 'brand_id' => $product->brand_id, 'main_category_id' => $this->category->id,
            'condition_id' => $this->intact->id, 'price' => 100000, 'description' => 'A product.', 'stock_quantity' => 1,
            'badge' => '  Hot Deal ', 'hide_condition_badge' => '1',
        ])->assertSessionHasNoErrors();

        $product->refresh();
        $this->assertSame('Hot Deal', $product->badge);
        $this->assertTrue($product->hide_condition_badge);
    }
}
