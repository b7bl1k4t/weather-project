<?php

namespace App\Http\Middleware;

use App\Support\UiSettings;
use Closure;
use Illuminate\Http\Request;

class ShareUiPreferences
{
    public function handle(Request $request, Closure $next)
    {
        $preferences = UiSettings::preferences($request);
        $langKey = $preferences['language'] ?? 'ru';
        $strings = [];

        if (app()->getLocale() !== $langKey) {
            app()->setLocale($langKey);
        }

        $strings = [
            'nav_home' => __('messages.nav_home'),
            'nav_about' => __('messages.nav_about'),
            'nav_stats' => __('messages.nav_stats'),
            'nav_admin' => __('messages.nav_admin'),
        ];

        view()->share([
            'preferences' => $preferences,
            'strings' => $strings,
        ]);

        return $next($request);
    }
}
