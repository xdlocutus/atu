<?php

declare(strict_types=1);

use App\Core\Env;
use App\Core\View;
use App\Security\Csrf;

require dirname(__DIR__) . '/vendor/autoload.php';

Env::load(dirname(__DIR__) . '/.env');

session_name(Env::get('SESSION_NAME', 'draft_panel_session'));
session_set_cookie_params([
    'httponly' => Env::get('SESSION_HTTP_ONLY', 'true') === 'true',
    'secure' => Env::get('SESSION_SECURE', 'false') === 'true',
    'samesite' => Env::get('SESSION_SAME_SITE', 'Strict'),
]);
session_start();

$route = $_GET['r'] ?? 'dashboard';

$stats = [
    'total_clients' => 0,
    'pending_quotes' => 0,
    'unpaid_invoices' => 0,
    'overdue_invoices' => 0,
];

switch ($route) {
    case 'dashboard':
        View::render('dashboard/index', ['title' => 'Dashboard', 'stats' => $stats]);
        break;
    case 'clients':
        View::render('clients/index', ['title' => 'Clients', 'csrf' => Csrf::token()]);
        break;
    case 'files':
        View::render('files/index', ['title' => 'Files', 'csrf' => Csrf::token()]);
        break;
    case 'quotes':
        View::render('quotes/index', ['title' => 'Quotes']);
        break;
    case 'invoices':
        View::render('invoices/index', ['title' => 'Invoices']);
        break;
    case 'payments':
        View::render('payments/index', ['title' => 'Payments']);
        break;
    case 'statements':
        View::render('statements/index', ['title' => 'Statements']);
        break;
    case 'settings':
        View::render('settings/index', ['title' => 'Settings']);
        break;
    default:
        http_response_code(404);
        echo '404';
}
