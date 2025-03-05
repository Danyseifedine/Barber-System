<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Landing page
Route::get('/', function () {
    return view('welcome');
});

// Auth routes
Auth::routes(['verify' => true]);

// Logout route
Route::middleware(['auth'])->group(function () {
    Route::get('/logout', [LoginController::class, 'logout'])->name('logout');
});

// Contact route
Route::post('/contact', [HomeController::class, 'contact'])->name('contact');

// Services route
Route::get('/services', [HomeController::class, 'services'])->name('services');

// Appointment routes
Route::middleware(['auth'])->group(function () {
    Route::post('/appointments', [App\Http\Controllers\AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments', [App\Http\Controllers\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}/confirmation', [App\Http\Controllers\AppointmentController::class, 'confirmation'])->name('appointments.confirmation');
    Route::post('/appointments/{appointment}/cancel', [App\Http\Controllers\AppointmentController::class, 'cancel'])->name('appointments.cancel');
});

Route::middleware(['verified'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    // Dashboard routes
    Route::middleware(['role:admin'])->group(function () {
        include __DIR__ . DIRECTORY_SEPARATOR . 'dashboard.php';
    });
});
