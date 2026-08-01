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

            $view->with([
                'siteLogo'          => SiteSetting::getValue('logo_light', '/media/b3ca13-kg-lockup-v2.png'),
                'siteLogoDark'      => SiteSetting::getValue('logo_dark',  '/media/b3ca13-kg-lockup-v2.png'),
                'siteName'          => SiteSetting::getValue('site_name',  'Khan Gadget'),
                'siteSlogan'        => SiteSetting::getValue('site_slogan', 'Brand NEW Intact BOX, Without BOX & Pre-Owned'),
                'siteDescription'   => SiteSetting::getValue('site_description', 'Bangladesh-er trusted destination for Brand new intact box, without box and certified pre-owned gadgets.'),
                'sitePhone'         => SiteSetting::getValue('site_phone', '+8801700000000'),
                'siteEmail'         => SiteSetting::getValue('site_email', 'khangadget.bd@gmail.com'),
                'siteAddress'       => SiteSetting::getValue('site_address', 'Level 4, House 12, Road 5, Dhanmondi, Dhaka 1205, Bangladesh'),
                'siteBusinessHours' => SiteSetting::getValue('site_business_hours', 'Sat – Thu · 10:00 AM – 9:00 PM'),
                'mobileMenuContact' => SiteSetting::getValue('mobile_menu_contact', '+8801341246152'),
                'socialFacebook'    => SiteSetting::getValue('social_facebook', 'https://facebook.com/khansgadget'),
                'socialInstagram'   => SiteSetting::getValue('social_instagram', 'https://bikroy.com/en/shops/khangadgets'),
                'socialWhatsapp'    => SiteSetting::getValue('social_whatsapp', 'https://bdstall.com/stall/2373'),
                'socialYoutube'     => SiteSetting::getValue('social_youtube', 'https://youtube.com/@khansgadget'),
                'footerCopyright'   => SiteSetting::getValue('footer_copyright', 'Khan Gadget. All rights reserved.'),
                'storeLocations'    => $storeLocations,
            ]);
        });
    }
}
