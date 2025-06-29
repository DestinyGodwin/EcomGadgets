<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\V1\PaymentController;
use App\Http\Controllers\V1\CategoryController;
use App\Http\Controllers\V1\LocationController;
use App\Http\Controllers\V1\Auth\AuthController;
use App\Http\Controllers\V1\Admin\UserController;
use App\Http\Controllers\V1\Stores\StoreController;
use App\Http\Controllers\V1\Product\ProductController;
use App\Http\Controllers\V1\Admin\SubscriptionPlanController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('v1/')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::controller(AuthController::class)->group(function () {
            Route::post('register', 'store');
            Route::post('login', 'login')->middleware('logged.in');
            Route::post('verify-email', 'verifyEmail')->middleware('auth:sanctum');
            Route::get('resend-otp', 'resendOtp')->middleware('auth:sanctum');
            Route::post('complete-profile', 'completeProfile')->middleware(['auth:sanctum', 'verified.otp']);
            Route::post('update-profile', 'updateProfile')->middleware(['auth:sanctum', 'verified.otp']);
            Route::post('change-password', 'changePassword')->middleware(['auth:sanctum', 'verified.otp']);
            Route::get('get-profile', 'getProfile')->middleware(['auth:sanctum', 'verified.otp']);
            Route::post('forgot-password', 'forgotPassword');
            Route::post('reset-password', 'resetPassword');
            Route::post('logout', 'logout')->middleware(['auth:sanctum']);
        });
    });
    Route::prefix('/admin')->middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::controller(SubscriptionPlanController::class)->group(function () {
            Route::post('subscriptions', 'store');
            Route::put('subscriptions/{subscription_plan}', 'update');
            Route::delete('subscriptions/{subscription_plan}', 'destroy');
        });
        Route::controller(UserController::class)->group(function () {
            Route::get('users', 'index');
            Route::get('vendors', 'getVendors');
            Route::get('users/search', 'search');
        });
        Route::controller(AdminCategoryController::class)->group(function () {
            Route::post('categories', 'store');
            Route::put('categories/{category}', 'update');
            Route::delete('categories/{category}', 'destroy');
        });
        Route::controller(StoreSubscriptionController::class)->group(function () {
            Route::get('store-subscriptions', 'adminSubscriptions');
            Route::get('store-subscriptions/{subscriptionId}', 'adminSubscription');
            Route::get('store-subs', 'getAll');
        });
        Route::post('send-notifications', [NotifyingController::class, 'send']);
        Route::get('stores/state/{stateId}', [StoreController::class, 'getStoresByState']);
        Route::get('stores/lga/{lgaId}', [StoreController::class, 'getStoresByLga']);
        Route::get('stores/{storeId}/stor', [StoreController::class, 'getStore']);

    });
    Route::middleware('auth:sanctum')->group(function () {

        
        Route::post('/paystack/initialize', [PaystackController::class, 'initialize']);
        Route::get('/paystack/transaction/verify/{reference}', [PaystackController::class, 'verifyPayment']);
        Route::controller(StoreController::class)->group(function () {
            Route::post('stores', 'store');
            Route::get('mystore', 'mystore');
            Route::put('stores/{store}', 'update');
            Route::delete('stores/{store}', 'destroy');
        });
        Route::controller(StoreSubscriptioncontroller::class)->group(function () {
            Route::get('my-subscriptions', 'storeSubscriptions');
            Route::get('my-subscriptions/{subscriptionId}', 'storeSubscription');
        });
        Route::controller(ProductController::class)->group(function () {
            Route::post('products', 'store');
            Route::put('products/{product}', 'update');
            Route::delete('products/{product}', 'destroy');
            Route::get('products/user-state', 'userState');
            Route::get('products/user-lga', 'userLga');

        });
        Route::prefix('notifications')->group(function () {
            Route::controller(NotificationController::class)->group(function () {
                Route::get('/', 'index');
                Route::post('/mark-as-read', 'markAllAsRead');
                Route::post('/{id}/mark-as-read', 'markAsRead');
                Route::delete('/{id}', 'destroy');
            });
        });
    });
    Route::controller(ProductController::class)->group(function () {
        Route::get('products/search', 'search');
        Route::get('products', 'index');
        Route::get('products/category/{categoryId}', 'byCategory');
        Route::get('products/brand/{brand}', 'byBrand');
        Route::get('products/by-state', 'byState');
        Route::get('products/by-lga', 'byLga');
        Route::get('products/{product}', 'show');
    });
    Route::get('stores', [StoreController::class, 'index']);
    Route::get('stores/search', [StoreController::class, 'search']);
    Route::get('stores/{store}', [StoreController::class, 'show']);
    Route::get('categories', [CategoryController::class, 'index']);

    // Route::post('/paystack/webhook', [PaystackController::class, 'webhook']);
    // Route::get('/paystack/callback', [PaystackController::class, 'callback']);
    Route::get('subscriptions', [SubscriptionPlanController::class, 'index']);
    Route::get('subscriptions/{subscription_plan}', [SubscriptionPlanController::class, 'show']);

    Route::get('states', [LocationController::class, 'getStates']);
    Route::get('states/{state}/lgas', [LocationController::class, 'getStateLgas']);

    Route::prefix('/payments')->middleware('auth:sanctum')->group(function () {
    Route::post('/subscribe', [PaymentController::class, 'subscribe']);
    Route::post('/feature-product', [PaymentController::class, 'featureProduct']);
    Route::post('/book-advert', [PaymentController::class, 'bookAdvert']);
});

});
