<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\WeatherRecord;
use App\Services\Fixtures\WeatherFixtureSeeder;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function __invoke(WeatherFixtureSeeder $seeder)
    {
        $autofilled = 0;
        $count = 0;
        $lastRecord = null;

        try {
            $pdo = DB::connection()->getPdo();
            $autofilled = $seeder->seedIfBelow($pdo, 50, 60);
            $count = WeatherRecord::query()->count();
            $lastRecord = WeatherRecord::query()->orderByDesc('created_at')->value('created_at');
        } catch (\Throwable $e) {
            return view('stats', [
                'dbError' => $e->getMessage(),
                'autofilled' => 0,
                'count' => 0,
                'lastRecord' => null,
            ]);
        }

        return view('stats', [
            'dbError' => null,
            'autofilled' => $autofilled,
            'count' => $count,
            'lastRecord' => $lastRecord,
        ]);
    }
}
