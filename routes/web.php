<?php

use App\Http\Controllers\Auth\ForgotPasswordWithCodeController;
use App\Http\Controllers\Auth\VerifyEmailCodeController;
use App\Http\Controllers\Auth\VerifyPasswordResetCodeController;
use App\Http\Controllers\HomeRedirectController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth'])->get('/home', HomeRedirectController::class)->name('home.redirect');

Route::post('email/verify/code', VerifyEmailCodeController::class)
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.code');

Route::middleware(['guest', 'throttle:5,1'])->group(function () {
    Route::post('forgot-password/send', [ForgotPasswordWithCodeController::class, 'store'])
        ->name('password.code.send');

    Route::get('forgot-password/verify', [VerifyPasswordResetCodeController::class, 'create'])
        ->name('password.code.verify');

    Route::post('forgot-password/verify', [VerifyPasswordResetCodeController::class, 'store'])
        ->name('password.code.verify.store');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::customer.dashboard')->name('dashboard');
    Route::livewire('menu', 'pages::customer.menu')->name('customer.menu');
    Route::livewire('checkout', 'pages::customer.checkout')->name('customer.checkout');
    Route::livewire('orders', 'pages::customer.orders')->name('customer.orders');
    Route::livewire('orders/{order}', 'pages::customer.order-detail')->name('customer.order-detail');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('dashboard', 'pages::admin.dashboard')->name('dashboard');
    Route::livewire('categories', 'pages::admin.categories.index')->name('categories.index');
    Route::livewire('products', 'pages::admin.products.index')->name('products.index');
    Route::livewire('orders', 'pages::admin.orders.index')->name('orders.index');
    Route::livewire('reports', 'pages::admin.reports.index')->name('reports.index');
    Route::livewire('business-settings', 'pages::admin.business-settings')->name('business-settings');
});

require __DIR__.'/settings.php';
