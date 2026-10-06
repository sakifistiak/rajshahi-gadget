<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use App\Models\User;
use App\Support\SslCommerz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentGatewaySettingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // SiteSetting keeps its map in a static property that outlives the rolled-back test database.
        SiteSetting::flushCache();
    }

    private function admin(): User
    {
        return User::factory()->create()->forceFill(['is_admin' => true]);
    }

    private function save(array $fields)
    {
        return $this->actingAs($this->admin())->post(route('admin.payment-gateway.update'), $fields);
    }

    public function test_the_page_is_admin_only(): void
    {
        $this->actingAs(User::factory()->create())->get(route('admin.payment-gateway.index'))->assertForbidden();
        $this->actingAs($this->admin())->get(route('admin.payment-gateway.index'))
            ->assertOk()
            ->assertSee('Hidden at checkout')
            ->assertSee(route('payment.sslcommerz.ipn'));
    }

    public function test_credentials_are_saved_with_the_password_encrypted_and_never_shown(): void
    {
        $this->save([
            'sslcommerz_enabled' => '1',
            'sslcommerz_sandbox' => '1',
            'sslcommerz_store_id' => 'mystore',
            'sslcommerz_store_password' => 'p@ss-123',
        ])->assertRedirect();

        $stored = SiteSetting::getValue('sslcommerz_store_password');
        $this->assertNotSame('p@ss-123', $stored);
        $this->assertSame(['store_id' => 'mystore', 'store_password' => 'p@ss-123', 'sandbox' => true], SslCommerz::credentials());
        $this->assertTrue(SslCommerz::enabled());

        $this->actingAs($this->admin())->get(route('admin.payment-gateway.index'))
            ->assertSee('Live at checkout (Sandbox)')
            ->assertDontSee('p@ss-123')
            ->assertDontSee($stored);
        $this->get('/checkout')->assertSee('value="sslcommerz"', false);
    }

    public function test_a_blank_password_keeps_the_saved_one_and_remove_clears_it(): void
    {
        $this->save(['sslcommerz_enabled' => '1', 'sslcommerz_store_id' => 'mystore', 'sslcommerz_store_password' => 'secret']);
        $this->save(['sslcommerz_enabled' => '1', 'sslcommerz_store_id' => 'mystore', 'sslcommerz_store_password' => '']);
        $this->assertSame('secret', SslCommerz::credentials()['store_password']);
        $this->assertFalse(SslCommerz::credentials()['sandbox']);

        $this->save(['sslcommerz_enabled' => '1', 'sslcommerz_store_id' => 'mystore', 'remove_store_password' => '1']);
        $this->assertFalse(SslCommerz::enabled());
    }

    public function test_the_off_switch_hides_online_payment(): void
    {
        $this->save(['sslcommerz_store_id' => 'mystore', 'sslcommerz_store_password' => 'secret']);

        $this->assertFalse(SslCommerz::enabled());
        $this->get('/checkout')->assertDontSee('value="sslcommerz"', false);
    }

    public function test_saved_settings_override_the_env_fallback(): void
    {
        config(['services.sslcommerz.store_id' => 'envstore', 'services.sslcommerz.store_password' => 'envpass']);
        $this->assertSame('envstore', SslCommerz::credentials()['store_id']);

        $this->save(['sslcommerz_enabled' => '1', 'sslcommerz_sandbox' => '1', 'sslcommerz_store_id' => 'adminstore', 'sslcommerz_store_password' => 'adminpass']);
        $this->assertSame('adminstore', SslCommerz::credentials()['store_id']);
    }

    public function test_the_test_button_reports_success_and_failure(): void
    {
        $this->save(['sslcommerz_enabled' => '1', 'sslcommerz_sandbox' => '1', 'sslcommerz_store_id' => 'mystore', 'sslcommerz_store_password' => 'secret']);

        Http::fake(['sandbox.sslcommerz.com/*' => Http::sequence()
            ->push(['status' => 'SUCCESS', 'GatewayPageURL' => 'https://sandbox.sslcommerz.com/EasyCheckOut/abc'])
            ->push(['status' => 'FAILED', 'failedreason' => 'Store Credential Error'])]);

        $this->actingAs($this->admin())->post(route('admin.payment-gateway.test'))
            ->assertSessionHas('test_url', 'https://sandbox.sslcommerz.com/EasyCheckOut/abc');
        Http::assertSent(fn (HttpRequest $r) => $r['store_id'] === 'mystore' && $r['store_passwd'] === 'secret' && (int) $r['total_amount'] === 10);

        $this->actingAs($this->admin())->post(route('admin.payment-gateway.test'))
            ->assertSessionHas('error', 'Connection failed: Store Credential Error');
    }
}
