<?php

use App\Http\Controllers\Api\V1\StoreController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\ValidateToken;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->middleware(ValidateToken::class)->group(function () {
    Route::get('/store', [StoreController::class, 'getStore']);

    Route::post('/add-store', [StoreController::class, 'createStore']);
});
