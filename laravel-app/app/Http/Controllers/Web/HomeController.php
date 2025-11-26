<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Models\WeatherRecord;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $preferences = $this->resolvePreferences($request);

        $latest = WeatherRecord::query()
            ->orderByDesc('created_at')
            ->first();

        $history = WeatherRecord::query()
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $uploads = Upload::query()
            ->orderByDesc('created_at')
            ->get();

        return view('home', [
            'preferences' => $preferences,
            'latest' => $latest,
            'history' => $history,
            'uploads' => $uploads,
        ]);
    }

    private function resolvePreferences(Request $request): array
    {
        $allowedThemes = ['light', 'dark', 'contrast'];
        $allowedLanguages = ['ru', 'en', 'es'];

        $preferences = [
            'login' => 'Гость',
            'theme' => 'light',
            'language' => 'ru',
        ];

        $sessionPrefs = $request->session()->get('preferences', []);
        if (is_array($sessionPrefs)) {
            $preferences = array_merge($preferences, array_intersect_key($sessionPrefs, $preferences));
        }

        $cookieLogin = trim((string) $request->cookie('weather_login', ''));
        $cookieTheme = trim((string) $request->cookie('weather_theme', ''));
        $cookieLanguage = trim((string) $request->cookie('weather_language', ''));

        if ($cookieLogin !== '') {
            $preferences['login'] = $cookieLogin;
        }
        if (in_array($cookieTheme, $allowedThemes, true)) {
            $preferences['theme'] = $cookieTheme;
        }
        if (in_array($cookieLanguage, $allowedLanguages, true)) {
            $preferences['language'] = $cookieLanguage;
        }

        return $preferences;
    }
}
