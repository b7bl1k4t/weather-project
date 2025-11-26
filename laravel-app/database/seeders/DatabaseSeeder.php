<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\Fixtures\WeatherFixtureSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (!User::query()->where('username', 'admin')->exists()) {
            User::create([
                'username' => 'admin',
                'email' => 'admin@weather.local',
                'password' => Hash::make('password'),
            ]);
        }

        /** @var WeatherFixtureSeeder $seeder */
        $seeder = app(WeatherFixtureSeeder::class);
        $pdo = DB::connection()->getPdo();
        $seeder->seedIfBelow($pdo, 50, 60);
    }
}
