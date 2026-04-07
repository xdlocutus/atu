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

<div class="detail-grid">
  <div class="block">
    <h3>Payment Details</h3>
    <div class="detail-row"><span>Name:</span> <strong>A.T.UNGERER</strong></div>
    <div class="detail-row"><span>Bank:</span> <strong>ABSA</strong></div>
    <div class="detail-row"><span>Acc.:</span> <strong>1102351386</strong></div>
    <div class="detail-row"><span>Type:</span> <strong>CHEQUE</strong></div>
    <div class="detail-row"><span>B/Code:</span> <strong>632005</strong></div>
    <div class="detail-row"><span>Pay ref:</span> <strong><?= htmlspecialchars((string)$quote['quote_number'], ENT_QUOTES, 'UTF-8') ?></strong></div>
  </div>

  <div class="block">
    <h3>Quotation Terms</h3>
    <ul class="terms-list">
      <li>50% deposit upon acceptance of this quotation.</li>
      <li>Full payment including submission fees when plans are submitted.</li>
      <li>This includes all prints, submission of plans untill passed.</li>
    </ul>
  </div>
</div>

<div class="block appointment-block">
  I ............................................hereby appoint Mr A.T.Ungerer as the draughtsperson to
  complete the plans of my property and accept the quotation as binding
  Date:.....................
</div>
