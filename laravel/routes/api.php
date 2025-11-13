<?php

use App\Http\Controllers\Api\WeatherController;
use Illuminate\Support\Facades\Route;

Route::prefix('weather')->group(function (): void {
    Route::get('current', [WeatherController::class, 'current']);
    Route::get('history', [WeatherController::class, 'history']);
    Route::post('/', [WeatherController::class, 'store']);
});
