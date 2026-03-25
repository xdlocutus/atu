<div class="head">
  <div>
    <?php if (!empty($company['logo'])): ?><img class="logo" src="<?= htmlspecialchars($company['logo'], ENT_QUOTES, 'UTF-8') ?>" alt="logo"><?php endif; ?>
    <h2><?= htmlspecialchars((string)$company['name'], ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="muted"><?= htmlspecialchars((string)$company['email'], ENT_QUOTES, 'UTF-8') ?> | <?= htmlspecialchars((string)$company['phone'], ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <div class="right">
    <h2>Client Statement</h2>
    <div>Client: <?= htmlspecialchars((string)$client['full_name'], ENT_QUOTES, 'UTF-8') ?></div>
    <div>Erf: <?= htmlspecialchars((string)$client['erf_number'], ENT_QUOTES, 'UTF-8') ?></div>
  </div>
</div>
<h4>Invoices</h4>
<table><thead><tr><th>#</th><th>Status</th><th>Total</th><th>Balance</th></tr></thead><tbody>
<?php foreach ($invoices as $i): ?><tr><td><?= htmlspecialchars((string)$i['invoice_number'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$i['status'], ENT_QUOTES, 'UTF-8') ?></td><td><?= number_format((float)$i['total'],2) ?></td><td><?= number_format((float)$i['balance_due'],2) ?></td></tr><?php endforeach; ?>
</tbody></table>
<h4>Payments</h4>
<table><thead><tr><th>Date</th><th>Invoice</th><th>Amount</th></tr></thead><tbody>
<?php foreach ($payments as $p): ?><tr><td><?= htmlspecialchars((string)$p['payment_date'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$p['invoice_number'], ENT_QUOTES, 'UTF-8') ?></td><td><?= number_format((float)$p['amount'],2) ?></td></tr><?php endforeach; ?>
</tbody></table>
