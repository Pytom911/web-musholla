<?php

require_once __DIR__ . '/../config/config.php';

function requireRole(array $allowedRoles)
{
    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        redirect('auth/sign_in.php');
    }

    $role = $_SESSION['role'] ?? null;

    if (!in_array($role, $allowedRoles, true)) {
        http_response_code(403);
        exit('Forbidden');
    }
}