<?php

namespace App\Providers;

use App\Repositories\EloquentWeatherRepository;
use App\Repositories\WeatherRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            WeatherRepositoryInterface::class,
            EloquentWeatherRepository::class,
        );
    }
}
