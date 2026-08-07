<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\Category;
use App\Models\Condition;
use Illuminate\Http\Request;

class HomeSettingController extends Controller
{
    public static function getDefaultSections(): array
    {
        return [
            [
                'id' => 'sec_1',
                'title' => 'Brand new intact box',
                'highlight' => 'intact box',
                'filter' => 'cond_intact',
                'limit' => '4',
                'active' => true,
            ],
            [
                'id' => 'sec_2',
                'title' => 'Brand new without box',
                'highlight' => 'without box',
                'filter' => 'cond_without-box',
                'limit' => '4',
                'active' => true,
            ],
            [
                'id' => 'sec_3',
                'title' => 'Certified pre-owned',
                'highlight' => 'pre-owned',
                'filter' => 'cond_pre-owned',
                'limit' => '4',
                'active' => true,
            ],
        ];
    }

    public function index()
    {
        $categories = Category::orderBy('name')->get();
        $conditions = Condition::orderBy('label')->get();

        $defaultTickerText = "🎉 Eid Special: Up to 15% off on Brand New Intact Box iPhones\n🚚 Same-day delivery inside Dhaka on orders before 3 PM\n🛡️ 7-day easy replacement on all Pre-Owned products\n💳 0% EMI up to 12 months on selected products\n📞 Chat with us on WhatsApp for instant support";

        $settingsKeys = [
            'home_hero_active'          => '1',
            'home_flash_active'         => '1',
            'home_flash_title'          => 'Limited time deals',
            'home_flash_highlight'      => 'deals',
            'home_promos_active'        => '1',
            'home_testimonials_active'  => '1',
            'home_ticker_active'        => '1',
            'home_ticker_text'          => $defaultTickerText,
        ];

        $settings = [];
        foreach ($settingsKeys as $key => $default) {
            $settings[$key] = SiteSetting::getValue($key, $default);
        }

        $sectionsJson = SiteSetting::getValue('home_sections_json');
        $sectionsList = [];
        if ($sectionsJson) {
            $decoded = json_decode($sectionsJson, true);
            if (is_array($decoded) && count($decoded) > 0) {
                $sectionsList = $decoded;
            }
        }

        if (empty($sectionsList)) {
            $sec1Title = SiteSetting::getValue('home_sec1_title', 'Brand new intact box');
            $sec1Hl    = SiteSetting::getValue('home_sec1_highlight', 'intact box');
            $sec1Filt  = SiteSetting::getValue('home_sec1_filter', 'cond_intact');
            $sec1Lim   = SiteSetting::getValue('home_sec1_limit', '4');
            $sec1Act   = SiteSetting::getValue('home_sec1_active', '1') == '1';

            $sec2Title = SiteSetting::getValue('home_sec2_title', 'Brand new without box');
            $sec2Hl    = SiteSetting::getValue('home_sec2_highlight', 'without box');
            $sec2Filt  = SiteSetting::getValue('home_sec2_filter', 'cond_without-box');
            $sec2Lim   = SiteSetting::getValue('home_sec2_limit', '4');
            $sec2Act   = SiteSetting::getValue('home_sec2_active', '1') == '1';

            $sec3Title = SiteSetting::getValue('home_sec3_title', 'Certified pre-owned');
            $sec3Hl    = SiteSetting::getValue('home_sec3_highlight', 'pre-owned');
            $sec3Filt  = SiteSetting::getValue('home_sec3_filter', 'cond_pre-owned');
            $sec3Lim   = SiteSetting::getValue('home_sec3_limit', '4');
            $sec3Act   = SiteSetting::getValue('home_sec3_active', '1') == '1';

            $sectionsList = [
                ['id' => 'sec_1', 'title' => $sec1Title, 'highlight' => $sec1Hl, 'filter' => $sec1Filt, 'limit' => $sec1Lim, 'active' => $sec1Act],
                ['id' => 'sec_2', 'title' => $sec2Title, 'highlight' => $sec2Hl, 'filter' => $sec2Filt, 'limit' => $sec2Lim, 'active' => $sec2Act],
                ['id' => 'sec_3', 'title' => $sec3Title, 'highlight' => $sec3Hl, 'filter' => $sec3Filt, 'limit' => $sec3Lim, 'active' => $sec3Act],
            ];
        }

        return view('admin.home-settings.index', compact('categories', 'conditions', 'settings', 'sectionsList'));
    }

    public function update(Request $request)
    {
        $checkboxKeys = [
            'home_hero_active',
            'home_flash_active',
            'home_promos_active',
            'home_testimonials_active',
            'home_ticker_active',
        ];

        foreach ($checkboxKeys as $cb) {
            SiteSetting::setValue($cb, $request->has($cb) ? '1' : '0');
        }

        if ($request->has('home_ticker_text')) {
            SiteSetting::setValue('home_ticker_text', $request->input('home_ticker_text', ''));
        }

        if ($request->has('home_flash_title')) {
            SiteSetting::setValue('home_flash_title', $request->input('home_flash_title', ''));
        }
        if ($request->has('home_flash_highlight')) {
            SiteSetting::setValue('home_flash_highlight', $request->input('home_flash_highlight', ''));
        }

        if ($request->has('home_sections_json')) {
            $json = $request->input('home_sections_json');
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                SiteSetting::setValue('home_sections_json', json_encode(array_values($decoded)));
            }
        }

        return redirect()->back()->with('success', 'Homepage settings updated successfully!');
    }
}
