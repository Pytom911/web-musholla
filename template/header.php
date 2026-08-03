<?php
require_once __DIR__ . '/../config/config.php';
$pageTitle = $pageTitle ?? 'Sistem Informasi Musholla - SMK Negeri 1 Kraksaan';
$isLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] === true;
$role = $isLoggedIn ? ($_SESSION['role'] ?? null) : null;
$isAdmin = $role === 'admin';
$isPetugas = $role === 'petugas';

$username = $_SESSION['username'] ?? '';
$nama = $_SESSION['nama'] ?? $username;
$role = $_SESSION['role'] ?? '';
$defaultPhoto = $isAdmin ? 'admin_profile.jpg' : 'petugas_profile.jpg';
$profilePhoto = basename($_SESSION['foto'] ?? $defaultPhoto);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
</head>

<body>
    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="main-wrapper" id="mainWrapper">
        <div class="top-header d-flex justify-content-between align-items-center px-4 py-3 bg-white shadow-sm mb-4">
            <button class="btn-toggle-sidebar btn btn-light border-0" id="sidebarToggle" title="Toggle Sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>

            <div class="top-header-right">
                <?php if ($isLoggedIn): ?>
                    <!-- TAMPILAN JIKA SUDAH LOGIN (Modern Profile Dropdown) -->
                    <div class="user-profile dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-info text-end me-3 d-none d-md-block">
                                <div class="user-name fw-bold text-dark" style="font-size: 0.95rem; line-height: 1;">
                                    <?= htmlspecialchars($username) ?>
                                </div>
                                <div class="user-role text-muted small mt-1">
                                    <?= htmlspecialchars(ucfirst($role)) ?>
                                </div>
                            </div>
                                <img src="<?= asset('img/' . $defaultPhoto) ?>" alt="Profile"
                                    class="user-avatar rounded-circle object-fit-cover shadow-sm border border-2 border-white"
                                    width="45" height="45">
                        </a>

                        <!-- Dropdown Menu -->
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownUser">
                            <li>
                                <h6 class="dropdown-header d-md-none"><?= htmlspecialchars($nama) ?></h6>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item py-2 text-danger" href="<?= url('auth/logout.php') ?>"><i
                                        class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- TAMPILAN JIKA BELUM LOGIN -->
                    <a href="<?= url('auth/sign_in.php') ?>" class="btn-login-hero">
                        <i class="bi bi-box-arrow-in-right fs-5"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>