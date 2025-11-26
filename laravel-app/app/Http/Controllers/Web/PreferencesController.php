<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\PreferencesRequest;
use Illuminate\Http\RedirectResponse;

class PreferencesController extends Controller
{
    public function store(PreferencesRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $request->session()->put('preferences', $data);

        $cookieOptions = [
            'minutes' => 60 * 24 * 30,
        ];

        return redirect()
            ->route('home')
            ->with('status', 'Настройки сохранены.')
            ->withCookies([
                cookie('weather_login', $data['login'], $cookieOptions['minutes'], '/'),
                cookie('weather_theme', $data['theme'], $cookieOptions['minutes'], '/'),
                cookie('weather_language', $data['language'], $cookieOptions['minutes'], '/'),
            ]);
    }
}
