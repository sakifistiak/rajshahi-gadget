<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $settings = [
            'site_name' => SiteSetting::getValue('site_name', 'Khan Gadget'),
            'site_slogan' => SiteSetting::getValue('site_slogan', 'Eternal Tech Companion'),
            'site_description' => SiteSetting::getValue('site_description', 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.'),
            'site_phone' => SiteSetting::getValue('site_phone', '+8801700000000'),
            'site_email' => SiteSetting::getValue('site_email', 'khangadget.bd@gmail.com'),
            'site_address' => SiteSetting::getValue('site_address', 'Level 4, House 12, Road 5, Dhanmondi, Dhaka 1205, Bangladesh'),
            'site_business_hours' => SiteSetting::getValue('site_business_hours', 'Sat – Thu · 10:00 AM – 9:00 PM'),
            'social_facebook' => SiteSetting::getValue('social_facebook', 'https://facebook.com/khansgadget'),
            'social_instagram' => SiteSetting::getValue('social_instagram', 'https://bikroy.com/en/shops/khangadgets'),
            'social_whatsapp' => SiteSetting::getValue('social_whatsapp', 'https://bdstall.com/stall/2373'),
            'social_youtube' => SiteSetting::getValue('social_youtube', 'https://youtube.com/@khansgadget'),
            'footer_copyright' => SiteSetting::getValue('footer_copyright', 'Khan Gadget. All rights reserved.'),
            'logo_light' => SiteSetting::getValue('logo_light', '/media/b3ca13-kg-lockup-v2.png'),
            'logo_dark' => SiteSetting::getValue('logo_dark', '/media/b3ca13-kg-lockup-v2.png'),
        ];

        return view('admin.settings.index', compact('settings'));
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
            'social_facebook' => 'nullable|string|max:500',
            'social_instagram' => 'nullable|string|max:500',
            'social_whatsapp' => 'nullable|string|max:500',
            'social_youtube' => 'nullable|string|max:500',
            'footer_copyright' => 'nullable|string|max:500',
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
            'social_facebook',
            'social_instagram',
            'social_whatsapp',
            'social_youtube',
            'footer_copyright'
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                SiteSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $request->input($key)]
                );
            }
        }

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
