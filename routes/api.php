<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;

/*
| Khan Gadget — API Routes
| সব ডেটা JSON হিসেবে পাঠায়
*/

Route::prefix('v1')->group(function () {
    // Products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);

    // Categories
    Route::get('/categories', [ProductController::class, 'categories']);
    Route::get('/categories/{slug}/products', [ProductController::class, 'byCategory']);

    // Conditions
    Route::get('/conditions', [ProductController::class, 'conditions']);
    Route::get('/conditions/{slug}/products', [ProductController::class, 'byCondition']);

    // Brands
    Route::get('/brands', [ProductController::class, 'brands']);

    // Blog
    Route::get('/blog', [ProductController::class, 'blogIndex']);
    Route::get('/blog/{slug}', [ProductController::class, 'blogShow']);

    // Social proof
    Route::get('/testimonials', [ProductController::class, 'testimonials']);
    Route::get('/feedbacks', [ProductController::class, 'feedbacks']);
    Route::get('/spotlights', [ProductController::class, 'spotlights']);
    Route::get('/philanthropic-works', [ProductController::class, 'philanthropicWorks']);

    // Site
    Route::get('/hero-sliders', [ProductController::class, 'heroSliders']);
    Route::get('/settings', [ProductController::class, 'settings']);
});
