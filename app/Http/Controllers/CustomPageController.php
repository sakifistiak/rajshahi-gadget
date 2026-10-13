<?php

namespace App\Http\Controllers;

use App\Models\CustomPage;
use Illuminate\View\View;

class CustomPageController extends Controller
{
    /**
     * Display the specified custom page on the public frontend.
     *
     * When no CMS page exists for the slug, a built-in policy page from
     * resources/views/pages/policies/ is shown instead, so the links required
     * by the payment gateway (terms, privacy, return & refund) never 404.
     */
    public function show(string $slug): View
    {
        $page = CustomPage::with('locations')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $page && view()->exists('pages.policies.'.$slug)) {
            $page = $this->builtInPolicyPage($slug);
        }

        abort_unless($page, 404);

        return view('pages.custom', compact('page'));
    }

    private function builtInPolicyPage(string $slug): CustomPage
    {
        $titles = [
            'return-refund-policy' => 'Return & Refund Policy',
            'privacy-policy' => 'Privacy Policy',
            'terms-conditions' => 'Terms & Conditions',
            'delivery-policy' => 'Delivery Policy',
        ];

        $page = new CustomPage([
            'title' => $titles[$slug] ?? ucwords(str_replace('-', ' ', $slug)),
            'slug' => $slug,
            'content' => view('pages.policies.'.$slug)->render(),
            'is_active' => true,
            'show_title' => true,
            'show_updated_at' => false,
        ]);
        $page->setRelation('locations', collect());

        return $page;
    }
}
