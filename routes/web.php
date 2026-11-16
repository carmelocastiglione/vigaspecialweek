<?php

use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

// Login con email
Route::view('/login/email', 'pages.auth.login-email')->name('auth.email');

// Login con Google
Route::get('/auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google.redirect');
Route::get('/auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');
});

Route::middleware(['auth', 'verified', 'role:student'])->group(function () {
    Route::livewire('/student/activities', 'pages::student.activities')->name('student.activities');
    Route::livewire('/student/activities/{activity}', 'pages::student.activity-details')->name('student.activities.show');
});

Route::middleware(['auth', 'verified', 'role:teacher'])->group(function () {
    Route::livewire('/teacher/classes', 'pages::teacher.classes')->name('teacher.classes');
    Route::livewire('/teacher/classes/{assignment}', 'pages::teacher.class-details')->whereNumber('assignment')->name('teacher.classes.show');
    Route::livewire('/teacher/activities', 'pages::teacher.activities')->name('teacher.activities');
});

Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->group(function () {
    // Impostazioni amministrative
    Route::livewire('/settings', 'pages::settings.admin.index')->name('settings.admin.index');
    // Gestione Utenti
    Route::livewire('/users', 'pages::users.index')->name('users.index');
    Route::livewire('/users/create', 'pages::users.create')->name('users.create');
    Route::livewire('/users/{user}/edit', 'pages::users.edit')->name('users.edit');
    Route::livewire('/assignments', 'pages::assignments.index')->name('assignments.index');
    // Gestione Dipartimenti
    Route::livewire('/departments', 'pages::departments.index')->name('departments.index');
    Route::livewire('/departments/create', 'pages::departments.create')->name('departments.create');
    Route::livewire('/departments/{department}/edit', 'pages::departments.edit')->name('departments.edit');
    // Gestione Categorie
    Route::livewire('/categories', 'pages::categories.index')->name('categories.index');
    Route::livewire('/categories/create', 'pages::categories.create')->name('categories.create');
    Route::livewire('/categories/{category}/edit', 'pages::categories.edit')->name('categories.edit');
    // Gestione Materie
    Route::livewire('/subjects', 'pages::subjects.index')->name('subjects.index');
    Route::livewire('/subjects/create', 'pages::subjects.create')->name('subjects.create');
    Route::livewire('/subjects/{subject}/edit', 'pages::subjects.edit')->name('subjects.edit');
    // Gestione Aule
    Route::livewire('/classrooms', 'pages::classrooms.index')->name('classrooms.index');
    Route::livewire('/classrooms/create', 'pages::classrooms.create')->name('classrooms.create');
    Route::livewire('/classrooms/{classroom}/edit', 'pages::classrooms.edit')->name('classrooms.edit');
    // Gestione Classi
    Route::livewire('/school-classes', 'pages::school-classes.index')->name('school-classes.index');
    Route::livewire('/school-classes/create', 'pages::school-classes.create')->name('school-classes.create');
    Route::livewire('/school-classes/{schoolClass}/edit', 'pages::school-classes.edit')->name('school-classes.edit');
    // Gestione Attività
    Route::livewire('/activities', 'pages::activities.index')->name('activities.index');
    Route::livewire('/activities/create', 'pages::activities.create')->name('activities.create');
    Route::livewire('/activities/{activity}/edit', 'pages::activities.edit')->name('activities.edit');
    Route::livewire('/likes', 'pages::likes.index')->name('admin.likes');
});

// require __DIR__.'/settings.php';
