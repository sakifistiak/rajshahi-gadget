<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Http\Request;

class HomeSettingController extends Controller
{
    /**
     * Display the Homepage Settings interface.
     */
    public function index()
    {
        $categories = Category::orderBy('name')->get();
        $conditions = Condition::orderBy('label')->get();

        $settingsKeys = [
            'home_hero_active'          => '1',
            'home_flash_active'         => '1',
            'home_flash_title'          => 'Limited time deals',
            'home_flash_highlight'      => 'deals',

            'home_sec1_active'         => '1',
            'home_sec1_title'          => 'Brand new intact box',
            'home_sec1_highlight'      => 'intact box',
            'home_sec1_filter'         => 'cond_intact',
            'home_sec1_limit'          => '4',

            'home_sec2_active'         => '1',
            'home_sec2_title'          => 'Brand new without box',
            'home_sec2_highlight'      => 'without box',
            'home_sec2_filter'         => 'cond_without-box',
            'home_sec2_limit'          => '4',

            'home_sec3_active'         => '1',
            'home_sec3_title'          => 'Certified pre-owned',
            'home_sec3_highlight'      => 'pre-owned',
            'home_sec3_filter'         => 'cond_pre-owned',
            'home_sec3_limit'          => '4',

            'home_promos_active'        => '1',
            'home_testimonials_active'  => '1',
        ];

        $settings = [];
        foreach ($settingsKeys as $key => $default) {
            $settings[$key] = SiteSetting::getValue($key, $default);
        }

        return view('admin.home-settings.index', compact('categories', 'conditions', 'settings'));
    }

    /**
     * Update Homepage Settings in database.
     */
    public function update(Request $request)
    {
        $checkboxKeys = [
            'home_hero_active',
            'home_flash_active',
            'home_sec1_active',
            'home_sec2_active',
            'home_sec3_active',
            'home_promos_active',
            'home_testimonials_active',
        ];

        // Ensure all checkboxes default to '0' if unchecked in HTML form
        foreach ($checkboxKeys as $cb) {
            SiteSetting::setValue($cb, $request->has($cb) ? '1' : '0');
        }

        // Save text & select inputs
        $textKeys = [
            'home_flash_title',
            'home_flash_highlight',

            'home_sec1_title',
            'home_sec1_highlight',
            'home_sec1_filter',
            'home_sec1_limit',

            'home_sec2_title',
            'home_sec2_highlight',
            'home_sec2_filter',
            'home_sec2_limit',

            'home_sec3_title',
            'home_sec3_highlight',
            'home_sec3_filter',
            'home_sec3_limit',
        ];

        foreach ($textKeys as $key) {
            if ($request->has($key)) {
                SiteSetting::setValue($key, $request->input($key, ''));
            }
        }

        return redirect()->back()->with('success', 'Homepage settings updated successfully!');
    }
}
