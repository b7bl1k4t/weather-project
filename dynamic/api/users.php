<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$pdo = get_pdo();
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

switch ($method) {
    case 'GET':
        $id = $_GET['id'] ?? null;
        if ($id !== null && $id !== '') {
            get_user_item($pdo, require_int_id($id));
        } else {
            list_user_items($pdo);
        }
        break;
    case 'POST':
        create_user_item($pdo);
        break;
    case 'PUT':
    case 'PATCH':
        $id = require_int_id($_GET['id'] ?? null);
        update_user_item($pdo, $id);
        break;
    case 'DELETE':
        $id = require_int_id($_GET['id'] ?? null);
        delete_user_item($pdo, $id);
        break;
    case 'OPTIONS':
        header('Allow: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        http_response_code(204);
        exit;
    default:
        header('Allow: GET, POST, PUT, PATCH, DELETE, OPTIONS');
        json_response(['error' => 'Метод не поддерживается.'], 405);
}

function list_user_items(PDO $pdo): void
{
    $limit = normalize_limit($_GET['limit'] ?? null);
    $stmt = $pdo->prepare('SELECT id, username, email, created_at FROM users ORDER BY created_at DESC LIMIT :limit');
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();

    json_response(['data' => $stmt->fetchAll()]);
}

function get_user_item(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('SELECT id, username, email, created_at FROM users WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $user = $stmt->fetch();

    if (!$user) {
        json_response(['error' => 'Пользователь не найден.'], 404);
    }

    json_response(['data' => $user]);
}

function create_user_item(PDO $pdo): void
{
    $payload = validate_user_payload(read_json_body(), false);

    try {
        $stmt = $pdo->prepare('INSERT INTO users (username, password, email) VALUES (:username, :password, :email)
            RETURNING id, username, email, created_at');
        $stmt->execute($payload);
    } catch (PDOException $e) {
        handle_unique_violation($e);
        throw $e;
    }

    json_response(['data' => $stmt->fetch()], 201);
}

function update_user_item(PDO $pdo, int $id): void
{
    ensure_user_exists($pdo, $id);
    $payload = validate_user_payload(read_json_body(), true);
    if (empty($payload)) {
        json_response(['error' => 'Нечего обновлять — передайте хотя бы одно поле.'], 422);
    }

    $setParts = [];
    $params = [':id' => $id];
    foreach ($payload as $column => $value) {
        $setParts[] = "{$column} = :{$column}";
        $params[":{$column}"] = $value;
    }

    $sql = 'UPDATE users SET ' . implode(', ', $setParts) . ' WHERE id = :id RETURNING id, username, email, created_at';

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } catch (PDOException $e) {
        handle_unique_violation($e);
        throw $e;
    }

    json_response(['data' => $stmt->fetch()]);
}

function delete_user_item(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
    $stmt->execute([':id' => $id]);

    if ($stmt->rowCount() === 0) {
        json_response(['error' => 'Пользователь не найден.'], 404);
    }

    http_response_code(204);
}

function ensure_user_exists(PDO $pdo, int $id): void
{
    $stmt = $pdo->prepare('SELECT 1 FROM users WHERE id = :id');
    $stmt->execute([':id' => $id]);
    if (!$stmt->fetchColumn()) {
        json_response(['error' => 'Пользователь не найден.'], 404);
    }
}

function validate_user_payload(array $data, bool $isUpdate): array
{
    $payload = [];

    if (!$isUpdate || array_key_exists('username', $data)) {
        $username = trim((string)($data['username'] ?? ''));
        if ($username === '' || mb_strlen($username) < 3) {
            json_response(['error' => 'Имя пользователя обязательно и должно содержать не менее 3 символов.'], 422);
        }
        if (mb_strlen($username) > 50) {
            json_response(['error' => 'Имя пользователя должно быть короче 50 символов.'], 422);
        }
        $payload['username'] = $username;
    }

    if (!$isUpdate || array_key_exists('password', $data)) {
        $password = (string)($data['password'] ?? '');
        if ($password === '' || mb_strlen($password) < 6) {
            json_response(['error' => 'Пароль обязателен и должен содержать не менее 6 символов.'], 422);
        }
        $payload['password'] = password_hash($password, PASSWORD_BCRYPT);
    }

    if (!$isUpdate || array_key_exists('email', $data)) {
        $emailRaw = trim((string)($data['email'] ?? ''));
        if ($emailRaw === '') {
            $payload['email'] = null;
        } else {
            if (!filter_var($emailRaw, FILTER_VALIDATE_EMAIL)) {
                json_response(['error' => 'Email указан в неверном формате.'], 422);
            }
            $payload['email'] = $emailRaw;
        }
    }

    return $payload;
}

function handle_unique_violation(PDOException $exception): void
{
    if ($exception->getCode() === '23505') {
        json_response(['error' => 'Пользователь с таким именем уже существует.'], 409);
    }
}
