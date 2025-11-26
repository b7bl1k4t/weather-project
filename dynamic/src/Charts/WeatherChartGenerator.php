<?php
declare(strict_types=1);

namespace App\Charts;

use Amenadiel\JpGraph\Graph\Graph;
use Amenadiel\JpGraph\Graph\PieGraph;
use Amenadiel\JpGraph\Plot\BarPlot;
use Amenadiel\JpGraph\Plot\LinePlot;
use Amenadiel\JpGraph\Plot\PiePlot;
use App\Fixtures\WeatherFixtureSeeder;
use DateInterval;
use DateTimeImmutable;
use PDO;
use RuntimeException;

class WeatherChartGenerator
{
    private PDO $pdo;
    private WeatherFixtureSeeder $seeder;
    private string $chartDir;
    private string $fontPath;

    public function __construct(PDO $pdo, WeatherFixtureSeeder $seeder)
    {
        $this->pdo = $pdo;
        $this->seeder = $seeder;
        $this->chartDir = $this->ensureChartDir();
        $this->fontPath = dirname(__DIR__, 2) . '/vendor/amenadiel/jpgraph/src/fonts/DejaVuSans.ttf';
    }

    public function ensureData(int $minimum = 50): void
    {
        $this->seeder->seedIfBelow($this->pdo, $minimum);
        $this->seeder->seedWindowIfBelow($this->pdo, '-24 hours', 'now', 24, 36);
        $this->seeder->seedWindowIfBelow($this->pdo, '-7 days', 'now', 28, 42);
        $this->seeder->seedWindowIfBelow($this->pdo, '-30 days', 'now', 40, 60);
    }

    public function generateDailyTemperature(): string
    {
        $this->ensureData();
        [$labels, $values] = $this->hourlyTemperatureSeries();

        $graph = new Graph(920, 360);
        $graph->SetMargin(60, 30, 40, 80);
        $graph->SetScale('textlin');
        $graph->img->SetAntiAliasing(true);
        $graph->title->Set('Температура за последние 24 часа');
        $graph->subtitle->Set('Средние значения по часам');

        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->SetLabelAngle(40);
        $graph->xaxis->title->Set('Время');
        $graph->yaxis->title->Set('°C');

        $line = new LinePlot($values);
        $line->SetColor('#2563eb');
        $line->SetWeight(2);
        $line->mark->SetType(MARK_FILLEDCIRCLE);
        $line->mark->SetColor('#1d4ed8');
        $line->mark->SetFillColor('#93c5fd');

        $graph->Add($line);

        $path = $this->chartPath('daily-temperature');
        $graph->Stroke($path);
        $this->applyWatermark($path);

        return $path;
    }

    public function generateWeeklyAverages(): string
    {
        $this->ensureData();
        [$labels, $values] = $this->dailyAveragesSeries();

        $graph = new Graph(920, 360);
        $graph->SetMargin(60, 30, 40, 60);
        $graph->SetScale('textlin');
        $graph->img->SetAntiAliasing(true);
        $graph->title->Set('Средняя температура за 7 дней');
        $graph->subtitle->Set('Агрегация по датам');

        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->SetLabelAngle(20);
        $graph->xaxis->title->Set('Дни');
        $graph->yaxis->title->Set('°C');

        $bar = new BarPlot($values);
        $bar->SetColor('#0284c7');
        $bar->SetFillColor('#0ea5e9');
        $bar->value->Show();
        $bar->value->SetFormat('%.1f°C');

        $graph->Add($bar);

        $path = $this->chartPath('weekly-averages');
        $graph->Stroke($path);
        $this->applyWatermark($path);

        return $path;
    }

    public function generateMonthlyConditions(): string
    {
        $this->ensureData();
        [$labels, $values] = $this->conditionDistribution();

        $graph = new PieGraph(920, 400);
        $graph->title->Set('Распределение погодных условий за месяц');

        $pie = new PiePlot($values);
        $pie->SetLegends($this->buildLegends($labels, $values));
        $pie->SetCenter(0.3);
        $pie->value->SetFormat('%2d');

        $graph->legend->SetPos(0.72, 0.5, 'center', 'center');
        $graph->legend->SetColumns(1);

        $graph->Add($pie);

        $path = $this->chartPath('monthly-conditions');
        $graph->Stroke($path);
        $this->applyWatermark($path);

        return $path;
    }

    private function hourlyTemperatureSeries(): array
    {
        $stmt = $this->pdo->query(
            "SELECT date_trunc('hour', created_at) AS bucket, AVG(temperature) AS avg_temp
             FROM weather_data
             WHERE created_at >= NOW() - INTERVAL '24 hours'
             GROUP BY bucket
             ORDER BY bucket ASC"
        );
        $rows = $stmt->fetchAll();

        $map = [];
        foreach ($rows as $row) {
            $map[(new DateTimeImmutable($row['bucket']))->format('Y-m-d H:00:00')] = (float) $row['avg_temp'];
        }

        $labels = [];
        $values = [];
        $now = new DateTimeImmutable('now');

        for ($i = 23; $i >= 0; $i--) {
            $point = $now->sub(new DateInterval(sprintf('PT%dH', $i)));
            $key = $point->format('Y-m-d H:00:00');
            $labels[] = $point->format('H:i');
            $values[] = round($map[$key] ?? 0, 2);
        }

        return [$labels, $values];
    }

    private function dailyAveragesSeries(): array
    {
        $stmt = $this->pdo->query(
            "SELECT date_trunc('day', created_at) AS bucket, AVG(temperature) AS avg_temp
             FROM weather_data
             WHERE created_at >= NOW() - INTERVAL '7 days'
             GROUP BY bucket
             ORDER BY bucket ASC"
        );
        $rows = $stmt->fetchAll();

        $map = [];
        foreach ($rows as $row) {
            $map[(new DateTimeImmutable($row['bucket']))->format('Y-m-d')] = (float) $row['avg_temp'];
        }

        $labels = [];
        $values = [];
        $today = new DateTimeImmutable('today');

        for ($i = 6; $i >= 0; $i--) {
            $day = $today->sub(new DateInterval(sprintf('P%dD', $i)));
            $key = $day->format('Y-m-d');
            $labels[] = $day->format('d.m');
            $values[] = round($map[$key] ?? 0, 2);
        }

        return [$labels, $values];
    }

    private function conditionDistribution(): array
    {
        $stmt = $this->pdo->query(
            "SELECT description, COUNT(*) AS total
             FROM weather_data
             WHERE created_at >= NOW() - INTERVAL '30 days'
             GROUP BY description
             ORDER BY total DESC"
        );
        $rows = $stmt->fetchAll();

        $labels = [];
        $values = [];
        foreach ($rows as $row) {
            $labels[] = (string) $row['description'];
            $values[] = max(0, (int) $row['total']);
        }

        $clean = [];
        foreach ($values as $idx => $value) {
            if ($value > 0) {
                $clean[] = [$labels[$idx] ?? '—', $value];
            }
        }

        if (empty($clean)) {
            return [['Нет данных'], [1]];
        }

        $labels = array_column($clean, 0);
        $values = array_column($clean, 1);

        return [$labels, $values];
    }

    private function buildLegends(array $labels, array $values): array
    {
        $total = array_sum($values) ?: 1;
        $legends = [];

        foreach ($labels as $index => $label) {
            $value = $values[$index] ?? 0;
            $percent = round(($value / $total) * 100);
            $legend = sprintf('%s — %d (≈%d%%)', $label, $value, $percent);
            $legends[] = str_replace('%', '%%', $legend);
        }

        return $legends;
    }

    private function applyWatermark(string $path): void
    {
        $contents = @file_get_contents($path);
        if ($contents === false || $contents === '') {
            throw new RuntimeException('Не удалось открыть изображение графика для водяного знака.');
        }

        $image = @imagecreatefromstring($contents);
        if ($image === false) {
            error_log('Watermark: cannot create image from ' . $path);
            return;
        }

        imagealphablending($image, true);
        imagesavealpha($image, true);

        $width = imagesx($image);
        $height = imagesy($image);
        $text = 'weather demo';

        $color = imagecolorallocatealpha($image, 255, 255, 255, 95);

        if (function_exists('imagettftext') && is_readable($this->fontPath)) {
            $size = max(14, (int) floor($width / 45));
            $angle = -18;
            $bbox = imagettfbbox($size, $angle, $this->fontPath, $text);
            $textWidth = (int) abs($bbox[4] - $bbox[0]);
            $textHeight = (int) abs($bbox[5] - $bbox[1]);
            $x = $width - $textWidth - 20;
            $y = $height - 20;
            imagettftext($image, $size, $angle, $x, $y, $color, $this->fontPath, $text);
        } else {
            imagestring($image, 5, $width - 160, $height - 30, $text, $color);
        }

        if (!@imagepng($image, $path)) {
            error_log('Watermark: failed to write ' . $path);
        }
        imagedestroy($image);
    }

    private function chartPath(string $name): string
    {
        return $this->chartDir . '/' . $name . '.png';
    }

    private function ensureChartDir(): string
    {
        $dir = dirname(__DIR__, 2) . '/storage/charts';
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            throw new RuntimeException('Не удалось создать каталог для графиков.');
        }

        return realpath($dir) ?: $dir;
    }
}
