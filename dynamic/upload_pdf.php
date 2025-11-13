<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/lib/files.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

if (!isset($_FILES['pdf_file'])) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Файл не передан.',
    ];
    header('Location: /index.php');
    exit;
}

$file = $_FILES['pdf_file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Ошибка загрузки файла.',
    ];
    header('Location: /index.php');
    exit;
}

if ($file['size'] > 5 * 1024 * 1024) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Файл слишком большой. Максимум 5 МБ.',
    ];
    header('Location: /index.php');
    exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($file['tmp_name']);
if ($mimeType !== 'application/pdf') {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Допускаются только PDF файлы.',
    ];
    header('Location: /index.php');
    exit;
}

try {
    $id = bin2hex(random_bytes(8));
} catch (Throwable $e) {
    $id = uniqid('', true);
}

$targetName = $id . '.pdf';
$targetPath = weather_upload_dir() . '/' . $targetName;

$originalName = $file['name'] ?? 'document.pdf';
$originalName = preg_replace('/[^\w\-\.\s\p{L}]/u', '', $originalName) ?: 'document.pdf';

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    $_SESSION['flash'] = [
        'type' => 'error',
        'message' => 'Не удалось сохранить файл.',
    ];
    header('Location: /index.php');
    exit;
}

$record = [
    'id' => $id,
    'stored_name' => $targetName,
    'original_name' => $originalName,
    'uploaded_at' => date('c'),
    'uploaded_by' => $_SESSION['preferences']['login'] ?? 'Гость',
];

weather_add_file($record);

$_SESSION['flash'] = [
    'type' => 'success',
    'message' => 'PDF загружен и готов к скачиванию.',
];

header('Location: /index.php');
exit;
