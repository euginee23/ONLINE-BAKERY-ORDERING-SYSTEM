<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::view('dashboard', 'pages.admin.dashboard')->name('dashboard');
    Route::livewire('categories', 'pages::admin.categories.index')->name('categories.index');
    Route::livewire('products', 'pages::admin.products.index')->name('products.index');
});

require __DIR__.'/settings.php';
