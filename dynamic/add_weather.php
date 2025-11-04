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
    $temperature = $_POST['temperature'];
    $humidity = $_POST['humidity'];
    $pressure = $_POST['pressure'];
    $wind_speed = $_POST['wind_speed'];
    $description = $_POST['description'];
    $icon = $_POST['icon'];
    
    $stmt = $pdo->prepare("INSERT INTO weather_data (temperature, humidity, pressure, wind_speed, description, icon) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$temperature, $humidity, $pressure, $wind_speed, $description, $icon]);
    
    header('Location: /index.php');
    exit;
}
?>