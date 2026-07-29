<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Khan Gadget — Public Frontend Routes (Laravel Blade PageController)
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
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
    });
});

require __DIR__.'/auth.php';

// Fallback for general pages (cart, checkout, account etc.)
Route::get('/{page}', [PageController::class, 'page'])->where('page', '[A-Za-z0-9\-]+');
