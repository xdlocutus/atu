<div class="card">
    <h3>Add Client</h3>
    <div class="row">
        <div style="flex:1 1 260px"><label>Full name</label><input/></div>
        <div style="flex:1 1 220px"><label>Contact number</label><input/></div>
        <div style="flex:1 1 220px"><label>Email</label><input type="email"/></div>
        <div style="flex:1 1 180px"><label>Erf number (required)</label><input required/></div>
    </div>
    <label>Notes</label><textarea rows="3"></textarea>
    <input type="hidden" name="csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>"/>
    <div style="margin-top:10px"><button class="btn">Save Client</button></div>
</div>
