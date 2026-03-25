<div class="card card-soft" style="max-width:480px">
  <div class="card-body">
    <h4 class="mb-3">Secure Login</h4>
    <?php if (!empty($flash_success)): ?><div class="alert alert-success"><?= htmlspecialchars((string)$flash_success, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if (!empty($flash_error)): ?><div class="alert alert-danger"><?= htmlspecialchars((string)$flash_error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <form method="post" class="row g-2">
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/>
      <input type="hidden" name="action" value="login"/>
      <div class="col-12"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
      <div class="col-12"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
      <div class="col-12"><button class="btn btn-primary w-100">Login</button></div>
    </form>
  </div>
</div>
