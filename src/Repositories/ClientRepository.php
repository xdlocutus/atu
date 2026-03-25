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

        $sql .= ' ORDER BY created_at DESC LIMIT :limit';

        $stmt = $pdo->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value, PDO::PARAM_STR);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function create(array $data): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO clients (full_name, contact_number, email, erf_number, notes)
            VALUES (:full_name, :contact_number, :email, :erf_number, :notes)');

        $stmt->execute([
            'full_name' => $data['full_name'],
            'contact_number' => $data['contact_number'] ?: null,
            'email' => $data['email'] ?: null,
            'erf_number' => $data['erf_number'],
            'notes' => $data['notes'] ?: null,
        ]);
    }

    public function totalCount(): int
    {
        $pdo = Database::connection();
        $row = $pdo->query('SELECT COUNT(*) AS c FROM clients WHERE archived_at IS NULL')->fetch();

        return (int)($row['c'] ?? 0);
    }
}
