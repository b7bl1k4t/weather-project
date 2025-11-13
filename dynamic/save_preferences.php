<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$allowedThemes = ['light', 'dark', 'contrast'];
$allowedLanguages = ['ru', 'en', 'es'];

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /index.php');
    exit;
}

$login = trim((string) filter_input(INPUT_POST, 'login', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$theme = trim((string) filter_input(INPUT_POST, 'theme', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$language = trim((string) filter_input(INPUT_POST, 'language', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

if ($login === '') {
    $login = 'Гость';
}

if (!in_array($theme, $allowedThemes, true)) {
    $theme = 'light';
}

if (!in_array($language, $allowedLanguages, true)) {
    $language = 'ru';
}

$sanitizedLogin = function_exists('mb_substr') ? mb_substr($login, 0, 40) : substr($login, 0, 40);

$preferences = [
    'login' => $sanitizedLogin,
    'theme' => $theme,
    'language' => $language,
];

$_SESSION['preferences'] = $preferences;

$cookieOptions = [
    'expires' => time() + 60 * 60 * 24 * 30,
    'path' => '/',
    'secure' => false,
    'httponly' => false,
    'samesite' => 'Lax',
];

foreach ($preferences as $key => $value) {
    setcookie('weather_' . $key, $value, $cookieOptions);
}

$_SESSION['flash'] = [
    'type' => 'success',
    'message' => 'Настройки сохранены.',
];

header('Location: /index.php');
exit;
