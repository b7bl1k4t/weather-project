<?php

namespace App\Repositories;

use App\Models\WeatherReading;
use Illuminate\Support\Collection;

interface WeatherRepositoryInterface
{
    public function latest(): ?WeatherReading;

    public function history(int $limit = 5): Collection;

    public function store(array $attributes): WeatherReading;
}
