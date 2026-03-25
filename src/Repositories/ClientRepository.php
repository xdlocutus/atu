<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use PDO;

final class ClientRepository
{
    public function list(string $search = '', int $limit = 100): array
    {
        $pdo = Database::connection();
        $safeLimit = max(1, min(500, $limit));
        $sql = 'SELECT id, full_name, contact_number, email, erf_number, notes, created_at
                FROM clients
                WHERE archived_at IS NULL';

        $params = [];
        if ($search !== '') {
            $sql .= ' AND (
                erf_number LIKE :q
                OR full_name LIKE :q
                OR contact_number LIKE :q
                OR email LIKE :q
            )';
            $params['q'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY created_at DESC LIMIT ' . $safeLimit;

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, full_name, contact_number, email, erf_number, notes, created_at FROM clients WHERE id = :id AND archived_at IS NULL');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public function totalCount(): int
    {
        $pdo = Database::connection();
        $row = $pdo->query('SELECT COUNT(*) AS c FROM clients WHERE archived_at IS NULL')->fetch();

        return (int)($row['c'] ?? 0);
    }

    public function archive(int $id): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('UPDATE clients SET archived_at = NOW() WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
