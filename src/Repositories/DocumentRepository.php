<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class DocumentRepository
{
    public function listByClient(int $clientId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, category, original_name, stored_name, size_bytes, notes, created_at
            FROM documents WHERE client_id = :client_id AND is_archived = 0 ORDER BY created_at DESC');
        $stmt->execute(['client_id' => $clientId]);

        return $stmt->fetchAll();
    }

    public function create(int $clientId, array $data): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('INSERT INTO documents
            (client_id, category, original_name, stored_name, mime_type, extension, size_bytes, notes)
            VALUES (:client_id, :category, :original_name, :stored_name, :mime_type, :extension, :size_bytes, :notes)');

        $stmt->execute([
            'client_id' => $clientId,
            'category' => $data['category'],
            'original_name' => $data['original_name'],
            'stored_name' => $data['stored_name'],
            'mime_type' => $data['mime_type'],
            'extension' => $data['extension'],
            'size_bytes' => $data['size_bytes'],
            'notes' => $data['notes'] ?: null,
        ]);
    }
}
