<?php

use App\Http\Controllers\HomeRedirectController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->get('/home', HomeRedirectController::class)->name('home.redirect');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::livewire('dashboard', 'pages::customer.dashboard')->name('dashboard');
    Route::livewire('menu', 'pages::customer.menu')->name('customer.menu');
    Route::livewire('checkout', 'pages::customer.checkout')->name('customer.checkout');
    Route::livewire('orders', 'pages::customer.orders')->name('customer.orders');
    Route::livewire('orders/{order}', 'pages::customer.order-detail')->name('customer.order-detail');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::livewire('dashboard', 'pages::admin.dashboard')->name('dashboard');
    Route::livewire('categories', 'pages::admin.categories.index')->name('categories.index');
    Route::livewire('products', 'pages::admin.products.index')->name('products.index');
    Route::livewire('orders', 'pages::admin.orders.index')->name('orders.index');
});

require __DIR__.'/settings.php';
