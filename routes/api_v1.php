<?php

use App\Http\Controllers\Api\v1\ProfileController;
use App\Http\Controllers\Api\v1\UsersController;
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

Route::controller(UsersController::class)->prefix("/users/")->group(function () {
    Route::post('signup', 'signup');
    Route::middleware('auth:sanctum')->get('self', 'getUser');
    Route::post('login', 'login');
});

Route::controller(ProfileController::class)->prefix("/profile/")->group(function () {
    Route::middleware('auth:sanctum')->post('addPicture', 'addPicture');
    Route::middleware('auth:sanctum')->post('', 'updateProfile');
});

