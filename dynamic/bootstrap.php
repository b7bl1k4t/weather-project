<?php
declare(strict_types=1);

$autoload = __DIR__ . '/vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

$redisHost = getenv('REDIS_HOST') ?: 'redis';
$redisPort = getenv('REDIS_PORT') ?: '6379';

if (!headers_sent()) {
    ini_set('session.save_handler', 'redis');
    ini_set('session.save_path', sprintf('tcp://%s:%s?persistent=1&weight=1&timeout=2.5&read_timeout=2.5', $redisHost, $redisPort));
    ini_set('session.gc_maxlifetime', '86400');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('weather_session');
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'httponly' => true,
        'secure' => false,
        'samesite' => 'Lax',
    ]);

    try {
        session_start();
    } catch (Throwable $e) {
        error_log('Не удалось запустить сессию: ' . $e->getMessage());
    }
}
