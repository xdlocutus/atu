<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class PaymentRepository
{
    public function listByClient(int $clientId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT p.id, p.invoice_id, i.invoice_number, p.payment_date, p.amount, p.method, p.reference_number, p.notes
        FROM payments p INNER JOIN invoices i ON i.id = p.invoice_id
        WHERE i.client_id = :client_id ORDER BY p.created_at DESC');
        $stmt->execute(['client_id' => $clientId]);

        return $stmt->fetchAll();
    }

    public function create(array $data): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();

        $stmt = $pdo->prepare('INSERT INTO payments (invoice_id, payment_date, amount, method, reference_number, notes)
            VALUES (:invoice_id, :payment_date, :amount, :method, :reference_number, :notes)');
        $stmt->execute([
            'invoice_id' => $data['invoice_id'],
            'payment_date' => $data['payment_date'],
            'amount' => $data['amount'],
            'method' => $data['method'],
            'reference_number' => $data['reference_number'] ?: null,
            'notes' => $data['notes'] ?: null,
        ]);

        $update = $pdo->prepare('UPDATE invoices SET amount_paid = amount_paid + :amount WHERE id = :invoice_id');
        $update->execute(['amount' => $data['amount'], 'invoice_id' => $data['invoice_id']]);
        $pdo->commit();
    }
}
