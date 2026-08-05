<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\SiteSetting;
use App\Models\StoreLocation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share site & footer settings and store locations with ALL views dynamically
        View::composer('*', function ($view) {
            $storeLocations = collect();
            try {
                $storeLocations = StoreLocation::where('is_active', true)->orderBy('sort_order', 'asc')->get();
            } catch (\Exception $e) {
                // Fail gracefully if DB table doesn't exist yet
            }

            $defaultCol1Links = [
                ['label' => 'BRAND NEW INTACT BOX', 'url' => '/shop?condition=intact'],
                ['label' => 'BRAND NEW WITHOUT BOX', 'url' => '/shop?condition=without-box'],
                ['label' => 'PRE-OWNED', 'url' => '/shop?condition=pre-owned'],
                ['label' => 'All products', 'url' => '/shop'],
                ['label' => 'Compare', 'url' => '/compare'],
            ];

            $defaultCol2Links = [
                ['label' => 'Blog', 'url' => '/blog'],
                ['label' => 'Customer Spotlight', 'url' => '/customer-spotlight'],
                ['label' => 'Philanthropic Work', 'url' => '/philanthropic-work'],
                ['label' => 'Customer Feedback', 'url' => '/customer-feedback'],
                ['label' => 'About us', 'url' => '/about'],
                ['label' => 'Contact', 'url' => '/contact'],
            ];

            $col1Raw = SiteSetting::getValue('footer_col1_links');
            $col1Decoded = !empty($col1Raw) ? json_decode($col1Raw, true) : null;
            $col1Links = is_array($col1Decoded) ? $col1Decoded : $defaultCol1Links;

            $col2Raw = SiteSetting::getValue('footer_col2_links');
            $col2Decoded = !empty($col2Raw) ? json_decode($col2Raw, true) : null;
            $col2Links = is_array($col2Decoded) ? $col2Decoded : $defaultCol2Links;

            $view->with([
                'siteLogo'          => SiteSetting::getValue('logo_light', '/media/b3ca13-kg-lockup-v2.png'),
                'siteLogoDark'      => SiteSetting::getValue('logo_dark',  '/media/b3ca13-kg-lockup-v2.png'),
                'siteName'          => SiteSetting::getValue('site_name',  'Khan Gadget'),
                'siteSlogan'        => SiteSetting::getValue('site_slogan', 'Brand NEW Intact BOX, Without BOX & Pre-Owned'),
                'siteDescription'   => SiteSetting::getValue('site_description', 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.'),
                'sitePhone'         => SiteSetting::getValue('site_phone', '+8801700000000'),
                // Used by WhatsApp ordering now and by the live-chat integration later.
                'whatsappNumber'    => SiteSetting::getValue('whatsapp_number', '8801700000001'),
                'liveChatEnabled'   => SiteSetting::getValue('live_chat_enabled', '1'),
                'liveChatWhatsappEnabled' => SiteSetting::getValue('live_chat_whatsapp_enabled', '1'),
                'liveChatWhatsappNumber' => SiteSetting::getValue('live_chat_whatsapp_number', SiteSetting::getValue('whatsapp_number', '8801700000001')),
                'liveChatMessengerEnabled' => SiteSetting::getValue('live_chat_messenger_enabled', '0'),
                'liveChatMessengerUrl' => SiteSetting::getValue('live_chat_messenger_url', ''),
                'liveChatCallEnabled' => SiteSetting::getValue('live_chat_call_enabled', '1'),
                'liveChatCallNumber' => SiteSetting::getValue('live_chat_call_number', SiteSetting::getValue('site_phone', '+8801700000000')),
                'liveChatToggleColor' => SiteSetting::getValue('live_chat_toggle_color', '#24272c'),
                'liveChatWhatsappColor' => SiteSetting::getValue('live_chat_whatsapp_color', '#25D366'),
                'liveChatMessengerColor' => SiteSetting::getValue('live_chat_messenger_color', '#0084FF'),
                'liveChatCallColor' => SiteSetting::getValue('live_chat_call_color', '#4f46e5'),
                'siteEmail'         => SiteSetting::getValue('site_email', 'khangadget.bd@gmail.com'),
                'siteAddress'       => SiteSetting::getValue('site_address', 'Level 4, House 12, Road 5, Dhanmondi, Dhaka 1205, Bangladesh'),
                'siteBusinessHours' => SiteSetting::getValue('site_business_hours', 'Sat – Thu · 10:00 AM – 9:00 PM'),
                'mobileMenuContact'     => SiteSetting::getValue('mobile_menu_contact', '+8801341246152'),
                'mobileDrawerStoreInfo' => SiteSetting::getValue('mobile_drawer_store_info', 'Elephant Road Branch: Shop No: 136 | Ground Floor | Computer City Center (Ex Multiplan Center) | New Elephant Road, Dhaka-1205'),
                'socialFacebook'    => SiteSetting::getValue('social_facebook', 'https://facebook.com/khansgadget'),
                'socialInstagram'   => SiteSetting::getValue('social_instagram', 'https://bikroy.com/en/shops/khangadgets'),
                'socialWhatsapp'    => SiteSetting::getValue('social_whatsapp', 'https://bdstall.com/stall/2373'),
                'socialYoutube'     => SiteSetting::getValue('social_youtube', 'https://youtube.com/@khansgadget'),
                'footerCopyright'   => SiteSetting::getValue('footer_copyright', 'Khan Gadget. All rights reserved.'),
                'footerCol1Active'  => SiteSetting::getValue('footer_col1_active', '1'),
                'footerCol1Title'   => SiteSetting::getValue('footer_col1_title', 'SHOP'),
                'footerCol1Links'   => $col1Links,
                'footerCol2Active'  => SiteSetting::getValue('footer_col2_active', '1'),
                'footerCol2Title'   => SiteSetting::getValue('footer_col2_title', 'EXPLORE'),
                'footerCol2Links'   => $col2Links,
                'storeLocations'    => $storeLocations,
            ]);
        });
    }
}
