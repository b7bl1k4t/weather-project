<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/lib/files.php';

$host = 'postgres';
$dbname = 'weather_db';
$username = 'weather_user';
$password = 'weather_pass';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

function getWeatherData(PDO $pdo): ?array
{
    try {
        $stmt = $pdo->query("SELECT * FROM weather_data ORDER BY created_at DESC LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    } catch (Exception $e) {
        return null;
    }
}

function getWeatherHistory(PDO $pdo, int $limit = 5): array
{
    try {
        $stmt = $pdo->query("SELECT * FROM weather_data ORDER BY created_at DESC LIMIT $limit");
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    } catch (Exception $e) {
        return [];
    }
}

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
        'page_title' => 'Погода — Динамический контент',
        'nav_home' => 'Главная',
        'nav_about' => 'О проекте',
        'nav_dynamic' => 'Динамика',
        'nav_admin' => 'Админка',
        'hero_title' => '🌤️ Прогноз Погоды',
        'hero_subtitle' => 'Динамические данные из базы данных',
        'current_weather_title' => 'Текущая погода в базе данных',
        'no_weather' => 'Нет данных о погоде',
        'no_weather_hint' => 'Добавьте данные через форму ниже',
        'history_title' => 'История погоды',
        'form_title' => 'Добавить данные о погоде',
        'form_temperature' => 'Температура (°C):',
        'form_humidity' => 'Влажность (%):',
        'form_pressure' => 'Давление (hPa):',
        'form_wind' => 'Скорость ветра (м/с):',
        'form_description' => 'Описание:',
        'form_icon' => 'Иконка:',
        'form_submit' => 'Добавить данные',
        'preferences_title' => 'Персональные настройки',
        'preferences_subtitle' => 'Сохраняем выбор в Redis и cookie на 30 дней',
        'login_label' => 'Имя пользователя',
        'theme_label' => 'Тема интерфейса',
        'language_label' => 'Язык',
        'preferences_submit' => 'Сохранить настройки',
        'greeting' => 'Здравствуйте',
        'theme_current' => 'Текущая тема',
        'language_current' => 'Текущий язык',
        'files_title' => 'PDF материалы',
        'files_subtitle' => 'Загрузите методички в формате PDF и делитесь ими',
        'file_input' => 'Выберите PDF (до 5 МБ)',
        'file_submit' => 'Загрузить PDF',
        'file_list_title' => 'Доступные материалы',
        'file_empty' => 'Файлы еще не загружены.',
        'download' => 'Скачать',
        'uploaded_by' => 'Загрузил',
        'uploaded_at' => 'от',
        'detail_humidity' => 'Влажность',
        'detail_pressure' => 'Давление',
        'detail_wind' => 'Ветер',
        'detail_updated' => 'Обновлено',
        'delete' => 'Удалить',
        'delete_confirm' => 'Удалить файл?',
    ],
    'en' => [
        'page_title' => 'Weather — Dynamic content',
        'nav_home' => 'Home',
        'nav_about' => 'About',
        'nav_dynamic' => 'Dynamic',
        'nav_admin' => 'Admin',
        'hero_title' => '🌤️ Weather Forecast',
        'hero_subtitle' => 'Dynamic data from the database',
        'current_weather_title' => 'Latest weather in the database',
        'no_weather' => 'No weather data',
        'no_weather_hint' => 'Add a record using the form below',
        'history_title' => 'Weather history',
        'form_title' => 'Add weather data',
        'form_temperature' => 'Temperature (°C):',
        'form_humidity' => 'Humidity (%):',
        'form_pressure' => 'Pressure (hPa):',
        'form_wind' => 'Wind speed (m/s):',
        'form_description' => 'Description:',
        'form_icon' => 'Icon:',
        'form_submit' => 'Add data',
        'preferences_title' => 'Personal settings',
        'preferences_subtitle' => 'Stored in Redis and cookies for 30 days',
        'login_label' => 'User name',
        'theme_label' => 'Theme',
        'language_label' => 'Language',
        'preferences_submit' => 'Save settings',
        'greeting' => 'Hello',
        'theme_current' => 'Current theme',
        'language_current' => 'Current language',
        'files_title' => 'PDF materials',
        'files_subtitle' => 'Upload PDF guides and share them',
        'file_input' => 'Choose PDF (up to 5 MB)',
        'file_submit' => 'Upload PDF',
        'file_list_title' => 'Available materials',
        'file_empty' => 'No files uploaded yet.',
        'download' => 'Download',
        'uploaded_by' => 'Uploaded by',
        'uploaded_at' => 'on',
        'detail_humidity' => 'Humidity',
        'detail_pressure' => 'Pressure',
        'detail_wind' => 'Wind',
        'detail_updated' => 'Updated at',
        'delete' => 'Delete',
        'delete_confirm' => 'Delete this file?',
    ],
    'es' => [
        'page_title' => 'Clima — Contenido dinámico',
        'nav_home' => 'Inicio',
        'nav_about' => 'Sobre el proyecto',
        'nav_dynamic' => 'Dinámica',
        'nav_admin' => 'Panel',
        'hero_title' => '🌤️ Pronóstico del tiempo',
        'hero_subtitle' => 'Datos dinámicos de la base de datos',
        'current_weather_title' => 'Último clima en la base',
        'no_weather' => 'No hay datos del clima',
        'no_weather_hint' => 'Agregue un registro usando el formulario',
        'history_title' => 'Historial del clima',
        'form_title' => 'Agregar datos del clima',
        'form_temperature' => 'Temperatura (°C):',
        'form_humidity' => 'Humedad (%):',
        'form_pressure' => 'Presión (hPa):',
        'form_wind' => 'Viento (m/s):',
        'form_description' => 'Descripción:',
        'form_icon' => 'Ícono:',
        'form_submit' => 'Agregar datos',
        'preferences_title' => 'Preferencias personales',
        'preferences_subtitle' => 'Guardamos su elección en Redis y cookies por 30 días',
        'login_label' => 'Nombre de usuario',
        'theme_label' => 'Tema',
        'language_label' => 'Idioma',
        'preferences_submit' => 'Guardar preferencias',
        'greeting' => 'Hola',
        'theme_current' => 'Tema actual',
        'language_current' => 'Idioma actual',
        'files_title' => 'Materiales PDF',
        'files_subtitle' => 'Suba guías en PDF y compártalas',
        'file_input' => 'Seleccione PDF (hasta 5 MB)',
        'file_submit' => 'Subir PDF',
        'file_list_title' => 'Materiales disponibles',
        'file_empty' => 'Aún no hay archivos.',
        'download' => 'Descargar',
        'uploaded_by' => 'Autor',
        'uploaded_at' => 'el',
        'detail_humidity' => 'Humedad',
        'detail_pressure' => 'Presión',
        'detail_wind' => 'Viento',
        'detail_updated' => 'Actualizado',
        'delete' => 'Eliminar',
        'delete_confirm' => '¿Eliminar el archivo?',
    ],
];

$strings = $translations[$preferences['language']] ?? $translations['ru'];

$themeNames = [
    'light' => ['ru' => 'Светлая', 'en' => 'Light', 'es' => 'Clara'],
    'dark' => ['ru' => 'Тёмная', 'en' => 'Dark', 'es' => 'Oscura'],
    'contrast' => ['ru' => 'Контрастная', 'en' => 'High contrast', 'es' => 'Alto contraste'],
];

$languageOptions = [
    'ru' => 'Русский',
    'en' => 'English',
    'es' => 'Español',
];

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$currentWeather = getWeatherData($pdo);
$weatherHistory = getWeatherHistory($pdo);
$uploadedFiles = array_reverse(weather_load_files());
$windUnit = $preferences['language'] === 'ru' ? 'м/с' : 'm/s';
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
                <span>🌤️ Weather</span>
            </div>
            <ul class="nav-menu">
                <li><a href="/index.html" class="nav-link"><?php echo e($strings['nav_home']); ?></a></li>
                <li><a href="/about.html" class="nav-link"><?php echo e($strings['nav_about']); ?></a></li>
                <li><a href="/index.php" class="nav-link active"><?php echo e($strings['nav_dynamic']); ?></a></li>
                <li><a href="/admin/" class="nav-link"><?php echo e($strings['nav_admin']); ?></a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1><?php echo e($strings['hero_title']); ?></h1>
            <p><?php echo e($strings['hero_subtitle']); ?></p>
        </div>

        <?php if ($flash): ?>
            <div class="flash-message flash-<?php echo e($flash['type']); ?>">
                <?php echo e($flash['message']); ?>
            </div>
        <?php endif; ?>

        <div class="weather-card preferences-card">
            <div>
                <h2><?php echo e($strings['preferences_title']); ?></h2>
                <p><?php echo e($strings['preferences_subtitle']); ?></p>
                <div class="preferences-summary">
                    <div>
                        <strong><?php echo e($strings['greeting']); ?>, <?php echo e($preferences['login']); ?></strong>
                    </div>
                    <div>
                        <?php echo e($strings['theme_current']); ?>: <?php echo e($themeNames[$preferences['theme']][$preferences['language']] ?? $preferences['theme']); ?>
                    </div>
                    <div>
                        <?php echo e($strings['language_current']); ?>: <?php echo e($languageOptions[$preferences['language']] ?? $preferences['language']); ?>
                    </div>
                </div>
            </div>
            <form action="/save_preferences.php" method="POST" class="preferences-form">
                <div class="form-group">
                    <label for="login"><?php echo e($strings['login_label']); ?></label>
                    <input type="text" id="login" name="login" value="<?php echo e($preferences['login']); ?>" maxlength="40" required>
                </div>
                <div class="form-group">
                    <label for="theme"><?php echo e($strings['theme_label']); ?></label>
                    <select id="theme" name="theme">
                        <?php foreach ($allowedThemes as $theme): ?>
                            <option value="<?php echo e($theme); ?>" <?php echo $theme === $preferences['theme'] ? 'selected' : ''; ?>>
                                <?php echo e($themeNames[$theme][$preferences['language']] ?? ucfirst($theme)); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="language"><?php echo e($strings['language_label']); ?></label>
                    <select id="language" name="language">
                        <?php foreach ($allowedLanguages as $lang): ?>
                            <option value="<?php echo e($lang); ?>" <?php echo $lang === $preferences['language'] ? 'selected' : ''; ?>>
                                <?php echo e($languageOptions[$lang]); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn"><?php echo e($strings['preferences_submit']); ?></button>
            </form>
        </div>

        <?php if ($currentWeather): ?>
        <div class="weather-card current-weather">
            <h2><?php echo e($strings['current_weather_title']); ?></h2>
            <div class="weather-icon"><?php echo e($currentWeather['icon']); ?></div>
            <div class="temperature"><?php echo e($currentWeather['temperature']); ?>°C</div>
            <div class="weather-description"><?php echo e($currentWeather['description']); ?></div>

            <div class="weather-details">
                <div class="detail-item">
                    <div class="detail-label"><?php echo e($strings['detail_humidity']); ?></div>
                    <div class="detail-value"><?php echo e($currentWeather['humidity']); ?>%</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><?php echo e($strings['detail_pressure']); ?></div>
                    <div class="detail-value"><?php echo e($currentWeather['pressure']); ?> hPa</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><?php echo e($strings['detail_wind']); ?></div>
                    <div class="detail-value"><?php echo e($currentWeather['wind_speed']); ?> <?php echo e($windUnit); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label"><?php echo e($strings['detail_updated']); ?></div>
                    <div class="detail-value"><?php echo e($currentWeather['created_at']); ?></div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="weather-card">
            <h2><?php echo e($strings['no_weather']); ?></h2>
            <p><?php echo e($strings['no_weather_hint']); ?></p>
        </div>
        <?php endif; ?>

        <?php if (!empty($weatherHistory)): ?>
        <div class="weather-card">
            <h2><?php echo e($strings['history_title']); ?></h2>
            <div class="weather-stats">
                <?php foreach ($weatherHistory as $record): ?>
                <div class="stat-card">
                    <div class="stat-value"><?php echo e($record['temperature']); ?>°C</div>
                    <div class="stat-label"><?php echo e($record['description']); ?></div>
                    <div class="stat-label"><?php echo e($record['created_at']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="weather-card file-section">
            <h2><?php echo e($strings['files_title']); ?></h2>
            <p><?php echo e($strings['files_subtitle']); ?></p>
            <form action="/upload_pdf.php" method="POST" enctype="multipart/form-data" class="file-form">
                <div class="form-group">
                    <label for="pdf_file"><?php echo e($strings['file_input']); ?></label>
                    <input type="file" id="pdf_file" name="pdf_file" accept="application/pdf" required>
                </div>
                <button type="submit" class="btn"><?php echo e($strings['file_submit']); ?></button>
            </form>

            <div class="uploaded-files">
                <h3><?php echo e($strings['file_list_title']); ?></h3>
                <?php if (empty($uploadedFiles)): ?>
                    <p><?php echo e($strings['file_empty']); ?></p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($uploadedFiles as $file): ?>
                            <li>
                                <div>
                                    <strong><?php echo e($file['original_name']); ?></strong>
                                    <span>
                                        <?php echo e($strings['uploaded_by']); ?>: <?php echo e($file['uploaded_by']); ?>, 
                                        <?php echo e($strings['uploaded_at']); ?> <?php echo e(date('d.m.Y H:i', strtotime($file['uploaded_at']))); ?>
                                    </span>
                                </div>
                                <div class="file-actions">
                                    <a class="btn btn-secondary" href="/download.php?id=<?php echo e($file['id']); ?>"><?php echo e($strings['download']); ?></a>
                                    <form action="/delete_pdf.php" method="POST" onsubmit="return confirm('<?php echo e($strings['delete_confirm']); ?>');">
                                        <input type="hidden" name="id" value="<?php echo e($file['id']); ?>">
                                        <button type="submit" class="btn btn-danger"><?php echo e($strings['delete']); ?></button>
                                    </form>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-panel">
            <h2><?php echo e($strings['form_title']); ?></h2>
            <form action="/add_weather.php" method="POST">
                <div class="form-group">
                    <label for="temperature"><?php echo e($strings['form_temperature']); ?></label>
                    <input type="number" id="temperature" name="temperature" step="0.1" required>
                </div>
                <div class="form-group">
                    <label for="humidity"><?php echo e($strings['form_humidity']); ?></label>
                    <input type="number" id="humidity" name="humidity" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label for="pressure"><?php echo e($strings['form_pressure']); ?></label>
                    <input type="number" id="pressure" name="pressure" required>
                </div>
                <div class="form-group">
                    <label for="wind_speed"><?php echo e($strings['form_wind']); ?></label>
                    <input type="number" id="wind_speed" name="wind_speed" step="0.1" required>
                </div>
                <div class="form-group">
                    <label for="description"><?php echo e($strings['form_description']); ?></label>
                    <select id="description" name="description" required>
                        <option value="Солнечно">☀️ Солнечно</option>
                        <option value="Облачно">⛅ Облачно</option>
                        <option value="Пасмурно">☁️ Пасмурно</option>
                        <option value="Дождь">🌧️ Дождь</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="icon"><?php echo e($strings['form_icon']); ?></label>
                    <select id="icon" name="icon" required>
                        <option value="☀️">☀️ Солнце</option>
                        <option value="⛅">⛅ Облачно</option>
                        <option value="☁️">☁️ Пасмурно</option>
                        <option value="🌧️">🌧️ Дождь</option>
                        <option value="⛈️">⛈️ Гроза</option>
                        <option value="❄️">❄️ Снег</option>
                    </select>
                </div>
                <button type="submit" class="btn"><?php echo e($strings['form_submit']); ?></button>
            </form>
        </div>
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
