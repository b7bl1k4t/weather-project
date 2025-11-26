<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Fixtures\WeatherFixtureSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FixturesController extends Controller
{
    public function fill(Request $request, WeatherFixtureSeeder $seeder): RedirectResponse
    {
        $count = max(10, min((int) $request->input('count', 60), 500));
        $minimum = max(1, min((int) $request->input('minimum', 50), $count));

        $pdo = DB::connection()->getPdo();
        $inserted = $seeder->seedIfBelow($pdo, $minimum, $count);

        $message = $inserted === 0
            ? 'Фикстуры уже есть: достаточно данных.'
            : sprintf('Добавлено %d демо-записей.', $inserted);

        return redirect()->route('stats')->with('status', $message);
    }

    public function reset(Request $request, WeatherFixtureSeeder $seeder): RedirectResponse
    {
        $count = max(10, min((int) $request->input('count', 80), 500));

        $pdo = DB::connection()->getPdo();
        $inserted = $seeder->seed($pdo, $count, true);

        return redirect()->route('stats')->with('status', sprintf('Таблица пересоздана, добавлено %d записей.', $inserted));
    }
}
