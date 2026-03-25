# Roles and Permissions Matrix

| Module | Admin | Manager | Staff | Viewer |
|---|---|---|---|---|
| Clients | CRUD + archive | CRUD | create/update | read |
| Documents | full access + restore | full access | upload/download/archive own | download |
| Quotes | full | full | draft/sent | read |
| Invoices | full | full | draft/sent | read |
| Payments | full | full | capture | read |
| Statements | full | full | generate | read |
| Settings | full | limited (company profile) | none | none |
| Users/Roles | full | none | none | none |

# Security Architecture
- Password hashing with `password_hash` (Argon2id preferred).
- CSRF tokens for state-changing requests.
- Output escaping for XSS prevention.
- Parameterized PDO queries for SQL injection prevention.
- Role checks on every module action.
- File upload validation by MIME + extension + max size + virus scanner hook.
- Files served through authorized controller endpoints (not direct web root).
- Activity logs for key events.
- Login rate limiting and lockout policy.
- Environment variables for secrets.

# Backup and recovery
- Daily DB dump + encrypted storage.
- File storage snapshots every 4 hours.
- Monthly restore tests into staging.
