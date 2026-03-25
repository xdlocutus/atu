<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><?= htmlspecialchars($title ?? 'Control Panel', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:#f5f7fb}
        .sidebar{min-height:100vh;background:#111827}
        .sidebar .nav-link{color:#c7d2fe;border-radius:.5rem}
        .sidebar .nav-link.active,.sidebar .nav-link:hover{background:#1f2937;color:#fff}
        .card-soft{border:0;box-shadow:0 6px 20px rgba(17,24,39,.08)}
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <aside class="col-12 col-lg-2 sidebar p-3">
            <h5 class="text-white mb-4">Drafting Control</h5>
            <nav class="nav flex-column gap-1">
                <?php foreach ([
                    'dashboard'=>'Dashboard','clients'=>'Clients','settings'=>'Settings'
                ] as $key=>$label): ?>
                    <a class="nav-link <?= (($_GET['r'] ?? 'dashboard') === $key) ? 'active' : '' ?>" href="?r=<?= $key ?>"><?= $label ?></a>
                <?php endforeach; ?>
            </nav>
        </aside>
        <main class="col-12 col-lg-10 p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
                <h3 class="mb-0"><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
                <form method="get" class="d-flex gap-2" style="max-width:560px;width:100%"><input type="hidden" name="r" value="clients"/><input name="q" class="form-control" placeholder="Quick search: Erf number, client, phone, email"/><button class="btn btn-outline-primary" type="submit">Go</button></form>
            </div>
            <?= $content ?>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
