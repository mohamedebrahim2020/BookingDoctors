<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SpecializationController;
use App\Http\Controllers\WorkingDayController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('doctor/login',[DoctorController::class, 'login'])->name('doctorLogin');
Route::apiResource('specializations', SpecializationController::class)->only(['index']);
Route::post('doctor/register',[DoctorController::class, 'register'])->name('doctorRegister');
Route::group(['middleware' => ['auth:admin']], function () {
    Route::apiResource('doctors', DoctorController::class)->only('index','show');
});
Route::group(['middleware' => ['auth:doctor','EnsureDoctorIsActivated']], function () {
    Route::apiResource('workingdays', WorkingDayController::class)->only('store');
    Route::post('/appointments/{appointment}/approve', [AppointmentController::class, 'approve'])->name('appointments.approve');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::post('/appointments/{appointment}/reject', [AppointmentController::class, 'reject'])->name('appointments.reject');
    Route::post('/appointments/{appointment}/completed', [AppointmentController::class, 'complete'])->name('appointments.complete');
    Route::get('/appointments/reset', [AppointmentController::class, 'reset'])->name('appointments.reset');
    Route::apiResource('/appointments', AppointmentController::class)->only(['index', 'show'])->names([
        'show' => 'appointments.doctor.show'
    ]);
    Route::prefix('doctor')->group(function () {
        Route::get('profile', [DoctorController::class, 'profile'])->name('doctor.profile');
        Route::post('change/password', [DoctorController::class, 'changePassword'])->name('doctor.changePassword');
        Route::post('device/token', [DoctorController::class, 'storeDeviceToken'])->name('doctor.storeDeviceToken');
        Route::post('reviews/{review}/respond', [ReviewController::class, 'respond'])->name('reviews.respond');
    });
});




