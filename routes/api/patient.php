<?php

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

Route::post('patient/register', [PatientController::class, 'register'])->name('patientRegister');
Route::post('patient/verify', [PatientController::class, 'verify'])->name('patientVerify');
Route::post('patient/login',[PatientController::class, 'login'])->name('patientLogin');


