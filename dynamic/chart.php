<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use App\Charts\WeatherChartGenerator;
use App\Fixtures\WeatherFixtureSeeder;
use App\Support\Database;

$chart = strtolower((string) ($_GET['chart'] ?? 'daily'));

try {
    $pdo = Database::makePdo();
    $generator = new WeatherChartGenerator($pdo, new WeatherFixtureSeeder());

    switch ($chart) {
        case 'daily':
            $path = $generator->generateDailyTemperature();
            break;
        case 'weekly':
            $path = $generator->generateWeeklyAverages();
            break;
        case 'monthly':
            $path = $generator->generateMonthlyConditions();
            break;
        default:
            http_response_code(404);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Chart not found.';
            exit;
    }

    if (!is_file($path)) {
        throw new RuntimeException('График не создан.');
    }

    header('Content-Type: image/png');
    header('Cache-Control: no-cache, must-revalidate');
    readfile($path);
} catch (Throwable $exception) {
    http_response_code(500);
    $message = 'Ошибка генерации графика: ' . $exception->getMessage();
    error_log($message);
    log_chart_error($message, $exception);
    header('Content-Type: image/png');
    echo render_placeholder_png($message);
}

function log_chart_error(string $message, Throwable $exception): void
{
    $dir = __DIR__ . '/storage/logs';
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    $line = sprintf(
        "[%s] %s\n%s\n\n",
        date('c'),
        $message,
        $exception->getTraceAsString()
    );
    @file_put_contents($dir . '/chart-errors.log', $line, FILE_APPEND);
}

function render_placeholder_png(string $text): string
{
    $width = 760;
    $height = 260;
    $image = imagecreatetruecolor($width, $height);

    $bg = imagecolorallocate($image, 248, 249, 250);
    $border = imagecolorallocate($image, 200, 200, 200);
    $textColor = imagecolorallocate($image, 80, 80, 80);

    imagefilledrectangle($image, 0, 0, $width - 1, $height - 1, $bg);
    imagerectangle($image, 0, 0, $width - 1, $height - 1, $border);

    $wrapped = wordwrap($text, 60, "\n");
    $lines = explode("\n", $wrapped);
    $y = 40;
    foreach ($lines as $line) {
        imagestring($image, 5, 20, $y, $line, $textColor);
        $y += 18;
    }

    ob_start();
    imagepng($image);
    $data = ob_get_clean();
    imagedestroy($image);

    return $data ?: '';
}
