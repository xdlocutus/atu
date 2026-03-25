<!doctype html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title><?= htmlspecialchars($title ?? 'Control Panel', ENT_QUOTES, 'UTF-8') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{background:var(--bs-body-bg)}
        .sidebar{background:#111827;min-height:100vh;width:260px;transition:width .2s ease}
        .sidebar.collapsed{width:86px}
        .sidebar .nav-link{color:#c7d2fe;border-radius:.5rem;white-space:nowrap;overflow:hidden}
        .sidebar .nav-link.active,.sidebar .nav-link:hover{background:#1f2937;color:#fff}
        .card-soft{border:0;box-shadow:0 6px 20px rgba(17,24,39,.08)}
        .shell{display:flex;min-height:100vh}
        .main-wrap{flex:1;min-width:0}
        .brand-text{display:inline}
        .sidebar.collapsed .brand-text,.sidebar.collapsed .link-text{display:none}
        @media (max-width: 991.98px){.sidebar{display:none}.shell{display:block}}
    </style>
</head>
<body>
<div class="shell">
    <aside id="desktopSidebar" class="sidebar p-3 d-none d-lg-block">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="text-white mb-0"><span class="brand-text">Drafting Control</span></h5>
            <button class="btn btn-sm btn-outline-light" id="collapseSidebarBtn" type="button">⟨</button>
        </div>
        <nav class="nav flex-column gap-1">
            <?php foreach ([
                'dashboard'=>'Dashboard','clients'=>'Clients','users'=>'Users','settings'=>'Settings'
            ] as $key=>$label): ?>
                <a class="nav-link <?= (($_GET['r'] ?? 'dashboard') === $key) ? 'active' : '' ?>" href="?r=<?= $key ?>"><span class="link-text"><?= $label ?></span></a>
            <?php endforeach; ?>
        </nav>
        <hr class="border-secondary"><a class="nav-link" href="?r=logout"><span class="link-text">Logout</span></a>
    </aside>

    <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header"><h5 class="offcanvas-title">Menu</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button></div>
        <div class="offcanvas-body">
            <nav class="nav flex-column gap-1">
                <?php foreach (['dashboard'=>'Dashboard','clients'=>'Clients','users'=>'Users','settings'=>'Settings'] as $key=>$label): ?>
                    <a class="nav-link <?= (($_GET['r'] ?? 'dashboard') === $key) ? 'active' : '' ?>" href="?r=<?= $key ?>"><?= $label ?></a>
                <?php endforeach; ?>
                <a class="nav-link" href="?r=logout">Logout</a>
            </nav>
        </div>
    </div>

    <main class="main-wrap p-3 p-lg-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div class="d-flex align-items-center gap-2">
                <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">☰</button>
                <h3 class="mb-0"><?= htmlspecialchars($title ?? '', ENT_QUOTES, 'UTF-8') ?></h3>
            </div>
            <div class="d-flex gap-2 align-items-center" style="max-width:700px;width:100%">
                <form method="get" class="d-flex gap-2 flex-grow-1"><input type="hidden" name="r" value="clients"/><input name="q" class="form-control" placeholder="Quick search: Erf number, client, phone, email"/><button class="btn btn-outline-primary" type="submit">Go</button></form>
                <button class="btn btn-outline-secondary" id="themeToggle" type="button">🌙</button>
            </div>
        </div>
        <?= $content ?>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(() => {
  const html = document.documentElement;
  const savedTheme = localStorage.getItem('theme_mode');
  if (savedTheme) html.setAttribute('data-bs-theme', savedTheme);
  const themeBtn = document.getElementById('themeToggle');
  if (themeBtn) {
    const setIcon = () => themeBtn.textContent = html.getAttribute('data-bs-theme') === 'dark' ? '☀️' : '🌙';
    setIcon();
    themeBtn.addEventListener('click', () => {
      const next = html.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
      html.setAttribute('data-bs-theme', next);
      localStorage.setItem('theme_mode', next);
      setIcon();
    });
  }

  const sidebar = document.getElementById('desktopSidebar');
  const collapseBtn = document.getElementById('collapseSidebarBtn');
  if (sidebar && collapseBtn) {
    const saved = localStorage.getItem('sidebar_collapsed') === '1';
    if (saved) sidebar.classList.add('collapsed');
    collapseBtn.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      localStorage.setItem('sidebar_collapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
      collapseBtn.textContent = sidebar.classList.contains('collapsed') ? '⟩' : '⟨';
    });
  }
})();
</script>
</body>
</html>
