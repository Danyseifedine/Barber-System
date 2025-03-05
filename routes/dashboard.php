<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\Pages\UserController;
// datatable controller
use Illuminate\Support\Facades\Route;
// Datatable Controllers
use App\Http\Controllers\Dashboard\Pages\FeedbackController;
use App\Http\Controllers\Dashboard\Pages\PaymentController;
use App\Http\Controllers\Dashboard\Pages\AppointmentServiceController;
use App\Http\Controllers\Dashboard\Pages\AppointmentController;
use App\Http\Controllers\Dashboard\Pages\ServiceController;
use App\Http\Controllers\Dashboard\Pages\BusinessHourController;



Route::prefix('dashboard')->name('dashboard.')->group(function () {

    // Dashboard routes
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/stats', 'getStats')->name('stats');
    });


    // ======================================================================= //
    // ====================== START USER DATATABLE =========================== //
    // ======================================================================= //

    Route::controller(UserController::class)->prefix("users")->name("users.")->group(function () {
        Route::post('/update', 'update')->name('update');
        Route::get('/{id}/show', 'show')->name('show');
        Route::get('/datatable', 'datatable')->name('datatable');
        Route::patch('/{id}/status', 'status')->name('status');
    });
    Route::resource('users', UserController::class)->except(['show', 'update']);

    // ======================================================================= //
    // ====================== END USER DATATABLE ============================= //

    // ======================================================================= //
    // ====================== START BUSINESSHOUR DATATABLE =========================== //
    // ======================================================================= //

    Route::controller(BusinessHourController::class)
        ->prefix('businessHours')
        ->name('businessHours.')
        ->group(function () {
            Route::post('/update', 'update')->name('update');
            Route::get('/{id}/show', 'show')->name('show');
            Route::get('/datatable', 'datatable')->name('datatable');
        });

    Route::resource('businessHours', BusinessHourController::class)
        ->except(['show', 'update']);

    // ======================================================================= //
    // ====================== END BUSINESSHOUR DATATABLE =========================== //
    // ======================================================================= //

    // ======================================================================= //
    // ====================== START SERVICE DATATABLE =========================== //
    // ======================================================================= //

    Route::controller(ServiceController::class)
        ->prefix('services')
        ->name('services.')
        ->group(function () {
            Route::post('/update', 'update')->name('update');
            Route::get('/{id}/show', 'show')->name('show');
            Route::get('/datatable', 'datatable')->name('datatable');
            Route::patch('/{id}/status', 'status')->name('status');
        });

    Route::resource('services', ServiceController::class)
        ->except(['show', 'update']);

    // ======================================================================= //
    // ====================== END SERVICE DATATABLE =========================== //
    // ======================================================================= //

    // ======================================================================= //
    // ====================== START APPOINTMENT DATATABLE =========================== //
    // ======================================================================= //

    Route::controller(AppointmentController::class)
        ->prefix('appointments')
        ->name('appointments.')
        ->group(function () {
            Route::post('/update', 'update')->name('update');
            Route::get('/{id}/show', 'show')->name('show');
            Route::get('/datatable', 'datatable')->name('datatable');
            Route::patch('{status}/{id}/status', 'status')->name('status');

            Route::controller(AppointmentServiceController::class)
                ->prefix('services')
                ->name('services.')
                ->group(function () {
                    Route::post('/update', 'update')->name('update');
                    Route::get('/{id}/show', 'show')->name('show');
                    Route::get('/datatable', 'datatable')->name('datatable');
                });

            Route::resource('services', AppointmentServiceController::class)
                ->except(['show', 'update']);
        });

    Route::resource('appointments', AppointmentController::class)
        ->except(['show', 'update']);

    // ======================================================================= //
    // ==================== END APPOINTMENT DATATABLE ======================== //

    // ======================================================================= //
    // ====================== START PAYMENT DATATABLE =========================== //
    // ======================================================================= //

    Route::controller(PaymentController::class)
        ->prefix('payments')
        ->name('payments.')
        ->group(function () {
            Route::post('/update', 'update')->name('update');
            Route::get('/{id}/show', 'show')->name('show');
            Route::get('/datatable', 'datatable')->name('datatable');
        });

    Route::resource('payments', PaymentController::class)
        ->except(['show', 'update']);

    // ======================================================================= //
    // ====================== END PAYMENT DATATABLE =========================== //
    // ======================================================================= //
    
    // ======================================================================= //
    // ====================== START FEEDBACK DATATABLE =========================== //
    // ======================================================================= //

    Route::controller(FeedbackController::class)
        ->prefix('feedbacks')
        ->name('feedbacks.')
        ->group(function () {
            Route::post('/update', 'update')->name('update');
            Route::get('/{id}/show', 'show')->name('show');
            Route::get('/datatable', 'datatable')->name('datatable');
    });

    Route::resource('feedbacks', FeedbackController::class)
        ->except(['show', 'update']);

    // ======================================================================= //
    // ====================== END FEEDBACK DATATABLE =========================== //
    // ======================================================================= //
// ======================================================================= //


    include __DIR__ . DIRECTORY_SEPARATOR . 'Privileges.php';
});
