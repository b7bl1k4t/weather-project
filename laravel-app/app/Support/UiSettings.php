<?php

namespace App\Support;

use Illuminate\Http\Request;

class UiSettings
{
    public static function preferences(Request $request): array
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

    public static function translations(string $group): array
    {
        $lang = app()->getLocale();
        return __('messages.' . $group, locale: $lang);
    }
}
