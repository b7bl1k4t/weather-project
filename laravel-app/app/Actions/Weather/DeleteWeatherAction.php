<?php

namespace App\Actions\Weather;

use App\Models\WeatherRecord;

class DeleteWeatherAction
{
    public function handle(int $id): void
    {
        $record = WeatherRecord::query()->findOrFail($id);
        $record->delete();
    }
}
