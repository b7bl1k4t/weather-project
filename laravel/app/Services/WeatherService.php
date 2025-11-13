<?php

namespace App\Services;

use App\DTO\WeatherData;
use App\Repositories\WeatherRepositoryInterface;
use Illuminate\Support\Collection;

class WeatherService
{
    public function __construct(
        private readonly WeatherRepositoryInterface $repository
    ) {
    }

    public function current(): ?WeatherData
    {
        $reading = $this->repository->latest();

        return $reading ? WeatherData::fromModel($reading) : null;
    }

    public function history(int $limit = 5): Collection
    {
        return $this->repository
            ->history($limit)
            ->map(static fn ($reading) => WeatherData::fromModel($reading));
    }

    public function record(WeatherData $data): WeatherData
    {
        $payload = $data->toArray();

        if (! $payload['observed_at']) {
            $payload['observed_at'] = now()->toDateTimeString();
        }

        $reading = $this->repository->store($payload);

        return WeatherData::fromModel($reading);
    }
}
