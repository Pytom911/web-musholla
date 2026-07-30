<?php
session_start();

// Mengecek apakah ada session login aktif
$isLoggedIn = isset($_SESSION['login']) && $_SESSION['login'] === true;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Musholla - SMK Negeri 1 Kraksaan</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Overlay untuk Tampilan Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <!-- Sidebar Header dengan Logo -->
        <div class="sidebar-header">
            <img src="assets/img/musholla_logo.png" alt="Logo Musholla" class="sidebar-logo">
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-title">Sistem Informasi</span>
                <span class="sidebar-brand-musholla">Musholla</span>
                <span class="sidebar-brand-subtitle">SMK Negeri 1 Kraksaan</span>
            </div>
        </div>

        <!-- Sidebar Navigation Menu -->
        <div class="sidebar-menu">
            <a href="#" class="sidebar-link active">
                <div class="sidebar-link-left">
                    <i class="bi bi-house-door-fill"></i>
                    <span>Beranda</span>
                </div>
            </a>
                <div class="brand-divider">
                    <span></span>
                    <i class="bi bi-asterisk"></i>
                    <span></span>
                </div>
            <a href="jadwal_sholat/jadwal_sholat.php" class="sidebar-link">
                <div class="sidebar-link-left">
                    <i class="bi bi-people"></i>
                    <span>Data User</span>
                </div>
            </a>
            <a href="jadwal_sholat/jadwal_sholat.php" class="sidebar-link">
                <div class="sidebar-link-left">
                    <i class="bi bi-people"></i>
                    <span>Data Guru</span>
                </div>
            </a>
            <a href="jadwal_sholat/jadwal_sholat.php" class="sidebar-link">
                <div class="sidebar-link-left">
                    <i class="bi bi-book    "></i>
                    <span>Data Kelas</span>
                </div>
            </a>
                <div class="brand-divider">
                    <span></span>
                    <i class="bi bi-asterisk"></i>
                    <span></span>
                </div>
            <a href="jadwal_sholat/jadwal_sholat.php" class="sidebar-link">
                <div class="sidebar-link-left">
                    <i class="bi bi-calendar-event"></i>
                    <span>Jadwal Sholat</span>
                </div>
            </a>
            <a href="jadwal_imam/jadwal_imam.php" class="sidebar-link">
                <div class="sidebar-link-left">
                    <i class="bi bi-person-badge"></i>
                    <span>Jadwal Imam</span>
                </div>
            </a>
            <div class="brand-divider">
                <span></span>
                <i class="bi bi-asterisk"></i>
                <span></span>
            </div>
            <a href="#" class="sidebar-link">
                <div class="sidebar-link-left">
                    <i class="bi bi-card-checklist"></i>
                    <span>Kegiatan</span>
                </div>
            </a>
            <a href="#" class="sidebar-link">
                <div class="sidebar-link-left">
                    <i class="bi bi-cash"></i>
                    <span>Data Infaq</span>
                </div>
            </a>
            <a href="#" class="sidebar-link">
                <div class="sidebar-link-left">
                    <i class="bi bi-cash"></i>
                    <span>Data Shodaqoh</span>
                </div>
            </a>
            <div class="brand-divider">
                <span></span>
                <i class="bi bi-asterisk"></i>
                <span></span>
            </div>
            

            <!-- Dropdown Keuangan -->
            <!-- <a href="#" class="sidebar-link" data-bs-toggle="collapse" data-bs-target="#menuKeuangan" aria-expanded="false">
                <div class="sidebar-link-left">
                    <i class="bi bi-wallet2"></i>
                    <span>Informasi Keuangan</span>
                </div>
                <i class="bi bi-chevron-down" style="font-size: 0.75rem;"></i>
            </a>
            <div class="collapse ps-3" id="menuKeuangan">
                <a href="#" class="sidebar-link py-2"><span style="font-size: 0.85rem;">Data Infaq</span></a>
                <a href="#" class="sidebar-link py-2"><span style="font-size: 0.85rem;">Data Shodaqoh</span></a>
            </div> -->

            <!-- Dropdown Laporan -->
            <a href="#" class="sidebar-link" data-bs-toggle="collapse" data-bs-target="#menuLaporan" aria-expanded="false">
                <div class="sidebar-link-left">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Laporan</span>
                </div>
                <i class="bi bi-chevron-down" style="font-size: 0.75rem;"></i>
            </a>
            <div class="collapse ps-3" id="menuLaporan">
                <a href="#" class="sidebar-link py-2"><span style="font-size: 0.85rem;">Laporan Keuangan</span></a>
                <a href="#" class="sidebar-link py-2"><span style="font-size: 0.85rem;">Laporan Kegiatan</span></a>
            </div>
        </div>

        <!-- Sidebar Quote (Bottom) -->
        <div class="quote-section">
            <div class="quote-icon">&ldquo;</div>
            <div class="quote-text">
                "Jadikan musholla sebagai pusat ibadah, ilmu, dan persaudaraan."
            </div>
            <div class="quote-author">&mdash; Pengurus Musholla</div>
        </div>
    </aside>

<!-- MAIN CONTENT WRAPPER -->
    <div class="main-wrapper" id="mainWrapper">

        <!-- TOP BAR DENGAN PROFILE / LOGIN -->
        <!-- Gunakan d-flex justify-content-between agar tombol toggle dan profile sejajar -->
        <div class="top-header d-flex justify-content-between align-items-center px-4 py-3 bg-white shadow-sm mb-4">
            <button class="btn-toggle-sidebar btn btn-light border-0" id="sidebarToggle" title="Toggle Sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>

            <div class="top-header-right">
                <?php if ($isLoggedIn): ?>
                    <!-- TAMPILAN JIKA SUDAH LOGIN (Modern Profile Dropdown) -->
                    <div class="user-profile dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <!-- Info teks: Disembunyikan di layar sangat kecil (mobile) -->
                            <div class="user-info text-end me-3 d-none d-md-block">
                                <div class="user-name fw-bold text-dark" style="font-size: 0.95rem; line-height: 1;">
                                    <?= htmlspecialchars($_SESSION['username']) ?>
                                </div>
                                <div class="user-role text-muted small mt-1">
                                    <?= ucfirst(htmlspecialchars($_SESSION['role'])) ?>
                                </div>
                            </div>
                            <!-- Foto Profil -->
                            <img src="assets/img/<?= htmlspecialchars($_SESSION['foto'] = "default_profile.jpg") ?>" alt="Profile" class="user-avatar rounded-circle object-fit-cover shadow-sm border border-2 border-white" width="45" height="45">
                        </a>
                        
                        <!-- Dropdown Menu -->
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownUser">
                            <li><h6 class="dropdown-header d-md-none"><?= htmlspecialchars($_SESSION['nama']) ?></h6></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger" href="auth/logout.php"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- TAMPILAN JIKA BELUM LOGIN -->
                    <a href="auth/sign_in.php" class="btn-login-hero">
                        <i class="bi bi-box-arrow-in-right fs-5"></i> Login
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Hero Section -->
        <div class="hero-banner">
            <div class="hero-content">
                <p class="hero-subtitle">Selamat Datang di</p>
                <h1 class="hero-title">Sistem Informasi Musholla<br>SMK Negeri 1 Kraksaan</h1>
                <p class="hero-desc">Informasi kegiatan, jadwal, dan laporan musholla sekolah<br class="d-none d-md-inline">dalam satu sistem yang mudah diakses.</p>
            </div>
            <img src="assets/img/musholla_logo.png" alt="Logo Musholla" class="hero-logo-large">
        </div>

        <!-- Stats Cards Grid -->
        <div class="row g-3">
            <!-- Card 1: Total Infaq -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div>
                        <div class="stat-header">
                            <div class="stat-icon green"><i class="bi bi-box2-heart"></i></div>
                            <h3 class="stat-title">Total Infaq <span class="fw-normal">(Bulan ini)</span></h3>
                        </div>
                        <div class="stat-value">Rp 12.500.000</div>
                    </div>
                    <a href="#" class="stat-link">Lihat detail &rarr;</a>
                </div>
            </div>

            <!-- Card 2: Total Shodaqoh -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div>
                        <div class="stat-header">
                            <div class="stat-icon blue"><i class="bi bi-camera"></i></div>
                            <h3 class="stat-title">Total Shodaqoh <span class="fw-normal">(Bulan ini)</span></h3>
                        </div>
                        <div class="stat-value">Rp 8.000.000</div>
                    </div>
                    <a href="#" class="stat-link">Lihat detail &rarr;</a>
                </div>
            </div>

            <!-- Card 3: Total Kegiatan -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div>
                        <div class="stat-header">
                            <div class="stat-icon orange"><i class="bi bi-gift"></i></div>
                            <h3 class="stat-title">Total Kegiatan</h3>
                        </div>
                        <div class="stat-value">25</div>
                    </div>
                    <a href="#" class="stat-link">Lihat detail &rarr;</a>
                </div>
            </div>

            <!-- Card 4: Jadwal Hari Ini -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="stat-card">
                    <div>
                        <div class="stat-header">
                            <div class="stat-icon purple"><i class="bi bi-clipboard-check"></i></div>
                            <h3 class="stat-title">Jadwal Hari Ini</h3>
                        </div>
                        <div class="stat-value">2</div>
                    </div>
                    <a href="#" class="stat-link">Lihat detail &rarr;</a>
                </div>
            </div>
        </div>

        <!-- Bottom Section: Jadwal & Kegiatan -->
        <div class="row g-4 mt-1">
            <!-- Left Column: Jadwal Hari Ini -->
            <div class="col-12 col-lg-6">
                <div class="section-header">
                    <h4 class="section-title">Jadwal Hari Ini</h4>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <div class="info-card">
                            <div class="jadwal-icon bg-light-green">
                                <i class="bi bi-clock"></i>
                            </div>
                            <div class="jadwal-name">Dhuzur</div>
                            <div class="jadwal-time">12:00 - 12:45</div>
                            <p class="jadwal-kelas">Kelas: XI RPL 1</p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="info-card">
                            <div class="jadwal-icon bg-light-blue">
                                <i class="bi bi-person"></i>
                            </div>
                            <div class="jadwal-name">Ashar</div>
                            <div class="jadwal-time">15:30 - 16:15</div>
                            <p class="jadwal-kelas">Kelas: XI RPL 1</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Kegiatan Terbaru -->
            <div class="col-12 col-lg-6">
                <div class="section-header">
                    <h4 class="section-title">Kegiatan Terbaru</h4>
                    <a href="#" class="section-link">Lihat Semua &rarr;</a>
                </div>
                <div class="info-card">
                    <div class="kegiatan-list">
                        <!-- Item 1 -->
                        <div class="kegiatan-item">
                            <img src="assets/img/img1.png" alt="Kajian Islam" class="kegiatan-img">
                            <div class="kegiatan-info">
                                <h6>Kajian Islam</h6>
                                <p><i class="bi bi-clock"></i> 20 Jul 2026</p>
                            </div>
                        </div>
                        <!-- Item 2 -->
                        <div class="kegiatan-item">
                            <img src="assets/img/img1.png" alt="Pesantren Ramadhan" class="kegiatan-img">
                            <div class="kegiatan-info">
                                <h6>Pesantren Ramadhan</h6>
                                <p><i class="bi bi-clock"></i> 10 Mar 2026</p>
                            </div>
                        </div>
                        <!-- Item 3 -->
                        <div class="kegiatan-item">
                            <img src="assets/img/img1.png" alt="Isra Mi'raj" class="kegiatan-img">
                            <div class="kegiatan-info">
                                <h6>Isra Mi'raj</h6>
                                <p><i class="bi bi-clock"></i> 27 Feb 2026</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript Toggle Sidebar & Responsif -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const mainWrapper = document.getElementById('mainWrapper');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                if (window.innerWidth < 992) {
                    // Perangkat Mobile/Tablet
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                } else {
                    // Perangkat Desktop
                    sidebar.classList.toggle('hidden');
                    mainWrapper.classList.toggle('expanded');
                }
            }

            sidebarToggle.addEventListener('click', toggleSidebar);
            sidebarOverlay.addEventListener('click', toggleSidebar);

            // Menutup sidebar mobile saat memilih menu (kecuali dropdown)
            document.querySelectorAll('.sidebar-link:not([data-bs-toggle])').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        sidebar.classList.remove('show');
                        sidebarOverlay.classList.remove('show');
                    }
                });
            });

            // Menyesuaikan tampilan saat resize jendela browser
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>