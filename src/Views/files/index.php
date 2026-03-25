<div class="card">
    <h3>Client File Manager</h3>
    <p style="color:var(--muted)">Categories: DWG Plans, PDFs, Images, Supporting Documents, Council / Compliance, Final Approved.</p>
    <div style="border:2px dashed #3a4c80;padding:24px;border-radius:14px;text-align:center;margin:12px 0">Drag and drop files here (multi-upload)</div>
    <div class="row">
        <select><option>DWG Plans</option><option>PDFs</option><option>Images</option><option>Supporting Documents</option><option>Council / Compliance Documents</option><option>Final Approved Plans</option></select>
        <input placeholder="File notes / revision notes"/>
    </div>
    <input type="hidden" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/>
    <div style="margin-top:10px"><button class="btn">Upload</button></div>
</div>
