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

define('APP_ENV', getenv('APP_ENV') ?: 'development');

error_reporting(E_ALL);

ini_set(
    'display_errors',
    APP_ENV === 'production' ? '0' : '1'
);

ini_set('log_errors', '1');

if (APP_ENV === 'production') {
    ini_set(
        'error_log',
        dirname(__DIR__) . '/storage/logs/php_errors.log'
    );
}

date_default_timezone_set('Asia/Jakarta');


/* ---------- 2. FILESYSTEM PATH (single source of truth) ---------- */

define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');


/* ---------- 3. ENV LOADER ---------- */

function load_env(string $file): void
{
    if (!file_exists($file)) {
        return;
    }

    $lines = file(
        $file,
        FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
    );

    foreach ($lines as $line) {

        $line = trim($line);

        // Lewati baris kosong dan komentar
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }

        // Pastikan ada tanda =
        if (!str_contains($line, '=')) {
            continue;
        }

        [$key, $value] = explode('=', $line, 2);

        $key = trim($key);
        $value = trim($value);

        // Hapus tanda kutip jika ada
        if (
            strlen($value) >= 2 &&
            (
                ($value[0] === '"' && $value[-1] === '"') ||
                ($value[0] === "'" && $value[-1] === "'")
            )
        ) {
            $value = substr($value, 1, -1);
        }

        putenv("$key=$value");
    }
}

load_env(ROOT_PATH . '/.env');


/* ---------- 4. DATABASE ---------- */

define(
    'DB_HOST',
    getenv('DB_HOST') ?: 'dbhost lu'
);

define(
    'DB_PORT',
    (int) (getenv('DB_PORT') ?: port lu)
);

define(
    'DB_USER',
    getenv('DB_USER') ?: 'user lu'
);

define(
    'DB_PASS',
    getenv('DB_PASS') ?: 'pass lu'
);

define(
    'DB_NAME',
    getenv('DB_NAME') ?: 'db lu'
);


/* ---------- 5. BASE_URL (otomatis, tidak pernah hardcode) ---------- */

define('BASE_URL', detect_base_url());

function detect_base_url(): string
{
    if (PHP_SAPI === 'cli') {
        return '/';
    }


    /* 5a. Protocol: dukung HTTPS dan reverse proxy / load balancer */

    $https =
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (
            isset($_SERVER['SERVER_PORT'])
            && (int) $_SERVER['SERVER_PORT'] === 443
        )
        || (
            isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
            && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'
        );

    $scheme = $https ? 'https' : 'http';


    /* 5b. Host */

    $host =
        $_SERVER['HTTP_HOST']
        ?? $_SERVER['SERVER_NAME']
        ?? 'localhost';


    /* 5c. Base path */

    $docRoot = rtrim(
        str_replace(
            '\\',
            '/',
            (string) (
                $_SERVER['DOCUMENT_ROOT'] ?? ''
            )
        ),
        '/'
    );

    $appRoot = rtrim(
        str_replace(
            '\\',
            '/',
            ROOT_PATH
        ),
        '/'
    );

    $basePath = '';

    if (
        $docRoot !== ''
        && strpos($appRoot, $docRoot) === 0
    ) {
        $basePath = substr(
            $appRoot,
            strlen($docRoot)
        );
    }

    $basePath = '/' . trim($basePath, '/');

    if ($basePath === '/') {
        $basePath = '';
    }

    return $scheme
        . '://'
        . $host
        . $basePath
        . '/';
}


/* ---------- 6. EMAIL & OTP ---------- */

/*
 * Kredensial database sengaja tidak dibaca dari .env (lihat blok DATABASE).
 * Pengaturan email/OTP boleh dioverride lewat .env.
 */

define('APP_URL', rtrim(getenv('APP_URL') ?: BASE_URL, '/') . '/');

define(
    'MAIL_FROM',
    getenv('MAIL_FROM')
        ?: 'Sistem Informasi Musholla <onboarding@resend.dev>'
);

define('MAIL_REPLY_TO', getenv('MAIL_REPLY_TO') ?: '');

define('RESEND_API_KEY', getenv('RESEND_API_KEY') ?: '');

define(
    'OTP_TTL_MINUTES',
    max(1, (int) (getenv('OTP_TTL_MINUTES') ?: 10))
);

define(
    'OTP_MAX_ATTEMPTS',
    max(1, (int) (getenv('OTP_MAX_ATTEMPTS') ?: 5))
);

define(
    'OTP_RESEND_COOLDOWN',
    max(0, (int) (getenv('OTP_RESEND_COOLDOWN') ?: 60))
);


/* ---------- 7. HELPERS ---------- */


/**
 * URL absolut relatif root project.
 * Contoh: url('guru/index.php')
 */

function url(string $path = ''): string
{
    return BASE_URL . ltrim($path, '/');
}


/**
 * URL asset.
 * Contoh: asset('css/style.css')
 */

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}


/**
 * Redirect aman.
 * Contoh: redirect('/') atau redirect('auth/logout.php')
 */

function redirect(string $path = '/'): void
{
    header('Location: ' . url($path));
    exit;
}


/**
 * Ambil nilai $_POST dengan default.
 * Contoh: post('username')
 */

function post(string $key, $default = '')
{
    return $_POST[$key] ?? $default;
}


/**
 * Token CSRF per session (dibuat sekali, dipakai semua form POST).
 */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}


/**
 * Hidden input CSRF siap cetak di dalam <form>.
 */

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="'
        . htmlspecialchars(csrf_token(), ENT_QUOTES)
        . '">';
}


/**
 * Validasi token CSRF dari $_POST.
 */

function csrf_check(): bool
{
    $token = $_POST['csrf_token'] ?? '';

    return is_string($token)
        && $token !== ''
        && hash_equals(
            (string) ($_SESSION['csrf_token'] ?? ''),
            $token
        );
}

function hari_indonesia(?string $tanggal): string
{
    $tanggal = trim($tanggal ?? '');

    if ($tanggal === '') {
        return '-';
    }

    $date = DateTime::createFromFormat('!Y-m-d', $tanggal);

    if (
        $date === false
        || $date->format('Y-m-d') !== $tanggal
    ) {
        return '-';
    }

    $days = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu',
    ];

    return $days[$date->format('l')] ?? '-';
}


/* ---------- 8. SESSION ---------- */

if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' =>
            !empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off',
    ]);

    session_start();
}


/* ---------- 9. KONEKSI DATABASE ---------- */

mysqli_report(
    MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT
);

$connect = null;

try {

    $connect = mysqli_init();

    mysqli_real_connect(
        $connect,
        DB_HOST,
        DB_USER,
        DB_PASS,
        DB_NAME,
        DB_PORT
    );

    mysqli_set_charset(
        $connect,
        'utf8mb4'
    );


} catch (mysqli_sql_exception $e) {

    error_log(
        'DB connection failed: '
        . $e->getMessage()
    );

    if (APP_ENV === 'development') {

        die(
            'Database tidak dapat diakses: '
            . htmlspecialchars(
                $e->getMessage()
            )
        );

    }

    die(
        'Terjadi kesalahan sistem. Silakan coba lagi.'
    );
}


/* ---------- 10. EMAIL & OTP HELPERS ---------- */

require_once CONFIG_PATH . '/mail.php';
require_once CONFIG_PATH . '/otp.php';
