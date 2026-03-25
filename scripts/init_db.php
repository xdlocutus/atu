<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__) . '/.env');
$pdo = Database::connection();
$sql = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
$pdo->exec($sql);

echo "Database initialized.\n";
