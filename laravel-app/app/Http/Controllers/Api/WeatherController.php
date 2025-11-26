<?php

namespace App\Http\Controllers\Api;

use App\Actions\Weather\CreateWeatherAction;
use App\Actions\Weather\DeleteWeatherAction;
use App\Actions\Weather\ListWeatherAction;
use App\Actions\Weather\ShowWeatherAction;
use App\Actions\Weather\UpdateWeatherAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\WeatherStoreRequest;
use App\Http\Requests\WeatherUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WeatherController extends Controller
{
    public function index(Request $request, ListWeatherAction $action): JsonResponse
    {
        $limit = (int) $request->query('limit', 20);
        $limit = $limit >= 1 && $limit <= 100 ? $limit : 20;

        return response()->json([
            'data' => $action->handle($limit),
        ]);
    }

    public function show(int $id, ShowWeatherAction $action): JsonResponse
    {
        return response()->json([
            'data' => $action->handle($id),
        ]);
    }

    public function store(WeatherStoreRequest $request, CreateWeatherAction $action): JsonResponse
    {
        $created = $action->handle($request->validated());

        return response()->json(['data' => $created], 201);
    }

    public function update(int $id, WeatherUpdateRequest $request, UpdateWeatherAction $action): JsonResponse
    {
        $updated = $action->handle($id, $request->validated());

        return response()->json(['data' => $updated]);
    }

    public function destroy(int $id, DeleteWeatherAction $action): JsonResponse
    {
        $action->handle($id);

        return response()->json(null, 204);
    }
}
