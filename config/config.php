<?php
/**
 * config/config.php
 * Konfigurasi terpusat - Sistem Informasi Musholla (PHP Native)
 *
 * BASE_URL otomatis: bekerja di localhost, subfolder, root domain, dan
 * di belakang HTTPS/reverse proxy. Semua path berbasis __DIR__/ROOT_PATH
 * sehingga aman dipindah folder maupun domain (Windows/Linux/cPanel).
 */

/* ---------- 1. ENVIRONMENT & ERROR HANDLING ---------- */
define('APP_ENV', getenv('APP_ENV') ?: 'development'); // ubah ke 'production' saat hosting

error_reporting(E_ALL);
ini_set('display_errors', APP_ENV === 'production' ? '0' : '1');
ini_set('log_errors', '1');
if (APP_ENV === 'production') {
    ini_set('error_log', dirname(__DIR__) . '/storage/logs/php_errors.log');
}
date_default_timezone_set('Asia/Jakarta');

/* ---------- 2. FILESYSTEM PATH (single source of truth) ---------- */
define('ROOT_PATH', dirname(__DIR__));        // root project
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');

/* ---------- 3. DATABASE (cukup ubah di sini saat pindah ke hosting) ---------- */
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_musholla');

/* ---------- 4. BASE_URL (otomatis, tidak pernah hardcode) ---------- */
define('BASE_URL', detect_base_url());

function detect_base_url(): string
{
    if (PHP_SAPI === 'cli') {
        return '/';
    }

    // 4a. Protocol: dukung HTTPS dan reverse proxy / load balancer
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
    $scheme = $https ? 'https' : 'http';

    // 4b. Host
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';

    // 4c. Base path = letak ROOT_PATH di bawah DOCUMENT_ROOT.
    //     Tidak memakai dirname(SCRIPT_NAME) karena nilainya berubah
    //     tergantung halaman yang dibuka.
    $docRoot = rtrim(str_replace('\\', '/', (string)($_SERVER['DOCUMENT_ROOT'] ?? '')), '/');
    $appRoot = rtrim(str_replace('\\', '/', ROOT_PATH), '/');

    $basePath = '';
    if ($docRoot !== '' && strpos($appRoot, $docRoot) === 0) {
        $basePath = substr($appRoot, strlen($docRoot));
    }
    $basePath = '/' . trim($basePath, '/');
    if ($basePath === '/') {
        $basePath = '';
    }

    return $scheme . '://' . $host . $basePath . '/';
}

/* ---------- 5. HELPERS ---------- */

/** URL absolut relatif root project. Contoh: url('guru/index.php') */
function url(string $path = ''): string
{
    return BASE_URL . ltrim($path, '/');
}

/** URL asset. Contoh: asset('css/style.css') */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/** Redirect aman. Contoh: redirect('/') atau redirect('auth/logout.php') */
function redirect(string $path = '/'): void
{
    header('Location: ' . url($path));
    exit;
}

/** Ambil nilai $_POST dengan default. Contoh: post('username') */
function post(string $key, $default = '')
{
    return $_POST[$key] ?? $default;
}

/* ---------- 6. SESSION (satu titik, mencegah warning berulang) ---------- */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
    ]);
    session_start();
}

/* ---------- 7. KONEKSI DATABASE (error ke log, bukan ke browser) ---------- */
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$connect = null;
try {
    $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    mysqli_set_charset($connect, 'utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log('DB connection failed: ' . $e->getMessage());
    if (APP_ENV === 'development') {
        die('Database tidak dapat diakses: ' . htmlspecialchars($e->getMessage()));
    }
    die('Terjadi kesalahan sistem. Silakan coba lagi.');
}
