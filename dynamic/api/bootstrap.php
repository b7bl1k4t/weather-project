<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

/**
 * Returns a shared PDO connection to the project database.
 */
function get_pdo(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $host = getenv('POSTGRES_HOST') ?: 'postgres';
    $dbname = getenv('POSTGRES_DB') ?: 'weather_db';
    $username = getenv('POSTGRES_USER') ?: 'weather_user';
    $password = getenv('POSTGRES_PASSWORD') ?: 'weather_pass';

    $dsn = sprintf('pgsql:host=%s;dbname=%s', $host, $dbname);
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

/**
 * Sends a JSON response and terminates the request.
 */
function json_response(array $payload, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Reads and decodes a JSON body from the request.
 */
function read_json_body(): array
{
    $rawBody = trim(file_get_contents('php://input') ?: '');
    if ($rawBody === '') {
        return [];
    }

    $data = json_decode($rawBody, true);
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
        json_response([
            'error' => 'Некорректный JSON в теле запроса.',
            'details' => json_last_error_msg(),
        ], 400);
    }

    return $data;
}

/**
 * Validates and normalizes an integer identifier from request parameters.
 */
function require_int_id($value): int
{
    if ($value === null || $value === '') {
        json_response(['error' => 'Параметр id обязателен для этого запроса.'], 400);
    }

    if (!filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
        json_response(['error' => 'Параметр id должен быть положительным целым числом.'], 400);
    }

    return (int)$value;
}

/**
 * Normalizes numeric limits for list endpoints.
 */
function normalize_limit($value, int $default = 20, int $max = 100): int
{
    if ($value === null || $value === '') {
        return $default;
    }

    $limit = filter_var($value, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 1, 'max_range' => $max],
    ]);

    return $limit ?: $default;
}

set_exception_handler(function (Throwable $throwable) {
    json_response([
        'error' => 'Внутренняя ошибка сервера',
        'details' => $throwable->getMessage(),
    ], 500);
});
