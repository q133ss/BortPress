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

Route::group(['middleware' => 'auth:sanctum'],function (){
    Route::get('/me', [App\Http\Controllers\ProfileController::class, 'index']);
    Route::post('/me', [App\Http\Controllers\ProfileController::class, 'update']);

    // ЛК Продавца
    Route::group(['prefix' => 'platform'],function (){
        Route::post('/offer', [App\Http\Controllers\Platform\OfferController::class, 'create']);
    });
});
