<?php

use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatAgentController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\CustomerFeedbackController;
use App\Http\Controllers\Admin\CustomerSpotlightController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FilterAttributeController;
use App\Http\Controllers\Admin\FlashSaleController;
use App\Http\Controllers\Admin\HeroSliderController as AdminHeroSliderController;
use App\Http\Controllers\Admin\HomeSettingController;
use App\Http\Controllers\Admin\LiveChatSettingController;
use App\Http\Controllers\Admin\MediaController as AdminMediaController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\PhilanthropicWorkController;
use App\Http\Controllers\Admin\PopupOfferController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\PromoBannerController as AdminPromoBannerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\StoreLocationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CustomPageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Khan Gadget — Public Frontend Routes (Laravel Blade PageController)
|--------------------------------------------------------------------------
*/
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/api/search', [PageController::class, 'ajaxSearch'])->name('api.search');
Route::get('/api/compare', [PageController::class, 'compareData'])->name('api.compare');
Route::get('/api/site-fonts', [PageController::class, 'siteFonts'])->name('api.site-fonts');
Route::get('/checkout', [PageController::class, 'checkout'])->name('checkout');
Route::get('/thank-you', [PageController::class, 'thankYou'])->name('thank-you');
Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
Route::get('/orders/{order:order_number}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
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

Route::get('/terms-and-conditions', fn () => redirect('/page/terms-conditions', 301))->name('terms');
Route::get('/terms', fn () => redirect('/page/terms-conditions', 301));
Route::get('/page/{slug}', [CustomPageController::class, 'show'])->name('pages.custom');

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
        Route::delete('/bulk-destroy', [AdminChatController::class, 'bulkDestroy'])->middleware('admin')->name('bulk-destroy');
        Route::get('/{conversation}', [AdminChatController::class, 'show'])->name('show');
        Route::get('/{conversation}/messages', [AdminChatController::class, 'messages'])->name('messages');
        Route::post('/{conversation}/messages', [AdminChatController::class, 'send'])->name('messages.send');
        Route::delete('/{conversation}', [AdminChatController::class, 'destroy'])->middleware('admin')->name('destroy');
    });

    // Admin CRUDs
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::delete('products/bulk-destroy', [AdminProductController::class, 'bulkDestroy'])->name('products.bulk-destroy');
        Route::resource('products', AdminProductController::class);
        Route::resource('categories', CategoryController::class)->except('show');
        Route::resource('brands', BrandController::class)->except('show');
        Route::resource('sliders', AdminHeroSliderController::class);
        Route::patch('sliders/{slider}/toggle', [AdminHeroSliderController::class, 'toggle'])->name('sliders.toggle');
        Route::resource('promos', AdminPromoBannerController::class);
        Route::patch('promos/{promo}/toggle', [AdminPromoBannerController::class, 'toggle'])->name('promos.toggle');
        Route::resource('flash-sales', FlashSaleController::class)->except('show');
        Route::resource('filter-attributes', FilterAttributeController::class)->except('show');
        Route::resource('blog-posts', BlogPostController::class)->except('show');
        Route::resource('customer-spotlights', CustomerSpotlightController::class)->except('show');
        Route::resource('customer-feedbacks', CustomerFeedbackController::class)->except('show');
        Route::resource('philanthropic-works', PhilanthropicWorkController::class)->except('show');
        Route::post('flash-sales/{flashSale}/products', [FlashSaleController::class, 'addProduct'])->name('flash-sales.products.store');
        Route::patch('flash-sales/{flashSale}/products/{flashSaleProduct}', [FlashSaleController::class, 'updateProduct'])->name('flash-sales.products.update');
        Route::delete('flash-sales/{flashSale}/products/{flashSaleProduct}', [FlashSaleController::class, 'removeProduct'])->name('flash-sales.products.destroy');
        Route::resource('customers', AdminCustomerController::class);
        Route::resource('pages', App\Http\Controllers\Admin\CustomPageController::class);
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('live-chat-settings', [LiveChatSettingController::class, 'index'])->name('live-chat-settings.index');
        Route::post('live-chat-settings', [LiveChatSettingController::class, 'update'])->name('live-chat-settings.update');
        Route::get('home-settings', [HomeSettingController::class, 'index'])->name('home-settings.index');
        Route::post('home-settings', [HomeSettingController::class, 'update'])->name('home-settings.update');
        Route::get('popup-offer', [PopupOfferController::class, 'index'])->name('popup-offer.index');
        Route::post('popup-offer', [PopupOfferController::class, 'update'])->name('popup-offer.update');
        Route::resource('store-locations', StoreLocationController::class);

        // Orders
        Route::get('orders', [AdminOrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
        Route::get('orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');
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
