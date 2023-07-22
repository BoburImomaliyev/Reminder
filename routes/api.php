<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::post('register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('login', [App\Http\Controllers\Api\AuthController::class, 'login']);


Route::middleware('auth:sanctum')->group(function () {
    //user - foydalanuvchilar
    Route::get('users', [App\Http\Controllers\Api\UserController::class, 'index']);
    Route::post('users-search', [App\Http\Controllers\Api\UserController::class, 'search']);
    Route::post('user-create', [App\Http\Controllers\Api\UserController::class, 'store']);
    Route::get('user-show/{id}', [App\Http\Controllers\Api\UserController::class, 'show']);

//debt - qarz
    Route::post('debt-create', [App\Http\Controllers\Api\DebtController::class, 'store']);
    Route::post('debt-delete', [App\Http\Controllers\Api\DebtController::class, 'update']);

//statistika
    Route::get('statistics', [App\Http\Controllers\Api\UserController::class, 'statistic']);
});



