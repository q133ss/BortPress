<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
 * TODO
 *
 * - ПРОВЕРКА КОМПАНИИ ?? НУЖЕН АПИ ФНС
 *
 * - СОЗДАНИЕ ОБЪЯВЛЕНИЙ
 * - СПИСОК ОБЪЯВЛЕНИЙ
 * - ДЕТАЛКА ОБЪЯВЛЕНИЯ
 * - АРХИВ ОБЪЯВЛЕНИЙ
 * - УВЕДОМЛЕНИЯ
 * - ПОДПИСКА
 * - ЧАТ
 *
 * TODO
 */

Route::get('/roles', [App\Http\Controllers\RoleController::class, 'roles']);
Route::post('/register', [App\Http\Controllers\RegisterController::class, 'register']);
Route::post('/login', [App\Http\Controllers\LoginController::class, 'login']);

Route::get('/types', [App\Http\Controllers\TypeController::class, 'index']);

Route::group(['middleware' => 'auth:sanctum'],function (){
    Route::get('/me', [App\Http\Controllers\ProfileController::class, 'index']);
    Route::post('/me', [App\Http\Controllers\ProfileController::class, 'update']);

    Route::get('/pay-formats', [App\Http\Controllers\PayFormatController::class, 'index']);
    // ЛК Продавца
    Route::group(['prefix' => 'platform'],function (){
        Route::post('/offer', [App\Http\Controllers\Platform\OfferController::class, 'create']);
    });
});
