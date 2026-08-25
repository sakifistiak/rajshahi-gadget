<?php

namespace App\Http\Controllers;

use App\Models\CustomPage;
use Illuminate\View\View;

class CustomPageController extends Controller
{
    /**
     * Display the specified custom page on the public frontend.
     */
    public function show(string $slug): View
    {
        $page = CustomPage::with('locations')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return view('pages.custom', compact('page'));
    }
}
