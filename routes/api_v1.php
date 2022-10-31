<?php

use App\Http\Controllers\Api\v1\ProfileController;
use App\Http\Controllers\Api\v1\SubscriptionController;
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

Route::controller(UsersController::class)->prefix("users")->group(function () {
    Route::post('signup', 'signup');
    Route::middleware('auth:sanctum')->get('self', 'getUser');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(ProfileController::class)->prefix("profiles")->group(function () {
        Route::post('addPicture', 'addPicture');
        Route::post('', 'updateProfile');
        Route::get('', 'index');

        Route::controller(SubscriptionController::class)->prefix("{profile}")->group(function () {
            Route::post('subscribe', 'subscribe');
            Route::delete('unsubscribe', 'unsubscribe');
        });
    });

    Route::prefix("subscriptions")->controller(SubscriptionController::class)->group(function () {
        Route::post('{id}', 'updateStatus');
        Route::delete('{id}', 'removeSubscriber');

        Route::get('', 'subscriptions');
    });

    Route::get('subscribers', [SubscriptionController::class, 'subscribers']);
});



