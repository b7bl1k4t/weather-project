<?php

namespace App\Actions\Weather;

use App\Models\WeatherRecord;
use Illuminate\Support\Carbon;

class CreateWeatherAction
{
    public function handle(array $payload): WeatherRecord
    {
        $data = $this->normalize($payload);

        $record = new WeatherRecord($data);
        $record->created_at = $data['created_at'] ?? Carbon::now();
        $record->save();

        return $record;
    }

    private function normalize(array $payload): array
    {
        $payload['temperature'] = round((float) ($payload['temperature'] ?? 0), 2);
        $payload['wind_speed'] = round((float) ($payload['wind_speed'] ?? 0), 2);

        return $payload;
    }
}
