<?php

use App\Http\Controllers\AdminController;
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

Route::post('admin/login', [AdminController::class, 'login'])->name('adminLogin');
Route::group(['middleware' => ['auth:admin']], function () {
    Route::apiResource('admins', AdminController::class);
    Route::group(['prefix' => 'admin'], function () {
        Route::get('permissions', [AdminController::class,'getPermissions']);
        Route::delete('reviews/{review}', [ReviewController::class,'delete'])->name('reviews.delete');
        Route::post('doctors/{doctor}/activate', [AdminController::class,'activateDoctor'])->name('activate.doctor');
    });
});
