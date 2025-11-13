<?php
declare(strict_types=1);

function weather_upload_dir(): string
{
    $dir = __DIR__ . '/../uploads';
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        throw new RuntimeException('Не удалось создать каталог для загрузок');
    }
    return realpath($dir) ?: $dir;
}

function weather_upload_meta_path(): string
{
    return weather_upload_dir() . '/uploads_meta.json';
}

function weather_load_files(): array
{
    $metaPath = weather_upload_meta_path();
    if (!file_exists($metaPath)) {
        return [];
    }
    $contents = file_get_contents($metaPath);
    if ($contents === false || trim($contents) === '') {
        return [];
    }
    $data = json_decode($contents, true);
    return is_array($data) ? $data : [];
}

function weather_save_files(array $files): void
{
    $metaPath = weather_upload_meta_path();
    $payload = json_encode(array_values($files), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if ($payload === false) {
        throw new RuntimeException('Не удалось сериализовать данные загрузок');
    }
    file_put_contents($metaPath, $payload, LOCK_EX);
}

function weather_add_file(array $record): array
{
    $files = weather_load_files();
    $files[] = $record;
    weather_save_files($files);
    return $files;
}

function weather_find_file(string $id): ?array
{
    foreach (weather_load_files() as $file) {
        if (($file['id'] ?? '') === $id) {
            return $file;
        }
    }
    return null;
}

function weather_delete_file(string $id): bool
{
    $files = weather_load_files();
    $updated = [];
    $deleted = null;

    foreach ($files as $file) {
        if (($file['id'] ?? '') === $id) {
            $deleted = $file;
            continue;
        }
        $updated[] = $file;
    }

    if ($deleted === null) {
        return false;
    }

    $path = weather_upload_dir() . '/' . ($deleted['stored_name'] ?? '');
    if (is_file($path)) {
        @unlink($path);
    }

    weather_save_files($updated);
    return true;
}
