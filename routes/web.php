<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\HeroSliderController as AdminHeroSliderController;
use App\Http\Controllers\Admin\PromoBannerController as AdminPromoBannerController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;

/*
|--------------------------------------------------------------------------
| Khan Gadget — Public Frontend Routes (Laravel Blade PageController)
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/api/search', [PageController::class, 'ajaxSearch'])->name('api.search');
Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');
Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
Route::get('/product/{slug}', [PageController::class, 'product'])->name('product');
Route::get('/blog/{slug}', [PageController::class, 'blog'])->name('blog');
Route::get('/shop/{category}', [PageController::class, 'category'])->name('category');

Route::get('/p/{slug}', [\App\Http\Controllers\CustomPageController::class, 'show'])->name('pages.custom');

/*
|--------------------------------------------------------------------------
| Khan Gadget — Secure Admin Panel (Laravel Breeze Auth Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Override default Breeze dashboard with our statistics dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Admin CRUDs
    Route::prefix('admin')->name('admin.')->group(function () {
        Route::resource('products', AdminProductController::class);
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except('show');
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class)->except('show');
        Route::resource('sliders', AdminHeroSliderController::class);
        Route::patch('sliders/{slider}/toggle', [AdminHeroSliderController::class, 'toggle'])->name('sliders.toggle');
        Route::resource('promos', AdminPromoBannerController::class);
        Route::patch('promos/{promo}/toggle', [AdminPromoBannerController::class, 'toggle'])->name('promos.toggle');
        Route::resource('customers', AdminCustomerController::class);
        Route::resource('pages', \App\Http\Controllers\Admin\CustomPageController::class);
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::get('live-chat-settings', [\App\Http\Controllers\Admin\LiveChatSettingController::class, 'index'])->name('live-chat-settings.index');
        Route::post('live-chat-settings', [\App\Http\Controllers\Admin\LiveChatSettingController::class, 'update'])->name('live-chat-settings.update');
        Route::get('home-settings', [\App\Http\Controllers\Admin\HomeSettingController::class, 'index'])->name('home-settings.index');
        Route::post('home-settings', [\App\Http\Controllers\Admin\HomeSettingController::class, 'update'])->name('home-settings.update');
        Route::resource('store-locations', \App\Http\Controllers\Admin\StoreLocationController::class);

        // Orders
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::delete('orders/{order}', [AdminOrderController::class, 'destroy'])->name('orders.destroy');

        // Media Library
        Route::get('media', [AdminMediaController::class, 'index'])->name('media.index');
        Route::post('media/upload', [AdminMediaController::class, 'upload'])->name('media.upload');
        Route::get('media/list', [AdminMediaController::class, 'list'])->name('media.list');
        Route::delete('media/{filename}', [AdminMediaController::class, 'destroy'])->name('media.destroy');
    });
});

require __DIR__.'/auth.php';

// Fallback for general pages (cart, checkout, account etc.)
Route::get('/{page}', [PageController::class, 'page'])->where('page', '[A-Za-z0-9\-]+');
