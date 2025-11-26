<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use App\Fixtures\WeatherFixtureSeeder;
use App\Support\Database;

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$allowedThemes = ['light', 'dark', 'contrast'];
$allowedLanguages = ['ru', 'en', 'es'];

$defaultPreferences = [
    'login' => 'Гость',
    'theme' => 'light',
    'language' => 'ru',
];

$preferences = $defaultPreferences;

if (!empty($_SESSION['preferences']) && is_array($_SESSION['preferences'])) {
    $preferences = array_merge($preferences, array_intersect_key($_SESSION['preferences'], $defaultPreferences));
}

$cookieLogin = trim((string) (filter_input(INPUT_COOKIE, 'weather_login', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''));
$cookieTheme = trim((string) (filter_input(INPUT_COOKIE, 'weather_theme', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''));
$cookieLanguage = trim((string) (filter_input(INPUT_COOKIE, 'weather_language', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? ''));

if ($cookieLogin !== '') {
    $preferences['login'] = $cookieLogin;
}
if (in_array($cookieTheme, $allowedThemes, true)) {
    $preferences['theme'] = $cookieTheme;
}
if (in_array($cookieLanguage, $allowedLanguages, true)) {
    $preferences['language'] = $cookieLanguage;
}

$translations = [
    'ru' => [
        'page_title' => 'Погодная статистика',
        'nav_home' => 'Главная',
        'nav_about' => 'О проекте',
        'nav_dynamic' => 'Динамика',
        'nav_stats' => 'Статистика',
        'nav_admin' => 'Админка',
        'header_title' => 'Статистика и графики',
        'header_subtitle' => 'Срезы за сутки, неделю и месяц на основе таблицы weather_data',
        'summary_total' => 'Записей в таблице',
        'summary_latest' => 'Последняя запись',
        'summary_hint' => 'Все графики строятся на стороне сервера и кэшируются как изображения с водяным знаком.',
        'fixtures_title' => 'Генерация фикстур',
        'fixtures_fill' => 'Добавить недостающие демо-записи (без очистки, минимум 60 строк).',
        'fixtures_reset' => 'Полностью пересоздать демо-данные (очищает таблицу).',
        'charts_daily' => 'Температура за сутки',
        'charts_weekly' => 'Средняя температура за неделю',
        'charts_monthly' => 'Условия за месяц',
        'charts_hint_day' => 'Средние значения по часам последних 24 часов.',
        'charts_hint_week' => 'Средняя температура по дням за последние 7 суток.',
        'charts_hint_month' => 'Распределение погодных описаний за 30 дней.',
        'cta_generate' => 'Добавить фикстуры',
        'cta_reset' => 'Пересоздать таблицу',
        'autofill_note' => 'Для графиков добавлено %d демо-записей.',
        'no_records' => 'Данных пока нет',
        'db_error' => 'Не удалось подключиться к базе данных.',
    ],
    'en' => [
        'page_title' => 'Weather stats',
        'nav_home' => 'Home',
        'nav_about' => 'About',
        'nav_dynamic' => 'Dynamic',
        'nav_stats' => 'Stats',
        'nav_admin' => 'Admin',
        'header_title' => 'Statistics and charts',
        'header_subtitle' => 'Daily, weekly and monthly cuts from weather_data',
        'summary_total' => 'Rows in table',
        'summary_latest' => 'Latest record',
        'summary_hint' => 'Charts are rendered server-side to PNG and stamped with a watermark.',
        'fixtures_title' => 'Fixture generator',
        'fixtures_fill' => 'Top up missing demo rows (keeps data, ensures at least 60).',
        'fixtures_reset' => 'Rebuild demo data from scratch (clears the table).',
        'charts_daily' => 'Day temperature',
        'charts_weekly' => 'Weekly averages',
        'charts_monthly' => 'Monthly conditions',
        'charts_hint_day' => 'Hourly averages for the last 24 hours.',
        'charts_hint_week' => 'Average temperature per day for the last 7 days.',
        'charts_hint_month' => 'Distribution of weather descriptions over 30 days.',
        'cta_generate' => 'Add fixtures',
        'cta_reset' => 'Reset table',
        'autofill_note' => '%d demo rows were added for the charts.',
        'no_records' => 'No records yet',
        'db_error' => 'Failed to connect to the database.',
    ],
    'es' => [
        'page_title' => 'Estadísticas del clima',
        'nav_home' => 'Inicio',
        'nav_about' => 'Sobre el proyecto',
        'nav_dynamic' => 'Dinámica',
        'nav_stats' => 'Estadísticas',
        'nav_admin' => 'Panel',
        'header_title' => 'Estadísticas y gráficos',
        'header_subtitle' => 'Cortes diarios, semanales y mensuales basados en weather_data',
        'summary_total' => 'Registros en la tabla',
        'summary_latest' => 'Último registro',
        'summary_hint' => 'Los gráficos se generan en el servidor como PNG con marca de agua.',
        'fixtures_title' => 'Generador de fixtures',
        'fixtures_fill' => 'Completar filas demo faltantes (sin limpiar, mínimo 60).',
        'fixtures_reset' => 'Reconstruir los datos demo desde cero (limpia la tabla).',
        'charts_daily' => 'Temperatura del día',
        'charts_weekly' => 'Promedios semanales',
        'charts_monthly' => 'Condiciones del mes',
        'charts_hint_day' => 'Promedios por hora de las últimas 24 horas.',
        'charts_hint_week' => 'Temperatura promedio por día de la última semana.',
        'charts_hint_month' => 'Distribución de descripciones del clima en 30 días.',
        'cta_generate' => 'Agregar fixtures',
        'cta_reset' => 'Recrear tabla',
        'autofill_note' => 'Se agregaron %d filas demo para los gráficos.',
        'no_records' => 'Aún no hay datos',
        'db_error' => 'No se pudo conectar a la base de datos.',
    ],
];

$strings = $translations[$preferences['language']] ?? $translations['ru'];

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$dbError = null;
$recordCount = 0;
$lastRecord = null;
$autofilled = 0;

try {
    $pdo = Database::makePdo();
    $seeder = new WeatherFixtureSeeder();
    $autofilled = $seeder->seedIfBelow($pdo, 50, 60);

    $recordCount = (int) $pdo->query('SELECT COUNT(*) FROM weather_data')->fetchColumn();
    $lastRecord = $pdo->query('SELECT created_at FROM weather_data ORDER BY created_at DESC LIMIT 1')->fetchColumn() ?: null;
} catch (Throwable $exception) {
    $dbError = $exception->getMessage();
}

$lastRecordText = $lastRecord ? date('d.m.Y H:i', strtotime($lastRecord)) : $strings['no_records'];
$cacheBuster = (string) time();
?>
<!DOCTYPE html>
<html lang="<?php echo e($preferences['language']); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($strings['page_title']); ?></title>
    <link rel="stylesheet" href="/css/style.css">
    <?php if ($flash): ?>
    <style>
        .flash-message {
            position: fixed;
            top: 90px;
            right: 20px;
            min-width: 260px;
            max-width: 320px;
            background: #f0fff4;
            border-left: 6px solid #2ed573;
            padding: 16px 22px;
            border-radius: 16px;
            color: #1b4332;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.2);
            z-index: 2000;
            opacity: 0;
            transform: translate3d(0, -20px, 0);
            animation: flash-in 0.35s ease forwards;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        .flash-message.flash-error {
            background: #fff5f5;
            border-left-color: #ff7675;
            color: #5c0a0a;
        }
        .flash-message.flash-hidden {
            opacity: 0;
            transform: translate3d(0, -15px, 0);
            pointer-events: none;
        }
        @keyframes flash-in {
            from { opacity: 0; transform: translate3d(0,-20px,0); }
            to { opacity: 1; transform: translate3d(0,0,0); }
        }
    </style>
    <?php endif; ?>
</head>
<body class="theme-<?php echo e($preferences['theme']); ?>" data-language="<?php echo e($preferences['language']); ?>">
    <nav class="main-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <span>📊 Weather Stats</span>
            </div>
            <ul class="nav-menu">
                <li><a href="/index.html" class="nav-link"><?php echo e($strings['nav_home']); ?></a></li>
                <li><a href="/about.html" class="nav-link"><?php echo e($strings['nav_about']); ?></a></li>
                <li><a href="/index.php" class="nav-link"><?php echo e($strings['nav_dynamic']); ?></a></li>
                <li><a href="/stats.php" class="nav-link active"><?php echo e($strings['nav_stats']); ?></a></li>
                <li><a href="/admin/" class="nav-link"><?php echo e($strings['nav_admin']); ?></a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1><?php echo e($strings['header_title']); ?></h1>
            <p><?php echo e($strings['header_subtitle']); ?></p>
        </div>

        <?php if ($flash): ?>
            <div class="flash-message flash-<?php echo e($flash['type']); ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <?php if ($autofilled > 0): ?>
            <div class="pill pill-success">
                <?php echo e(sprintf($strings['autofill_note'], $autofilled)); ?>
            </div>
        <?php endif; ?>

        <?php if ($dbError): ?>
            <div class="weather-card">
                <h2><?php echo e($strings['db_error']); ?></h2>
                <p><?php echo e($dbError); ?></p>
            </div>
        <?php else: ?>
            <div class="weather-card stats-summary">
                <div class="summary-item">
                    <div class="summary-value"><?php echo e($recordCount); ?></div>
                    <div class="summary-label"><?php echo e($strings['summary_total']); ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-value"><?php echo e($lastRecordText); ?></div>
                    <div class="summary-label"><?php echo e($strings['summary_latest']); ?></div>
                </div>
                <div class="summary-item">
                    <div class="summary-label muted"><?php echo e($strings['summary_hint']); ?></div>
                </div>
            </div>

            <div class="weather-card fixtures-card">
                <div>
                    <h2><?php echo e($strings['fixtures_title']); ?></h2>
                    <p><?php echo e($strings['fixtures_fill']); ?></p>
                </div>
                <div class="fixture-actions">
                    <form action="/seed_fixtures.php" method="POST">
                        <input type="hidden" name="mode" value="fill">
                        <input type="hidden" name="count" value="60">
                        <input type="hidden" name="minimum" value="50">
                        <button type="submit" class="btn"><?php echo e($strings['cta_generate']); ?></button>
                    </form>
                    <form action="/seed_fixtures.php" method="POST">
                        <input type="hidden" name="mode" value="reset">
                        <input type="hidden" name="count" value="80">
                        <button type="submit" class="btn btn-danger"><?php echo e($strings['cta_reset']); ?></button>
                    </form>
                </div>
                <p class="muted"><?php echo e($strings['fixtures_reset']); ?></p>
            </div>

            <div class="chart-grid">
                <div class="weather-card chart-card">
                    <h3><?php echo e($strings['charts_daily']); ?></h3>
                    <img src="/chart.php?chart=daily&v=<?php echo e($cacheBuster); ?>" alt="<?php echo e($strings['charts_daily']); ?>">
                    <p class="chart-caption"><?php echo e($strings['charts_hint_day']); ?></p>
                </div>
                <div class="weather-card chart-card">
                    <h3><?php echo e($strings['charts_weekly']); ?></h3>
                    <img src="/chart.php?chart=weekly&v=<?php echo e($cacheBuster); ?>" alt="<?php echo e($strings['charts_weekly']); ?>">
                    <p class="chart-caption"><?php echo e($strings['charts_hint_week']); ?></p>
                </div>
                <div class="weather-card chart-card">
                    <h3><?php echo e($strings['charts_monthly']); ?></h3>
                    <img src="/chart.php?chart=monthly&v=<?php echo e($cacheBuster); ?>" alt="<?php echo e($strings['charts_monthly']); ?>">
                    <p class="chart-caption"><?php echo e($strings['charts_hint_month']); ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php if ($flash): ?>
    <script>
        (function () {
            const flashEl = document.querySelector('.flash-message');
            if (!flashEl) {
                return;
            }
            const hide = () => {
                flashEl.classList.add('flash-hidden');
                setTimeout(() => flashEl.remove(), 400);
            };
            setTimeout(hide, 4000);
            flashEl.addEventListener('click', hide);
        })();
    </script>
    <?php endif; ?>
</body>
</html>
