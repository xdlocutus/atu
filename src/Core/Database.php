<?php

declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo) {
            return self::$pdo;
        }

        $dbPath = Env::get('DB_DATABASE', 'database/app.sqlite');
        if (!str_starts_with($dbPath, '/')) {
            $dbPath = dirname(__DIR__, 2) . '/' . $dbPath;
        }
        if (!file_exists($dbPath)) {
            touch($dbPath);
        }

        self::$pdo = new PDO('sqlite:' . $dbPath);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return self::$pdo;
    }
}
