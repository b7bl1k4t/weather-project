<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\WeatherRecord;
use App\Support\UiSettings;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function __invoke()
    {
        $preferences = UiSettings::preferences(request());
        $strings = UiSettings::translations('home');

        $weatherCount = WeatherRecord::query()->count();
        $lastWeather = WeatherRecord::query()->orderByDesc('created_at')->value('created_at');
        $usersCount = (int) DB::table('users')->count();

        return view('admin', [
            'preferences' => $preferences,
            'strings' => $strings,
            'weatherCount' => $weatherCount,
            'lastWeather' => $lastWeather,
            'usersCount' => $usersCount,
        ]);
    }
}
