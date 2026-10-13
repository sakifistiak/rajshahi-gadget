<?php

namespace Tests\Feature;

use App\Models\CustomPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentGatewayComplianceTest extends TestCase
{
    use RefreshDatabase;

    public function test_built_in_policy_pages_are_served_when_no_cms_page_exists(): void
    {
        $this->get('/page/return-refund-policy')->assertOk()->assertSee('7-10 working days');
        $this->get('/page/privacy-policy')->assertOk()->assertSee('Privacy Policy');
        $this->get('/page/terms-conditions')->assertOk()->assertSee('Terms &amp; Conditions', false);
    }

    public function test_cms_page_wins_over_the_built_in_one(): void
    {
        CustomPage::create(['title' => 'Our Privacy', 'slug' => 'privacy-policy', 'content' => '<p>cms privacy text</p>', 'is_active' => true]);

        $this->get('/page/privacy-policy')->assertOk()->assertSee('cms privacy text');
    }

    public function test_unknown_pages_still_404(): void
    {
        $this->get('/page/does-not-exist')->assertNotFound();
    }

    public function test_footer_shows_license_address_delivery_time_and_policy_links(): void
    {
        $this->get('/page/return-refund-policy')
            ->assertSee('TRAD/DSCC/004397/2023')
            ->assertSee('69-71 Mirpur Road')
            ->assertSee('Inside Dhaka - 5 days, Outside Dhaka - 10 days')
            ->assertSee('href="/page/return-refund-policy"', false)
            ->assertSee('href="/page/privacy-policy"', false);
    }
}
