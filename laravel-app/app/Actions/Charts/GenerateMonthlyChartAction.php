<?php

namespace App\Actions\Charts;

use App\Services\Charts\WeatherChartGenerator;

class GenerateMonthlyChartAction
{
    public function __construct(private readonly WeatherChartGenerator $generator)
    {
    }

    public function handle(): string
    {
        return $this->generator->generateMonthlyConditions();
    }
}
