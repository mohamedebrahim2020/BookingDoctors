<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\LoginController;
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

Route::post('admin/login',[LoginController::class, 'adminLogin'])->name('adminLogin');
Route::group(['middleware' => ['auth:admin']], function () {
    Route::apiResource('admins', AdminController::class);
    Route::get('admin/permissions', [AdminController::class,'getPermissions']);    
});
