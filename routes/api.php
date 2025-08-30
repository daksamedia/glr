<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\StatisticController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('verify', [AuthController::class, 'verify']);
    Route::post('activate', [AuthController::class, 'activate']);
    Route::post('forgot', [AuthController::class, 'forgotPassword']);
    Route::post('reset_password', [AuthController::class, 'resetPassword']);
});

Route::get('category/read', [CategoryController::class, 'index']);
Route::get('category/detail', [CategoryController::class, 'show']);

Route::get('product/read', [VendorController::class, 'index']);
Route::get('product/detail', [VendorController::class, 'show']);
Route::get('product/search', [VendorController::class, 'search']);
Route::post('product/search_filter', [VendorController::class, 'searchFilter']);

Route::post('booking/create', [BookingController::class, 'store']);

// Protected routes
Route::middleware('auth.jwt')->group(function () {
    // Auth routes
    Route::prefix('auth')->group(function () {
        Route::get('get_data', [AuthController::class, 'me']);
        Route::post('edit', [AuthController::class, 'updateProfile']);
        Route::post('update_avatar', [AuthController::class, 'updateAvatar']);
        Route::post('update_password', [AuthController::class, 'updatePassword']);
        Route::post('like', [AuthController::class, 'like']);
        Route::post('dislike', [AuthController::class, 'dislike']);
        Route::get('get_likes', [AuthController::class, 'getLikes']);
    });

    // Vendor routes
    Route::prefix('product')->group(function () {
        Route::post('create', [VendorController::class, 'store']);
        Route::post('update', [VendorController::class, 'update']);
        Route::get('me', [VendorController::class, 'me']);
        Route::post('upload_cover', [VendorController::class, 'uploadCover']);
    });

    // Booking routes
    Route::prefix('booking')->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('me', [BookingController::class, 'myBookings']);
            Route::get('detail', [BookingController::class, 'show']);
            Route::post('update', [BookingController::class, 'update']);
        });
        
        Route::prefix('vendor')->group(function () {
            Route::get('me', [BookingController::class, 'vendorBookings']);
            Route::get('detail', [BookingController::class, 'vendorShow']);
            Route::post('update', [BookingController::class, 'vendorUpdate']);
        });
    });

    // Service routes
    Route::prefix('services')->group(function () {
        Route::get('me', [ServiceController::class, 'index']);
        Route::post('create', [ServiceController::class, 'store']);
        Route::post('update', [ServiceController::class, 'update']);
        Route::post('delete', [ServiceController::class, 'destroy']);
    });

    // Rating routes
    Route::prefix('ratings')->group(function () {
        Route::post('create', [RatingController::class, 'store']);
        Route::get('myratings', [RatingController::class, 'myRatings']);
    });

    // Gallery routes
    Route::prefix('product/gallery')->group(function () {
        Route::get('me', [GalleryController::class, 'index']);
        Route::post('create', [GalleryController::class, 'store']);
        Route::post('delete', [GalleryController::class, 'destroy']);
    });

    // Statistics routes
    Route::get('statistic/me', [StatisticController::class, 'me']);
});

// Public rating and service routes
Route::get('ratings/read', [RatingController::class, 'show']);
Route::get('services/read', [ServiceController::class, 'show']);
Route::get('services/detail', [ServiceController::class, 'detail']);
Route::get('product/gallery/read', [GalleryController::class, 'show']);