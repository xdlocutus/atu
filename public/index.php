<?php

declare(strict_types=1);

use App\Core\Env;
use App\Core\View;
use App\Repositories\ClientRepository;
use App\Repositories\DocumentRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\UserRepository;
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
$documentRepo = new DocumentRepository();
$quoteRepo = new QuoteRepository();
$invoiceRepo = new InvoiceRepository();
$paymentRepo = new PaymentRepository();
$userRepo = new UserRepository();
$dbError = null;

function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function flash(string $key, string $message): void
{
    $_SESSION[$key] = $message;
}

function isAuthenticated(): bool
{
    return isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0;
}

function streamDownload(string $path, string $downloadName, string $mimeType = 'application/octet-stream'): void
{
    if (!is_file($path)) {
        http_response_code(404);
        echo 'File not found';
        exit;
    }

    header('Content-Description: File Transfer');
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . rawurlencode($downloadName) . '"');
    header('Content-Length: ' . filesize($path));
    header('Cache-Control: no-cache, must-revalidate');
    readfile($path);
    exit;
}

function documentAbsolutePath(int $clientId, array $doc): string
{
    $sharedRoot = trim((string)Env::get('AUTOCAD_SHARED_ROOT', ''));
    $base = $sharedRoot !== '' ? rtrim($sharedRoot, '/\\') : dirname(__DIR__) . '/storage/app/clients';

    return $base . '/' . $clientId . '/' . $doc['category'] . '/' . $doc['stored_name'];
}

function quoteItemsFromPost(array $post): array
{
    $descriptions = $post['item_description'] ?? [];
    $quantities = $post['item_quantity'] ?? [];
    $rates = $post['item_rate'] ?? [];

    if (!is_array($descriptions) || !is_array($quantities) || !is_array($rates)) {
        return [[
            'description' => trim((string)($post['description'] ?? 'Drafting services')),
            'quantity' => (float)($post['quantity'] ?? 1),
            'rate' => (float)($post['rate'] ?? 0),
        ]];
    }

    $items = [];
    $lineCount = max(count($descriptions), count($quantities), count($rates));
    for ($i = 0; $i < $lineCount; $i++) {
        $items[] = [
            'description' => trim((string)($descriptions[$i] ?? '')),
            'quantity' => (float)($quantities[$i] ?? 0),
            'rate' => (float)($rates[$i] ?? 0),
        ];
    }

    return $items;
}


if ($route === 'download_document') {
    $clientId = (int)($_GET['client_id'] ?? 0);
    $documentId = (int)($_GET['document_id'] ?? 0);

    try {
        $doc = $documentRepo->findByClient($clientId, $documentId);
        if (!$doc) {
            throw new RuntimeException('Document not found.');
        }
        $path = documentAbsolutePath($clientId, $doc);
        streamDownload($path, (string)$doc['original_name'], (string)($doc['mime_type'] ?: 'application/octet-stream'));
    } catch (Throwable $e) {
        flash('flash_error', 'Unable to download file.');
        redirect('?r=client&id=' . $clientId . '&tab=files');
    }
}

if (!isAuthenticated() && $route !== 'login') {
    redirect('?r=login');
}
if (isAuthenticated() && $route === 'login') {
    redirect('?r=dashboard');
}

if ($route === 'download_client_zip') {
    $clientId = (int)($_GET['client_id'] ?? 0);
    try {
        $client = $clientRepo->find($clientId);
        if (!$client) {
            throw new RuntimeException('Client not found.');
        }
        $documents = $documentRepo->listByClient($clientId);
        if (empty($documents)) {
            throw new RuntimeException('No documents to zip.');
        }
        $tmpZip = tempnam(sys_get_temp_dir(), 'client_docs_');
        if ($tmpZip === false) {
            throw new RuntimeException('Could not create temp archive.');
        }
        $zipPath = $tmpZip . '.zip';
        rename($tmpZip, $zipPath);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not open zip archive.');
        }

        foreach ($documents as $doc) {
            $path = documentAbsolutePath($clientId, $doc);
            if (is_file($path)) {
                $zip->addFile($path, $doc['category'] . '/' . $doc['original_name']);
            }
        }
        $zip->close();

        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="client_' . $clientId . '_documents.zip"');
        header('Content-Length: ' . filesize($zipPath));
        readfile($zipPath);
        @unlink($zipPath);
        exit;
    } catch (Throwable $e) {
        flash('flash_error', 'Unable to generate ZIP download.');
        redirect('?r=client&id=' . $clientId . '&tab=files');
    }
}

if ($route === 'export_quote') {
    $clientId = (int)($_GET['client_id'] ?? 0);
    $quoteId = (int)($_GET['quote_id'] ?? 0);
    $quote = $quoteRepo->find($quoteId);
    $client = $clientRepo->find($clientId);
    if (!$quote || !$client || (int)$quote['client_id'] !== $clientId) {
        http_response_code(404);
        echo 'Quote not found';
        exit;
    }
    $company = [
        'name' => Env::get('COMPANY_NAME', 'Your Company Name'),
        'email' => Env::get('COMPANY_EMAIL', ''),
        'phone' => Env::get('COMPANY_PHONE', ''),
        'address' => Env::get('COMPANY_ADDRESS', ''),
        'logo' => Env::get('COMPANY_LOGO_URL', ''),
    ];
    View::render('pdf/quote', ['title' => 'Quote PDF', 'quote' => $quote, 'client' => $client, 'company' => $company]);
    exit;
}

if ($route === 'export_quote_word') {
    $clientId = (int)($_GET['client_id'] ?? 0);
    $quoteId = (int)($_GET['quote_id'] ?? 0);
    $quote = $quoteRepo->find($quoteId);
    $client = $clientRepo->find($clientId);
    if (!$quote || !$client || (int)$quote['client_id'] !== $clientId) {
        http_response_code(404);
        echo 'Quote not found';
        exit;
    }
    $company = [
        'name' => Env::get('COMPANY_NAME', 'Your Company Name'),
        'email' => Env::get('COMPANY_EMAIL', ''),
        'phone' => Env::get('COMPANY_PHONE', ''),
        'address' => Env::get('COMPANY_ADDRESS', ''),
        'logo' => Env::get('COMPANY_LOGO_URL', ''),
    ];
    $safeQuoteNumber = preg_replace('/[^A-Za-z0-9_-]/', '_', (string)$quote['quote_number']) ?: (string)$quoteId;
    header('Content-Type: application/msword; charset=UTF-8');
    header('Content-Disposition: attachment; filename="quote_' . $safeQuoteNumber . '.doc"');
    View::render('word/quote', ['title' => 'Quote Word Export', 'quote' => $quote, 'client' => $client, 'company' => $company]);
    exit;
}

if ($route === 'export_invoice') {
    $clientId = (int)($_GET['client_id'] ?? 0);
    $invoiceId = (int)($_GET['invoice_id'] ?? 0);
    $client = $clientRepo->find($clientId);
    $invoices = $invoiceRepo->listByClient($clientId);
    $invoice = null;
    foreach ($invoices as $i) {
        if ((int)$i['id'] === $invoiceId) {
            $invoice = $i;
            break;
        }
    }
    if (!$client || !$invoice) {
        http_response_code(404);
        echo 'Invoice not found';
        exit;
    }
    $company = [
        'name' => Env::get('COMPANY_NAME', 'Your Company Name'),
        'email' => Env::get('COMPANY_EMAIL', ''),
        'phone' => Env::get('COMPANY_PHONE', ''),
        'address' => Env::get('COMPANY_ADDRESS', ''),
        'logo' => Env::get('COMPANY_LOGO_URL', ''),
    ];
    View::render('pdf/invoice', ['title' => 'Invoice PDF', 'invoice' => $invoice, 'client' => $client, 'company' => $company]);
    exit;
}

if ($route === 'export_statement') {
    $clientId = (int)($_GET['client_id'] ?? 0);
    $client = $clientRepo->find($clientId);
    if (!$client) {
        http_response_code(404);
        echo 'Client not found';
        exit;
    }
    $invoices = $invoiceRepo->listByClient($clientId);
    $payments = $paymentRepo->listByClient($clientId);
    $company = [
        'name' => Env::get('COMPANY_NAME', 'Your Company Name'),
        'email' => Env::get('COMPANY_EMAIL', ''),
        'phone' => Env::get('COMPANY_PHONE', ''),
        'address' => Env::get('COMPANY_ADDRESS', ''),
        'logo' => Env::get('COMPANY_LOGO_URL', ''),
    ];
    View::render('pdf/statement', ['title' => 'Statement PDF', 'invoices' => $invoices, 'payments' => $payments, 'client' => $client, 'company' => $company]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf'] ?? null;
    if (!Csrf::verify(is_string($token) ? $token : null)) {
        flash('flash_error', 'Invalid CSRF token. Please retry.');
        redirect($_SERVER['HTTP_REFERER'] ?? '?r=clients');
    }

    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'create_client') {
            $fullName = trim((string)($_POST['full_name'] ?? ''));
            $contact = trim((string)($_POST['contact_number'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $erf = trim((string)($_POST['erf_number'] ?? ''));
            $notes = trim((string)($_POST['notes'] ?? ''));
            if ($fullName === '' || $erf === '') {
                throw new RuntimeException('Full name and Erf number are required.');
            }
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new RuntimeException('Please provide a valid email address.');
            }
            $clientRepo->create([
                'full_name' => $fullName,
                'contact_number' => $contact,
                'email' => $email,
                'erf_number' => $erf,
                'notes' => $notes,
            ]);
            flash('flash_success', 'Client created successfully.');
            redirect('?r=clients');
        }

        if ($action === 'login') {
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');
            $user = $userRepo->findByEmail($email);
            if (!$user || !(int)$user['is_active'] || !password_verify($password, (string)$user['password_hash'])) {
                throw new RuntimeException('Invalid login credentials.');
            }
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_role'] = (string)$user['role'];
            $_SESSION['user_name'] = (string)$user['full_name'];
            flash('flash_success', 'Welcome back, ' . (string)$user['full_name']);
            redirect('?r=dashboard');
        }

        if ($action === 'create_user') {
            if (($_SESSION['user_role'] ?? '') !== 'admin') {
                throw new RuntimeException('Only admin can create users.');
            }
            $name = trim((string)($_POST['full_name'] ?? ''));
            $email = trim((string)($_POST['email'] ?? ''));
            $password = (string)($_POST['password'] ?? '');
            $role = trim((string)($_POST['role'] ?? 'staff'));
            if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
                throw new RuntimeException('Invalid user details. Password must be at least 8 characters.');
            }
            $userRepo->create($name, $email, $password, $role);
            flash('flash_success', 'User created successfully.');
            redirect('?r=users');
        }

        if ($action === 'delete_client') {
            $targetClientId = (int)($_POST['target_client_id'] ?? 0);
            if ($targetClientId <= 0) {
                throw new RuntimeException('Invalid client id.');
            }
            $clientRepo->archive($targetClientId);
            flash('flash_success', 'Client deleted (archived).');
            redirect('?r=clients');
        }

        $clientId = (int)($_POST['client_id'] ?? 0);
        if ($clientId <= 0) {
            throw new RuntimeException('Invalid client selected.');
        }

        if ($action === 'upload_document') {
            $allowed = explode(',', (string)Env::get('ALLOWED_EXTENSIONS', 'pdf,dwg,jpg,jpeg,png'));
            $maxSize = ((int)Env::get('MAX_UPLOAD_MB', '50')) * 1024 * 1024;
            if (!isset($_FILES['upload'])) {
                throw new RuntimeException('Please select at least one file to upload.');
            }

            $category = trim((string)($_POST['category'] ?? 'supporting'));
            $notes = trim((string)($_POST['notes'] ?? ''));
            $sharedRoot = trim((string)Env::get('AUTOCAD_SHARED_ROOT', ''));
            $base = $sharedRoot !== '' ? rtrim($sharedRoot, '/\\') : dirname(__DIR__) . '/storage/app/clients';
            $dir = $base . '/' . $clientId . '/' . $category;
            if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
                throw new RuntimeException('Could not create storage directory.');
            }
            $names = (array)($_FILES['upload']['name'] ?? []);
            $tmpNames = (array)($_FILES['upload']['tmp_name'] ?? []);
            $sizes = (array)($_FILES['upload']['size'] ?? []);
            $errors = (array)($_FILES['upload']['error'] ?? []);
            $uploadedCount = 0;

            foreach ($names as $idx => $name) {
                if (($errors[$idx] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                    continue;
                }
                $original = (string)$name;
                $tmp = (string)($tmpNames[$idx] ?? '');
                $size = (int)($sizes[$idx] ?? 0);
                $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
                if (!in_array($ext, $allowed, true)) {
                    continue;
                }
                if ($size <= 0 || $size > $maxSize) {
                    continue;
                }
                $safeName = uniqid('doc_', true) . '.' . $ext;
                $target = $dir . '/' . $safeName;
                if (!move_uploaded_file($tmp, $target)) {
                    continue;
                }
                $documentRepo->create($clientId, [
                    'category' => $category,
                    'original_name' => $original,
                    'stored_name' => $safeName,
                    'mime_type' => mime_content_type($target) ?: 'application/octet-stream',
                    'extension' => $ext,
                    'size_bytes' => $size,
                    'notes' => $notes,
                ]);
                $uploadedCount++;
            }
            if ($uploadedCount === 0) {
                throw new RuntimeException('No valid files were uploaded.');
            }
            flash('flash_success', 'File uploaded successfully.');
            redirect('?r=client&id=' . $clientId . '&tab=files');
        }

        if ($action === 'delete_document') {
            $documentId = (int)($_POST['document_id'] ?? 0);
            $doc = $documentRepo->delete($clientId, $documentId);
            if (!$doc) {
                throw new RuntimeException('File not found.');
            }
            $path = documentAbsolutePath($clientId, $doc);
            if (is_file($path)) {
                @unlink($path);
            }
            flash('flash_success', 'File deleted.');
            redirect('?r=client&id=' . $clientId . '&tab=files');
        }

        if ($action === 'create_quote') {
            $quoteRepo->create($clientId, [
                'quote_number' => trim((string)($_POST['quote_number'] ?? '')),
                'status' => $_POST['status'] ?? 'draft',
                'quote_date' => $_POST['quote_date'] ?? date('Y-m-d'),
                'expiry_date' => $_POST['expiry_date'] ?? date('Y-m-d', strtotime('+14 days')),
                'items' => quoteItemsFromPost($_POST),
                'vat_rate' => 0.0,
                'notes' => trim((string)($_POST['notes'] ?? '')),
                'terms' => trim((string)($_POST['terms'] ?? '')),
            ]);
            flash('flash_success', 'Quote created.');
            redirect('?r=client&id=' . $clientId . '&tab=quotes');
        }

        if ($action === 'update_quote') {
            $quoteRepo->update((int)($_POST['quote_id'] ?? 0), $clientId, [
                'quote_number' => trim((string)($_POST['quote_number'] ?? '')),
                'status' => $_POST['status'] ?? 'draft',
                'quote_date' => $_POST['quote_date'] ?? date('Y-m-d'),
                'expiry_date' => $_POST['expiry_date'] ?? date('Y-m-d', strtotime('+14 days')),
                'items' => quoteItemsFromPost($_POST),
                'vat_rate' => 0.0,
                'notes' => trim((string)($_POST['notes'] ?? '')),
                'terms' => trim((string)($_POST['terms'] ?? '')),
            ]);
            flash('flash_success', 'Quote updated.');
            redirect('?r=client&id=' . $clientId . '&tab=quotes');
        }

        if ($action === 'convert_quote') {
            $quoteId = (int)($_POST['quote_id'] ?? 0);
            $quote = $quoteRepo->find($quoteId);
            if (!$quote || (int)$quote['client_id'] !== $clientId) {
                throw new RuntimeException('Quote not found for this client.');
            }
            $invoiceRepo->createFromQuote($quote);
            $quoteRepo->delete($quoteId, $clientId);
            flash('flash_success', 'Quote converted to invoice and removed from quotes.');
            redirect('?r=client&id=' . $clientId . '&tab=invoices');
        }

        if ($action === 'create_invoice') {
            $invoiceRepo->createManual($clientId, [
                'status' => $_POST['status'] ?? 'draft',
                'invoice_date' => $_POST['invoice_date'] ?? date('Y-m-d'),
                'due_date' => $_POST['due_date'] ?? date('Y-m-d', strtotime('+14 days')),
                'description' => trim((string)($_POST['description'] ?? 'Drafting services')),
                'quantity' => (float)($_POST['quantity'] ?? 1),
                'rate' => (float)($_POST['rate'] ?? 0),
                'vat_rate' => 0.0,
                'notes' => trim((string)($_POST['notes'] ?? '')),
            ]);
            flash('flash_success', 'Invoice created.');
            redirect('?r=client&id=' . $clientId . '&tab=invoices');
        }

        if ($action === 'update_invoice') {
            $invoiceRepo->update((int)($_POST['invoice_id'] ?? 0), $clientId, [
                'status' => $_POST['status'] ?? 'draft',
                'invoice_date' => $_POST['invoice_date'] ?? date('Y-m-d'),
                'due_date' => $_POST['due_date'] ?? date('Y-m-d', strtotime('+14 days')),
                'description' => trim((string)($_POST['description'] ?? 'Drafting services')),
                'quantity' => (float)($_POST['quantity'] ?? 1),
                'rate' => (float)($_POST['rate'] ?? 0),
                'vat_rate' => 0.0,
                'notes' => trim((string)($_POST['notes'] ?? '')),
            ]);
            flash('flash_success', 'Invoice updated.');
            redirect('?r=client&id=' . $clientId . '&tab=invoices');
        }

        if ($action === 'credit_invoice') {
            $invoiceRepo->creditUnpaid((int)($_POST['invoice_id'] ?? 0), $clientId);
            flash('flash_success', 'Invoice credited/cancelled.');
            redirect('?r=client&id=' . $clientId . '&tab=invoices');
        }

        if ($action === 'create_payment') {
            $invoiceId = (int)($_POST['invoice_id'] ?? 0);
            $paymentRepo->create([
                'invoice_id' => $invoiceId,
                'payment_date' => $_POST['payment_date'] ?? date('Y-m-d'),
                'amount' => (float)($_POST['amount'] ?? 0),
                'method' => trim((string)($_POST['method'] ?? 'EFT')),
                'reference_number' => trim((string)($_POST['reference_number'] ?? '')),
                'notes' => trim((string)($_POST['notes'] ?? '')),
            ]);
            $invoiceRepo->recalcStatus($invoiceId);
            flash('flash_success', 'Payment captured and invoice updated.');
            redirect('?r=client&id=' . $clientId . '&tab=payments');
        }

        if ($action === 'delete_payment') {
            $invoiceId = $paymentRepo->delete((int)($_POST['payment_id'] ?? 0), $clientId);
            $invoiceRepo->recalcStatus($invoiceId);
            flash('flash_success', 'Payment deleted and invoice totals updated.');
            redirect('?r=client&id=' . $clientId . '&tab=payments');
        }

        if ($action === 'delete_invoice') {
            $invoiceRepo->delete((int)($_POST['invoice_id'] ?? 0), $clientId);
            flash('flash_success', 'Invoice deleted.');
            redirect('?r=client&id=' . $clientId . '&tab=invoices');
        }

    } catch (Throwable $e) {
        flash('flash_error', $e->getMessage());
        $fallback = $clientId > 0 ? '?r=client&id=' . $clientId : '?r=clients';
        redirect($fallback);
    }
}

$stats = ['total_clients' => 0, 'pending_quotes' => 0, 'unpaid_invoices' => 0, 'overdue_invoices' => 0];
try {
    $stats['total_clients'] = $clientRepo->totalCount();
} catch (Throwable $e) {
    $dbError = 'Database connection unavailable. Please configure MariaDB and run init_db.';
}

$flashError = $_SESSION['flash_error'] ?? null;
$flashSuccess = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_error'], $_SESSION['flash_success']);

switch ($route) {
    case 'login':
        View::render('auth/login', [
            'title' => 'Login',
            'csrf' => Csrf::token(),
            'flash_error' => $flashError,
            'flash_success' => $flashSuccess,
        ]);
        break;
    case 'logout':
        session_unset();
        session_destroy();
        session_start();
        flash('flash_success', 'Logged out successfully.');
        redirect('?r=login');
        break;
    case 'dashboard':
        View::render('dashboard/index', ['title' => 'Dashboard', 'stats' => $stats, 'db_error' => $dbError]);
        break;
    case 'clients':
        $query = trim((string)($_GET['q'] ?? ''));
        $clients = [];
        try {
            $clients = $clientRepo->list($query);
        } catch (Throwable $e) {
            $dbError = 'Search failed: ' . $e->getMessage();
        }

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
    case 'client':
        $clientId = (int)($_GET['id'] ?? 0);
        $tab = (string)($_GET['tab'] ?? 'files');
        $client = null;
        $documents = $quotes = $invoices = $payments = $statement = [];
        try {
            $client = $clientRepo->find($clientId);
            if (!$client) {
                throw new RuntimeException('Client not found.');
            }
            $documents = $documentRepo->listByClient($clientId);
            $sharedRoot = trim((string)Env::get('AUTOCAD_SHARED_ROOT', ''));
            foreach ($documents as &$doc) {
                $fullPath = documentAbsolutePath($clientId, $doc);
                $doc['autocad_path'] = $fullPath;
                $doc['autocad_uri'] = 'file:///' . str_replace(DIRECTORY_SEPARATOR, '/', ltrim($fullPath, '/\\'));
                $doc['can_open_cad'] = $sharedRoot !== '' && in_array(strtolower((string)$doc['extension']), ['dwg', 'dxf'], true);
            }
            unset($doc);
            $quotes = $quoteRepo->listByClient($clientId);
            $invoices = $invoiceRepo->listByClient($clientId);
            $payments = $paymentRepo->listByClient($clientId);
            $statement = ['invoices' => $invoices, 'payments' => $payments];
        } catch (Throwable $e) {
            $dbError = $e->getMessage();
        }

        View::render('clients/show', [
            'title' => 'Client Workspace',
            'csrf' => Csrf::token(),
            'client' => $client,
            'tab' => $tab,
            'documents' => $documents,
            'quotes' => $quotes,
            'invoices' => $invoices,
            'payments' => $payments,
            'statement' => $statement,
            'flash_error' => $flashError,
            'flash_success' => $flashSuccess,
            'db_error' => $dbError,
        ]);
        break;
    case 'settings':
        View::render('settings/index', ['title' => 'Settings']);
        break;
    case 'users':
        $users = [];
        try {
            $users = $userRepo->list();
        } catch (Throwable $e) {
            $dbError = $e->getMessage();
        }
        View::render('users/index', [
            'title' => 'Users',
            'users' => $users,
            'csrf' => Csrf::token(),
            'flash_error' => $flashError,
            'flash_success' => $flashSuccess,
            'db_error' => $dbError,
        ]);
        break;
    default:
        http_response_code(404);
        echo '404';
}
