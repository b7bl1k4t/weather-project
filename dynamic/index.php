<?php
// Подключение к PostgreSQL
$host = 'postgres';
$dbname = 'weather_db';
$username = 'weather_user';
$password = 'weather_pass';

try {
    $pdo = new PDO("pgsql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}

// Получение данных о погоде из базы данных
function getWeatherData($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM weather_data ORDER BY created_at DESC LIMIT 1");
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

function getWeatherHistory($pdo, $limit = 5) {
    try {
        $stmt = $pdo->query("SELECT * FROM weather_data ORDER BY created_at DESC LIMIT $limit");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return [];
    }
}

$currentWeather = getWeatherData($pdo);
$weatherHistory = getWeatherHistory($pdo);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Погода - Динамический контент</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <!-- МЕНЮ -->
    <nav class="main-nav">
        <div class="nav-container">
            <div class="nav-logo">
                <span>🌤️ Погода</span>
            </div>
            <ul class="nav-menu">
                <li><a href="/index.html" class="nav-link">Главная</a></li>
                <li><a href="/about.html" class="nav-link">О проекте</a></li>
                <li><a href="/index.php" class="nav-link active">Динамика</a></li>
                <li><a href="/admin/" class="nav-link">Админка</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="header">
            <h1>🌤️ Прогноз Погоды</h1>
            <p>Динамические данные из базы данных</p>
        </div>

        <?php if ($currentWeather): ?>
        <div class="weather-card current-weather">
            <h2>Текущая погода в базе данных</h2>
            <div class="weather-icon"><?php echo htmlspecialchars($currentWeather['icon']); ?></div>
            <div class="temperature"><?php echo htmlspecialchars($currentWeather['temperature']); ?>°C</div>
            <div class="weather-description"><?php echo htmlspecialchars($currentWeather['description']); ?></div>
            
            <div class="weather-details">
                <div class="detail-item">
                    <div class="detail-label">Влажность</div>
                    <div class="detail-value"><?php echo htmlspecialchars($currentWeather['humidity']); ?>%</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Давление</div>
                    <div class="detail-value"><?php echo htmlspecialchars($currentWeather['pressure']); ?> hPa</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Ветер</div>
                    <div class="detail-value"><?php echo htmlspecialchars($currentWeather['wind_speed']); ?> м/с</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Обновлено</div>
                    <div class="detail-value"><?php echo htmlspecialchars($currentWeather['created_at']); ?></div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="weather-card">
            <h2>Нет данных о погоде</h2>
            <p>Добавьте данные через форму ниже</p>
        </div>
        <?php endif; ?>

        <?php if (!empty($weatherHistory)): ?>
        <div class="weather-card">
            <h2>История погоды</h2>
            <div class="weather-stats">
                <?php foreach ($weatherHistory as $record): ?>
                <div class="stat-card">
                    <div class="stat-value"><?php echo htmlspecialchars($record['temperature']); ?>°C</div>
                    <div class="stat-label"><?php echo htmlspecialchars($record['description']); ?></div>
                    <div class="stat-label"><?php echo htmlspecialchars($record['created_at']); ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="admin-panel">
            <h2>Добавить данные о погоде</h2>
            <form action="/add_weather.php" method="POST">
                <div class="form-group">
                    <label for="temperature">Температура (°C):</label>
                    <input type="number" id="temperature" name="temperature" step="0.1" required>
                </div>
                <div class="form-group">
                    <label for="humidity">Влажность (%):</label>
                    <input type="number" id="humidity" name="humidity" min="0" max="100" required>
                </div>
                <div class="form-group">
                    <label for="pressure">Давление (hPa):</label>
                    <input type="number" id="pressure" name="pressure" required>
                </div>
                <div class="form-group">
                    <label for="wind_speed">Скорость ветра (м/с):</label>
                    <input type="number" id="wind_speed" name="wind_speed" step="0.1" required>
                </div>
                <div class="form-group">
                    <label for="description">Описание:</label>
                    <select id="description" name="description" required>
                        <option value="Солнечно">☀️ Солнечно</option>
                        <option value="Облачно">⛅ Облачно</option>
                        <option value="Пасмурно">☁️ Пасмурно</option>
                        <option value="Дождь">🌧️ Дождь</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="icon">Иконка:</label>
                    <select id="icon" name="icon" required>
                        <option value="☀️">☀️ Солнце</option>
                        <option value="⛅">⛅ Облачно</option>
                        <option value="☁️">☁️ Пасмурно</option>
                        <option value="🌧️">🌧️ Дождь</option>
                        <option value="⛈️">⛈️ Гроза</option>
                        <option value="❄️">❄️ Снег</option>
                    </select>
                </div>
                <button type="submit" class="btn">Добавить данные</button>
            </form>
        </div>
    </div>
</body>
</html>