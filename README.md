# Drafting Business Control Panel (PHP)

## 1) Recommended PHP stack

**Recommended stack (non-Laravel):**
- PHP 8.2+
- MariaDB 10.6+
- Bootstrap 5.3 UI
- PDO + prepared statements
- Modular MVC-style structure (controllers/services/repositories/views)

Why this stack:
- Fast and lightweight for daily office operations.
- Easy hosting on common LAMP/LEMP environments.
- Strong security control with explicit handling of sessions, CSRF, validation, and authorization.
- Bootstrap provides a professional, clean, mobile-responsive UI quickly.

---

## 2) Database schema (implemented)

Schema file: `database/schema.sql` (MariaDB / MySQL compatible)

Core entities:
- `users` (RBAC roles: admin/manager/staff/viewer)
- `clients` (**unique indexed `erf_number`** as primary searchable field)
- `client_notes_history`
- `documents` (category, metadata, version, archive)
- `quotes`, `quote_items`
- `invoices`, `invoice_items`
- `payments` (partial payments)
- `reminders`, `tasks`, `submissions`
- `activity_logs` (audit trail)
- `settings` (company details, VAT defaults, numbering sequences)

---

## 3) Module structure

- Auth & Security
- Dashboard
- Clients
- Documents / File Manager
- Quotes
- Invoices
- Payments
- Statements
- Settings

---

## 4) User flow

1. Login securely.
2. Use quick search by Erf number / name / phone / email.
3. Open dedicated client workspace.
4. Manage files, quotes, invoices, payments, and statements in one place.
5. Convert accepted quote to invoice with one action.
6. Capture partial/full payments and auto-update balances/statuses.

---

## 5) Scaffold included

- Front controller router (`public/index.php`)
- Environment/config + DB connector (`src/Core/*`)
- CSRF helper (`src/Security/Csrf.php`)
- MariaDB schema (`database/schema.sql`)
- Bootstrap 5 responsive UI shell and module pages (`src/Views/*`)
- Initialization script (`scripts/init_db.php`)

---

## 6) Build order

1. Auth + RBAC + activity logs
2. Client management (erf-first search)
3. File/document storage + ZIP downloads + versioning
4. Quotes + PDF generation
5. Invoices + quote conversion
6. Payments + balance/status automation
7. Statements + PDF export
8. Reminders, tasks, council tracker, email delivery
9. Security hardening + backups/recovery

## Run locally

```bash
cp .env.example .env
composer dump-autoload
php scripts/init_db.php
php -S localhost:8080 -t public
```


### AutoCAD direct-open integration
Set `AUTOCAD_SHARED_ROOT` in `.env` to a shared/network folder that AutoCAD users can access. Uploaded DWG/DXF files are stored there and can be opened via the **Open CAD** button from the client Files tab. Saving in AutoCAD writes back to the same shared path used by the system.


### Create first admin user
```bash
php scripts/create_user.php marco marco@example.com Hero3338351 admin
```
Then login with that user.
