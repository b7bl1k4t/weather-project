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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $temperature = filter_var($_POST['temperature'], FILTER_VALIDATE_FLOAT);
    $humidity = filter_var($_POST['humidity'], FILTER_VALIDATE_INT);
    $pressure = filter_var($_POST['pressure'], FILTER_VALIDATE_INT);
    $wind_speed = filter_var($_POST['wind_speed'], FILTER_VALIDATE_FLOAT);
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? '');

    if ($temperature === false || $temperature < -99.99 || $temperature > 99.99) {
        die('Температура должна быть в диапазоне от -99.99 до 99.99.');
    }
    if ($humidity === false || $humidity < 0 || $humidity > 100) {
        die('Влажность должна быть от 0 до 100.');
    }
    if ($pressure === false || $pressure < 0 || $pressure > 2000) {
        die('Давление должно быть положительным и реалистичным.');
    }
    if ($wind_speed === false || $wind_speed < 0 || $wind_speed > 99.99) {
        die('Скорость ветра должна быть в диапазоне от 0 до 99.99.');
    }
    if ($description === '' || $icon === '') {
        die('Описание и иконка обязательны.');
    }
    
    $stmt = $pdo->prepare("INSERT INTO weather_data (temperature, humidity, pressure, wind_speed, description, icon) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        round($temperature, 2),
        $humidity,
        $pressure,
        round($wind_speed, 2),
        $description,
        $icon
    ]);
    
    header('Location: /index.php');
    exit;
}
?>
