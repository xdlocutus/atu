<div class="head">
  <div>
    <?php if (!empty($company['logo'])): ?><img class="logo" src="<?= htmlspecialchars($company['logo'], ENT_QUOTES, 'UTF-8') ?>" alt="logo"><?php endif; ?>
    <h2><?= htmlspecialchars((string)$company['name'], ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="muted"><?= htmlspecialchars((string)$company['email'], ENT_QUOTES, 'UTF-8') ?> | <?= htmlspecialchars((string)$company['phone'], ENT_QUOTES, 'UTF-8') ?></div>
    <div class="muted"><?= htmlspecialchars((string)$company['address'], ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <div class="right">
    <h2>Invoice</h2>
    <div>Invoice #: <?= htmlspecialchars((string)$invoice['invoice_number'], ENT_QUOTES, 'UTF-8') ?></div>
    <div>Date: <?= htmlspecialchars((string)$invoice['invoice_date'], ENT_QUOTES, 'UTF-8') ?></div>
    <div>Due: <?= htmlspecialchars((string)$invoice['due_date'], ENT_QUOTES, 'UTF-8') ?></div>
  </div>
</div>
<div><strong>Client:</strong> <?= htmlspecialchars((string)$client['full_name'], ENT_QUOTES, 'UTF-8') ?> (Erf <?= htmlspecialchars((string)$client['erf_number'], ENT_QUOTES, 'UTF-8') ?>)</div>
<?php
$statusLabel = match ((string)$invoice['status']) {
    'paid' => 'Paid',
    'partially_paid' => 'Partially Paid',
    'sent', 'draft', 'overdue' => 'Due',
    default => ucwords(str_replace('_', ' ', (string)$invoice['status'])),
};
?>
<table><thead><tr><th>Status</th><th>Total</th><th>Paid</th><th>Balance</th></tr></thead><tbody>
<tr><td><?= htmlspecialchars($statusLabel, ENT_QUOTES, 'UTF-8') ?></td><td>R <?= number_format((float)$invoice['total'],2) ?></td><td>R <?= number_format((float)$invoice['amount_paid'],2) ?></td><td>R <?= number_format((float)$invoice['balance_due'],2) ?></td></tr>
</tbody></table>
<div class="note"><strong>Deposit terms:</strong> 50% deposit due before work starts.</div>
