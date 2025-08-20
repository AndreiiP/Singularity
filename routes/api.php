<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SpaceController;
use App\Http\Controllers\PaymentController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/nasa/apod', [SpaceController::class, 'apod']);
Route::get('/nasa/mars/{rover}/{date}', [SpaceController::class, 'mars']);
Route::get('/nasa/search', [SpaceController::class, 'search']);

Route::post('/pay', [PaymentController::class, 'pay']);
