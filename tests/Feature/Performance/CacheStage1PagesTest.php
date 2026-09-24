<?php

namespace Tests\Feature\Performance;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Condition;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Cache stage 1 on real responses: the font and category endpoints are session-free and cacheable for
 * five minutes, and every page that shows order or customer data is never stored. Needs a real schema,
 * so it uses RefreshDatabase like the rest of the DB-backed suite.
 */
class CacheStage1PagesTest extends TestCase
{
    use RefreshDatabase;

    private const ORDER_NUMBER = 'KG-260924-ABC123';

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.canonical_url' => null]);
        SiteSetting::flushCache();
        Cache::flush();
    }

    private function cacheControl($response): string
    {
        return (string) $response->headers->get('Cache-Control');
    }

    private function assertNoStore($response, string $what): void
    {
        $this->assertStringContainsString('no-store', $this->cacheControl($response), "$what must send no-store.");
        $this->assertStringNotContainsString('public', $this->cacheControl($response), $what);
        $this->assertSame('no-cache', $response->headers->get('Pragma'), $what);
    }

    private function order(): Order
    {
        $order = Order::forceCreate([
            'order_number' => self::ORDER_NUMBER, 'customer_name' => 'Rahim Uddin', 'phone' => '01700000000',
            'email' => 'rahim@example.com', 'address' => 'House 1, Road 2, Dhaka', 'delivery_area' => 'inside',
            'payment_method' => 'cod', 'delivery_method' => 'home_delivery', 'subtotal' => 1000,
            'shipping_fee' => 60, 'total' => 1060, 'status' => 'pending',
        ]);
        $brand = Brand::query()->firstOrCreate(['slug' => 'hp'], ['name' => 'HP']);
        $condition = Condition::query()->firstOrCreate(['slug' => 'intact'], ['label' => 'BRAND NEW INTACT BOX', 'short' => 'I', 'tagline' => 'S']);
        $category = Category::query()->firstOrCreate(['slug' => 'laptops'], ['name' => 'Laptops']);
        $product = Product::forceCreate([
            'slug' => 'hp-p1', 'name' => 'HP P1', 'brand_id' => $brand->id, 'category_id' => $category->id,
            'condition_id' => $condition->id, 'price' => 1000, 'description' => 'd',
        ]);
        OrderItem::forceCreate([
            'order_id' => $order->id, 'product_id' => $product->id, 'product_name' => 'HP P1', 'product_slug' => 'hp-p1',
            'unit_price' => 1000, 'quantity' => 1, 'line_total' => 1000,
        ]);

        return $order;
    }

    private function admin(): User
    {
        return User::forceCreate([
            'name' => 'Admin', 'email' => 'admin@example.test', 'password' => bcrypt('secret'),
            'email_verified_at' => now(), 'is_admin' => true,
        ]);
    }

    /** @return array<string, array{0: string}> */
    public static function cacheableEndpoints(): array
    {
        return [
            'site fonts' => ['/api/site-fonts'],
            'menu categories' => ['/api/nav-categories'],
            'category list' => ['/api/v1/categories'],
        ];
    }

    #[DataProvider('cacheableEndpoints')]
    public function test_the_endpoint_is_public_for_five_minutes_with_an_etag_and_sets_no_cookie(string $uri): void
    {
        $response = $this->get($uri)->assertOk();

        $this->assertStringContainsString('public', $this->cacheControl($response));
        $this->assertStringContainsString('max-age=300', $this->cacheControl($response));
        $this->assertStringContainsString('s-maxage=300', $this->cacheControl($response));
        $this->assertNotNull($response->headers->get('ETag'));
        $this->assertSame([], $response->headers->getCookies());
        $this->assertNull($response->headers->get('Set-Cookie'));
    }

    public function test_the_session_free_endpoints_write_no_session_row_and_a_normal_page_does(): void
    {
        config(['session.driver' => 'database']);
        $this->assertSame(0, DB::table('sessions')->count());

        $this->get('/api/site-fonts')->assertOk();
        $this->get('/api/nav-categories')->assertOk();
        $this->get('/api/v1/categories')->assertOk();
        $this->assertSame(0, DB::table('sessions')->count(), 'The cache-friendly endpoints must not start a session.');

        $this->get('/shop')->assertOk();
        $this->assertSame(1, DB::table('sessions')->count(), 'Control: a normal page still does, so this measurement works.');
    }

    public function test_the_endpoints_still_return_the_same_data(): void
    {
        $this->order();
        SiteSetting::updateOrCreate(['key' => 'site_font_english'], ['value' => 'Poppins']);

        $this->get('/api/site-fonts')->assertOk()->assertExactJson(['english' => 'Poppins', 'bangla' => 'Hind Siliguri']);
        $menu = $this->get('/api/nav-categories')->assertOk()->json();
        $this->assertSame('laptops', $menu[0]['slug']);
        $this->assertSame('hp', $menu[0]['brands'][0]['slug']);
        $this->assertSame('laptops', $this->get('/api/v1/categories')->assertOk()->json()[0]['slug']);
    }

    public function test_a_repeat_request_with_the_etag_is_answered_with_a_304(): void
    {
        $etag = $this->get('/api/site-fonts')->assertOk()->headers->get('ETag');

        $second = $this->withHeaders(['If-None-Match' => $etag])->get('/api/site-fonts');

        $this->assertSame(304, $second->getStatusCode());
        $this->assertSame('', $second->getContent());
    }

    public function test_an_admin_change_shows_up_with_a_new_etag(): void
    {
        $before = $this->get('/api/site-fonts')->headers->get('ETag');
        SiteSetting::updateOrCreate(['key' => 'site_font_english'], ['value' => 'Poppins']);

        $after = $this->get('/api/site-fonts')->headers->get('ETag');

        $this->assertNotSame($before, $after);
    }

    public function test_the_order_confirmation_page_is_never_stored(): void
    {
        $this->order();

        $found = $this->get('/thank-you?order='.self::ORDER_NUMBER)->assertOk();
        $this->assertStringContainsString('Rahim Uddin', $found->getContent(), 'The fixture really renders customer data.');
        $this->assertNoStore($found, 'The confirmation page');
        $this->assertNoStore($this->get('/thank-you?order=KG-000000-NOPE00')->assertRedirect('/'), 'The redirect for an unknown order');
        $this->assertNoStore($this->get('/thank-you')->assertRedirect('/'), 'The redirect with no order number');
    }

    public function test_the_public_invoice_is_never_stored_even_when_the_order_does_not_exist(): void
    {
        $this->order();

        $found = $this->get('/orders/'.self::ORDER_NUMBER.'/invoice')->assertOk();
        $this->assertStringContainsString('Rahim Uddin', $found->getContent());
        $this->assertNoStore($found, 'The invoice');
        $this->assertNoStore($this->get('/orders/KG-000000-NOPE00/invoice')->assertNotFound(), 'The 404 for an unknown invoice');
    }

    public function test_the_customer_chat_endpoints_are_never_stored(): void
    {
        $this->assertNoStore($this->get('/chat/messages')->assertNotFound(), 'Polling a chat that does not exist');
    }

    /** @return array<string, array{0: string}> */
    public static function adminPages(): array
    {
        return [
            'dashboard (recent customer orders)' => ['/dashboard'],
            'profile' => ['/profile'],
            'orders' => ['/admin/orders'],
            'customers' => ['/admin/customers'],
            'live chat' => ['/admin/live-chat'],
            'abandoned carts' => ['/admin/cart-abandonment'],
            'visitor analytics' => ['/admin/analytics'],
            'products' => ['/admin/products'],
            'settings' => ['/admin/settings'],
        ];
    }

    #[DataProvider('adminPages')]
    public function test_everything_behind_the_login_is_never_stored(string $uri): void
    {
        $this->order();

        $response = $this->actingAs($this->admin())->get($uri)->assertOk();

        $this->assertNoStore($response, $uri);
    }

    public function test_an_admin_order_page_with_the_customers_details_is_never_stored(): void
    {
        $order = $this->order();

        $response = $this->actingAs($this->admin())->get('/admin/orders/'.$order->id)->assertOk();

        $this->assertStringContainsString('Rahim Uddin', $response->getContent(), 'The fixture really renders customer data.');
        $this->assertNoStore($response, 'The admin order page');
    }

    /** @return array<string, array{0: string}> */
    public static function unchangedPublicPages(): array
    {
        return [
            'home' => ['/'],
            'shop' => ['/shop'],
            'checkout' => ['/checkout'],
            'blog' => ['/blog'],
            'search' => ['/api/search?q=hp'],
            'compare data' => ['/api/compare?ids=1'],
        ];
    }

    #[DataProvider('unchangedPublicPages')]
    public function test_public_pages_outside_stage_one_are_exactly_as_before(string $uri): void
    {
        $response = $this->get($uri)->assertOk();

        $this->assertSame('no-cache, private', $this->cacheControl($response), 'Only stage 1 endpoints change; nothing else may be cached or no-store yet.');
        $this->assertNotSame([], $response->headers->getCookies(), 'Sessions and cookies on these pages are untouched.');
    }
}
