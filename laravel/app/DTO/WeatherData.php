<?php

namespace App\DTO;

use App\Models\WeatherReading;
use Carbon\CarbonImmutable;

class WeatherData
{
    public function __construct(
        public readonly float $temperature,
        public readonly int $humidity,
        public readonly int $pressure,
        public readonly float $windSpeed,
        public readonly string $description,
        public readonly string $icon,
        public readonly ?CarbonImmutable $observedAt = null,
        public readonly ?int $userId = null,
        public readonly ?int $id = null,
    ) {
    }

    public static function fromModel(WeatherReading $reading): self
    {
        return new self(
            temperature: $reading->temperature,
            humidity: $reading->humidity,
            pressure: $reading->pressure,
            windSpeed: $reading->wind_speed,
            description: $reading->description,
            icon: $reading->icon,
            observedAt: $reading->observed_at?->toImmutable(),
            userId: $reading->user_id,
            id: $reading->id,
        );
    }

    public static function fromArray(array $payload): self
    {
        return new self(
            temperature: (float) $payload['temperature'],
            humidity: (int) $payload['humidity'],
            pressure: (int) $payload['pressure'],
            windSpeed: (float) $payload['wind_speed'],
            description: $payload['description'],
            icon: $payload['icon'],
            observedAt: isset($payload['observed_at'])
                ? CarbonImmutable::parse($payload['observed_at'])
                : null,
            userId: $payload['user_id'] ?? null,
            id: $payload['id'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'temperature' => $this->temperature,
            'humidity' => $this->humidity,
            'pressure' => $this->pressure,
            'wind_speed' => $this->windSpeed,
            'description' => $this->description,
            'icon' => $this->icon,
            'observed_at' => $this->observedAt?->toDateTimeString(),
            'user_id' => $this->userId,
        ];
    }
}
