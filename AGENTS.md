# AGENTS.md

## What this is
Procedural PHP + MySQLi web app ("Sistem Informasi Musholla", SMK Negeri 1 Kraksaan) — an Indonesian student exam (UKK) project. No framework, no build step. UI is Bootstrap 5.3 + icons via CDN with custom styling in `assets/css/style.css`. UI text is Indonesian.

## Run / verify
- Serve with XAMPP from `D:\xampp\htdocs\...`, open `http://localhost/.../index.php`.
- MySQL DB `db_musholla` on `localhost`, user `root`, empty password. Credentials live in `config/config.php` (`DB_*` constants) — this is the ONLY place to change them for hosting. No SQL dump exists in the repo; the DB must be created manually.
- No test/lint tooling. Syntax-check changed files: `D:\xampp\php\php.exe -l <file>.php`. For a functional smoke test, `php -S localhost:8899 -t .` then curl the pages.

## Bootstrap & conventions (IMPORTANT)
- **`config/config.php` is the single bootstrap**: defines `APP_ENV`, `ROOT_PATH`, `DB_*`, auto-detected `BASE_URL` (works on localhost, subfolder, root domain, HTTPS/proxy — do NOT hardcode it), session start (guarded), DB connection `$connect`, and helpers `url()`, `asset()`, `redirect()`, `post()`. Pages must `require_once __DIR__ . '/../config/config.php';` (or include `template/header.php`, which does it).
- **Layout partials**: `template/header.php` (doctype + head + sidebar + opens `.main-wrapper`, uses `$pageTitle` var) and `template/footer.php` (closes wrapper + Bootstrap JS + sidebar toggle script). All module pages are shells that only set `$pageTitle`, include header/footer, and render placeholder content.
- All URLs/assets use `url()`/`asset()` (absolute from root) — never relative paths like `../assets/`. Redirects use `redirect('/')`.
- `template/base_url.php` and `config/connect.php` are legacy shims (`require_once` into `config/config.php`); don't put real logic there.
- Use procedural `mysqli_*` on global `$connect` (prepared statements), escape output with `htmlspecialchars()`.
- One folder per module (`users`, `guru`, `kelas`, `infaq`, `shodaqoh`, `kegiatan`, `jadwal_sholat`, `jadwal_imam`, `laporan`) with a fixed file set: `index.php` (list), `<mod>.php` (page), `tambah.php`, `simpan.php`, `edit.php`, `update.php`, `hapus.php`. **Almost all `tambah/simpan/edit/update/hapus` are still 0-byte stubs** — treat them as unimplemented.

## Gotchas (verified)
- `auth/sign_in.php` compares `password_verify()` with a plaintext fallback (`hash_equals`) because the DB stores plaintext passwords. Migrate rows to `password_hash()` and then remove the fallback. SQL is a prepared statement.
- Login redirects ALL roles to `index.php` (the old `dashboard-petugas.php`/`dashboard-user.php` targets never existed).
- Sidebar links, dashboard stats, jadwal, and kegiatan are still hardcoded/placeholders; dashboard numbers are not read from DB.
- dompdf 3.1.4 is vendored under `laporan/dompdf/` but no page uses it yet. `composer.json` exists at root for reproducibility, but there is no `vendor/` at app level (dompdf ships its own).
- Security files: root `.htaccess` (no listing, denies `config/`, `storage/`, `.git`), `config/.htaccess` and `storage/logs/.htaccess` deny all. `laporan/dompdf/` is untracked in git — don't commit the vendor dir unless intended.
- `storage/logs/` is the production `error_log` target; keep `.htaccess` in it.
