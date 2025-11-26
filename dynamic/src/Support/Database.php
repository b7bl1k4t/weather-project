<?php
declare(strict_types=1);

namespace App\Support;

use PDO;

class Database
{
    public static function makePdo(): PDO
    {
        $host = getenv('POSTGRES_HOST') ?: 'postgres';
        $dbname = getenv('POSTGRES_DB') ?: 'weather_db';
        $username = getenv('POSTGRES_USER') ?: 'weather_user';
        $password = getenv('POSTGRES_PASSWORD') ?: 'weather_pass';

        $dsn = sprintf('pgsql:host=%s;dbname=%s', $host, $dbname);

        return new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
