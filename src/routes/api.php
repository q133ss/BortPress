<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
 * TODO
 *
 * - ПРОВЕРКА КОМПАНИИ ?? НУЖЕН АПИ ФНС
 *
 * + СОЗДАНИЕ ПРЕДЛОЖЕНИЙ
 * + СПИСОК ПРЕДЛОЖЕНИЙ
 * + ДЕТАЛКА ПРЕДЛОЖЕНИЯ
 * + СОЗДАНИЕ ЗАПРОСОВ
 * + СПИСОК ЗАПРОСОВ
 * + ДЕТАЛКА ЗАПРОСА
 * + АРХИВ ОБЪЯВЛЕНИЙ
 * + УВЕДОМЛЕНИЯ
 * - ПОДПИСКА
 * + ЧАТ
 * - АДМИНКА
 *
 * TODO
 */

Route::get('/roles', [App\Http\Controllers\RoleController::class, 'roles']);
Route::post('/register', [App\Http\Controllers\RegisterController::class, 'register']);
Route::post('/login', [App\Http\Controllers\LoginController::class, 'login']);

Route::get('/types', [App\Http\Controllers\TypeController::class, 'index']);

Route::get('/adv/offers', [App\Http\Controllers\OfferController::class, 'index']);
Route::get('/adv/offers/{id}', [App\Http\Controllers\OfferController::class, 'show']);

Route::get('/requests', [App\Http\Controllers\RequestController::class, 'index']);
Route::get('/requests/{id}', [App\Http\Controllers\OfferController::class, 'show']);

Route::group(['middleware' => ['auth:sanctum','blockCheck']],function (){
    Route::get('/me', [App\Http\Controllers\ProfileController::class, 'index']);
    Route::post('/me', [App\Http\Controllers\ProfileController::class, 'update']);

    Route::get('/profile/archive', [App\Http\Controllers\ProfileController::class, 'archive']);

    Route::get('/pay-formats', [App\Http\Controllers\PayFormatController::class, 'index']);
    // ЛК Продавца
    Route::group(['prefix' => 'platform'],function (){
        Route::post('/offer', [App\Http\Controllers\Platform\OfferController::class, 'create']);
        Route::post('/offer/update/{id}', [App\Http\Controllers\Platform\OfferController::class, 'update']);
    });

    Route::group(['prefix' => 'buyer'], function (){
        Route::post('/offer', [App\Http\Controllers\Buyer\OfferController::class, 'create']);
        Route::post('/offer/update/{id}', [App\Http\Controllers\Buyer\OfferController::class, 'update']);
    });

    Route::post('/chat/create/{adv_id}', [App\Http\Controllers\ChatController::class, 'create']);
    Route::get('/chats', [App\Http\Controllers\ChatController::class, 'index']);
    Route::get('/messages/{chat_id}', [App\Http\Controllers\ChatController::class, 'messages']);
    Route::post('/chat/send/{chat_id}', [App\Http\Controllers\ChatController::class, 'send']);
});

Route::prefix('admin')->middleware(['auth:sanctum', \App\Http\Middleware\IsAdmin::class])->group(function (){
    Route::apiResource('platform',\App\Http\Controllers\Admin\PlatformController::class)->except('update');
    Route::post('platform/{platform}', [\App\Http\Controllers\Admin\PlatformController::class, 'update']);
    Route::get('/adv', [\App\Http\Controllers\Admin\AdvController::class, 'index']);

    Route::post('/ad/{id}', [\App\Http\Controllers\Admin\AdController::class, 'update']);
});
