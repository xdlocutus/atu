<?php
$formatStatus = static function (string $status): string {
    return match ($status) {
        'paid' => 'Paid',
        'partially_paid' => 'Partially Paid',
        'sent', 'draft', 'overdue' => 'Due',
        'cancelled' => 'Cancelled',
        'accepted' => 'Accepted',
        'rejected' => 'Rejected',
        'expired' => 'Expired',
        default => ucwords(str_replace('_', ' ', $status)),
    };
};
?>
<?php if (!empty($db_error)): ?>
  <div class="alert alert-warning"><?= htmlspecialchars((string)$db_error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (!empty($flash_error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars((string)$flash_error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (!empty($flash_success)): ?>
  <div class="alert alert-success"><?= htmlspecialchars((string)$flash_success, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if ($client): ?>
<div class="card card-soft mb-3"><div class="card-body">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <h4 class="mb-1"><?= htmlspecialchars((string)$client['full_name'], ENT_QUOTES, 'UTF-8') ?></h4>
      <div class="text-secondary">Erf: <strong><?= htmlspecialchars((string)$client['erf_number'], ENT_QUOTES, 'UTF-8') ?></strong> | <?= htmlspecialchars((string)($client['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?> | <?= htmlspecialchars((string)($client['contact_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
    </div>
    <a class="btn btn-outline-secondary" href="?r=clients">Back to Clients</a>
  </div>
</div></div>

<ul class="nav nav-tabs mb-3">
  <?php foreach (['files'=>'Files','quotes'=>'Quotes','invoices'=>'Invoices','payments'=>'Payments','statements'=>'Statements'] as $key => $label): ?>
    <li class="nav-item"><a class="nav-link <?= $tab === $key ? 'active' : '' ?>" href="?r=client&id=<?= (int)$client['id'] ?>&tab=<?= $key ?>"><?= $label ?></a></li>
  <?php endforeach; ?>
</ul>

<?php if ($tab === 'files'): ?>
  <div class="card card-soft mb-3"><div class="card-body">
    <div class="d-flex justify-content-between align-items-center"><h5 class="mb-0">Upload File</h5><a class="btn btn-outline-primary btn-sm" href="?r=download_client_zip&client_id=<?= (int)$client['id'] ?>">Download All (ZIP)</a></div>
    <form method="post" enctype="multipart/form-data" class="row g-2">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/>
      <input type="hidden" name="action" value="upload_document"/>
      <input type="hidden" name="client_id" value="<?= (int)$client['id'] ?>"/>
      <div class="col-md-3"><select name="category" class="form-select"><option value="dwg_plans">DWG Plans</option><option value="pdfs">PDFs</option><option value="images">Images</option><option value="supporting">Supporting</option><option value="council_compliance">Council/Compliance</option><option value="final_approved">Final Approved</option></select></div>
      <div class="col-md-4"><input class="form-control" type="file" name="upload[]" multiple required/></div>
      <div class="col-md-3"><input class="form-control" name="notes" placeholder="Notes"/></div>
      <div class="col-md-2"><button class="btn btn-primary w-100">Upload</button></div>
    </form>
  </div></div>
  <div class="card card-soft"><div class="card-body"><h5>Client Files</h5><div class="table-responsive"><table class="table"><thead><tr><th>Category</th><th>Name</th><th>Size</th><th>Notes</th><th>Date</th><th>AutoCAD Path</th><th></th></tr></thead><tbody>
  <?php foreach ($documents as $d): ?><tr><td><?= htmlspecialchars((string)$d['category'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$d['original_name'], ENT_QUOTES, 'UTF-8') ?></td><td><?= number_format(((int)$d['size_bytes'])/1024, 1) ?> KB</td><td><?= htmlspecialchars((string)($d['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$d['created_at'], ENT_QUOTES, 'UTF-8') ?></td><td><small><?= htmlspecialchars((string)($d['autocad_path'] ?? ''), ENT_QUOTES, 'UTF-8') ?></small></td><td class="d-flex gap-1"><a class="btn btn-sm btn-outline-primary" href="?r=download_document&client_id=<?= (int)$client['id'] ?>&document_id=<?= (int)$d['id'] ?>">Download</a><?php if (!empty($d['can_open_cad'])): ?><a class="btn btn-sm btn-outline-secondary" href="<?= htmlspecialchars((string)$d['autocad_uri'], ENT_QUOTES, 'UTF-8') ?>">Open CAD</a><?php endif; ?><form method="post" class="m-0" onsubmit="return confirm('Are you sure you want to delete this file?');"><input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/><input type="hidden" name="action" value="delete_document"/><input type="hidden" name="client_id" value="<?= (int)$client['id'] ?>"/><input type="hidden" name="document_id" value="<?= (int)$d['id'] ?>"/><button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form></td></tr><?php endforeach; ?>
  <?php if (empty($documents)): ?><tr><td colspan="7" class="text-secondary">No files uploaded yet.</td></tr><?php endif; ?>
  </tbody></table></div></div></div>
<?php endif; ?>

<?php if ($tab === 'quotes'): ?>
  <div class="card card-soft mb-3"><div class="card-body"><h5>Create Quote</h5>
  <form method="post" class="row g-2">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/><input type="hidden" name="action" value="create_quote"/><input type="hidden" name="client_id" value="<?= (int)$client['id'] ?>"/>
    <div class="col-md-2"><input name="quote_number" class="form-control" placeholder="Quote # (e.g. 202608)"/></div><div class="col-md-2"><input name="quote_date" type="date" value="<?= date('Y-m-d') ?>" class="form-control"/></div><div class="col-md-2"><input name="expiry_date" type="date" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" class="form-control"/></div><div class="col-md-6 small text-muted d-flex align-items-center">Line items (add up to 3 rows)</div>
    <div class="col-md-6"><input name="item_description[]" class="form-control" placeholder="Line item description" required/></div><div class="col-md-2"><input name="item_quantity[]" type="number" step="0.01" value="1" class="form-control"/></div><div class="col-md-2"><input name="item_rate[]" type="number" step="0.01" class="form-control" placeholder="Rate"/></div><div class="col-md-2"></div>
    <div class="col-md-6"><input name="item_description[]" class="form-control" placeholder="Line item description"/></div><div class="col-md-2"><input name="item_quantity[]" type="number" step="0.01" class="form-control"/></div><div class="col-md-2"><input name="item_rate[]" type="number" step="0.01" class="form-control" placeholder="Rate"/></div><div class="col-md-2"></div>
    <div class="col-md-6"><input name="item_description[]" class="form-control" placeholder="Line item description"/></div><div class="col-md-2"><input name="item_quantity[]" type="number" step="0.01" class="form-control"/></div><div class="col-md-2"><input name="item_rate[]" type="number" step="0.01" class="form-control" placeholder="Rate"/></div><div class="col-md-2"></div>
    <div class="col-md-3"><select name="status" class="form-select"><option value="draft">Draft</option><option value="sent">Sent</option><option value="accepted">Accepted</option><option value="rejected">Rejected</option><option value="expired">Expired</option></select></div><div class="col-md-4"><input name="notes" class="form-control" placeholder="Notes"/></div><div class="col-md-3"><input name="terms" class="form-control" placeholder="Terms"/></div><div class="col-md-2"><button class="btn btn-primary w-100">Save Quote</button></div>
  </form></div></div>
  <div class="card card-soft"><div class="card-body"><h5>Quotes</h5><table class="table"><thead><tr><th>Quote #</th><th>Status</th><th>Date</th><th>Expiry</th><th>Total</th><th>Actions</th></tr></thead><tbody>
  <?php foreach ($quotes as $q): ?><tr><td><?= htmlspecialchars((string)$q['quote_number'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($formatStatus((string)$q['status']), ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$q['quote_date'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$q['expiry_date'], ENT_QUOTES, 'UTF-8') ?></td><td>R <?= number_format((float)$q['total'],2) ?></td><td class="d-flex gap-1"><a class="btn btn-sm btn-outline-secondary" href="?r=export_quote&client_id=<?= (int)$client['id'] ?>&quote_id=<?= (int)$q['id'] ?>" target="_blank">PDF</a><form method="post" class="m-0"><input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/><input type="hidden" name="action" value="convert_quote"/><input type="hidden" name="client_id" value="<?= (int)$client['id'] ?>"/><input type="hidden" name="quote_id" value="<?= (int)$q['id'] ?>"/><button class="btn btn-sm btn-outline-primary">Convert</button></form></td></tr><?php endforeach; ?>
  <?php if (empty($quotes)): ?><tr><td colspan="6" class="text-secondary">No quotes yet.</td></tr><?php endif; ?>
  </tbody></table></div><p class="small text-muted">Deposit policy: 50% deposit due before work starts.</p>
  <?php if (!empty($quotes)): ?>
  <div class="mt-3 border-top pt-3"><h6>Edit Quote</h6>
    <form method="post" class="row g-2">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/><input type="hidden" name="action" value="update_quote"/><input type="hidden" name="client_id" value="<?= (int)$client['id'] ?>"/>
      <div class="col-md-2"><select name="quote_id" class="form-select"><?php foreach ($quotes as $q): ?><option value="<?= (int)$q['id'] ?>"><?= htmlspecialchars((string)$q['quote_number'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
      <div class="col-md-2"><input name="quote_number" class="form-control" placeholder="Quote #"/></div><div class="col-md-2"><input name="quote_date" type="date" value="<?= date('Y-m-d') ?>" class="form-control"/></div><div class="col-md-2"><input name="expiry_date" type="date" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" class="form-control"/></div><div class="col-md-6 small text-muted d-flex align-items-center">Replace existing line items (up to 3 rows)</div>
      <div class="col-md-6"><input name="item_description[]" class="form-control" placeholder="Line item description" required/></div><div class="col-md-2"><input name="item_quantity[]" value="1" type="number" step="0.01" class="form-control"/></div><div class="col-md-2"><input name="item_rate[]" type="number" step="0.01" class="form-control"/></div><div class="col-md-2"></div>
      <div class="col-md-6"><input name="item_description[]" class="form-control" placeholder="Line item description"/></div><div class="col-md-2"><input name="item_quantity[]" type="number" step="0.01" class="form-control"/></div><div class="col-md-2"><input name="item_rate[]" type="number" step="0.01" class="form-control"/></div><div class="col-md-2"></div>
      <div class="col-md-6"><input name="item_description[]" class="form-control" placeholder="Line item description"/></div><div class="col-md-2"><input name="item_quantity[]" type="number" step="0.01" class="form-control"/></div><div class="col-md-2"><input name="item_rate[]" type="number" step="0.01" class="form-control"/></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Update</button></div>
      <div class="col-md-2"><select name="status" class="form-select"><option value="draft">Draft</option><option value="sent">Sent</option><option value="accepted">Accepted</option><option value="rejected">Rejected</option><option value="expired">Expired</option></select></div><div class="col-md-5"><input name="notes" class="form-control" placeholder="Notes"/></div><div class="col-md-5"><input name="terms" class="form-control" placeholder="Terms"/></div>
    </form>
  </div>
  <?php endif; ?>
</div></div>
<?php endif; ?>

<?php if ($tab === 'invoices'): ?>
  <div class="card card-soft mb-3"><div class="card-body"><h5>Create Manual Invoice</h5>
  <form method="post" class="row g-2">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/><input type="hidden" name="action" value="create_invoice"/><input type="hidden" name="client_id" value="<?= (int)$client['id'] ?>"/>
    <div class="col-md-3"><input name="description" class="form-control" placeholder="Description" required/></div><div class="col-md-1"><input name="quantity" type="number" step="0.01" value="1" class="form-control"/></div><div class="col-md-2"><input name="rate" type="number" step="0.01" class="form-control" placeholder="Rate"/></div><div class="col-md-2"><input name="invoice_date" type="date" value="<?= date('Y-m-d') ?>" class="form-control"/></div><div class="col-md-2"><input name="due_date" type="date" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" class="form-control"/></div>
    <div class="col-md-3"><select name="status" class="form-select"><option value="draft">Draft</option><option value="sent">Sent</option><option value="cancelled">Cancelled</option></select></div><div class="col-md-7"><input name="notes" class="form-control" placeholder="Notes"/></div><div class="col-md-2"><button class="btn btn-primary w-100">Save Invoice</button></div>
  </form></div></div>
  <div class="card card-soft"><div class="card-body"><h5>Invoices</h5><table class="table"><thead><tr><th>Invoice #</th><th>Status</th><th>Due</th><th>Total</th><th>Paid</th><th>Balance</th><th>Actions</th></tr></thead><tbody>
  <?php foreach ($invoices as $i): ?><tr><td><?= htmlspecialchars((string)$i['invoice_number'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($formatStatus((string)$i['status']), ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$i['due_date'], ENT_QUOTES, 'UTF-8') ?></td><td>R <?= number_format((float)$i['total'],2) ?></td><td>R <?= number_format((float)$i['amount_paid'],2) ?></td><td>R <?= number_format((float)$i['balance_due'],2) ?></td><td class="d-flex gap-1"><a class="btn btn-sm btn-outline-secondary" target="_blank" href="?r=export_invoice&client_id=<?= (int)$client['id'] ?>&invoice_id=<?= (int)$i['id'] ?>">PDF</a><form method="post" class="m-0"><input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/><input type="hidden" name="action" value="credit_invoice"/><input type="hidden" name="client_id" value="<?= (int)$client['id'] ?>"/><input type="hidden" name="invoice_id" value="<?= (int)$i['id'] ?>"/><button class="btn btn-sm btn-outline-danger">Credit</button></form><form method="post" class="m-0" onsubmit="return confirm('Are you sure you want to delete this invoice?');"><input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/><input type="hidden" name="action" value="delete_invoice"/><input type="hidden" name="client_id" value="<?= (int)$client['id'] ?>"/><input type="hidden" name="invoice_id" value="<?= (int)$i['id'] ?>"/><button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr><?php endforeach; ?>
  <?php if (empty($invoices)): ?><tr><td colspan="7" class="text-secondary">No invoices yet.</td></tr><?php endif; ?>
  </tbody></table></div><p class="small text-muted">Deposit policy: 50% deposit due before work starts.</p>
  <?php if (!empty($invoices)): ?>
  <div class="mt-3 border-top pt-3"><h6>Edit Invoice</h6>
    <form method="post" class="row g-2">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/><input type="hidden" name="action" value="update_invoice"/><input type="hidden" name="client_id" value="<?= (int)$client['id'] ?>"/>
      <div class="col-md-2"><select name="invoice_id" class="form-select"><?php foreach ($invoices as $i): ?><option value="<?= (int)$i['id'] ?>"><?= htmlspecialchars((string)$i['invoice_number'], ENT_QUOTES, 'UTF-8') ?></option><?php endforeach; ?></select></div>
      <div class="col-md-2"><input name="description" class="form-control" placeholder="Description" required/></div><div class="col-md-1"><input name="quantity" value="1" type="number" step="0.01" class="form-control"/></div><div class="col-md-1"><input name="rate" type="number" step="0.01" class="form-control"/></div><div class="col-md-2"><input name="invoice_date" type="date" value="<?= date('Y-m-d') ?>" class="form-control"/></div><div class="col-md-2"><input name="due_date" type="date" value="<?= date('Y-m-d', strtotime('+14 days')) ?>" class="form-control"/></div><div class="col-md-1"><button class="btn btn-outline-primary w-100">Update</button></div>
      <div class="col-md-2"><select name="status" class="form-select"><option value="draft">Draft</option><option value="sent">Sent</option><option value="cancelled">Cancelled</option></select></div><div class="col-md-10"><input name="notes" class="form-control" placeholder="Notes"/></div>
    </form>
  </div>
  <?php endif; ?>
</div></div>
<?php endif; ?>

<?php if ($tab === 'payments'): ?>
  <div class="card card-soft mb-3"><div class="card-body"><h5>Capture Payment</h5>
  <form method="post" class="row g-2">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/><input type="hidden" name="action" value="create_payment"/><input type="hidden" name="client_id" value="<?= (int)$client['id'] ?>"/>
    <div class="col-md-3"><select name="invoice_id" class="form-select" required><?php foreach ($invoices as $i): ?><option value="<?= (int)$i['id'] ?>"><?= htmlspecialchars((string)$i['invoice_number'], ENT_QUOTES, 'UTF-8') ?> (R <?= number_format((float)$i['balance_due'],2) ?>)</option><?php endforeach; ?></select></div>
    <div class="col-md-2"><input name="payment_date" type="date" value="<?= date('Y-m-d') ?>" class="form-control"/></div><div class="col-md-2"><input name="amount" type="number" step="0.01" class="form-control" placeholder="Amount" required/></div><div class="col-md-2"><input name="method" class="form-control" value="EFT"/></div><div class="col-md-2"><input name="reference_number" class="form-control" placeholder="Reference"/></div><div class="col-md-1"><button class="btn btn-primary w-100">Save</button></div>
    <div class="col-12"><input name="notes" class="form-control" placeholder="Notes"/></div>
  </form></div></div>
  <div class="card card-soft"><div class="card-body"><h5>Payments</h5><table class="table"><thead><tr><th>Date</th><th>Invoice</th><th>Amount</th><th>Method</th><th>Reference</th><th></th></tr></thead><tbody>
  <?php foreach ($payments as $p): ?><tr><td><?= htmlspecialchars((string)$p['payment_date'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$p['invoice_number'], ENT_QUOTES, 'UTF-8') ?></td><td>R <?= number_format((float)$p['amount'],2) ?></td><td><?= htmlspecialchars((string)$p['method'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)($p['reference_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td><td><form method="post" class="m-0"><input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/><input type="hidden" name="action" value="delete_payment"/><input type="hidden" name="client_id" value="<?= (int)$client['id'] ?>"/><input type="hidden" name="payment_id" value="<?= (int)$p['id'] ?>"/><button class="btn btn-sm btn-outline-danger">Delete</button></form></td></tr><?php endforeach; ?>
  <?php if (empty($payments)): ?><tr><td colspan="6" class="text-secondary">No payments yet.</td></tr><?php endif; ?>
  </tbody></table></div></div>
<?php endif; ?>

<?php if ($tab === 'statements'): ?>
  <div class="card card-soft"><div class="card-body"><div class="d-flex justify-content-between"><h5>Statement</h5><a class="btn btn-sm btn-outline-secondary" target="_blank" href="?r=export_statement&client_id=<?= (int)$client['id'] ?>">PDF</a></div><p class="text-secondary">Per-client statement summary.</p>
    <h6>Invoices</h6><table class="table table-sm"><thead><tr><th>#</th><th>Status</th><th>Date</th><th>Total</th><th>Balance</th></tr></thead><tbody><?php foreach ($statement['invoices'] as $i): ?><tr><td><?= htmlspecialchars((string)$i['invoice_number'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($formatStatus((string)$i['status']), ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$i['invoice_date'], ENT_QUOTES, 'UTF-8') ?></td><td>R <?= number_format((float)$i['total'],2) ?></td><td>R <?= number_format((float)$i['balance_due'],2) ?></td></tr><?php endforeach; ?></tbody></table>
    <h6>Payments</h6><table class="table table-sm"><thead><tr><th>Date</th><th>Invoice</th><th>Amount</th></tr></thead><tbody><?php foreach ($statement['payments'] as $p): ?><tr><td><?= htmlspecialchars((string)$p['payment_date'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$p['invoice_number'], ENT_QUOTES, 'UTF-8') ?></td><td>R <?= number_format((float)$p['amount'],2) ?></td></tr><?php endforeach; ?></tbody></table>
  </div></div>
<?php endif; ?>

<?php endif; ?>
