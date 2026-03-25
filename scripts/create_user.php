<?php

declare(strict_types=1);

use App\Core\Database;
use App\Core\Env;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__) . '/.env');
$pdo = Database::connection();

$name = $argv[1] ?? 'marco';
$email = $argv[2] ?? 'marco@example.com';
$password = $argv[3] ?? 'Hero3338351';
$role = $argv[4] ?? 'admin';

$stmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, role) VALUES (:full_name, :email, :password_hash, :role)');
$stmt->execute([
    'full_name' => $name,
    'email' => $email,
    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    'role' => $role,
]);

echo "User created: {$email}\n";
