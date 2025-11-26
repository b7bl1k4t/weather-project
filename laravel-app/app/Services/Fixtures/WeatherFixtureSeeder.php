<?php

namespace App\Services\Fixtures;

use Faker\Factory;
use Faker\Generator;
use PDO;

class WeatherFixtureSeeder
{
    private Generator $faker;

    private array $conditions = [
        ['icon' => '☀️', 'description' => 'Солнечно'],
        ['icon' => '⛅', 'description' => 'Переменная облачность'],
        ['icon' => '☁️', 'description' => 'Пасмурно'],
        ['icon' => '🌧️', 'description' => 'Дождь'],
        ['icon' => '🌦️', 'description' => 'Кратковременный дождь'],
        ['icon' => '⛈️', 'description' => 'Гроза'],
        ['icon' => '❄️', 'description' => 'Снег'],
        ['icon' => '🌫️', 'description' => 'Туман'],
    ];

    public function __construct(?Generator $faker = null)
    {
        $this->faker = $faker ?? Factory::create('ru_RU');
    }

    public function seed(PDO $pdo, int $count = 60, bool $reset = false): int
    {
        if ($count < 1) {
            return 0;
        }

        if ($reset) {
            $pdo->exec('TRUNCATE TABLE weather_data RESTART IDENTITY');
        }

        $plan = $this->buildBuckets($count);
        $inserted = 0;

        $stmt = $pdo->prepare(
            'INSERT INTO weather_data 
            (temperature, humidity, pressure, wind_speed, description, icon, created_at) 
            VALUES (:temperature, :humidity, :pressure, :wind_speed, :description, :icon, :created_at)'
        );

        foreach ($plan as [$from, $to, $bucketCount]) {
            for ($i = 0; $i < $bucketCount; $i++) {
                $payload = $this->makePayload($from, $to);
                $stmt->execute($payload);
                $inserted++;
            }
        }

        return $inserted;
    }

    public function seedWindowIfBelow(PDO $pdo, string $from, string $to, int $minimum, int $batchSize): int
    {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM weather_data WHERE created_at BETWEEN :from AND :to');
        $stmt->execute([
            ':from' => (new \DateTimeImmutable($from))->format('Y-m-d H:i:s'),
            ':to' => (new \DateTimeImmutable($to))->format('Y-m-d H:i:s'),
        ]);
        $current = (int) $stmt->fetchColumn();

        if ($current >= $minimum) {
            return 0;
        }

        return $this->seedRange($pdo, max($batchSize, $minimum - $current), $from, $to);
    }

    public function seedIfBelow(PDO $pdo, int $minimum = 50, int $batchSize = 60): int
    {
        $current = (int) $pdo->query('SELECT COUNT(*) FROM weather_data')->fetchColumn();

        if ($current >= $minimum) {
            return 0;
        }

        return $this->seed($pdo, max($batchSize, $minimum - $current));
    }

    private function seedRange(PDO $pdo, int $count, string $from, string $to): int
    {
        $stmt = $pdo->prepare(
            'INSERT INTO weather_data 
            (temperature, humidity, pressure, wind_speed, description, icon, created_at) 
            VALUES (:temperature, :humidity, :pressure, :wind_speed, :description, :icon, :created_at)'
        );

        for ($i = 0; $i < $count; $i++) {
            $payload = $this->makePayloadBetween($from, $to);
            $stmt->execute($payload);
        }

        return $count;
    }

    private function buildBuckets(int $count): array
    {
        $day = max(10, (int) ceil($count * 0.35));
        $week = max(15, (int) ceil($count * 0.35));
        $month = max(0, $count - $day - $week);

        return [
            ['-24 hours', 'now', $day],
            ['-7 days', '-1 day', $week],
            ['-30 days', '-7 days', $month],
        ];
    }

    private function makePayload(string $from, string $to): array
    {
        return $this->makePayloadBetween($from, $to);
    }

    private function makePayloadBetween(string $from, string $to): array
    {
        $condition = $this->faker->randomElement($this->conditions);

        return [
            'temperature' => round($this->faker->randomFloat(2, -25, 38), 2),
            'humidity' => $this->faker->numberBetween(35, 100),
            'pressure' => $this->faker->numberBetween(980, 1038),
            'wind_speed' => round($this->faker->randomFloat(2, 0, 18), 2),
            'description' => $condition['description'],
            'icon' => $condition['icon'],
            'created_at' => $this->faker->dateTimeBetween($from, $to)->format('Y-m-d H:i:s'),
        ];
    }
}
