<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReviewController;
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
Route::prefix('patient')->group(function () {
    Route::post('register', [PatientController::class, 'register'])->name('patientRegister');
    Route::post('verify', [PatientController::class, 'verify'])->name('patientVerify');
    Route::post('login',[PatientController::class, 'login'])->name('patientLogin');
    Route::group(['middleware' => ['auth:patient', 'EnsurePatientIsVerified']], function () {
        Route::apiResource('doctors.appointments', AppointmentController::class)->only(['store']);
        Route::apiResource('doctors', DoctorController::class)->only('index','show')->names([
            'index' => 'doctors.list',
            'show' => 'doctors.details'
        ]); 
        Route::get('/appointments/current', [AppointmentController::class, 'getCurrent'])->name('patient.get.current.appointment');
        Route::post('/appointments/{appointment}/check', [AppointmentController::class, 'checkCurrent'])->name('patient.check.current.appointment');
        Route::apiResource('/appointments', AppointmentController::class)->only('show')->names([
            'show' => 'appointments.patient.show'
        ]);
        Route::get('profile', [PatientController::class, 'profile'])->name('patient.profile');
        Route::post('change/password', [PatientController::class, 'changePassword'])->name('patient.changePassword');
        Route::post('device/token', [PatientController::class, 'storeDeviceToken'])->name('patient.storeDeviceToken');

        Route::get('doctors/{doctor}/reviews', [DoctorController::class, 'getReviews'])->name('get.doctor.reviews');
        Route::apiResource('reviews', ReviewController::class)->only('store')->names([
            'store' => 'patient.reviews.store'
        ]);

    });
    Route::group(['middleware' => ['customthrottle:3,30']], function () {
        Route::post('code/resend', [PatientController::class, 'codeResend'])->name('codeResend');
    });
});



