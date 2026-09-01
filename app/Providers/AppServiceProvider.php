<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Models\StoreLocation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        // Share Stock & Price Disclaimer Notice settings ONLY with relevant views
        View::composer(['pages.product.detail', 'pages.cart', 'admin.home-settings.index'], function ($view) {
            $view->with([
                'stockPriceNoticeActive' => SiteSetting::getValue('stock_price_notice_active', '1'),
                'stockPriceNoticeText' => SiteSetting::getValue('stock_price_notice_text', 'অর্ডার করার পূর্বে স্টক ও প্রাইজ কমতে বাড়তে পারে'),
                'stockPriceNoticeType' => SiteSetting::getValue('stock_price_notice_type', 'warning'),
                'productTrustBadgesActive' => SiteSetting::getValue('product_trust_badges_active', '1'),
            ]);
        });

        // Share home-delivery shipping fee settings ONLY with the checkout page
        View::composer('pages.checkout', function ($view) {
            $view->with([
                'shippingFeeInsideDhaka' => (int) SiteSetting::getValue('shipping_fee_inside_dhaka', 70),
                'shippingFeeOutsideDhaka' => (int) SiteSetting::getValue('shipping_fee_outside_dhaka', 130),
                'codAdvanceNoticeActive' => SiteSetting::getValue('checkout_cod_notice_active', '1'),
                'codAdvanceNoticeText' => SiteSetting::getValue('checkout_cod_notice_text', 'অর্ডার কনফার্ম করার জন্য ন্যূনতম ২,০০০ টাকা অগ্রিম পেমেন্ট করতে হবে।'),
            ]);
        });

        // Share site & footer settings and store locations with ALL views dynamically
        View::composer('*', function ($view) {
            $storeLocations = StoreLocation::activeOrdered();

            // Do not inject fallback navigation: the footer only displays links
            // explicitly configured by the administrator.
            $defaultCol1Links = [];
            $defaultCol2Links = [];

            $col1Raw = SiteSetting::getValue('footer_col1_links');
            $col1Decoded = ! empty($col1Raw) ? json_decode($col1Raw, true) : null;
            $col1Links = is_array($col1Decoded) ? $col1Decoded : $defaultCol1Links;

            $col2Raw = SiteSetting::getValue('footer_col2_links');
            $col2Decoded = ! empty($col2Raw) ? json_decode($col2Raw, true) : null;
            $col2Links = is_array($col2Decoded) ? $col2Decoded : $defaultCol2Links;

            $col3Raw = SiteSetting::getValue('footer_col3_links');
            $col3Decoded = ! empty($col3Raw) ? json_decode($col3Raw, true) : null;
            $col3Links = is_array($col3Decoded) ? $col3Decoded : [];

            // Mobile slide menu's "Info Links" section (About Us, Contact, Privacy &
            // Policy, ...) — admin-editable via Settings, defaulting to the site's
            // original static link set so behavior is unchanged until edited.
            $defaultMobileDrawerInfoLinks = [
                ['label' => 'About Us', 'url' => '/about', 'icon' => 'info'],
                ['label' => 'Contact', 'url' => '/contact', 'icon' => 'phone'],
                ['label' => 'Privacy & Policy', 'url' => '/privacy-policy', 'icon' => 'shield'],
                ['label' => 'Terms & Conditions', 'url' => '/terms-conditions', 'icon' => 'file-text'],
                ['label' => 'Complain / Advice', 'url' => '/complain-advice', 'icon' => 'alert-triangle'],
            ];
            $mobileDrawerInfoLinksRaw = SiteSetting::getValue('mobile_drawer_info_links');
            $mobileDrawerInfoLinksDecoded = ! empty($mobileDrawerInfoLinksRaw) ? json_decode($mobileDrawerInfoLinksRaw, true) : null;
            $mobileDrawerInfoLinks = is_array($mobileDrawerInfoLinksDecoded) ? $mobileDrawerInfoLinksDecoded : $defaultMobileDrawerInfoLinks;

            $view->with([
                'siteLogo' => SiteSetting::getValue('logo_light', '/media/b3ca13-kg-lockup-v2.png'),
                'siteLogoDark' => SiteSetting::getValue('logo_dark', '/media/logo_dark_1786184552.png'),
                'siteFavicon' => SiteSetting::getValue('site_favicon', '/favicon.png'),
                'siteShareImage' => SiteSetting::getValue('site_share_image', '/media/b3ca13-kg-lockup-v2.png'),
                'siteName' => SiteSetting::getValue('site_name', 'Khan Gadget'),
                'siteSlogan' => SiteSetting::getValue('site_slogan', 'Brand NEW Intact BOX, Without BOX & Pre-Owned'),
                'siteDescription' => SiteSetting::getValue('site_description', 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.'),
                'sitePhone' => SiteSetting::getValue('site_phone', '+8801700000000'),
                'footerPhoneLinkType' => SiteSetting::getValue('footer_phone_link_type', 'tel'),
                // Used by WhatsApp ordering now and by the live-chat integration later.
                'whatsappNumber' => SiteSetting::getValue('whatsapp_number', '8801700000001'),
                'liveChatEnabled' => SiteSetting::getValue('live_chat_enabled', '1'),
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
                'siteEmail' => SiteSetting::getValue('site_email', 'khangadget.bd@gmail.com'),
                'siteAddress' => SiteSetting::getValue('site_address', 'Level 4, House 12, Road 5, Dhanmondi, Dhaka 1205, Bangladesh'),
                'siteBusinessHours' => SiteSetting::getValue('site_business_hours', 'Sat – Thu · 10:00 AM – 9:00 PM'),
                'mobileMenuContact' => SiteSetting::getValue('mobile_menu_contact', '+8801341246152'),
                'mobileDrawerStoreInfo' => SiteSetting::getValue('mobile_drawer_store_info', 'Elephant Road Branch: Shop No: 136 | Ground Floor | Computer City Center (Ex Multiplan Center) | New Elephant Road, Dhaka-1205'),
                'socialFacebook' => SiteSetting::getValue('social_facebook', 'https://facebook.com/khansgadget'),
                'socialInstagram' => SiteSetting::getValue('social_instagram', 'https://bikroy.com/en/shops/khangadgets'),
                'socialWhatsapp' => SiteSetting::getValue('social_whatsapp', 'https://bdstall.com/stall/2373'),
                'socialYoutube' => SiteSetting::getValue('social_youtube', 'https://youtube.com/@khansgadget'),
                'socialDaraz' => SiteSetting::getValue('social_daraz', 'https://daraz.com.bd/shop/ki2kz4ne'),
                'footerCopyright' => SiteSetting::getValue('footer_copyright', 'Khan Gadget. All rights reserved.'),
                'footerCol1Active' => SiteSetting::getValue('footer_col1_active', '1'),
                'footerCol1Title' => SiteSetting::getValue('footer_col1_title', 'SHOP'),
                'footerCol1Links' => $col1Links,
                'footerCol2Active' => SiteSetting::getValue('footer_col2_active', '1'),
                'footerCol2Title' => SiteSetting::getValue('footer_col2_title', 'EXPLORE'),
                'footerCol2Links' => $col2Links,
                'footerCol3Active' => SiteSetting::getValue('footer_col3_active', '0'),
                'footerCol3Title' => SiteSetting::getValue('footer_col3_title', 'MORE'),
                'footerCol3Links' => $col3Links,
                'mobileDrawerInfoLinks' => $mobileDrawerInfoLinks,
                'storeLocations' => $storeLocations,
            ]);
        });
    }
}
