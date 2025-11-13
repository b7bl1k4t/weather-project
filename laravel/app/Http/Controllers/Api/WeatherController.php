<?php

namespace App\Http\Controllers\Api;

use App\DTO\WeatherData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWeatherRequest;
use App\Http\Resources\WeatherResource;
use App\Services\WeatherService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WeatherController extends Controller
{
    public function __construct(
        private readonly WeatherService $weatherService
    ) {
    }

    public function current(): JsonResponse
    {
        $current = $this->weatherService->current();

        if (! $current) {
            return response()->json(['message' => 'Нет данных'], 404);
        }

        return (new WeatherResource($current))
            ->response();
    }

    public function history(): ResourceCollection
    {
        return WeatherResource::collection(
            $this->weatherService->history(10)
        );
    }

    public function store(StoreWeatherRequest $request): JsonResponse
    {
        $payload = WeatherData::fromArray($request->validated());
        $resource = new WeatherResource($this->weatherService->record($payload));

        return $resource
            ->response()
            ->setStatusCode(201);
    }
}
