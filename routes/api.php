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

Route::get("videos/", [VideoController::class, "index"])->name("videos");


Route::middleware('cache')->group(function () {
    /**
     * Base Routes (V1)
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
     * Addresses (V1)
     */
    Route::name('api.')->prefix('address/')->group(function () {
        Route::get('regions/', [AddressController::class, 'regions'])->name('regions');
        Route::get('districts/{id}/', [AddressController::class, 'districts'])->name('districts');
    });
});

/**
 * -------------------------------------------------------------------------
 * Public helpers / misc
 * -------------------------------------------------------------------------
 */
Route::get('update-fcm', [AuthController::class, 'updateFcm']);

Route::middleware('auth:sanctum')->group(function () {
    /**
     * Like (V1 authenticated)
     */
    Route::post('save/', [LikeController::class, 'index']);
    Route::post("video-like/", [VideoController::class, "like"])->name("like");
    Route::get('save/', [LikeController::class, 'get']);
    Route::delete('save/{id}/', [LikeController::class, 'remove'])
        ->where(['id' => '[0-9]+']);

    /**
     * Baskets (V1 authenticated)
     */
    Route::post('basket/', [BasketController::class, 'index'])->name('api:basket-create');
    Route::get('basket/', [BasketController::class, 'get'])->name('api:basket-get');
    Route::delete('basket/{id}/', [BasketController::class, 'remove'])->name('api:basket-remove')
        ->where(['id' => '[0-9]+']);
    Route::post('basket/{id}/edit-count/', [BasketController::class, 'editCount'])
        ->where(['id' => '[0-9]+']);
    Route::delete('basket/clear/', [BasketController::class, 'clear'])->name('api:clear');

    /**
     * Orders (V1 authenticated)
     */
    Route::post('order/', [OrderController::class, 'index']);
    Route::get('order/', [OrderController::class, 'get']);
    Route::delete('order/{id}/', [OrderController::class, 'cancel'])
        ->where(['id' => '[0-9]+']);
});


Route::prefix('v2')->group(function () {

    // Public cached routes (V2)
    Route::middleware('cache')->group(function () {
        Route::get('videos/', [\App\Http\Controllers\Api\V2\VideoController::class, 'index']);

        Route::get('delivery/', [\App\Http\Controllers\Api\V2\DeliveryController::class, 'index']);
        Route::get('banners/', [\App\Http\Controllers\Api\V2\BannerController::class, 'index']);
        Route::get('categories/', [\App\Http\Controllers\Api\V2\CategoryController::class, 'index']);
        Route::get('subcategories/{id}/', [\App\Http\Controllers\Api\V2\CategoryController::class, 'subCategory']);
        Route::get('category-products/', [\App\Http\Controllers\Api\V2\CategoryController::class, 'categoryProducts']);

        // products (V2) — returns variant/options structure
        Route::get('products/', [\App\Http\Controllers\Api\V2\ProductController::class, 'index'])->name('api.v2.products');
        Route::post('products/filter/', [\App\Http\Controllers\Api\V2\ProductController::class, 'filter'])->name('api.v2.products-filter');
        Route::get('products/{id}/is_already/', [\App\Http\Controllers\Api\V2\ProductController::class, 'is_already']);
        Route::get('products/{id}/', [\App\Http\Controllers\Api\V2\ProductController::class, 'view'])->name('api.v2.product');
        Route::get('getMeta/{id}/', [\App\Http\Controllers\Api\V2\ProductController::class, 'getMeta']);
        Route::get('brands/', [\App\Http\Controllers\Api\V2\BrandController::class, 'index'])->name('api.v2.brands');
    });

    // Authenticated V2 endpoints
    // Route::middleware('auth:sanctum')->group(function () {
    //     Route::post('save/', [\App\Http\Controllers\Api\V2\LikeController::class, 'index']);
    //     Route::get('save/', [\App\Http\Controllers\Api\V2\LikeController::class, 'get']);
    //     Route::delete('save/{id}/', [\App\Http\Controllers\Api\V2\LikeController::class, 'remove'])
    //         ->where(['id' => '[0-9]+']);

    //     Route::post('basket/', [\App\Http\Controllers\Api\V2\BasketController::class, 'index']);
    //     Route::get('basket/', [\App\Http\Controllers\Api\V2\BasketController::class, 'get']);
    //     Route::delete('basket/{id}/', [\App\Http\Controllers\Api\V2\BasketController::class, 'remove'])
    //         ->where(['id' => '[0-9]+']);
    //     Route::post('basket/{id}/edit-count/', [\App\Http\Controllers\Api\V2\BasketController::class, 'editCount'])
    //         ->where(['id' => '[0-9]+']);
    //     Route::delete('basket/clear/', [\App\Http\Controllers\Api\V2\BasketController::class, 'clear']);

    //     Route::post('order/', [\App\Http\Controllers\Api\V2\OrderController::class, 'index']);
    //     Route::get('order/', [\App\Http\Controllers\Api\V2\OrderController::class, 'get']);
    //     Route::delete('order/{id}/', [\App\Http\Controllers\Api\V2\OrderController::class, 'cancel'])
    //         ->where(['id' => '[0-9]+']);
    // });
});

require base_path('routes/api_v2.php');