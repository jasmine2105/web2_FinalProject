<?php

declare(strict_types=1);

namespace Core\Database;

use PDO;
use Exception;

class Connection
{
    public static function create(string $driver, array $config): PDO
    {
        return match($driver) {
            'sqlite' => self::connectSqlite($config),
            'mysql'  => self::connectMysql($config),
            default  => throw new Exception("Unsupported database driver: {$driver}"),
        };
    }

    private static function connectSqlite(array $config): PDO
    {
        $path = $config['database'];
        $dir  = dirname($path);

        if (!is_dir($dir)) {
            mkdir($dir, 0755, recursive: true);
        }

        return new PDO("sqlite:{$path}", options: [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    private static function connectMysql(array $config): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host']     ?? '127.0.0.1',
            $config['port']     ?? '3306',
            $config['database'] ?? '',
            $config['charset']  ?? 'utf8mb4'
        );

        return new PDO($dsn, $config['username'] ?? '', $config['password'] ?? '', [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
