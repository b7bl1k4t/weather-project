<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/lib/files.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$id = trim((string) filter_input(INPUT_POST, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

if ($id === '') {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Некорректный идентификатор файла.',
    ];
    header('Location: /index.php');
    exit;
}

if (!weather_delete_file($id)) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Файл не найден или уже удалён.',
    ];
    header('Location: /index.php');
    exit;
}

$_SESSION['flash'] = [
    'type' => 'success',
    'message' => 'PDF удалён.',
];

header('Location: /index.php');
exit;
