<?php

declare(strict_types=1);

use App\Core\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__) . '/.env');

$host = Env::get('DB_HOST', '127.0.0.1');
$port = Env::get('DB_PORT', '3306');
$dbName = Env::get('DB_DATABASE', 'draft_control');
$user = Env::get('DB_USERNAME', 'root');
$pass = Env::get('DB_PASSWORD', '');
$charset = Env::get('DB_CHARSET', 'utf8mb4');
$collation = Env::get('DB_COLLATION', 'utf8mb4_unicode_ci');

$serverDsn = sprintf('mysql:host=%s;port=%s;charset=%s', $host, $port, $charset);
$pdo = new PDO($serverDsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec(sprintf('CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET %s COLLATE %s', $dbName, $charset, $collation));
$pdo->exec(sprintf('USE `%s`', $dbName));

$sql = file_get_contents(dirname(__DIR__) . '/database/schema.sql');
$pdo->exec($sql);

echo "MariaDB database initialized: {$dbName}\n";
