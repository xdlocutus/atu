# Drafting Business Control Panel (PHP)

## 1) Recommended PHP stack

**Best choice: Laravel 12 + PostgreSQL + Redis + Tailwind CSS + Alpine.js + Vite**.

Why Laravel is the best fit for your brief:
- Mature security defaults (CSRF, bcrypt/Argon hashing, validation, authorization gates/policies).
- Fast scaffolding for modules (clients, files, quotes, invoices, payments, statements).
- Excellent queue/email/pdf ecosystem.
- Clean architecture support via service classes, repositories, form requests, policies, jobs.
- Easy to maintain and scale for office daily use.

> In this repository, a **secure modular PHP scaffold** is implemented (framework-agnostic) so development can start immediately. You can migrate this structure directly into Laravel modules/controllers/services in the next iteration.

---

## 2) Database schema (implemented)

Schema file: `database/schema.sql`

Core entities:
- `users` (RBAC roles: admin/manager/staff/viewer)
- `clients` (with **unique indexed `erf_number`** as primary searchable field)
- `client_notes_history`
- `documents` (category, metadata, version, archive)
- `quotes`, `quote_items`
- `invoices`, `invoice_items`
- `payments` (partial payments supported)
- `reminders`, `tasks`, `submissions`
- `activity_logs` (audit trail)
- `settings` (company profile, VAT defaults, numbering sequences)

Search indexes included for erf/name/phone/email.

---

## 3) Module structure

- **Auth & Security**: login/logout, sessions, CSRF, password hashing, rate limiting hooks.
- **Dashboard**: quick totals and recent activity widgets.
- **Clients**: CRUD + dedicated client page + notes history.
- **Documents**: multi-upload pipeline, categories, metadata, archive, activity logs.
- **Quotes**: draft/sent/accepted/rejected/expired + quote items + PDF endpoint.
- **Invoices**: manual or converted from accepted quote + statuses + balance tracking.
- **Payments**: capture full/partial payments and auto-update invoice status.
- **Statements**: date-filtered statement generation and PDF export endpoint.
- **Settings**: company details, VAT defaults, numbering sequences.

---

## 4) User flow

1. User logs in securely.
2. Lands on dashboard with quick search (erf/name/phone/email).
3. Creates/fetches client by **erf number**.
4. Uses client workspace tabs:
   - Overview
   - Files
   - Quotes
   - Invoices
   - Payments
   - Statements
   - Tasks/Reminders/Submissions
5. Accepted quote converts to invoice in one click.
6. Payments update invoice balances/status instantly.
7. Statements exported per client/date range.

---

## 5) Scaffold included

- Front controller router (`public/index.php`)
- Lightweight core classes (`src/Core/*`)
- CSRF helper (`src/Security/Csrf.php`)
- SQL schema (`database/schema.sql`)
- Environment template (`.env.example`)
- UI shell with sidebar/topbar and responsive cards/tables

---

## 6) Build order (best-practice implementation plan)

1. **Foundation**: auth, roles, settings, activity logs, dashboard KPIs.
2. **Client module**: erf-first search + client dashboard.
3. **Files module**: secure upload, metadata/versioning/archive, ZIP download.
4. **Quotes module**: numbering, items, statuses, PDF export.
5. **Invoices + conversion**: quote->invoice pipeline.
6. **Payments**: partial payments, automated status transitions.
7. **Statements**: filters + PDF.
8. **Automation**: reminders/emails.
9. **Hardening**: throttling, backups, restore drills, penetration checks.

---

## Run locally

```bash
cp .env.example .env
php scripts/init_db.php
php -S localhost:8080 -t public
```

Open `http://localhost:8080`.
