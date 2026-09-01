<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Condition;
use App\Models\SiteSetting;
use App\Support\ImageUploader;
use App\Support\SectionTitleStyle;
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

    public static function getDefaultTrustBarItems(): array
    {
        return [
            ['id' => 'tb_1', 'icon_type' => 'lucide', 'icon_lucide' => 'credit-card', 'icon_image' => '', 'label' => '36 Months EMI', 'active' => true],
            ['id' => 'tb_2', 'icon_type' => 'lucide', 'icon_lucide' => 'truck', 'icon_image' => '', 'label' => 'Fastest Home Delivery', 'active' => true],
            ['id' => 'tb_3', 'icon_type' => 'lucide', 'icon_lucide' => 'refresh-ccw', 'icon_image' => '', 'label' => 'Exchange Facility', 'active' => true],
            ['id' => 'tb_4', 'icon_type' => 'lucide', 'icon_lucide' => 'tag', 'icon_image' => '', 'label' => 'Best Price Deals', 'active' => true],
            ['id' => 'tb_5', 'icon_type' => 'lucide', 'icon_lucide' => 'headphones', 'icon_image' => '', 'label' => 'After-Sales Service', 'active' => true],
        ];
    }

    public function index()
    {
        $categories = Category::orderBy('name')->get();
        $conditions = Condition::orderBy('label')->get();

        $defaultTickerText = "🎉 Eid Special: Up to 15% off on Brand New Intact Box iPhones\n🚚 Same-day delivery inside Dhaka on orders before 3 PM\n🛡️ 7-day easy replacement on all Pre-Owned products\n💳 0% EMI up to 12 months on selected products\n📞 Chat with us on WhatsApp for instant support";

        $settingsKeys = [
            'home_hero_active' => '1',
            'home_flash_active' => '1',
            'home_flash_title' => 'Limited time deals',
            'home_flash_highlight' => 'deals',
            'home_flash_badge_active' => '1',
            'home_flash_badge_icon' => '',
            'home_flash_badge_text' => 'Flash Deals',
            'home_flash_subtitle_active' => '1',
            'home_flash_subtitle_text' => 'Limited stock · 0% EMI up to 12 months · Free Dhaka delivery',
            'home_new_arrival_active' => '0',
            'home_new_arrival_title' => 'New Arrivals',
            'home_new_arrival_highlight' => 'New',
            'home_new_arrival_position' => 'below_flash',
            'home_new_arrival_limit' => '4',
            'home_new_arrival_badge_active' => '1',
            'home_new_arrival_badge_icon' => '',
            'home_new_arrival_badge_text' => 'New Arrival',
            'home_new_arrival_subtitle_active' => '1',
            'home_new_arrival_subtitle_text' => 'Fresh stock, sourced on request — order now, get it soon',
            'home_promos_active' => '1',
            'home_testimonials_active' => '1',
            'home_ticker_active' => '1',
            'home_ticker_text' => $defaultTickerText,
            'home_ticker_effect' => 'fade',
            'home_ticker_speed' => '6',
            'popup_offer_active' => '0',
            'popup_offer_image' => '',
            'popup_offer_image_mobile' => '',
            'popup_offer_link' => '/shop',
            'popup_offer_target' => '_self',
            'popup_offer_frequency' => 'session',
            'popup_offer_delay' => '1',
            'stock_price_notice_active' => '1',
            'stock_price_notice_text' => 'অর্ডার করার পূর্বে স্টক ও প্রাইজ কমতে বাড়তে পারে',
            'stock_price_notice_type' => 'warning',
            'checkout_cod_notice_active' => '1',
            'checkout_cod_notice_text' => 'অর্ডার কনফার্ম করার জন্য ন্যূনতম ২,০০০ টাকা অগ্রিম পেমেন্ট করতে হবে।',
            'product_trust_badges_active' => '1',
            'home_trustbar_active' => '1',
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
            $sec1Hl = SiteSetting::getValue('home_sec1_highlight', 'intact box');
            $sec1Filt = SiteSetting::getValue('home_sec1_filter', 'cond_intact');
            $sec1Lim = SiteSetting::getValue('home_sec1_limit', '4');
            $sec1Act = SiteSetting::getValue('home_sec1_active', '1') == '1';

            $sec2Title = SiteSetting::getValue('home_sec2_title', 'Brand new without box');
            $sec2Hl = SiteSetting::getValue('home_sec2_highlight', 'without box');
            $sec2Filt = SiteSetting::getValue('home_sec2_filter', 'cond_without-box');
            $sec2Lim = SiteSetting::getValue('home_sec2_limit', '4');
            $sec2Act = SiteSetting::getValue('home_sec2_active', '1') == '1';

            $sec3Title = SiteSetting::getValue('home_sec3_title', 'Certified pre-owned');
            $sec3Hl = SiteSetting::getValue('home_sec3_highlight', 'pre-owned');
            $sec3Filt = SiteSetting::getValue('home_sec3_filter', 'cond_pre-owned');
            $sec3Lim = SiteSetting::getValue('home_sec3_limit', '4');
            $sec3Act = SiteSetting::getValue('home_sec3_active', '1') == '1';

            $sectionsList = [
                ['id' => 'sec_1', 'title' => $sec1Title, 'highlight' => $sec1Hl, 'filter' => $sec1Filt, 'limit' => $sec1Lim, 'active' => $sec1Act],
                ['id' => 'sec_2', 'title' => $sec2Title, 'highlight' => $sec2Hl, 'filter' => $sec2Filt, 'limit' => $sec2Lim, 'active' => $sec2Act],
                ['id' => 'sec_3', 'title' => $sec3Title, 'highlight' => $sec3Hl, 'filter' => $sec3Filt, 'limit' => $sec3Lim, 'active' => $sec3Act],
            ];
        }

        foreach ($sectionsList as &$sec) {
            $sec['style'] = SectionTitleStyle::sanitizeFull($sec['style'] ?? null);
        }
        unset($sec);

        $trustbarJson = SiteSetting::getValue('home_trustbar_items_json');
        $trustbarItems = [];
        if ($trustbarJson) {
            $decoded = json_decode($trustbarJson, true);
            if (is_array($decoded) && count($decoded) > 0) {
                $trustbarItems = $decoded;
            }
        }
        if (empty($trustbarItems)) {
            $trustbarItems = self::getDefaultTrustBarItems();
        }

        $titleStyleFonts = SectionTitleStyle::FONTS;
        $titleStyleShadows = SectionTitleStyle::SHADOWS;
        $titleStyleDefaults = [
            'base' => SectionTitleStyle::BASE_DEFAULTS,
            'highlight' => SectionTitleStyle::DEFAULTS,
            'font_size' => SectionTitleStyle::FONT_SIZE_DEFAULTS,
        ];

        $flashTitleStyle = SectionTitleStyle::sanitizeFull(
            json_decode(SiteSetting::getValue('home_flash_title_style', '{}'), true)
        );

        $newArrivalTitleStyle = SectionTitleStyle::sanitizeFull(
            json_decode(SiteSetting::getValue('home_new_arrival_title_style', '{}'), true)
        );

        return view('admin.home-settings.index', compact(
            'categories', 'conditions', 'settings', 'sectionsList', 'trustbarItems',
            'titleStyleFonts', 'titleStyleShadows', 'titleStyleDefaults', 'flashTitleStyle', 'newArrivalTitleStyle'
        ));
    }

    public function update(Request $request)
    {
        if ($request->has('popup_offer_active')) {
            $existingDesktopImage = SiteSetting::getValue('popup_offer_image');
            $hasNewDesktopImage = $request->hasFile('popup_offer_image_file') || $request->filled('popup_offer_image');
            if (empty($existingDesktopImage) && ! $hasNewDesktopImage) {
                return redirect()->back()->withErrors(['popup_offer_image' => 'Desktop banner image is required to enable popup offer.'])->withInput();
            }
        }

        $checkboxKeys = [
            'home_hero_active',
            'home_flash_active',
            'home_flash_badge_active',
            'home_flash_subtitle_active',
            'home_new_arrival_active',
            'home_new_arrival_badge_active',
            'home_new_arrival_subtitle_active',
            'home_promos_active',
            'home_testimonials_active',
            'home_ticker_active',
            'popup_offer_active',
            'home_trustbar_active',
        ];

        foreach ($checkboxKeys as $cb) {
            SiteSetting::setValue($cb, $request->has($cb) ? '1' : '0');
        }

        if ($request->hasFile('popup_offer_image_file')) {
            $path = ImageUploader::storeOnDisk($request->file('popup_offer_image_file'), 'popups');
            SiteSetting::setValue('popup_offer_image', '/storage/'.$path);
        } elseif ($request->has('popup_offer_image')) {
            SiteSetting::setValue('popup_offer_image', $request->input('popup_offer_image', ''));
        }

        if ($request->hasFile('popup_offer_image_mobile_file')) {
            $pathMobile = ImageUploader::storeOnDisk($request->file('popup_offer_image_mobile_file'), 'popups');
            SiteSetting::setValue('popup_offer_image_mobile', '/storage/'.$pathMobile);
        } elseif ($request->has('popup_offer_image_mobile')) {
            SiteSetting::setValue('popup_offer_image_mobile', $request->input('popup_offer_image_mobile', ''));
        }

        $popupInputs = [
            'popup_offer_link',
            'popup_offer_target',
            'popup_offer_frequency',
            'popup_offer_delay',
        ];

        foreach ($popupInputs as $inputKey) {
            if ($request->has($inputKey)) {
                SiteSetting::setValue($inputKey, $request->input($inputKey));
            }
        }

        if ($request->has('home_ticker_text')) {
            SiteSetting::setValue('home_ticker_text', $request->input('home_ticker_text', ''));
        }

        if ($request->has('home_ticker_effect')) {
            $effect = in_array($request->input('home_ticker_effect'), ['fade', 'scroll'], true)
                ? $request->input('home_ticker_effect')
                : 'fade';
            SiteSetting::setValue('home_ticker_effect', $effect);
        }

        if ($request->has('home_ticker_speed')) {
            $speed = (float) $request->input('home_ticker_speed', 6);
            $speed = max(2, min(20, $speed));
            SiteSetting::setValue('home_ticker_speed', (string) $speed);
        }

        if ($request->has('home_flash_title')) {
            SiteSetting::setValue('home_flash_title', $request->input('home_flash_title', ''));
        }
        if ($request->has('home_flash_highlight')) {
            SiteSetting::setValue('home_flash_highlight', $request->input('home_flash_highlight', ''));
        }
        if ($request->has('home_flash_badge_icon')) {
            SiteSetting::setValue('home_flash_badge_icon', mb_substr(trim($request->input('home_flash_badge_icon', '')), 0, 8));
        }
        if ($request->has('home_flash_badge_text')) {
            SiteSetting::setValue('home_flash_badge_text', mb_substr(trim($request->input('home_flash_badge_text', '')), 0, 40));
        }
        if ($request->has('home_flash_subtitle_text')) {
            SiteSetting::setValue('home_flash_subtitle_text', mb_substr(trim($request->input('home_flash_subtitle_text', '')), 0, 150));
        }
        if ($request->has('home_flash_title_style')) {
            $decodedFlashStyle = json_decode($request->input('home_flash_title_style', '{}'), true);
            SiteSetting::setValue(
                'home_flash_title_style',
                json_encode(SectionTitleStyle::sanitizeFull(is_array($decodedFlashStyle) ? $decodedFlashStyle : null))
            );
        }

        if ($request->has('home_new_arrival_title')) {
            SiteSetting::setValue('home_new_arrival_title', $request->input('home_new_arrival_title', ''));
        }
        if ($request->has('home_new_arrival_highlight')) {
            SiteSetting::setValue('home_new_arrival_highlight', $request->input('home_new_arrival_highlight', ''));
        }
        if ($request->has('home_new_arrival_position')) {
            $position = in_array($request->input('home_new_arrival_position'), ['above_flash', 'below_flash'], true)
                ? $request->input('home_new_arrival_position')
                : 'below_flash';
            SiteSetting::setValue('home_new_arrival_position', $position);
        }
        if ($request->has('home_new_arrival_limit')) {
            $limit = (int) $request->input('home_new_arrival_limit', 4);
            $limit = in_array($limit, [4, 8, 12, 16], true) ? $limit : 4;
            SiteSetting::setValue('home_new_arrival_limit', (string) $limit);
        }
        if ($request->has('home_new_arrival_badge_icon')) {
            SiteSetting::setValue('home_new_arrival_badge_icon', mb_substr(trim($request->input('home_new_arrival_badge_icon', '')), 0, 8));
        }
        if ($request->has('home_new_arrival_badge_text')) {
            SiteSetting::setValue('home_new_arrival_badge_text', mb_substr(trim($request->input('home_new_arrival_badge_text', '')), 0, 40));
        }
        if ($request->has('home_new_arrival_subtitle_text')) {
            SiteSetting::setValue('home_new_arrival_subtitle_text', mb_substr(trim($request->input('home_new_arrival_subtitle_text', '')), 0, 150));
        }
        if ($request->has('home_new_arrival_title_style')) {
            $decodedNewArrivalStyle = json_decode($request->input('home_new_arrival_title_style', '{}'), true);
            SiteSetting::setValue(
                'home_new_arrival_title_style',
                json_encode(SectionTitleStyle::sanitizeFull(is_array($decodedNewArrivalStyle) ? $decodedNewArrivalStyle : null))
            );
        }

        if ($request->has('home_sections_json')) {
            $json = $request->input('home_sections_json');
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                foreach ($decoded as &$sectionItem) {
                    if (is_array($sectionItem)) {
                        $sectionItem['style'] = SectionTitleStyle::sanitizeFull($sectionItem['style'] ?? null);
                    }
                }
                unset($sectionItem);
                SiteSetting::setValue('home_sections_json', json_encode(array_values($decoded)));
            }
        }

        if ($request->has('home_trustbar_items_json')) {
            $decoded = json_decode($request->input('home_trustbar_items_json'), true);
            if (is_array($decoded)) {
                $sanitized = [];
                foreach ($decoded as $item) {
                    if (! is_array($item)) {
                        continue;
                    }
                    $iconType = ($item['icon_type'] ?? 'lucide') === 'image' ? 'image' : 'lucide';
                    $iconLucide = mb_substr(preg_replace('/[^a-z0-9\-]/', '', strtolower(trim((string) ($item['icon_lucide'] ?? '')))), 0, 40);
                    $iconImage = mb_substr(trim((string) ($item['icon_image'] ?? '')), 0, 500);
                    $label = mb_substr(trim((string) ($item['label'] ?? '')), 0, 60);
                    $sanitized[] = [
                        'id' => mb_substr((string) ($item['id'] ?? uniqid('tb_')), 0, 40),
                        'icon_type' => $iconType,
                        'icon_lucide' => $iconLucide,
                        'icon_image' => $iconImage,
                        'label' => $label,
                        'active' => ! empty($item['active']),
                    ];
                }
                SiteSetting::setValue('home_trustbar_items_json', json_encode(array_values($sanitized)));
            }
        }

        // Save Stock & Price Notice Disclaimer settings
        SiteSetting::setValue('stock_price_notice_active', $request->has('stock_price_notice_active') ? '1' : '0');
        if ($request->has('stock_price_notice_text')) {
            $noticeText = mb_substr(trim($request->input('stock_price_notice_text', '')), 0, 255);
            SiteSetting::setValue('stock_price_notice_text', $noticeText);
        }
        if ($request->has('stock_price_notice_type')) {
            $type = in_array($request->input('stock_price_notice_type'), ['info', 'warning', 'danger'])
                ? $request->input('stock_price_notice_type')
                : 'warning';
            SiteSetting::setValue('stock_price_notice_type', $type);
        }

        // Save Checkout COD Advance Payment Notice settings
        SiteSetting::setValue('checkout_cod_notice_active', $request->has('checkout_cod_notice_active') ? '1' : '0');
        if ($request->has('checkout_cod_notice_text')) {
            $codNoticeText = mb_substr(trim($request->input('checkout_cod_notice_text', '')), 0, 255);
            SiteSetting::setValue('checkout_cod_notice_text', $codNoticeText);
        }

        SiteSetting::setValue('product_trust_badges_active', $request->has('product_trust_badges_active') ? '1' : '0');

        return redirect()->back()->with('success', 'Homepage settings updated successfully!');
    }
}
