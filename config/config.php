<?php

/**
 * config/config.php
 * Sistem Informasi Musholla - InfinityFree
 */


/* =========================================================
   1. PATH
   ========================================================= */

define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');


/* =========================================================
   2. ENVIRONMENT
   ========================================================= */

define('APP_ENV', 'production');

error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

date_default_timezone_set('Asia/Jakarta');


/* =========================================================
   3. DATABASE INFINITYFREE
   ========================================================= */

define('DB_HOST', 'localhost');
define('DB_PORT', 3306);
define('DB_USER', '3080814109');
define('DB_PASS', '09s7ed');
define('DB_NAME', '3080814109_db_musholla_v2
');
/* =========================================================
   4. BASE URL
   ========================================================= */

define('BASE_URL', detect_base_url());

function detect_base_url(): string
{
    if (PHP_SAPI === 'cli') {
        return '/';
    }

    $https =
        (
            !empty($_SERVER['HTTPS']) &&
            strtolower($_SERVER['HTTPS']) !== 'off'
        )
        ||
        (
            isset($_SERVER['SERVER_PORT']) &&
            (int) $_SERVER['SERVER_PORT'] === 443
        )
        ||
        (
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https'
        );

    $scheme = $https ? 'https' : 'http';

    $host =
        $_SERVER['HTTP_HOST']
        ?? $_SERVER['SERVER_NAME']
        ?? 'localhost';

    /*
     * Hilangkan port dari hostname.
     */
    $host = preg_replace('/:\d+$/', '', $host);

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
        $docRoot !== '' &&
        strpos($appRoot, $docRoot) === 0
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

    return
        $scheme .
        '://' .
        $host .
        $basePath .
        '/';
}


/* =========================================================
   5. URL HELPERS
   ========================================================= */

function url(string $path = ''): string
{
    return BASE_URL . ltrim($path, '/');
}


function asset(string $path): string
{
    return url(
        'assets/' . ltrim($path, '/')
    );
}


function redirect(string $path = '/'): void
{
    header(
        'Location: ' . url($path)
    );

    exit;
}


function post(
    string $key,
    $default = ''
) {
    return $_POST[$key] ?? $default;
}


/* =========================================================
   6. HARI INDONESIA
   ========================================================= */

function hari_indonesia(
    ?string $tanggal
): string {

    $tanggal = trim(
        $tanggal ?? ''
    );

    if ($tanggal === '') {
        return '-';
    }

    $date = DateTime::createFromFormat(
        '!Y-m-d',
        $tanggal
    );

    if (
        $date === false ||
        $date->format('Y-m-d') !== $tanggal
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


/* =========================================================
   7. SESSION
   ========================================================= */

if (
    session_status() === PHP_SESSION_NONE
) {

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' =>
            !empty($_SERVER['HTTPS']) &&
            $_SERVER['HTTPS'] !== 'off',
    ]);

    session_start();
}


/* =========================================================
   8. DATABASE CONNECTION
   ========================================================= */

mysqli_report(
    MYSQLI_REPORT_ERROR |
    MYSQLI_REPORT_STRICT
);

$connect = null;

try {

    $connect = mysqli_init();

    /*
     * PENTING:
     *
     * Tidak menggunakan:
     * mysqli_ssl_set()
     * MYSQLI_CLIENT_SSL
     * isrgrootx1.pem
     *
     * karena itu merupakan konfigurasi TiDB lama.
     */

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
        'DB connection failed: ' .
        $e->getMessage()
    );

    die(
        'Database tidak dapat diakses: ' .
        htmlspecialchars(
            $e->getMessage(),
            ENT_QUOTES,
            'UTF-8'
        )
    );
}