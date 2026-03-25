<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class UserRepository
{
    public function findByEmail(string $email): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role, is_active FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function list(): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->query('SELECT id, full_name, email, role, is_active, created_at FROM users ORDER BY created_at DESC');

        return $stmt->fetchAll();
    }

    public function create(string $name, string $email, string $password, string $role = 'staff'): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, role) VALUES (:full_name, :email, :password_hash, :role)');
        $stmt->execute([
            'full_name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role' => $role,
        ]);
    }
}
