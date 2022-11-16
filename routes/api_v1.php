<?php

use App\Http\Controllers\Api\v1\AttachmentsController;
use App\Http\Controllers\Api\v1\LikesController;
use App\Http\Controllers\Api\v1\MessagesController;
use App\Http\Controllers\Api\v1\PostsController;
use App\Http\Controllers\Api\v1\ProfileController;
use App\Http\Controllers\Api\v1\StaticController;
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

Route::get("static", [StaticController::class, 'static']);

Route::controller(UsersController::class)->prefix("users")->group(function () {
    Route::post('signup', 'signup');
    Route::middleware('auth:sanctum')->get('self', 'getUser');
    Route::post('login', 'login');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('profiles', ProfileController::class)->except(["destroy", "store"]);
    Route::controller(ProfileController::class)->prefix("profiles")->group(function () {
        Route::post('addPicture', 'addPicture');

        Route::prefix("{profile}")->group(function () {
            Route::post('subscribe', [SubscriptionController::class, 'subscribe']);
            Route::delete('unsubscribe', [SubscriptionController::class, 'unsubscribe']);

            Route::apiResource("posts", PostsController::class);
            Route::apiResource("posts.attachments", AttachmentsController::class)->except("show", "index", "update");
            Route::apiResource("messages", MessagesController::class)->except("update", "show");

            Route::get("posts/feed", [PostsController::class, "feed"]);

            Route::prefix("posts/{post}/likes")->controller(LikesController::class)->group(function () {
                Route::get("", 'index');
                Route::post("", 'toggle');
            });
        });
    });

    Route::prefix("subscriptions")->controller(SubscriptionController::class)->group(function () {
        Route::post('{id}', 'updateStatus');
        Route::delete('{id}', 'removeSubscriber');

        Route::get('', 'subscriptions');
    });

    Route::get('subscribers', [SubscriptionController::class, 'subscribers']);
    Route::get("feed", [PostsController::class, 'feed']);
});



