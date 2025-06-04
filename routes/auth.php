<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\GuestController;


/**
 * Register new user
 */
Route::post('register', [AuthController::class, 'register']);


/** 
 * Guest user
 */
Route::prefix("guest")->group(function () {
    Route::post('register', [GuestController::class, 'register']);
    Route::get('get-me', [GuestController::class, 'get_me']);
});

/**
 * Login api
 */
Route::post('login', [AuthController::class, 'login']);

/**
 * Resend otp code
 */
Route::post('resend', [AuthController::class, 'resend']);

/**
 * Reset user password
 */
Route::post('reset', [AuthController::class, 'reset']);

/**
 * Reset password confirm
 */
Route::post('resetConfirm', [AuthController::class, 'resetConfirm']);

/**
 * Set user password
 */
Route::post('setPassword', [AuthController::class, 'setPassword']);

/**
 * Confirm otp code
 */
Route::post('confirm', [AuthController::class, 'confirm']);

/**
 * Get user data
 */
Route::get('me', [AuthController::class, 'me']);

/**
 * Update user data
 */
Route::post('update', [AuthController::class, 'update']);
