<?php

use App\Http\Controllers\Web\FixturesController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\PreferencesController;
use App\Http\Controllers\Web\StatsController;
use App\Http\Controllers\Web\UploadFormController;
use App\Http\Controllers\Web\WeatherFormController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/stats', StatsController::class)->name('stats');

Route::post('/preferences', [PreferencesController::class, 'store'])->name('preferences.store');
Route::post('/weather', [WeatherFormController::class, 'store'])->name('weather.store');

Route::post('/uploads', [UploadFormController::class, 'store'])->name('uploads.store');
Route::delete('/uploads/{id}', [UploadFormController::class, 'destroy'])->name('uploads.destroy');
Route::get('/uploads/{id}/download', [UploadFormController::class, 'download'])->name('uploads.download');

Route::post('/fixtures/fill', [FixturesController::class, 'fill'])->name('fixtures.fill');
Route::post('/fixtures/reset', [FixturesController::class, 'reset'])->name('fixtures.reset');
