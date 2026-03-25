<div class="card card-soft mb-3"><div class="card-body">
  <h5>Create User</h5>
  <?php if (!empty($flash_error)): ?><div class="alert alert-danger"><?= htmlspecialchars((string)$flash_error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
  <?php if (!empty($flash_success)): ?><div class="alert alert-success"><?= htmlspecialchars((string)$flash_success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
  <form method="post" class="row g-2">
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/>
    <input type="hidden" name="action" value="create_user"/>
    <div class="col-md-3"><input class="form-control" name="full_name" placeholder="Full name" required></div>
    <div class="col-md-3"><input class="form-control" type="email" name="email" placeholder="Email" required></div>
    <div class="col-md-3"><input class="form-control" type="password" name="password" placeholder="Password" required></div>
    <div class="col-md-2"><select class="form-select" name="role"><option>admin</option><option>manager</option><option selected>staff</option><option>viewer</option></select></div>
    <div class="col-md-1"><button class="btn btn-primary w-100">Save</button></div>
  </form>
</div></div>
<div class="card card-soft"><div class="card-body"><h5>Users</h5>
<table class="table"><thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th></tr></thead><tbody>
<?php foreach ($users as $u): ?><tr><td><?= htmlspecialchars((string)$u['full_name'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$u['email'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string)$u['role'], ENT_QUOTES, 'UTF-8') ?></td><td><?= (int)$u['is_active'] ? 'Active' : 'Disabled' ?></td><td><?= htmlspecialchars((string)$u['created_at'], ENT_QUOTES, 'UTF-8') ?></td></tr><?php endforeach; ?>
<?php if (empty($users)): ?><tr><td colspan="5" class="text-secondary">No users found.</td></tr><?php endif; ?>
</tbody></table></div></div>
