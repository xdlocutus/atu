<div class="card card-soft">
  <div class="card-body">
    <h5>Client File Manager</h5>
    <p class="text-secondary">Categories: DWG Plans, PDFs, Images, Supporting Documents, Council / Compliance, Final Approved.</p>
    <div class="border border-2 border-secondary-subtle rounded p-4 text-center bg-light mb-3">Drag and drop files here (multi-upload)</div>
    <div class="row g-3">
      <div class="col-md-4"><select class="form-select"><option>DWG Plans</option><option>PDFs</option><option>Images</option><option>Supporting Documents</option><option>Council / Compliance Documents</option><option>Final Approved Plans</option></select></div>
      <div class="col-md-8"><input class="form-control" placeholder="File notes / revision notes"/></div>
    </div>
    <input type="hidden" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/>
    <button class="btn btn-primary mt-3">Upload</button>
  </div>
</div>
