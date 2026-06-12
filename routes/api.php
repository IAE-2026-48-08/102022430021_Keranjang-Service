<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\SsoController;

Route::prefix('v1')->group(function () {
    Route::get('/sso/verify', [SsoController::class, 'verify']);
});

Route::prefix('v1')->middleware('api.key')->group(function () {
    Route::get('/carts', [CartController::class, 'index']);
    Route::get('/carts/{id}', [CartController::class, 'show']);
    Route::post('/carts/items', [CartController::class, 'addItem']);
});