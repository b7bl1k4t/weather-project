<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$pdo = get_pdo();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        if ($id !== null && $id !== '') {
            get_weather_item($pdo, require_int_id($id));
        } else {
            list_weather_items($pdo);
        }
        break;
    case 'POST':
        create_weather_item($pdo);
        break;
    case 'PUT':
    case 'PATCH':
        $id = require_int_id($_GET['id'] ?? null);
        update_weather_item($pdo, $id);
        break;
    case 'DELETE':
        $id = require_int_id($_GET['id'] ?? null);
        delete_weather_item($pdo, $id);
        break;
    case 'OPTIONS':
        header('Allow: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        http_response_code(204);
        exit;
    default:
        header('Allow: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        json_response(['error' => 'Метод не поддерживается.'], 405);
}

function list_weather_items(PDO $pdo): void
{
    $limit = normalize_limit($_GET['limit'] ?? null);
    $stmt = $pdo->prepare('SELECT * FROM weather_data ORDER BY created_at DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    json_response(['data' => $stmt->fetchAll()]);
}

function get_weather_item(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('SELECT * FROM weather_data WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch();

    if (!$record) {
        json_response(['error' => 'Запись о погоде не найдена.'], 404);
    }

    json_response(['data' => $record]);
}

function create_weather_item(PDO $pdo): void
{
    $payload = validate_weather_payload(read_json_body());

    $stmt = $pdo->prepare('INSERT INTO weather_data 
        (temperature, humidity, pressure, wind_speed, description, icon) 
        VALUES (:temperature, :humidity, :pressure, :wind_speed, :description, :icon)
        RETURNING *');
    $stmt->execute($payload);
    $created = $stmt->fetch();

    json_response(['data' => $created], 201);
}

function update_weather_item(PDO $pdo, int $id): void
{
    ensure_weather_exists($pdo, $id);
    $payload = validate_weather_payload(read_json_body());
    $payload['id'] = $id;

    $stmt = $pdo->prepare('UPDATE weather_data SET
        temperature = :temperature,
        humidity = :humidity,
        pressure = :pressure,
        wind_speed = :wind_speed,
        description = :description,
        icon = :icon
        WHERE id = :id
        RETURNING *');
    $stmt->execute($payload);
    $updated = $stmt->fetch();

    json_response(['data' => $updated]);
}

function delete_weather_item(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM weather_data WHERE id = :id');
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 0) {
        json_response(['error' => 'Запись о погоде не найдена.'], 404);
    }

    http_response_code(204);
}

function ensure_weather_exists(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('SELECT 1 FROM weather_data WHERE id = :id');
    $stmt->execute([':id' => $id]);
    if (!$stmt->fetchColumn()) {
        json_response(['error' => 'Запись о погоде не найдена.'], 404);
    }
}

function validate_weather_payload(array $data): array
{
    $required = ['temperature', 'humidity', 'pressure', 'wind_speed', 'description', 'icon'];
    foreach ($required as $field) {
        if (!array_key_exists($field, $data)) {
            json_response(['error' => "Поле {$field} является обязательным."], 422);
        }
    }

    $temperature = filter_var($data['temperature'], FILTER_VALIDATE_FLOAT);
    $windSpeed = filter_var($data['wind_speed'], FILTER_VALIDATE_FLOAT);
    $humidity = filter_var($data['humidity'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 100]]);
    $pressure = filter_var($data['pressure'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0]]);
    $description = trim((string)$data['description']);
    $icon = trim((string)$data['icon']);

    if ($temperature === false || $temperature < -99.99 || $temperature > 99.99) {
        json_response(['error' => 'Температура должна быть в диапазоне от -99.99 до 99.99.'], 422);
    }
    if ($humidity === false) {
        json_response(['error' => 'Влажность должна быть числом от 0 до 100.'], 422);
    }
    if ($pressure === false || $pressure > 2000) {
        json_response(['error' => 'Давление должно быть положительным и не превышать 2000.'], 422);
    }
    if ($windSpeed === false || $windSpeed < 0 || $windSpeed > 99.99) {
        json_response(['error' => 'Скорость ветра должна быть в диапазоне от 0 до 99.99.'], 422);
    }
    if ($description === '') {
        json_response(['error' => 'Описание не может быть пустым.'], 422);
    }
    if ($icon === '' || mb_strlen($icon) > 10) {
        json_response(['error' => 'Иконка обязательна и должна быть короче 10 символов.'], 422);
    }

    return [
        'temperature' => round((float)$temperature, 2),
        'humidity' => $humidity,
        'pressure' => $pressure,
        'wind_speed' => round((float)$windSpeed, 2),
        'description' => $description,
        'icon' => $icon,
    ];
}
