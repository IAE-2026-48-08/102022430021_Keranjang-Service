<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CartController;

Route::prefix('v1')->middleware('api.key')->group(function () {
    Route::get('/carts', [CartController::class, 'index']);
    Route::get('/carts/{id}', [CartController::class, 'show']);
    Route::post('/carts/items', [CartController::class, 'addItem']);
});