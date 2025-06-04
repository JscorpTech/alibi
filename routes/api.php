<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\V1\AddressController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\BasketController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\DeliveryController;
use App\Http\Controllers\Api\V1\LikeController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\VideoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("videos/",[VideoController::class,"index"])->name("videos");
/**
 * Cached routes
 */
Route::middleware('cache')->group(function () {
    /**
     * Base Routes
     */
    Route::get('delivery/', [DeliveryController::class, 'index']);
    Route::get('banners/', [BannerController::class, 'index']);
    Route::get('categories/', [CategoryController::class, 'index'])->name('api:categories');
    Route::get('subcategories/{id}/', [CategoryController::class, 'subCategory']);
    Route::get('category-products/', [CategoryController::class, 'categoryProducts']);
    Route::get('products/', [ProductController::class, 'index'])->name('api:products');
    Route::get('brands/', [BrandController::class, 'index'])->name('api:brands');
    Route::post('products/filter/', [ProductController::class, 'filter'])->name('api:products-filter');
    Route::get('products/{id}/is_already/', [ProductController::class, 'is_already']);
    Route::get('products/{id}/', [ProductController::class, 'view'])->name('api:category');
    Route::get('getMeta/{id}/', [ProductController::class, 'getMeta'])->name('api:getMeta');

    /**
     * Addresses
     */
    Route::name('api.')->prefix('address/')->group(function () {
        Route::get('regions/', [AddressController::class, 'regions'])->name('regions');
        Route::get('districts/{id}/', [AddressController::class, 'districts'])->name('districts');
    });
});

Route::get('update-fcm', [AuthController::class, 'updateFcm']);
Route::middleware('auth:sanctum')->group(function () {
    /**
     * Like
     */
    Route::post('save/', [LikeController::class, 'index']);
    Route::post("video-like/",[VideoController::class,"like"])->name("like");
    Route::get('save/', [LikeController::class, 'get']);
    Route::delete('save/{id}/', [LikeController::class, 'remove'])
        ->where([
            'id' => '[0-9]+',
        ]);

    /**
     * Baskets
     */
    Route::post('basket/', [BasketController::class, 'index'])->name('api:basket-create');
    Route::get('basket/', [BasketController::class, 'get'])->name('api:basket-get');
    Route::delete('basket/{id}/', [BasketController::class, 'remove'])->name('api:basket-remove')
        ->where([
            'id' => '[0-9]+',
        ]);
    Route::post('basket/{id}/edit-count/', [BasketController::class, 'editCount'])
        ->where([
            'id' => '[0-9]+',
        ]);
    Route::delete('basket/clear/', [BasketController::class, 'clear'])->name('api:clear');

    /**
     * Orders
     */
    Route::post('order/', [OrderController::class, 'index']);
    Route::get('order/', [OrderController::class, 'get']);
    Route::delete('order/{id}/', [OrderController::class, 'cancel'])
        ->where([
            'id' => '[0-9]+',
        ]);
});
