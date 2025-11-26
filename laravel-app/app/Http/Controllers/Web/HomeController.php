<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Upload;
use App\Models\WeatherRecord;
use App\Support\UiSettings;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request)
    {
        $preferences = UiSettings::preferences($request);
        $strings = UiSettings::translations('home');

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
            'strings' => $strings,
            'latest' => $latest,
            'history' => $history,
            'uploads' => $uploads,
            'themeNames' => [
                'light' => ['ru' => 'Светлая', 'en' => 'Light', 'es' => 'Clara'],
                'dark' => ['ru' => 'Тёмная', 'en' => 'Dark', 'es' => 'Oscura'],
                'contrast' => ['ru' => 'Контрастная', 'en' => 'High contrast', 'es' => 'Alto contraste'],
            ],
            'languageOptions' => [
                'ru' => 'Русский',
                'en' => 'English',
                'es' => 'Español',
            ],
        ]);
    }
}
