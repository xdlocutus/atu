<div class="card card-soft">
  <div class="card-body">
    <h5 class="mb-3">Add Client</h5>
    <form class="row g-3">
      <div class="col-md-6"><label class="form-label">Full name</label><input class="form-control"/></div>
      <div class="col-md-3"><label class="form-label">Contact number</label><input class="form-control"/></div>
      <div class="col-md-3"><label class="form-label">Email</label><input type="email" class="form-control"/></div>
      <div class="col-md-4"><label class="form-label">Erf number (required)</label><input class="form-control" required/></div>
      <div class="col-12"><label class="form-label">Notes</label><textarea class="form-control" rows="3"></textarea></div>
      <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/>
      <div class="col-12"><button class="btn btn-primary">Save Client</button></div>
    </form>
  </div>
</div>
