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
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\ChatAgentController;

/*
|--------------------------------------------------------------------------
| Khan Gadget — Public Frontend Routes (Laravel Blade PageController)
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/api/search', [PageController::class, 'ajaxSearch'])->name('api.search');
Route::get('/api/site-fonts', [PageController::class, 'siteFonts'])->name('api.site-fonts');
Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');
Route::get('/thank-you', [PageController::class, 'thankYou'])->name('thank-you');
Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store'])->name('orders.store');
Route::middleware('throttle:30,1')->group(function () {
    Route::post('/chat/start', [ChatController::class, 'start'])->name('chat.start');
    Route::get('/chat/messages', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/messages', [ChatController::class, 'send'])->name('chat.messages.send');
});
Route::get('/product/{slug}', [PageController::class, 'product'])->name('product');
Route::get('/blog/load-more', [PageController::class, 'blogLoadMore'])->name('blog.load-more');
Route::get('/blog/{slug}', [PageController::class, 'blog'])->name('blog');
Route::get('/philanthropic-work/{slug}', [PageController::class, 'philanthropicWork'])->name('philanthropic-work');
Route::get('/shop/{category}', [PageController::class, 'category'])->name('category');

Route::get('/page/{slug}', [\App\Http\Controllers\CustomPageController::class, 'show'])->name('pages.custom');

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

    Route::prefix('admin/live-chat')->name('admin.live-chat.')->middleware('chat.access')->group(function () {
        Route::get('/', [AdminChatController::class, 'index'])->name('index');
        Route::get('/list', [AdminChatController::class, 'list'])->name('list');
        Route::get('/unread-count', [AdminChatController::class, 'unreadCount'])->name('unread-count');
        Route::get('/agents', [ChatAgentController::class, 'index'])->middleware('admin')->name('agents.index');
        Route::post('/agents', [ChatAgentController::class, 'store'])->middleware('admin')->name('agents.store');
        Route::patch('/agents/{user}/toggle', [ChatAgentController::class, 'toggle'])->middleware('admin')->name('agents.toggle');
        Route::get('/{conversation}', [AdminChatController::class, 'show'])->name('show');
        Route::get('/{conversation}/messages', [AdminChatController::class, 'messages'])->name('messages');
        Route::post('/{conversation}/messages', [AdminChatController::class, 'send'])->name('messages.send');
    });

    // Admin CRUDs
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::resource('products', AdminProductController::class);
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class)->except('show');
        Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class)->except('show');
        Route::resource('sliders', AdminHeroSliderController::class);
        Route::patch('sliders/{slider}/toggle', [AdminHeroSliderController::class, 'toggle'])->name('sliders.toggle');
        Route::resource('promos', AdminPromoBannerController::class);
        Route::patch('promos/{promo}/toggle', [AdminPromoBannerController::class, 'toggle'])->name('promos.toggle');
        Route::resource('flash-sales', \App\Http\Controllers\Admin\FlashSaleController::class)->except('show');
        Route::resource('filter-attributes', \App\Http\Controllers\Admin\FilterAttributeController::class)->except('show');
        Route::resource('blog-posts', \App\Http\Controllers\Admin\BlogPostController::class)->except('show');
        Route::resource('customer-spotlights', \App\Http\Controllers\Admin\CustomerSpotlightController::class)->except('show');
        Route::resource('customer-feedbacks', \App\Http\Controllers\Admin\CustomerFeedbackController::class)->except('show');
        Route::resource('philanthropic-works', \App\Http\Controllers\Admin\PhilanthropicWorkController::class)->except('show');
        Route::post('flash-sales/{flashSale}/products', [\App\Http\Controllers\Admin\FlashSaleController::class, 'addProduct'])->name('flash-sales.products.store');
        Route::patch('flash-sales/{flashSale}/products/{flashSaleProduct}', [\App\Http\Controllers\Admin\FlashSaleController::class, 'updateProduct'])->name('flash-sales.products.update');
        Route::delete('flash-sales/{flashSale}/products/{flashSaleProduct}', [\App\Http\Controllers\Admin\FlashSaleController::class, 'removeProduct'])->name('flash-sales.products.destroy');
        Route::resource('customers', AdminCustomerController::class);
        Route::resource('pages', \App\Http\Controllers\Admin\CustomPageController::class);
        Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
        Route::get('live-chat-settings', [\App\Http\Controllers\Admin\LiveChatSettingController::class, 'index'])->name('live-chat-settings.index');
        Route::post('live-chat-settings', [\App\Http\Controllers\Admin\LiveChatSettingController::class, 'update'])->name('live-chat-settings.update');
        Route::get('home-settings', [\App\Http\Controllers\Admin\HomeSettingController::class, 'index'])->name('home-settings.index');
        Route::post('home-settings', [\App\Http\Controllers\Admin\HomeSettingController::class, 'update'])->name('home-settings.update');
        Route::get('popup-offer', [\App\Http\Controllers\Admin\PopupOfferController::class, 'index'])->name('popup-offer.index');
        Route::post('popup-offer', [\App\Http\Controllers\Admin\PopupOfferController::class, 'update'])->name('popup-offer.update');
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
