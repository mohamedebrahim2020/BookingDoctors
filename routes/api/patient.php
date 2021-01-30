<?php

use App\Http\Controllers\DoctorAppointmentController;
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
        Route::apiResource('doctors.appointments', DoctorAppointmentController::class)->only(['store']);      
    });
});



