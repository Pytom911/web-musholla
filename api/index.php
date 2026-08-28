<?php

$request = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$request = urldecode($request);

if ($request === '/' || $request === '') {
    require_once __DIR__ . '/../index.php';
    exit;
}

$file = realpath(__DIR__ . '/..' . $request);

$root = realpath(__DIR__ . '/..');

if (
    $file &&
    $root &&
    str_starts_with($file, $root) &&
    is_file($file) &&
    pathinfo($file, PATHINFO_EXTENSION) === 'php'
) {
    require_once $file;
    exit;
}

http_response_code(404);
echo '404 - Page Not Found';