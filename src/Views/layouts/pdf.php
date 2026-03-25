<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($title ?? 'Document', ENT_QUOTES, 'UTF-8') ?></title>
  <style>
    body{font-family:Arial,sans-serif;color:#1f2937;margin:24px}
    .head{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:2px solid #e5e7eb;padding-bottom:12px;margin-bottom:16px}
    .logo{max-height:70px}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{border:1px solid #e5e7eb;padding:8px;text-align:left}
    .muted{color:#6b7280}.right{text-align:right}
    .note{margin-top:16px;padding:10px;background:#f9fafb;border:1px solid #e5e7eb}
  </style>
</head>
<body>
<?= $content ?>
<script>window.print();</script>
</body>
</html>
