<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'guest.home')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('admin/basecolorguest', 'pages::admin.basecolorguest')
        ->name('admin.basecolorguest');

    Route::livewire('admin/site', 'pages::admin.site')
        ->name('admin.site');

    Route::livewire('admin/medsos', 'pages::admin.medsos')
        ->name('admin.medsos');

    // Skills moved into About page
    Route::redirect('admin/skills', 'admin/about')->name('admin.skills');

    Route::livewire('admin/hero', 'pages::admin.hero')
        ->name('admin.hero');

    Route::livewire('admin/about', 'pages::admin.about')
        ->name('admin.about');

    Route::livewire('admin/contact', 'pages::admin.contact')
        ->name('admin.contact');

    Route::livewire('admin/footer', 'pages::admin.footer')
        ->name('admin.footer');

    Route::livewire('admin/services', 'pages::admin.services')
        ->name('admin.services');

    Route::livewire('admin/portfolio', 'pages::admin.portfolio')
        ->name('admin.portfolio');

    Route::livewire('admin/testimonials', 'pages::admin.testimonials')
        ->name('admin.testimonials');

    Route::livewire('admin/faq', 'pages::admin.faq')
        ->name('admin.faq');

    Route::livewire('admin/resume', 'pages::admin.resume')
        ->name('admin.resume');

    Route::livewire('admin/data-user', 'pages::admin.data-user')
        ->name('admin.data-user');
});

Route::prefix('portfolio')->name('portfolio.')->group(function () {
    Route::view('/', 'guest.home')->name('home');
    Route::view('/portfolio-details', 'guest.portfolio-details')->name('details');
    Route::view('/service-details', 'guest.service-details')->name('service-details');
    Route::view('/starter', 'guest.starter')->name('starter');
});

require __DIR__.'/settings.php';
