<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use App\Fixtures\WeatherFixtureSeeder;
use App\Support\Database;

$redirect = static function (string $type, string $message): void {
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
    header('Location: /stats.php');
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $redirect('error', 'Генерация фикстур доступна только через POST-запрос.');
}

$mode = $_POST['mode'] ?? 'fill';
$count = (int) ($_POST['count'] ?? 60);
$minimum = (int) ($_POST['minimum'] ?? 50);
$reset = ($_POST['reset'] ?? '') === '1';

$count = max(50, min($count, 500));
$minimum = max(1, min($minimum, $count));

try {
    $pdo = Database::makePdo();
    $seeder = new WeatherFixtureSeeder();

    if ($mode === 'reset' || $reset) {
        $inserted = $seeder->seed($pdo, $count, true);
        $redirect('success', sprintf('Таблица weather_data пересоздана: добавлено %d фикстур.', $inserted));
    }

    $inserted = $seeder->seedIfBelow($pdo, $minimum, $count);
    if ($inserted === 0) {
        $redirect('success', 'Фикстуры уже в наличии: таблица содержит достаточно записей.');
    }

    $redirect('success', sprintf('Добавлено %d демо-записей погоды.', $inserted));
} catch (Throwable $exception) {
    error_log('Fixture seeding failed: ' . $exception->getMessage());
    $redirect('error', 'Не удалось сгенерировать фикстуры: ' . $exception->getMessage());
}
