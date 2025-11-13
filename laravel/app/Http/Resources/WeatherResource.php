<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read \App\DTO\WeatherData $resource
 */
class WeatherResource extends JsonResource
{
    /**
     * @param  Request  $request
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->resource->id,
            'temperature' => $this->resource->temperature,
            'humidity' => $this->resource->humidity,
            'pressure' => $this->resource->pressure,
            'wind_speed' => $this->resource->windSpeed,
            'description' => $this->resource->description,
            'icon' => $this->resource->icon,
            'observed_at' => optional($this->resource->observedAt)->toDateTimeString(),
            'user_id' => $this->resource->userId,
        ];
    }
}
