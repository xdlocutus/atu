<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><?= htmlspecialchars($title ?? 'Control Panel', ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        :root{--bg:#0b1020;--card:#131a2e;--card2:#1b2542;--text:#e7ecff;--muted:#99a7cf;--accent:#4f8cff;--ok:#2eb67d;--warn:#f5a524}
        *{box-sizing:border-box}body{margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:linear-gradient(120deg,#0b1020,#0f1730);color:var(--text)}
        .app{display:grid;grid-template-columns:240px 1fr;min-height:100vh}.side{background:#0a0f1d;padding:20px;border-right:1px solid #243053}
        .brand{font-size:1.05rem;font-weight:700;margin-bottom:1rem}.nav a{display:block;padding:10px 12px;border-radius:10px;color:var(--muted);text-decoration:none;margin-bottom:5px}
        .nav a:hover,.nav a.active{background:var(--card);color:#fff}.main{padding:20px}.top{display:flex;justify-content:space-between;gap:16px;align-items:center;margin-bottom:16px}
        .search{width:min(580px,100%);background:var(--card);border:1px solid #2a3761;color:#fff;border-radius:12px;padding:12px}
        .grid{display:grid;gap:14px}.kpi{background:var(--card);border:1px solid #2a3761;border-radius:14px;padding:16px}.kpi .label{color:var(--muted);font-size:.9rem}.kpi .value{font-size:1.6rem;font-weight:700;margin-top:6px}
        .card{background:var(--card);border:1px solid #2a3761;border-radius:14px;padding:16px}.row{display:flex;gap:12px;flex-wrap:wrap}.btn{border:0;background:var(--accent);color:white;padding:10px 14px;border-radius:10px;cursor:pointer}
        input,select,textarea{width:100%;padding:10px;border-radius:10px;background:var(--card2);border:1px solid #334575;color:white}.table{width:100%;border-collapse:collapse}th,td{padding:10px;border-bottom:1px solid #2b3863;text-align:left}
        .tag{padding:4px 8px;border-radius:999px;background:#223056;color:#b6c8ff;font-size:.8rem}
        @media(max-width:900px){.app{grid-template-columns:1fr}.side{position:sticky;top:0;z-index:5}}
    </style>
</head>
<body>
<div class="app">
    <aside class="side">
        <div class="brand">Drafting Control</div>
        <nav class="nav">
            <?php foreach ([
                'dashboard'=>'Dashboard','clients'=>'Clients','files'=>'Files','quotes'=>'Quotes','invoices'=>'Invoices','payments'=>'Payments','statements'=>'Statements','settings'=>'Settings'
            ] as $key=>$label): ?>
                <a class="<?= (($_GET['r'] ?? 'dashboard') === $key) ? 'active' : '' ?>" href="?r=<?= $key ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </nav>
    </aside>
    <main class="main">
        <div class="top">
            <h2 style="margin:0"><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></h2>
            <input class="search" placeholder="Quick search: Erf number, client, phone, email"/>
        </div>
        <?= $content ?>
    </main>
</div>
</body>
</html>
