<?php

namespace App\Actions\Charts;

use App\Services\Charts\WeatherChartGenerator;

class GenerateWeeklyChartAction
{
    public function __construct(private readonly WeatherChartGenerator $generator)
    {
    }

    public function handle(): string
    {
        return $this->generator->generateWeeklyAverages();
    }
}
