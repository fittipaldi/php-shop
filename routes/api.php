<?php

use app\Http\Controllers\Api\V1\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::get('/store', [StoreController::class, 'getStore']);

    Route::post('/add-store', [StoreController::class, 'createStore']);
})->middleware('AuthApi');
