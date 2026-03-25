<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class InvoiceRepository
{
    public function listByClient(int $clientId): array
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT id, invoice_number, quote_id, status, invoice_date, due_date, total, amount_paid, balance_due
            FROM invoices WHERE client_id = :client_id ORDER BY created_at DESC');
        $stmt->execute(['client_id' => $clientId]);

        return $stmt->fetchAll();
    }

    public function createManual(int $clientId, array $data): void
    {
        $number = 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
        $subtotal = (float)$data['quantity'] * (float)$data['rate'];
        $vatRate = (float)$data['vat_rate'];
        $vatAmount = $subtotal * ($vatRate / 100);
        $total = $subtotal + $vatAmount;

        $pdo = Database::connection();
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO invoices
        (invoice_number, client_id, status, invoice_date, due_date, subtotal, vat_rate, vat_amount, total, balance_due, notes)
        VALUES (:invoice_number, :client_id, :status, :invoice_date, :due_date, :subtotal, :vat_rate, :vat_amount, :total, :balance_due, :notes)');
        $stmt->execute([
            'invoice_number' => $number,
            'client_id' => $clientId,
            'status' => $data['status'],
            'invoice_date' => $data['invoice_date'],
            'due_date' => $data['due_date'],
            'subtotal' => $subtotal,
            'vat_rate' => $vatRate,
            'vat_amount' => $vatAmount,
            'total' => $total,
            'balance_due' => $total,
            'notes' => $data['notes'] ?: null,
        ]);
        $invoiceId = (int)$pdo->lastInsertId();
        $item = $pdo->prepare('INSERT INTO invoice_items (invoice_id, description, quantity, rate, subtotal)
            VALUES (:invoice_id, :description, :quantity, :rate, :subtotal)');
        $item->execute([
            'invoice_id' => $invoiceId,
            'description' => $data['description'],
            'quantity' => $data['quantity'],
            'rate' => $data['rate'],
            'subtotal' => $subtotal,
        ]);
        $pdo->commit();
    }

    public function createFromQuote(array $quote): void
    {
        $pdo = Database::connection();
        $number = 'INV-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

        $pdo->beginTransaction();
        $stmt = $pdo->prepare('INSERT INTO invoices
        (invoice_number, client_id, quote_id, status, invoice_date, due_date, subtotal, vat_rate, vat_amount, total, balance_due, notes)
        VALUES (:invoice_number, :client_id, :quote_id, :status, :invoice_date, :due_date, :subtotal, :vat_rate, :vat_amount, :total, :balance_due, :notes)');
        $stmt->execute([
            'invoice_number' => $number,
            'client_id' => $quote['client_id'],
            'quote_id' => $quote['id'],
            'status' => 'sent',
            'invoice_date' => date('Y-m-d'),
            'due_date' => date('Y-m-d', strtotime('+14 days')),
            'subtotal' => $quote['subtotal'],
            'vat_rate' => $quote['vat_rate'],
            'vat_amount' => $quote['vat_amount'],
            'total' => $quote['total'],
            'balance_due' => $quote['total'],
            'notes' => $quote['notes'] ?? null,
        ]);
        $invoiceId = (int)$pdo->lastInsertId();
        $itemStmt = $pdo->prepare('INSERT INTO invoice_items (invoice_id, description, quantity, rate, subtotal)
            VALUES (:invoice_id, :description, :quantity, :rate, :subtotal)');
        foreach ($quote['items'] as $item) {
            $itemStmt->execute([
                'invoice_id' => $invoiceId,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'subtotal' => $item['subtotal'],
            ]);
        }
        $pdo->commit();
    }


    public function update(int $invoiceId, int $clientId, array $data): void
    {
        $subtotal = (float)$data['quantity'] * (float)$data['rate'];
        $vatRate = (float)$data['vat_rate'];
        $vatAmount = $subtotal * ($vatRate / 100);
        $total = $subtotal + $vatAmount;

        $pdo = Database::connection();
        $pdo->beginTransaction();
        $stmt = $pdo->prepare('UPDATE invoices SET status=:status, invoice_date=:invoice_date, due_date=:due_date, subtotal=:subtotal, vat_rate=:vat_rate, vat_amount=:vat_amount, total=:total, notes=:notes, balance_due=GREATEST(:total - amount_paid,0) WHERE id=:id AND client_id=:client_id');
        $stmt->execute([
            'status'=>$data['status'],'invoice_date'=>$data['invoice_date'],'due_date'=>$data['due_date'],
            'subtotal'=>$subtotal,'vat_rate'=>$vatRate,'vat_amount'=>$vatAmount,'total'=>$total,
            'notes'=>$data['notes'] ?: null,'id'=>$invoiceId,'client_id'=>$clientId
        ]);
        $item = $pdo->prepare('UPDATE invoice_items SET description=:description, quantity=:quantity, rate=:rate, subtotal=:subtotal WHERE invoice_id=:invoice_id LIMIT 1');
        $item->execute(['description'=>$data['description'],'quantity'=>$data['quantity'],'rate'=>$data['rate'],'subtotal'=>$subtotal,'invoice_id'=>$invoiceId]);
        $pdo->commit();
    }

    public function creditUnpaid(int $invoiceId, int $clientId): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT amount_paid FROM invoices WHERE id=:id AND client_id=:client_id');
        $stmt->execute(['id'=>$invoiceId,'client_id'=>$clientId]);
        $row=$stmt->fetch();
        if (!$row) {
            throw new \RuntimeException('Invoice not found.');
        }
        if ((float)$row['amount_paid'] > 0) {
            throw new \RuntimeException('Only unpaid invoices can be credited.');
        }
        $upd = $pdo->prepare("UPDATE invoices SET status='cancelled', notes=CONCAT(IFNULL(notes,''), '\n[Credited] Invoice credited before payment.'), balance_due=0 WHERE id=:id AND client_id=:client_id");
        $upd->execute(['id'=>$invoiceId,'client_id'=>$clientId]);
    }

    public function recalcStatus(int $invoiceId): void
    {
        $pdo = Database::connection();
        $stmt = $pdo->prepare('SELECT total, amount_paid, due_date FROM invoices WHERE id = :id');
        $stmt->execute(['id' => $invoiceId]);
        $invoice = $stmt->fetch();
        if (!$invoice) {
            return;
        }

        $balance = (float)$invoice['total'] - (float)$invoice['amount_paid'];
        $status = 'sent';
        if ($balance <= 0) {
            $status = 'paid';
            $balance = 0;
        } elseif ((float)$invoice['amount_paid'] > 0) {
            $status = 'partially_paid';
        } elseif (strtotime((string)$invoice['due_date']) < time()) {
            $status = 'overdue';
        }

        $update = $pdo->prepare('UPDATE invoices SET status = :status, balance_due = :balance WHERE id = :id');
        $update->execute(['status' => $status, 'balance' => $balance, 'id' => $invoiceId]);
    }

    public function delete(int $invoiceId, int $clientId): void
    {
        $pdo = Database::connection();
        $pdo->beginTransaction();
        $payments = $pdo->prepare('DELETE p FROM payments p INNER JOIN invoices i ON i.id = p.invoice_id WHERE i.id = :id AND i.client_id = :client_id');
        $payments->execute(['id' => $invoiceId, 'client_id' => $clientId]);
        $items = $pdo->prepare('DELETE ii FROM invoice_items ii INNER JOIN invoices i ON i.id = ii.invoice_id WHERE i.id = :id AND i.client_id = :client_id');
        $items->execute(['id' => $invoiceId, 'client_id' => $clientId]);
        $invoice = $pdo->prepare('DELETE FROM invoices WHERE id = :id AND client_id = :client_id');
        $invoice->execute(['id' => $invoiceId, 'client_id' => $clientId]);
        $pdo->commit();
    }
}
