<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\WeatherReading;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WeatherReading>
 */
class WeatherReadingFactory extends Factory
{
    protected $model = WeatherReading::class;

    public function definition(): array
    {
        $descriptions = [
            ['text' => 'Солнечно', 'icon' => '☀️'],
            ['text' => 'Облачно', 'icon' => '⛅'],
            ['text' => 'Пасмурно', 'icon' => '☁️'],
            ['text' => 'Дождь', 'icon' => '🌧️'],
        ];

        $variant = $this->faker->randomElement($descriptions);

        return [
            'temperature' => $this->faker->randomFloat(1, -20, 35),
            'humidity' => $this->faker->numberBetween(30, 95),
            'pressure' => $this->faker->numberBetween(990, 1030),
            'wind_speed' => $this->faker->randomFloat(1, 0, 15),
            'description' => $variant['text'],
            'icon' => $variant['icon'],
            'observed_at' => $this->faker->dateTimeBetween('-7 days', 'now'),
            'user_id' => User::factory(),
        ];
    }
}
