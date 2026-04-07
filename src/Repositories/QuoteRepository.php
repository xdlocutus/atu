<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class QuoteRepository
{
    public function listByClient(int $clientId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, quote_number, status, quote_date, expiry_date, subtotal, vat_amount, total
            FROM quotes WHERE client_id = :client_id ORDER BY created_at DESC');
        $stmt->execute(['client_id' => $clientId]);

        return $stmt->fetchAll();
    }

    public function create(int $clientId, array $data): void
    {
        $pdo = Database::connection();
        $number = trim((string)($data['quote_number'] ?? ''));
        if ($number === '') {
            $number = 'Q-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
        }
        $items = $this->normalizeItems($data['items'] ?? []);
        $subtotal = array_sum(array_column($items, 'subtotal'));
        $vatRate = (float)$data['vat_rate'];
        $vatAmount = $subtotal * ($vatRate / 100);
        $total = $subtotal + $vatAmount;

        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO quotes
            (quote_number, client_id, status, quote_date, expiry_date, subtotal, vat_rate, vat_amount, total, notes, terms)
            VALUES (:quote_number, :client_id, :status, :quote_date, :expiry_date, :subtotal, :vat_rate, :vat_amount, :total, :notes, :terms)');
        $stmt->execute([
            'quote_number' => $number,
            'client_id' => $clientId,
            'status' => $data['status'],
            'quote_date' => $data['quote_date'],
            'expiry_date' => $data['expiry_date'],
            'subtotal' => $subtotal,
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'total' => $total,
            'notes' => $data['notes'] ?: null,
            'terms' => $data['terms'] ?: null,
        ]);

        $quoteId = (int)$pdo->lastInsertId();
        $item = $pdo->prepare('INSERT INTO quote_items (quote_id, description, quantity, rate, subtotal)
            VALUES (:quote_id, :description, :quantity, :rate, :subtotal)');
        foreach ($items as $lineItem) {
            $item->execute([
                'quote_id' => $quoteId,
                'description' => $lineItem['description'],
                'quantity' => $lineItem['quantity'],
                'rate' => $lineItem['rate'],
                'subtotal' => $lineItem['subtotal'],
            ]);
        }
        $pdo->commit();
    }


    public function update(int $quoteId, int $clientId, array $data): void
    {
        $pdo = Database::connection();
        $items = $this->normalizeItems($data['items'] ?? []);
        $subtotal = array_sum(array_column($items, 'subtotal'));
        $vatRate = (float)$data['vat_rate'];
        $vatAmount = $subtotal * ($vatRate / 100);
        $total = $subtotal + $vatAmount;

        $pdo->beginTransaction();
        $stmt = $pdo->prepare('UPDATE quotes SET quote_number=:quote_number, status=:status, quote_date=:quote_date, expiry_date=:expiry_date, subtotal=:subtotal, vat_rate=:vat_rate, vat_amount=:vat_amount, total=:total, notes=:notes, terms=:terms WHERE id=:id AND client_id=:client_id');
        $stmt->execute([
            'quote_number' => trim((string)($data['quote_number'] ?? '')),
            'status'=>$data['status'],'quote_date'=>$data['quote_date'],'expiry_date'=>$data['expiry_date'],
            'subtotal'=>$subtotal,'vat_rate'=>$vatRate,'vat_amount'=>$vatAmount,'total'=>$total,
            'notes'=>$data['notes'] ?: null,'terms'=>$data['terms'] ?: null,'id'=>$quoteId,'client_id'=>$clientId
        ]);
        $pdo->prepare('DELETE FROM quote_items WHERE quote_id = :quote_id')->execute(['quote_id' => $quoteId]);
        $item = $pdo->prepare('INSERT INTO quote_items (quote_id, description, quantity, rate, subtotal)
            VALUES (:quote_id, :description, :quantity, :rate, :subtotal)');
        foreach ($items as $lineItem) {
            $item->execute([
                'quote_id' => $quoteId,
                'description' => $lineItem['description'],
                'quantity' => $lineItem['quantity'],
                'rate' => $lineItem['rate'],
                'subtotal' => $lineItem['subtotal'],
            ]);
        }
        $pdo->commit();
    }

    private function normalizeItems(array $items): array
    {
        $normalized = [];
        foreach ($items as $item) {
            $description = trim((string)($item['description'] ?? ''));
            $quantity = (float)($item['quantity'] ?? 0);
            $rate = (float)($item['rate'] ?? 0);
            if ($description === '' || $quantity <= 0) {
                continue;
            }
            $normalized[] = [
                'description' => $description,
                'quantity' => $quantity,
                'rate' => $rate,
                'subtotal' => $quantity * $rate,
            ];
        }

        if ($normalized === []) {
            throw new \InvalidArgumentException('At least one valid line item is required.');
        }

        return $normalized;
    }

    public function find(int $quoteId): ?array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT * FROM quotes WHERE id = :id');
        $stmt->execute(['id' => $quoteId]);
        $quote = $stmt->fetch();
        if (!$quote) {
            return null;
        }
        $items = $pdo->prepare('SELECT description, quantity, rate, subtotal FROM quote_items WHERE quote_id = :id');
        $items->execute(['id' => $quoteId]);
        $quote['items'] = $items->fetchAll();

        return $quote;
    }

    public function delete(int $quoteId, int $clientId): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        $invoiceLinks = $pdo->prepare('UPDATE invoices SET quote_id = NULL WHERE quote_id = :quote_id AND client_id = :client_id');
        $invoiceLinks->execute(['quote_id' => $quoteId, 'client_id' => $clientId]);
        $items = $pdo->prepare('DELETE FROM quote_items WHERE quote_id = :quote_id');
        $items->execute(['quote_id' => $quoteId]);
        $quote = $pdo->prepare('DELETE FROM quotes WHERE id = :id AND client_id = :client_id');
        $quote->execute(['id' => $quoteId, 'client_id' => $clientId]);
        $pdo->commit();
    }
}
