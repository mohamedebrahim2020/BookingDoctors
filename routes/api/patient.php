<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\PatientController;
use Illuminate\Http\Request;
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
        Route::apiResource('/appointments', AppointmentController::class)->only('show')->names([
            'show' => 'appointments.patient.show'
        ]);
        Route::get('profile', [PatientController::class, 'profile'])->name('patient.profile');
        Route::post('change/password', [PatientController::class, 'changePassword'])->name('patient.changePassword');
        Route::post('device/token', [PatientController::class, 'storeDeviceToken'])->name('patient.storeDeviceToken');
    });
    Route::group(['middleware' => ['customthrottle:3,30']], function () {
        Route::post('code/resend', [PatientController::class, 'codeResend'])->name('codeResend');
    });
});



