<?php

use App\Http\Controllers\Auth\SocialAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::view('/', 'home')->name('home');

// Login con email
Route::view('/login/email', 'pages.auth.login-email')->name('auth.email');

// Login con Google
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    // Impostazioni amministrative
    Route::livewire('/settings', 'pages::settings.admin.index')->name('settings.admin.index');
    // Gestione Utenti
    Route::livewire('/users', 'pages::users.index')->name('users.index');
    Route::livewire('/users/create', 'pages::users.create')->name('users.create');
    Route::livewire('/users/{user}/edit', 'pages::users.edit')->name('users.edit');
});

// require __DIR__.'/settings.php';
