<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\SiteSetting;

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
        // Share site settings (logo, name) with ALL frontend blade views
        View::composer('pages.*', function ($view) {
            $view->with([
                'siteLogo'      => SiteSetting::getValue('logo_light', '/media/b3ca13-kg-lockup-v2.png'),
                'siteLogoDark'  => SiteSetting::getValue('logo_dark',  '/media/b3ca13-kg-lockup-v2.png'),
                'siteName'      => SiteSetting::getValue('site_name',  'Khan Gadget'),
                'siteSlogan'    => SiteSetting::getValue('site_slogan', 'Eternal Tech Companion'),
            ]);
        });

        // Auto-inject single product page button script and CSS
        View::composer('*', function ($view) {
            // Shared CSS for single product pages & static product cards
        });
    }

}
