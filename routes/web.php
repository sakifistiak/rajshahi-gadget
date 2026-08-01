<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\HeroSliderController as AdminHeroSliderController;
use App\Http\Controllers\Admin\PromoBannerController as AdminPromoBannerController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;

/*
|--------------------------------------------------------------------------
| Khan Gadget — Public Frontend Routes (Laravel Blade PageController)
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/api/search', [PageController::class, 'ajaxSearch'])->name('api.search');
Route::get('/product/{slug}', [PageController::class, 'product'])->name('product');
Route::get('/blog/{slug}', [PageController::class, 'blog'])->name('blog');
Route::get('/shop/{category}', [PageController::class, 'category'])->name('category');

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
        Route::resource('sliders', AdminHeroSliderController::class);
        Route::patch('sliders/{slider}/toggle', [AdminHeroSliderController::class, 'toggle'])->name('sliders.toggle');
        Route::resource('promos', AdminPromoBannerController::class);
        Route::patch('promos/{promo}/toggle', [AdminPromoBannerController::class, 'toggle'])->name('promos.toggle');
        Route::resource('customers', AdminCustomerController::class);
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');

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
