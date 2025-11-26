<?php

use App\Http\Controllers\Api\ChartController;
use App\Http\Controllers\Api\UploadController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WeatherController;
use Illuminate\Support\Facades\Route;

Route::get('/weather', [WeatherController::class, 'index']);
Route::get('/weather/{id}', [WeatherController::class, 'show'])->whereNumber('id');
Route::post('/weather', [WeatherController::class, 'store']);
Route::match(['put', 'patch'], '/weather/{id}', [WeatherController::class, 'update'])->whereNumber('id');
Route::delete('/weather/{id}', [WeatherController::class, 'destroy'])->whereNumber('id');

Route::get('/users', [UserController::class, 'index']);
Route::get('/users/{id}', [UserController::class, 'show'])->whereNumber('id');
Route::post('/users', [UserController::class, 'store']);
Route::match(['put', 'patch'], '/users/{id}', [UserController::class, 'update'])->whereNumber('id');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->whereNumber('id');

Route::get('/uploads', [UploadController::class, 'index']);
Route::post('/uploads', [UploadController::class, 'store']);
Route::delete('/uploads/{id}', [UploadController::class, 'destroy']);
Route::get('/uploads/{id}/download', [UploadController::class, 'download']);

Route::get('/charts/{chart}', [ChartController::class, 'show'])
    ->whereIn('chart', ['daily', 'weekly', 'monthly']);
