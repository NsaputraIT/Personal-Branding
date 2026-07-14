<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'guest.home')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::livewire('admin/basecolorguest', 'pages::admin.basecolorguest')->name('admin.basecolorguest');
    Route::livewire('admin/site', 'pages::admin.site')->name('admin.site');
    Route::livewire('admin/medsos', 'pages::admin.medsos')->name('admin.medsos');
});

Route::prefix('portfolio')->name('portfolio.')->group(function () {
    Route::view('/', 'guest.home')->name('home');
    Route::view('/portfolio-details', 'guest.portfolio-details')->name('details');
    Route::view('/service-details', 'guest.service-details')->name('service-details');
    Route::view('/starter', 'guest.starter')->name('starter');
});

require __DIR__.'/settings.php';
