<?php

namespace App\Actions\Weather;

use App\Models\WeatherRecord;

class ShowWeatherAction
{
    public function handle(int $id): WeatherRecord
    {
        return WeatherRecord::query()->findOrFail($id);
    }
}
