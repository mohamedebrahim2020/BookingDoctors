<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\RegisterController;
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
Route::apiResource('specializations', SpecializationController::class)->only(['index']);
Route::post('doctor/register',[RegisterController::class, 'doctorRegister'])->name('doctorRegister');

