<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\User\AuthController;
use App\Http\Controllers\Web\User\HomeController;
use App\Livewire\DeleteAccount\Confirm;
use App\Livewire\DeleteAccount\Done;
use App\Livewire\DeleteAccount\Info;
use App\Livewire\DeleteAccount\Phone;
use App\Livewire\Pages\About;
use App\Livewire\Pages\BasePage;
use App\Livewire\User\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/show/{id}', [HomeController::class, 'show'])->name('show');
Route::get('/list/{type}/{id}', [HomeController::class, 'category'])->name('category');

Route::middleware('auth')->group(function () {
    Route::get('/cabinet', [HomeController::class, 'cabinet'])->name('cabinet');
});

Route::get('/auth', Auth::class)->middleware('guest')->name('auth');
Route::any('/logout', [LoginController::class, 'logout'])->name('logout');

/**
 * Livewire routes
 */
Route::get('/page/{page}/', BasePage::class)->name('base:page');
Route::get('/delete-account/', Info::class)->name('delete-account:info');
Route::get('/delete-account/phone/', Phone::class)->name('delete-account:phone');
Route::get('/delete-account/confirm/', Confirm::class)->name('delete-account:confirm');
Route::get('/delete-account/done/', Done::class)->name('delete-account:done');
