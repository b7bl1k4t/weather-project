<?php

namespace App\Actions\Weather;

use App\Models\WeatherRecord;
use Illuminate\Support\Collection;

class ListWeatherAction
{
    public function handle(int $limit = 20): Collection
    {
        $limit = max(1, min($limit, 100));

        return WeatherRecord::query()
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }
}
