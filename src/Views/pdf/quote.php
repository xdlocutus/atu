<div class="head">
  <div>
    <?php if (!empty($company['logo'])): ?><img class="logo" src="<?= htmlspecialchars($company['logo'], ENT_QUOTES, 'UTF-8') ?>" alt="logo"><?php endif; ?>
    <h2><?= htmlspecialchars((string)$company['name'], ENT_QUOTES, 'UTF-8') ?></h2>
    <div class="muted"><?= htmlspecialchars((string)$company['email'], ENT_QUOTES, 'UTF-8') ?> | <?= htmlspecialchars((string)$company['phone'], ENT_QUOTES, 'UTF-8') ?></div>
    <div class="muted"><?= htmlspecialchars((string)$company['address'], ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <div class="right">
    <h2>Quotation</h2>
    <div>Quote #: <?= htmlspecialchars((string)$quote['quote_number'], ENT_QUOTES, 'UTF-8') ?></div>
    <div>Date: <?= htmlspecialchars((string)$quote['quote_date'], ENT_QUOTES, 'UTF-8') ?></div>
    <div>Expiry: <?= htmlspecialchars((string)$quote['expiry_date'], ENT_QUOTES, 'UTF-8') ?></div>
  </div>
</div>
<div><strong>Client:</strong> <?= htmlspecialchars((string)$client['full_name'], ENT_QUOTES, 'UTF-8') ?> (Erf <?= htmlspecialchars((string)$client['erf_number'], ENT_QUOTES, 'UTF-8') ?>)</div>
<table><thead><tr><th>Description</th><th>Qty</th><th>Rate</th><th>Subtotal</th></tr></thead><tbody>
<?php foreach ($quote['items'] as $item): ?><tr><td><?= htmlspecialchars((string)$item['description'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$item['quantity'], ENT_QUOTES, 'UTF-8') ?></td><td>R <?= number_format((float)$item['rate'],2) ?></td><td>R <?= number_format((float)$item['subtotal'],2) ?></td></tr><?php endforeach; ?>
</tbody></table>
<div class="right" style="margin-top:12px">Subtotal: R <?= number_format((float)$quote['subtotal'],2) ?><br><strong>Total: R <?= number_format((float)$quote['total'],2) ?></strong></div>
<div class="note"><strong>Deposit terms:</strong> 50% deposit due before work starts.</div>
