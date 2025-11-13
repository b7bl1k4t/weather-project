<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WeatherReading;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'email' => 'admin@weather.local',
                'password' => Hash::make('password'),
            ]
        );

        $seedData = [
            ['temperature' => 22.5, 'humidity' => 65, 'pressure' => 1013, 'wind_speed' => 3.2, 'description' => 'Солнечно', 'icon' => '☀️'],
            ['temperature' => 18.3, 'humidity' => 78, 'pressure' => 1010, 'wind_speed' => 4.1, 'description' => 'Облачно', 'icon' => '⛅'],
            ['temperature' => 15.7, 'humidity' => 82, 'pressure' => 1008, 'wind_speed' => 2.5, 'description' => 'Дождь', 'icon' => '🌧️'],
            ['temperature' => 20.1, 'humidity' => 70, 'pressure' => 1012, 'wind_speed' => 3.8, 'description' => 'Солнечно', 'icon' => '☀️'],
            ['temperature' => 16.4, 'humidity' => 85, 'pressure' => 1009, 'wind_speed' => 3.0, 'description' => 'Пасмурно', 'icon' => '☁️'],
        ];

        foreach ($seedData as $index => $payload) {
            $observedAt = Carbon::now()->subHours($index * 6);

            WeatherReading::query()->firstOrCreate(
                [
                    'description' => $payload['description'],
                    'temperature' => $payload['temperature'],
                    'observed_at' => $observedAt,
                ],
                array_merge($payload, [
                    'observed_at' => $observedAt,
                    'user_id' => $admin->id,
                ])
            );
        }
    }
}
