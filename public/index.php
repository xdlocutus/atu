<?php

declare(strict_types=1);

use App\Core\Env;
use App\Core\View;
use App\Repositories\ClientRepository;
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
$clientRepo = new ClientRepository();
$dbError = null;

if ($route === 'clients' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? null;
    if (!Csrf::verify(is_string($token) ? $token : null)) {
        $_SESSION['flash_error'] = 'Invalid CSRF token. Please retry.';
        header('Location: ?r=clients');
        exit;
    }

    $fullName = trim((string)($_POST['full_name'] ?? ''));
    $contact = trim((string)($_POST['contact_number'] ?? ''));
    $email = trim((string)($_POST['email'] ?? ''));
    $erf = trim((string)($_POST['erf_number'] ?? ''));
    $notes = trim((string)($_POST['notes'] ?? ''));

    if ($fullName === '' || $erf === '') {
        $_SESSION['flash_error'] = 'Full name and Erf number are required.';
        header('Location: ?r=clients');
        exit;
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_error'] = 'Please provide a valid email address.';
        header('Location: ?r=clients');
        exit;
    }

    try {
        $clientRepo->create([
            'full_name' => $fullName,
            'contact_number' => $contact,
            'email' => $email,
            'erf_number' => $erf,
            'notes' => $notes,
        ]);
        $_SESSION['flash_success'] = 'Client created successfully.';
    } catch (Throwable $e) {
        $_SESSION['flash_error'] = str_contains(strtolower($e->getMessage()), 'duplicate')
            ? 'Erf number already exists. Please use a unique Erf number.'
            : 'Unable to save client right now.';
    }

    header('Location: ?r=clients');
    exit;
}

$stats = [
    'total_clients' => 0,
    'pending_quotes' => 0,
    'unpaid_invoices' => 0,
    'overdue_invoices' => 0,
];

try {
    $stats['total_clients'] = $clientRepo->totalCount();
} catch (Throwable $e) {
    $dbError = 'Database connection unavailable. Please configure MariaDB and run init_db.';
}

switch ($route) {
    case 'dashboard':
        View::render('dashboard/index', ['title' => 'Dashboard', 'stats' => $stats, 'db_error' => $dbError]);
        break;
    case 'clients':
        $query = trim((string)($_GET['q'] ?? ''));
        $clients = [];
        try {
            $clients = $clientRepo->list($query);
        } catch (Throwable $e) {
            $dbError = 'Database connection unavailable. Please configure MariaDB and run init_db.';
        }

        $flashError = $_SESSION['flash_error'] ?? null;
        $flashSuccess = $_SESSION['flash_success'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_success']);

        View::render('clients/index', [
            'title' => 'Clients',
            'csrf' => Csrf::token(),
            'clients' => $clients,
            'search' => $query,
            'flash_error' => $flashError,
            'flash_success' => $flashSuccess,
            'db_error' => $dbError,
        ]);
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
