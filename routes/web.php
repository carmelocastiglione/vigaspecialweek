<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::view('/login/email', 'pages.auth.login-email')->name('auth.email');
Route::view('/google', 'pages.auth.google')->name('auth.google');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
