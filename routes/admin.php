<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ColorController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SizeController;
use App\Http\Controllers\Admin\UserController;
use App\Livewire\Admin\Product\Images;
use App\Livewire\Admin\Product\Options;
use App\Livewire\Admin\Settings\DataList;
use App\Livewire\Admin\SizeInfo;
use App\Livewire\Admin\SubCategory;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\LabelPrintController;

Route::middleware('guest')->group(function () {
    Route::get('login/', [LoginController::class, 'index'])->name('login');
    Route::post('login/', [LoginController::class, 'store']);
});

Route::middleware(['auth', 'role:' . RoleEnum::ADMIN])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('order', OrderController::class);
    Route::post('order/{id}/editStatus', [OrderController::class, 'editStatus'])->name('order.edit:status');
    Route::resource('product', ProductController::class);
    Route::resource('category', CategoryController::class);
    Route::resource('size', SizeController::class);
    Route::resource('color', ColorController::class);
    Route::resource('user', UserController::class);
    Route::get('subcategory', SubCategory::class)->name('subcategory');
    Route::get('banners', [BannerController::class, 'index'])->name('banners');

    /**
     * Livewire routes
     */
    Route::get('product/options/{id}', Options::class)->name('product.options');
    Route::get('settings/', DataList::class)->name('settings.index');
    Route::get('size-info/', SizeInfo::class)->name('size.info');
    Route::get('product/images/{id}', Images::class)->name('product.images');

    // 👉 POS (включается только если FEATURE_POS=true)
    // 👉 POS (включается только если FEATURE_POS=true)
    if (config('feature.pos') === true) {
        Route::get('pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('pos/scan', [PosController::class, 'scan'])->name('pos.scan');
        Route::post('pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    }

    // 👉 Печать штрих-кодов

    Route::get('print/barcode/{product}', [LabelPrintController::class, 'one'])->name('print.barcode');
    Route::get('print/barcodes', [LabelPrintController::class, 'many'])->name('print.barcodes');

});
