<div class="d-flex justify-content-between align-items-center mb-3">
  <form method="get" class="d-flex gap-2" style="max-width:640px;width:100%">
    <input type="hidden" name="r" value="clients">
    <input name="q" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>" class="form-control" placeholder="Search by Erf number, client name, phone, email">
    <button class="btn btn-outline-primary" type="submit">Search</button>
  </form>
  <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#newClientForm">+ New Client</button>
</div>

<?php if (!empty($db_error)): ?>
  <div class="alert alert-warning"><?= htmlspecialchars((string)$db_error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<?php if (!empty($flash_error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars((string)$flash_error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>
<?php if (!empty($flash_success)): ?>
  <div class="alert alert-success"><?= htmlspecialchars((string)$flash_success, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="collapse mb-3" id="newClientForm">
  <div class="card card-soft">
    <div class="card-body">
      <h5 class="mb-3">Create Client</h5>
      <form method="post" class="row g-3">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/>
        <div class="col-md-6"><label class="form-label">Full name *</label><input name="full_name" class="form-control" required/></div>
        <div class="col-md-3"><label class="form-label">Contact number</label><input name="contact_number" class="form-control"/></div>
        <div class="col-md-3"><label class="form-label">Email</label><input name="email" type="email" class="form-control"/></div>
        <div class="col-md-4"><label class="form-label">Erf number *</label><input name="erf_number" class="form-control" required/></div>
        <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="3"></textarea></div>
        <div class="col-12"><button class="btn btn-primary" type="submit">Save Client</button></div>
      </form>
    </div>
  </div>
</div>

<div class="card card-soft">
  <div class="card-body">
    <h5 class="mb-3">All Clients</h5>
    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>Erf Number</th>
            <th>Client Name</th>
            <th>Phone</th>
            <th>Email</th>
            <th>Created</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($clients)): ?>
            <tr><td colspan="5" class="text-secondary">No clients found.</td></tr>
          <?php else: ?>
            <?php foreach ($clients as $client): ?>
              <tr>
                <td><span class="badge text-bg-primary"><?= htmlspecialchars((string)$client['erf_number'], ENT_QUOTES, 'UTF-8') ?></span></td>
                <td><?= htmlspecialchars((string)$client['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string)($client['contact_number'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string)($client['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string)$client['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
