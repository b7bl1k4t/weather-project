<?php

namespace App\Http\Controllers\Api;

use App\Actions\Charts\GenerateDailyChartAction;
use App\Actions\Charts\GenerateMonthlyChartAction;
use App\Actions\Charts\GenerateWeeklyChartAction;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ChartController extends Controller
{
    public function show(string $chart, GenerateDailyChartAction $daily, GenerateWeeklyChartAction $weekly, GenerateMonthlyChartAction $monthly): BinaryFileResponse
    {
        $map = [
            'daily' => $daily,
            'weekly' => $weekly,
            'monthly' => $monthly,
        ];

        if (!isset($map[$chart])) {
            throw new NotFoundHttpException('Chart not found');
        }

        /** @var callable $action */
        $action = $map[$chart];
        $path = $action->handle();

        return response()->file($path, [
            'Content-Type' => 'image/png',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]);
    }
}
