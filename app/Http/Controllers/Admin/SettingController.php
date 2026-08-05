<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Models\CustomPage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $defaultCol1Links = json_encode([
            ['label' => 'BRAND NEW INTACT BOX', 'url' => '/shop?condition=intact'],
            ['label' => 'BRAND NEW WITHOUT BOX', 'url' => '/shop?condition=without-box'],
            ['label' => 'PRE-OWNED', 'url' => '/shop?condition=pre-owned'],
            ['label' => 'All products', 'url' => '/shop'],
            ['label' => 'Compare', 'url' => '/compare'],
        ]);

        $defaultCol2Links = json_encode([
            ['label' => 'Blog', 'url' => '/blog'],
            ['label' => 'Customer Spotlight', 'url' => '/customer-spotlight'],
            ['label' => 'Philanthropic Work', 'url' => '/philanthropic-work'],
            ['label' => 'Customer Feedback', 'url' => '/customer-feedback'],
            ['label' => 'About us', 'url' => '/about'],
            ['label' => 'Contact', 'url' => '/contact'],
        ]);

        $settings = [
            'site_name' => SiteSetting::getValue('site_name', 'Khan Gadget'),
            'site_slogan' => SiteSetting::getValue('site_slogan', 'Brand NEW Intact BOX, Without BOX & Pre-Owned'),
            'site_description' => SiteSetting::getValue('site_description', 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.'),
            'site_phone' => SiteSetting::getValue('site_phone', '+8801700000000'),
            'site_email' => SiteSetting::getValue('site_email', 'khangadget.bd@gmail.com'),
            'site_address' => SiteSetting::getValue('site_address', 'Level 4, House 12, Road 5, Dhanmondi, Dhaka 1205, Bangladesh'),
            'site_business_hours' => SiteSetting::getValue('site_business_hours', 'Sat – Thu · 10:00 AM – 9:00 PM'),
            'mobile_menu_contact' => SiteSetting::getValue('mobile_menu_contact', '+8801341246152'),
            'mobile_drawer_store_info' => SiteSetting::getValue('mobile_drawer_store_info', 'Elephant Road Branch: Shop No: 136 | Ground Floor | Computer City Center (Ex Multiplan Center) | New Elephant Road, Dhaka-1205'),
            'social_facebook' => SiteSetting::getValue('social_facebook', 'https://facebook.com/khansgadget'),
            'social_instagram' => SiteSetting::getValue('social_instagram', 'https://bikroy.com/en/shops/khangadgets'),
            'social_whatsapp' => SiteSetting::getValue('social_whatsapp', 'https://bdstall.com/stall/2373'),
            'social_youtube' => SiteSetting::getValue('social_youtube', 'https://youtube.com/@khansgadget'),
            'footer_copyright' => SiteSetting::getValue('footer_copyright', 'Khan Gadget. All rights reserved.'),
            'logo_light' => SiteSetting::getValue('logo_light', '/media/b3ca13-kg-lockup-v2.png'),
            'logo_dark' => SiteSetting::getValue('logo_dark', '/media/b3ca13-kg-lockup-v2.png'),
            'footer_col1_active' => SiteSetting::getValue('footer_col1_active', '1'),
            'footer_col1_title' => SiteSetting::getValue('footer_col1_title', 'SHOP'),
            'footer_col1_links' => SiteSetting::getValue('footer_col1_links', $defaultCol1Links),
            'footer_col2_active' => SiteSetting::getValue('footer_col2_active', '1'),
            'footer_col2_title' => SiteSetting::getValue('footer_col2_title', 'EXPLORE'),
            'footer_col2_links' => SiteSetting::getValue('footer_col2_links', $defaultCol2Links),
        ];

        $customPages = CustomPage::where('is_active', true)->orderBy('title')->get();

        return view('admin.settings.index', compact('settings', 'customPages'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_slogan' => 'nullable|string|max:255',
            'site_description' => 'nullable|string|max:1000',
            'site_phone' => 'nullable|string|max:100',
            'site_email' => 'nullable|string|max:255',
            'site_address' => 'nullable|string|max:500',
            'site_business_hours' => 'nullable|string|max:255',
            'mobile_menu_contact' => 'nullable|string|max:100',
            'mobile_drawer_store_info' => 'nullable|string|max:1000',
            'social_facebook' => 'nullable|string|max:500',
            'social_instagram' => 'nullable|string|max:500',
            'social_whatsapp' => 'nullable|string|max:500',
            'social_youtube' => 'nullable|string|max:500',
            'footer_copyright' => 'nullable|string|max:500',
            'footer_col1_active' => 'nullable|string',
            'footer_col1_title' => 'nullable|string|max:255',
            'footer_col1_links' => 'nullable|string',
            'footer_col2_active' => 'nullable|string',
            'footer_col2_title' => 'nullable|string|max:255',
            'footer_col2_links' => 'nullable|string',
            'logo_light_file' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:4096',
            'logo_dark_file' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:4096',
        ]);

        $keys = [
            'site_name',
            'site_slogan',
            'site_description',
            'site_phone',
            'site_email',
            'site_address',
            'site_business_hours',
            'mobile_menu_contact',
            'mobile_drawer_store_info',
            'social_facebook',
            'social_instagram',
            'social_whatsapp',
            'social_youtube',
            'footer_copyright',
            'footer_col1_title',
            'footer_col1_links',
            'footer_col2_title',
            'footer_col2_links',
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }

        // Save column active toggles
        SiteSetting::updateOrCreate(
            ['key' => 'footer_col1_active'],
            ['value' => $request->has('footer_col1_active') ? '1' : '0']
        );

        SiteSetting::updateOrCreate(
            ['key' => 'footer_col2_active'],
            ['value' => $request->has('footer_col2_active') ? '1' : '0']
        );

        // Handle Light Mode Logo Upload
        if ($request->hasFile('logo_light_file')) {
            $file = $request->file('logo_light_file');
            $filename = 'logo_light_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('media'), $filename);
            SiteSetting::updateOrCreate(
                ['key' => 'logo_light'],
                ['value' => '/media/' . $filename]
            );
        }

        // Handle Dark Mode Logo Upload
        if ($request->hasFile('logo_dark_file')) {
            $file = $request->file('logo_dark_file');
            $filename = 'logo_dark_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('media'), $filename);
            SiteSetting::updateOrCreate(
                ['key' => 'logo_dark'],
                ['value' => '/media/' . $filename]
            );
        }

        return redirect()->back()->with('success', 'Site & Footer settings updated successfully!');
    }
}
