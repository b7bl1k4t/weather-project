<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/lib/files.php';

$id = trim((string) filter_input(INPUT_GET, 'id', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

if ($id === '') {
    http_response_code(404);
    exit('Файл не найден.');
}

$file = weather_find_file($id);

if (!$file) {
    http_response_code(404);
    exit('Файл не найден.');
}

$path = weather_upload_dir() . '/' . ($file['stored_name'] ?? '');

if (!is_file($path)) {
    http_response_code(404);
    exit('Файл отсутствует на сервере.');
}

header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . basename($file['original_name']) . '"');
header('Content-Length: ' . filesize($path));
readfile($path);
exit;
