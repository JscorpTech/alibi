<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V2\ProductController;

Route::prefix('v2')->group(function () {
    Route::get('products', [ProductController::class, 'index']);
});