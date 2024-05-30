<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/roles', [App\Http\Controllers\RoleController::class, 'roles']);
Route::post('/register', [App\Http\Controllers\RegisterController::class, 'register']);
Route::post('/login', [App\Http\Controllers\LoginController::class, 'login']);

Route::group(['middleware' => 'auth:sanctum'],function (){
    Route::get('/me', [App\Http\Controllers\ProfileController::class, 'index']);
    Route::post('/me', [App\Http\Controllers\ProfileController::class, 'update']);
});
