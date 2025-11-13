<?php

namespace App\Repositories;

use App\Models\WeatherReading;
use Illuminate\Support\Collection;

class EloquentWeatherRepository implements WeatherRepositoryInterface
{
    public function latest(): ?WeatherReading
    {
        return WeatherReading::query()
            ->latest('observed_at')
            ->first();
    }

    public function history(int $limit = 5): Collection
    {
        return WeatherReading::query()
            ->orderByDesc('observed_at')
            ->limit($limit)
            ->get();
    }

    public function store(array $attributes): WeatherReading
    {
        return WeatherReading::query()->create($attributes);
    }
}
