<?php

namespace App\Actions\Weather;

use App\Models\WeatherRecord;

class UpdateWeatherAction
{
    public function handle(int $id, array $payload): WeatherRecord
    {
        $record = WeatherRecord::query()->findOrFail($id);
        $data = $this->normalize($payload);
        unset($data['created_at']);

        $record->fill($data);
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
