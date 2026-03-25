<div class="row g-3 mb-3">
    <div class="col-6 col-xl-3"><div class="card card-soft"><div class="card-body"><div class="text-secondary small">Total Clients</div><div class="fs-3 fw-semibold"><?= (int)$stats['total_clients'] ?></div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card card-soft"><div class="card-body"><div class="text-secondary small">Pending Quotes</div><div class="fs-3 fw-semibold"><?= (int)$stats['pending_quotes'] ?></div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card card-soft"><div class="card-body"><div class="text-secondary small">Unpaid Invoices</div><div class="fs-3 fw-semibold"><?= (int)$stats['unpaid_invoices'] ?></div></div></div></div>
    <div class="col-6 col-xl-3"><div class="card card-soft"><div class="card-body"><div class="text-secondary small">Overdue Invoices</div><div class="fs-3 fw-semibold"><?= (int)$stats['overdue_invoices'] ?></div></div></div></div>
</div>
<div class="card card-soft"><div class="card-body"><h5>Recent activity</h5><p class="text-secondary mb-0">Uploads, downloads, quote changes, and payments will appear here once services are connected.</p></div></div>
