<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(180px,1fr));margin-bottom:14px">
    <div class="kpi"><div class="label">Total Clients</div><div class="value"><?= (int)$stats['total_clients'] ?></div></div>
    <div class="kpi"><div class="label">Pending Quotes</div><div class="value"><?= (int)$stats['pending_quotes'] ?></div></div>
    <div class="kpi"><div class="label">Unpaid Invoices</div><div class="value"><?= (int)$stats['unpaid_invoices'] ?></div></div>
    <div class="kpi"><div class="label">Overdue Invoices</div><div class="value"><?= (int)$stats['overdue_invoices'] ?></div></div>
</div>
<div class="card">
    <h3>Recent activity</h3>
    <p style="color:var(--muted)">Uploads, downloads, quote status changes, and payments will appear here once modules are connected.</p>
</div>
