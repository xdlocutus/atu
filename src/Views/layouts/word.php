<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($title ?? 'Document', ENT_QUOTES, 'UTF-8') ?></title>
  <style>
    body{font-family:Calibri,Arial,sans-serif;color:#1f2937;margin:24px}
    .head{display:flex;justify-content:space-between;align-items:flex-start;border-bottom:1px solid #d1d5db;padding-bottom:12px;margin-bottom:16px}
    .logo{max-height:40px;width:auto;height:auto}
    table{width:100%;border-collapse:collapse;margin-top:12px}
    th,td{border:1px solid #d1d5db;padding:8px;text-align:left}
    .muted{color:#6b7280}.right{text-align:right}
    .detail-grid{display:flex;gap:12px;margin-top:16px}
    .detail-grid .block{flex:1}
    .block{padding:12px;background:#f9fafb;border:1px solid #d1d5db;border-radius:6px}
    .block h3{margin:0 0 10px 0;font-size:14px;color:#111827;border-bottom:1px solid #d1d5db;padding-bottom:6px}
    .detail-row{margin:4px 0;font-size:13px}
    .detail-row span{display:inline-block;min-width:62px;color:#4b5563}
    .terms-list{margin:0;padding-left:18px}
    .terms-list li{margin-bottom:6px}
    .appointment-block{margin-top:16px;white-space:pre-line}
  </style>
</head>
<body>
<?= $content ?>
</body>
</html>
