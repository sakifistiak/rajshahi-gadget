<?php

namespace Tests\Feature;

use App\Models\CustomPage;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomPageGoogleFormTest extends TestCase
{
    use RefreshDatabase;

    private const EMBED = 'https://docs.google.com/forms/d/e/1FAIpQLSf-abc_123/viewform?embedded=true';

    protected function setUp(): void
    {
        parent::setUp();

        // SiteSetting keeps a static in-process map that survives the per-test database rollback.
        SiteSetting::flushCache();
    }

    private function admin(): User
    {
        return User::forceCreate([
            'name' => 'Admin', 'email' => 'admin@example.test', 'password' => bcrypt('secret'),
            'email_verified_at' => now(), 'is_admin' => true,
        ]);
    }

    private function page(array $attributes = []): CustomPage
    {
        return CustomPage::forceCreate($attributes + [
            'title' => 'Complain & Advice', 'slug' => 'complain-advice', 'is_active' => true,
            'show_title' => true, 'content' => '<p>Tell us what we can do better.</p>',
        ]);
    }

    public function test_the_admin_create_and_edit_screens_have_the_google_form_fields(): void
    {
        $admin = $this->admin();
        $page = $this->page(['google_form_url' => self::EMBED, 'google_form_height' => 1500]);

        $this->actingAs($admin)->get(route('admin.pages.create'))->assertOk()
            ->assertSee('name="google_form_url"', false)
            ->assertSee('name="google_form_height"', false);

        $this->actingAs($admin)->get(route('admin.pages.edit', $page))->assertOk()
            ->assertSee(e(self::EMBED), false)
            ->assertSee('value="1500"', false);
    }

    public function test_admin_can_save_the_embed_code_and_the_clean_url_is_stored(): void
    {
        $page = $this->page();

        $this->actingAs($this->admin())->put(route('admin.pages.update', $page), [
            'title' => $page->title,
            'slug' => $page->slug,
            'content' => $page->content,
            'is_active' => 1,
            'google_form_url' => '<iframe src="'.self::EMBED.'" width="640" height="1377" frameborder="0">Loading…</iframe>',
            'google_form_height' => 1500,
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.pages.index'));

        $page->refresh();
        $this->assertSame(self::EMBED, $page->google_form_url);
        $this->assertSame(1500, $page->google_form_height);
    }

    public function test_a_link_that_is_not_a_public_google_form_is_rejected(): void
    {
        $page = $this->page();

        $this->actingAs($this->admin())->put(route('admin.pages.update', $page), [
            'title' => $page->title,
            'content' => $page->content,
            'google_form_url' => 'https://docs.google.com/forms/d/1FAIpQLSf-abc_123/edit',
        ])->assertSessionHasErrors('google_form_url');

        $this->assertNull($page->refresh()->google_form_url);
    }

    public function test_clearing_the_field_removes_the_form(): void
    {
        $page = $this->page(['google_form_url' => self::EMBED]);

        $this->actingAs($this->admin())->put(route('admin.pages.update', $page), [
            'title' => $page->title,
            'content' => $page->content,
            'google_form_url' => '',
        ])->assertSessionHasNoErrors();

        $this->assertNull($page->refresh()->google_form_url);
    }

    public function test_the_public_page_shows_the_form_below_the_content(): void
    {
        $this->page(['google_form_url' => self::EMBED, 'google_form_height' => 1500]);

        $html = $this->get('/page/complain-advice')->assertOk()->getContent();

        $this->assertStringContainsString('<iframe src="'.e(self::EMBED).'"', $html);
        $this->assertStringContainsString('--kg-form-h: 1500px', $html);
        $this->assertStringContainsString('href="https://docs.google.com/forms/d/e/1FAIpQLSf-abc_123/viewform"', $html);
        $this->assertGreaterThan(strpos($html, 'Tell us what we can do better.'), strpos($html, '<iframe'));
    }

    public function test_the_default_height_is_used_when_none_is_set(): void
    {
        $this->page(['google_form_url' => self::EMBED]);

        $this->get('/page/complain-advice')->assertOk()
            ->assertSee('--kg-form-h: '.CustomPage::DEFAULT_GOOGLE_FORM_HEIGHT.'px', false);
    }

    public function test_a_page_without_a_form_has_no_iframe(): void
    {
        $this->page();

        $this->assertStringNotContainsString('<iframe', $this->get('/page/complain-advice')->assertOk()->getContent());
    }

    public function test_a_stored_url_that_is_not_a_google_form_is_never_framed(): void
    {
        // Values are cleaned on save, but the page checks again in case the row was edited directly.
        $this->page(['google_form_url' => 'https://evil.test/forms/d/e/x/viewform']);

        $this->assertStringNotContainsString('<iframe', $this->get('/page/complain-advice')->assertOk()->getContent());
    }
}
