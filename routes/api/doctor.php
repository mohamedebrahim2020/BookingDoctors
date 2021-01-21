<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\SpecializationController;
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

Route::group(['middleware' => ['auth:admin']], function () {
    Route::apiResource('doctors', DoctorController::class)->only('index','show');
});

Route::apiResource('specializations', SpecializationController::class)->only(['index']);
Route::post('doctor/register',[DoctorController::class, 'register'])->name('doctorRegister');


