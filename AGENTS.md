# AGENTS.md

## Cursor Cloud specific instructions

This is a **Laravel 12 (PHP 8.3)** server-rendered web app ("Web Sieu Re", a Vietnamese
website-theme marketplace) with a **Vite 7 + Tailwind 4** asset pipeline. Standard commands
live in `composer.json` (`setup`, `dev`, `test` scripts), `package.json`, and `readme.txt`.

### Services
- **Web app**: `php artisan serve --host=0.0.0.0 --port=8000`.
- **Frontend**: `npm run dev` (hot reload) or `npm run build` (one-off build; required for Blade
  `@vite` to resolve assets if no dev server is running).
- `composer dev` runs server + queue worker + log viewer + Vite concurrently.
- **Database**: MySQL. Queue, cache, and session all use the `database` driver, and mail uses the
  `log` driver, so no Redis/SMTP/Memcached is needed.

### Non-obvious gotchas
- **MySQL is not auto-started** (no systemd here). Start it each session with
  `sudo service mysql start`. Root connects over `127.0.0.1` with an empty password
  (configured to `mysql_native_password`). The `.env` is already set to
  `DB_CONNECTION=mysql`, `DB_DATABASE=websieure`.
- **A fresh `php artisan migrate` on an empty database FAILS.** Migration
  `database/migrations/2026_06_15_000005_add_role_to_users_table.php` queries the `is_admin`
  column, but `is_admin` is only created by the later-dated `2026_06_16_000004` migration, so
  the ordering is broken for a clean install. **Do not** try to fix this by editing migrations.
- **The database is seeded from the committed dump** `database/websieure.sql`. That dump is
  **UTF-16LE** (Windows/MariaDB export) and must be converted to UTF-8 before importing, e.g.:
  ```bash
  iconv -f UTF-16LE -t UTF-8 database/websieure.sql -o /tmp/websieure_utf8.sql
  mysql -h 127.0.0.1 -u root < /tmp/websieure_utf8.sql
  ```
  The dump is from an *earlier* schema state (it has `is_admin`, no affiliate/role/settings
  tables). After importing it, run `php artisan migrate --force` — the pending affiliate/role/
  settings migrations then apply cleanly because `is_admin` now exists. This import-then-migrate
  order is the working setup path; the data is normally persisted in the VM snapshot, so re-import
  only if the DB is missing.
- Run `php artisan storage:link` once so uploaded thumbnails resolve (persisted in snapshot).

### Access / credentials
- Storefront `/`, catalog `/themes`, topics `/chu-de`, login `/dang-nhap`, admin `/admin`.
- Seeded admin: `admin@websieure.test` / `password`.

### Tests & lint
- `php artisan test` runs against **in-memory SQLite** (see `phpunit.xml`), so it needs no DB
  server. Note `tests/Feature/ExampleTest.php` is the default Laravel scaffold with
  `RefreshDatabase` commented out; it FAILS because it hits `/` (which queries `categories`)
  against an unmigrated test DB. This is a pre-existing repo issue, not an environment problem.
- Lint/format: `./vendor/bin/pint` (fix) / `./vendor/bin/pint --test` (check only). There is no
  `pint.json`, so it uses Laravel defaults; the repo currently has many pre-existing formatting
  diffs.
