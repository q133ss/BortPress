<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

# TODO

// Админка

# / TODO

Route::get('/roles', [App\Http\Controllers\RoleController::class, 'roles']);
Route::get('/roles/all', [App\Http\Controllers\RoleController::class, 'all']);
Route::post('/register', [App\Http\Controllers\RegisterController::class, 'register']);
Route::post('/login', [App\Http\Controllers\LoginController::class, 'login']);

Route::get('/types', [App\Http\Controllers\TypeController::class, 'index']);
Route::get('/regions', [\App\Http\Controllers\RegionController::class, 'index']);
Route::get('/pay-formats', [App\Http\Controllers\PayFormatController::class, 'index']);

// Уникальные предложения
Route::get('/index/unique', [\App\Http\Controllers\IndexController::class, 'uniques']);
Route::get('/index/catalog/{type}', [\App\Http\Controllers\IndexController::class, 'catalog']);

Route::get('/adv/offers', [App\Http\Controllers\OfferController::class, 'index']);
Route::get('/adv/offers/{id}', [App\Http\Controllers\OfferController::class, 'show']);

Route::get('/requests', [App\Http\Controllers\RequestController::class, 'index']);
Route::get('/requests/{id}', [App\Http\Controllers\OfferController::class, 'show']);

Route::get('/unique/offers', [\App\Http\Controllers\OfferController::class, 'unique']);

Route::group(['middleware' => ['auth:sanctum','blockCheck']],function (){
    Route::get('/subscribe/check', [App\Http\Controllers\SubscribeController::class, 'check']);

    Route::get('/me', [App\Http\Controllers\ProfileController::class, 'index']);
    Route::post('/me', [App\Http\Controllers\ProfileController::class, 'update']);

    Route::get('/profile/archive', [App\Http\Controllers\ProfileController::class, 'archive']);

    Route::get('/show/phone/{ad_id}', [\App\Http\Controllers\OfferController::class, 'showPhone']);

    // ЛК Продавца
    Route::group(['prefix' => 'platform'],function (){
        Route::post('/offer', [App\Http\Controllers\Platform\OfferController::class, 'create']);
        Route::get('/offers', [App\Http\Controllers\Platform\OfferController::class, 'index']);
        Route::get('/offer/{id}', [App\Http\Controllers\Platform\OfferController::class, 'show']);
        Route::post('/offer/update/{id}', [App\Http\Controllers\Platform\OfferController::class, 'update']);
        Route::delete('/offer/delete/{id}', [App\Http\Controllers\Platform\OfferController::class, 'delete']);
    });

    Route::group(['prefix' => 'buyer'], function (){
        Route::get('/archive/offers', [App\Http\Controllers\Buyer\OfferController::class, 'archive']);
        Route::get('/offers', [App\Http\Controllers\Buyer\OfferController::class, 'index']);
        Route::post('/offer', [App\Http\Controllers\Buyer\OfferController::class, 'create']);
        Route::get('/offer/{id}', [App\Http\Controllers\Buyer\OfferController::class, 'show']);
        Route::post('/offer/update/{id}', [App\Http\Controllers\Buyer\OfferController::class, 'update']);
        Route::delete('/offer/{id}', [App\Http\Controllers\Buyer\OfferController::class, 'delete']);
    });

    Route::post('/chat/create/{adv_id}', [App\Http\Controllers\ChatController::class, 'create']);
    Route::get('/chats', [App\Http\Controllers\ChatController::class, 'index']);
    Route::get('/messages/{chat_id}', [App\Http\Controllers\ChatController::class, 'messages']);
    Route::post('/chat/send/{chat_id}', [App\Http\Controllers\ChatController::class, 'send']);
});

Route::prefix('admin')->middleware(['auth:sanctum', \App\Http\Middleware\IsAdmin::class])->group(function (){
    Route::apiResource('platform',\App\Http\Controllers\Admin\PlatformController::class)->except('update');
    Route::post('platform/update/{platform}', [\App\Http\Controllers\Admin\PlatformController::class, 'update']);
    Route::get('/adv', [\App\Http\Controllers\Admin\AdvController::class, 'index']);

    Route::post('/ad/{id}', [\App\Http\Controllers\Admin\AdController::class, 'update']);
});

Route::post('/feedback', [\App\Http\Controllers\FeedbackController::class, 'store']);

Route::get('/item/categories', [\App\Http\Controllers\ItemController::class, 'categories']);
Route::get('/items/{category_id}', [\App\Http\Controllers\ItemController::class, 'items']);

Route::get('/role/admin', [\App\Http\Controllers\RoleController::class, 'admin']);
