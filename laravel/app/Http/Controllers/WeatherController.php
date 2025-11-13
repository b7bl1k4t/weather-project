<?php

namespace App\Http\Controllers;

use App\DTO\WeatherData;
use App\Http\Requests\StoreWeatherRequest;
use App\Services\WeatherService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WeatherController extends Controller
{
    public function __construct(
        private readonly WeatherService $weatherService
    ) {
    }

    public function index(): View
    {
        return view('weather.dashboard', [
            'currentWeather' => $this->weatherService->current(),
            'history' => $this->weatherService->history(5),
        ]);
    }

    public function store(StoreWeatherRequest $request): RedirectResponse
    {
        $payload = WeatherData::fromArray($request->validated());
        $this->weatherService->record($payload);

        return redirect()
            ->route('weather.index')
            ->with('status', 'Запись сохранена');
    }
}
