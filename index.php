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

    <style>
        * { box-sizing: border-box; }

        :root {
            --primary-green: #0C4E2B;       /* Hijau khas sidebar */
            --primary-green-hover: #08381F; /* Hijau lebih gelap untuk hover */
            --active-item-bg: #145E36;      /* Hijau tombol aktif */
            --soft-green: #DCFCE7;
            --bg-body: #F4F7F6;
            --text-dark: #1E293B;
            --text-gray: #64748B;
            --sidebar-width: 270px;
        }

        html { -webkit-text-size-adjust: 100%; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
            overflow-x: hidden;
            width: 100%;
        }

        img, svg { max-width: 100%; }

        /* ================= SIDEBAR ================= */
        .sidebar {
            width: var(--sidebar-width);
            max-width: 82vw;
            background-color: var(--primary-green);
            color: #ffffff;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            height: 100dvh;
            z-index: 1050;
            transition: all 0.3s ease-in-out;
            display: flex;
            flex-direction: column;
        }

        .sidebar.hidden {
            transform: translateX(-100%);
        }

        .sidebar-header {
            padding: 1.6rem 1.4rem 1.3rem 1.4rem;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-logo {
            width: 42px;
            height: 42px;
            object-fit: contain;
            flex-shrink: 0;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
            min-width: 0;
        }

        .sidebar-brand-title {
            font-size: 0.72rem;
            opacity: 0.85;
            font-weight: 400;
        }

        .sidebar-brand-musholla {
            font-size: 1.2rem;
            font-weight: 800;
            letter-spacing: 0.3px;
        }

        .sidebar-brand-subtitle {
            font-size: 0.68rem;
            opacity: 0.8;
            margin-top: 1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-menu {
            padding: 0.5rem 1rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 6px;
            font-weight: 500;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar-link.active {
            color: #ffffff;
            background-color: var(--active-item-bg);
            font-weight: 600;
        }

        .sidebar-link-left {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .sidebar-link i {
            font-size: 1.15rem;
            width: 20px;
            text-align: center;
        }

        .sidebar-link .bi-chevron-down { transition: transform 0.2s ease; }
        .sidebar-link[aria-expanded="true"] .bi-chevron-down { transform: rotate(-180deg); }

        /* Quote Section */
        .quote-section {
            padding: 1.4rem 1.5rem 1.8rem 1.5rem;
            margin-top: auto;
        }

        .quote-icon {
            font-size: 2.2rem;
            color: rgba(255,255,255,0.3);
            line-height: 1;
            margin-bottom: 8px;
            font-family: serif;
        }

        .quote-text {
            font-size: 0.82rem;
            font-style: italic;
            opacity: 0.9;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .quote-author {
            font-size: 0.78rem;
            color: #86EFAC; /* Light green accent */
            font-weight: 500;
        }

        /* Overlay for Mobile Drawer */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.4);
            z-index: 1040;
            display: none;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* ================= MAIN CONTENT ================= */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: all 0.3s ease-in-out;
            padding: 1.5rem 2rem 2.5rem 2rem;
            max-width: 100%;
        }

        .main-wrapper.expanded {
            margin-left: 0;
        }

        /* Top Bar */
        .top-header {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .btn-toggle-sidebar {
            background: #ffffff;
            border: 1px solid #E2E8F0;
            color: var(--primary-green);
            padding: 8px 14px;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
        }

        .btn-toggle-sidebar:hover {
            background: #F8FAFC;
            color: var(--primary-green-hover);
        }

        /* Hero Banner */
        .hero-banner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 0.5rem 0;
            gap: 2rem;
        }

        .hero-content {
            max-width: 620px;
            min-width: 0;
        }

        .hero-subtitle {
            font-size: 1.05rem;
            color: var(--text-gray);
            margin-bottom: 0.25rem;
            font-weight: 500;
        }

        .hero-title {
            font-size: clamp(1.4rem, 1.05rem + 1.6vw, 2.1rem);
            font-weight: 800;
            color: #0F172A;
            line-height: 1.25;
            margin-bottom: 0.85rem;
            letter-spacing: -0.5px;
        }

        .hero-desc {
            color: var(--text-gray);
            font-size: 0.95rem;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .btn-login-hero {
            background-color: var(--primary-green);
            color: #ffffff;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.2s ease;
        }

        .btn-login-hero:hover {
            background-color: var(--primary-green-hover);
            color: #ffffff;
        }

        .hero-logo-large {
            width: 340px;
            height: auto;
            max-height: 340px;
            object-fit: contain;
            opacity: 0.95;
            flex-shrink: 0;
        }

        /* Stats Cards */
        .stat-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 1.35rem 1.25rem;
            border: 1px solid #F1F5F9;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 0.85rem;
        }

        .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.05rem;
            flex-shrink: 0;
        }

        .stat-icon.green { background-color: #16A34A; }
        .stat-icon.blue { background-color: #2563EB; }
        .stat-icon.orange { background-color: #F59E0B; }
        .stat-icon.purple { background-color: #7C3AED; }

        .stat-title {
            font-size: 0.8rem;
            color: var(--text-gray);
            font-weight: 500;
            margin: 0;
            line-height: 1.2;
        }

        .stat-value {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 0.75rem;
            word-break: break-word;
        }

        .stat-link {
            font-size: 0.82rem;
            color: #16A34A;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .stat-link:hover {
            color: #15803D;
        }

        /* Section Titles */
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            margin-top: 2rem;
        }

        .section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--text-dark);
            margin: 0;
        }

        .section-link {
            font-size: 0.85rem;
            color: #16A34A;
            font-weight: 600;
            text-decoration: none;
        }

        .section-link:hover {
            color: #15803D;
        }

        /* Cards Info (Jadwal & Kegiatan) */
        .info-card {
            background: #ffffff;
            border-radius: 14px;
            padding: 1.25rem;
            border: 1px solid #F1F5F9;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.015);
            height: 100%;
        }

        .jadwal-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 1.05rem;
        }

        .bg-light-green { background-color: #DCFCE7; color: #16A34A; }
        .bg-light-blue { background-color: #DBEAFE; color: #2563EB; }

        .jadwal-name {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.2rem;
        }

        .jadwal-time {
            font-size: 1.15rem;
            font-weight: 800;
            margin-bottom: 0.75rem;
            color: var(--text-dark);
        }

        .jadwal-kelas {
            font-size: 0.8rem;
            color: var(--text-gray);
            margin: 0;
            font-weight: 500;
        }

        /* List Kegiatan */
        .kegiatan-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .kegiatan-item {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .kegiatan-img {
            width: 68px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
            background-color: #E2E8F0;
            flex-shrink: 0;
        }

        .kegiatan-info {
            min-width: 0;
        }

        .kegiatan-info h6 {
            font-size: 0.95rem;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: var(--text-dark);
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .kegiatan-info p {
            font-size: 0.78rem;
            color: var(--text-gray);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ================= RESPONSIVE DESIGN ================= */
        @media (max-width: 1199.98px) {
            .hero-logo-large { width: 190px; }
        }

        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-wrapper {
                margin-left: 0;
                padding: 1.25rem;
            }

            .hero-banner {
                flex-direction: column-reverse;
                text-align: center;
                gap: 1.25rem;
            }

            .hero-content {
                margin: 0 auto;
                max-width: 100%;
            }

            .hero-logo-large {
                width: 170px;
            }

            .btn-login-hero {
                justify-content: center;
            }
        }

        @media (max-width: 575.98px) {
            .main-wrapper { padding: 1rem 0.9rem 2rem 0.9rem; }
            .stat-value { font-size: 1.2rem; }
            .jadwal-time { font-size: 1rem; }
            .hero-logo-large { width: 130px; }
            .hero-desc br { display: none; }
            .hero-title br { display: none; }
            .sidebar-header { padding: 1.3rem 1.1rem 1.1rem 1.1rem; }
            .section-header { flex-wrap: wrap; row-gap: 0.4rem; }
        }

        @media (max-width: 360px) {
            .hero-logo-large { width: 105px; }
        }
    </style>
</head>
<body>

    <!-- Overlay untuk Tampilan Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <!-- Sidebar Header dengan Logo -->
        <div class="sidebar-header">
            <img src="img/musholla_logo.png" alt="Logo Musholla" class="sidebar-logo">
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
            <a href="page/jadwal_sholat.php" class="sidebar-link">
                <div class="sidebar-link-left">
                    <i class="bi bi-calendar-event"></i>
                    <span>Jadwal Sholat</span>
                </div>
            </a>
            <a href="page/jadwal_imam.php   " class="sidebar-link">
                <div class="sidebar-link-left">
                    <i class="bi bi-person-badge"></i>
                    <span>Jadwal Imam</span>
                </div>
            </a>
            <a href="#" class="sidebar-link">
                <div class="sidebar-link-left">
                    <i class="bi bi-card-checklist"></i>
                    <span>Kegiatan</span>
                </div>
            </a>

            <!-- Dropdown Keuangan -->
            <a href="#" class="sidebar-link" data-bs-toggle="collapse" data-bs-target="#menuKeuangan" aria-expanded="false">
                <div class="sidebar-link-left">
                    <i class="bi bi-wallet2"></i>
                    <span>Informasi Keuangan</span>
                </div>
                <i class="bi bi-chevron-down" style="font-size: 0.75rem;"></i>
            </a>
            <div class="collapse ps-3" id="menuKeuangan">
                <a href="#" class="sidebar-link py-2"><span style="font-size: 0.85rem;">Data Infaq</span></a>
                <a href="#" class="sidebar-link py-2"><span style="font-size: 0.85rem;">Data Shodaqoh</span></a>
            </div>

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

        <!-- Top Bar Toggle Button -->
        <div class="top-header">
            <button class="btn-toggle-sidebar" id="sidebarToggle" title="Sembunyikan / Tampilkan Sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>
        </div>

        <!-- Hero Section -->
        <div class="hero-banner">
            <div class="hero-content">
                <p class="hero-subtitle">Selamat Datang di</p>
                <h1 class="hero-title">Sistem Informasi Musholla<br>SMK Negeri 1 Kraksaan</h1>
                <p class="hero-desc">Informasi kegiatan, jadwal, dan laporan musholla sekolah<br class="d-none d-md-inline">dalam satu sistem yang mudah diakses.</p>
                <a href="login_page/sign_in.php" class="btn-login-hero">
                    <i class="bi bi-box-arrow-in-right fs-5"></i> Login
                </a>
            </div>
            <!-- Logo Utama Kanan Hero -->
            <img src="img/musholla_logo.png" alt="Logo Musholla" class="hero-logo-large">
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
                            <img src="img/img1.png" alt="Kajian Islam" class="kegiatan-img">
                            <div class="kegiatan-info">
                                <h6>Kajian Islam</h6>
                                <p><i class="bi bi-clock"></i> 20 Jul 2026</p>
                            </div>
                        </div>
                        <!-- Item 2 -->
                        <div class="kegiatan-item">
                            <img src="img/img1.png" alt="Pesantren Ramadhan" class="kegiatan-img">
                            <div class="kegiatan-info">
                                <h6>Pesantren Ramadhan</h6>
                                <p><i class="bi bi-clock"></i> 10 Mar 2026</p>
                            </div>
                        </div>
                        <!-- Item 3 -->
                        <div class="kegiatan-item">
                            <img src="img/img1.png" alt="Isra Mi'raj" class="kegiatan-img">
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