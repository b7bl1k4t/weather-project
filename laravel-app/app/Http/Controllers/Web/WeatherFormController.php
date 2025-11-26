<?php

namespace App\Http\Controllers\Web;

use App\Actions\Weather\CreateWeatherAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\WeatherStoreRequest;
use Illuminate\Http\RedirectResponse;

class WeatherFormController extends Controller
{
    public function store(WeatherStoreRequest $request, CreateWeatherAction $action): RedirectResponse
    {
        $action->handle($request->validated());

        return redirect()->route('home')->with('status', 'Данные о погоде добавлены.');
    }
}
