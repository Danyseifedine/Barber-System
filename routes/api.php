<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/check-availability', [AppointmentController::class, 'checkAvailability']);
Route::get('/business-hours', [AppointmentController::class, 'getBusinessHours']);

// Add new login API route
Route::post('/login', [LoginController::class, 'apiLogin']);

// Add route for getting user appointments - protected by auth:sanctum middleware
Route::middleware('auth:sanctum')->get('/appointments', [AppointmentController::class, 'getUserAppointments']);

// Add route for updating user profile
Route::middleware('auth:sanctum')->post('/profile/update', [UserController::class, 'updateProfile']);

// Add route for getting all services
Route::get('/services', [ServiceController::class, 'getAllServices']);

// Add route for creating a new appointment
Route::middleware('auth:sanctum')->post('/appointmentStore', [AppointmentController::class, 'apiStore']);

// Add route for cancelling an appointment
Route::middleware('auth:sanctum')->post('/appointments/{id}/cancel', [AppointmentController::class, 'apiCancel']);
