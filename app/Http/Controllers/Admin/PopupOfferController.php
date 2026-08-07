<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class PopupOfferController extends Controller
{
    public function index()
    {
        $settingsKeys = [
            'popup_offer_active'        => '0',
            'popup_offer_image'         => '',
            'popup_offer_image_mobile'  => '',
            'popup_offer_link'          => '/shop',
            'popup_offer_target'        => '_self',
            'popup_offer_frequency'     => 'session',
            'popup_offer_delay'         => '1',
            'popup_offer_backdrop_blur' => 'md',
        ];

        $settings = [];
        foreach ($settingsKeys as $key => $default) {
            $settings[$key] = SiteSetting::getValue($key, $default);
        }

        return view('admin.popup-offer.index', compact('settings'));
    }

    public function update(Request $request)
    {
        if ($request->has('popup_offer_active')) {
            $existingDesktopImage = SiteSetting::getValue('popup_offer_image');
            $hasNewDesktopImage = $request->hasFile('popup_offer_image_file') || $request->filled('popup_offer_image');
            if (empty($existingDesktopImage) && !$hasNewDesktopImage) {
                return redirect()->back()->withErrors(['popup_offer_image' => 'Desktop banner image is required to enable popup offer.'])->withInput();
            }
        }

        SiteSetting::setValue('popup_offer_active', $request->has('popup_offer_active') ? '1' : '0');

        if ($request->hasFile('popup_offer_image_file')) {
            $path = $request->file('popup_offer_image_file')->store('popups', 'public');
            SiteSetting::setValue('popup_offer_image', '/storage/' . $path);
        } elseif ($request->has('popup_offer_image')) {
            SiteSetting::setValue('popup_offer_image', $request->input('popup_offer_image', ''));
        }

        if ($request->hasFile('popup_offer_image_mobile_file')) {
            $pathMobile = $request->file('popup_offer_image_mobile_file')->store('popups', 'public');
            SiteSetting::setValue('popup_offer_image_mobile', '/storage/' . $pathMobile);
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

        // Store backdrop blur as clamped integer (0–50)
        if ($request->has('popup_offer_backdrop_blur')) {
            $blurPx = max(0, min(50, (int) $request->input('popup_offer_backdrop_blur', 8)));
            SiteSetting::setValue('popup_offer_backdrop_blur', (string) $blurPx);
        }

        return redirect()->back()->with('success', 'Popup offer banner settings updated successfully!');
    }
}
