<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Musholla - Dashboard Petugas</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Colors matching the design */
            --brand-green: #115E34; 
            --brand-green-hover: #0D4928;
            --brand-green-light: #EAF3EE;
            --bg-body: #F4F7F6;
            --text-dark: #1E293B;
            --text-gray: #64748B;
            --border-light: #E2E8F0;
            
            /* Card Icon Colors */
            --icon-infaq-bg: #115E34;
            --icon-shodaqoh-bg: #2563EB;
            --icon-kegiatan-bg: #F59E0B;
            --icon-saldo-bg: #8B5CF6;
            
            --sidebar-width: 260px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-dark);
            overflow-x: hidden;
        }

        /* ============ SIDEBAR ============ */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--brand-green);
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1000;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            background-color: #ffffff; /* White background for logo area as in design */
            border-bottom: 1px solid var(--border-light);
        }

        .sidebar-logo-img {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand-title {
            color: var(--text-dark);
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.2;
            margin: 0;
        }

        .sidebar-brand-subtitle {
            color: var(--text-gray);
            font-size: 0.75rem;
            margin: 0;
        }

        .sidebar-menu {
            padding: 1.5rem 1rem;
            flex-grow: 1;
            overflow-y: auto;
        }

        .nav-category {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 1.5rem 0 0.5rem 1rem;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 4px;
            font-weight: 500;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .sidebar-link:hover, .sidebar-link.active {
            color: #ffffff;
            background-color: rgba(255, 255, 255, 0.15);
        }

        .sidebar-link i {
            font-size: 1.2rem;
        }

        .sidebar-footer {
            padding: 1rem;
            margin-top: auto;
        }

        /* ============ MAIN CONTENT ============ */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: margin-left 0.3s ease;
        }

        /* ============ TOPBAR ============ */
        .topbar {
            height: 70px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .topbar-toggler {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--brand-green);
            cursor: pointer;
            padding: 0;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .notification-btn {
            position: relative;
            background: none;
            border: none;
            font-size: 1.3rem;
            color: var(--text-gray);
            cursor: pointer;
        }

        .notification-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background-color: var(--brand-green);
            color: white;
            font-size: 0.6rem;
            font-weight: bold;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #ffffff;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: var(--brand-green-light);
            color: var(--brand-green);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.95rem;
        }

        /* ============ CONTENT AREA ============ */
        .content-area {
            padding: 2rem;
            flex-grow: 1;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 0.25rem;
        }

        .page-subtitle {
            color: var(--text-gray);
            font-size: 0.95rem;
            margin-bottom: 2rem;
        }

        /* SUMMARY CARDS */
        .summary-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            border: 1px solid var(--border-light);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            height: 100%;
        }

        .summary-icon {
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #ffffff;
            flex-shrink: 0;
        }

        .icon-infaq { background-color: var(--icon-infaq-bg); }
        .icon-shodaqoh { background-color: var(--icon-shodaqoh-bg); }
        .icon-kegiatan { background-color: var(--icon-kegiatan-bg); }
        .icon-saldo { background-color: var(--icon-saldo-bg); }

        .summary-details {
            flex-grow: 1;
        }

        .summary-title {
            color: var(--text-gray);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .summary-amount {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.75rem;
        }

        .text-infaq { color: var(--icon-infaq-bg); }
        .text-shodaqoh { color: var(--icon-shodaqoh-bg); }
        .text-kegiatan { color: var(--icon-kegiatan-bg); }
        .text-saldo { color: var(--icon-saldo-bg); }

        .summary-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.8rem;
            color: var(--text-gray);
        }
        
        .chart-icon {
            font-size: 1.2rem;
        }

        /* DATA TABLE CARD */
        .data-card {
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid var(--border-light);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
            margin-top: 2rem;
            overflow: hidden;
        }

        .data-card-header {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--border-light);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .data-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--brand-green);
            margin: 0;
        }

        .btn-add {
            background-color: var(--brand-green);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-add:hover { background-color: var(--brand-green-hover); color: white; }

        .btn-outline-custom {
            background-color: white;
            color: var(--brand-green);
            border: 1px solid var(--brand-green);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
        }
        .btn-outline-custom:hover { background-color: var(--brand-green-light); }

        .table-custom {
            margin-bottom: 0;
            white-space: nowrap;
        }

        .table-custom thead th {
            background-color: #F8FAFC;
            color: var(--text-dark);
            font-weight: 600;
            font-size: 0.9rem;
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-light);
        }

        .table-custom tbody td {
            padding: 1rem 1.5rem;
            vertical-align: middle;
            font-size: 0.95rem;
            color: var(--text-dark);
            border-bottom: 1px solid var(--border-light);
        }

        .table-custom tbody tr:last-child td {
            border-bottom: none;
        }

        .text-nominal-green {
            color: var(--brand-green);
            font-weight: 500;
        }

        .btn-action {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            color: white;
            font-size: 0.9rem;
        }
        .btn-edit { background-color: #FBBF24; }
        .btn-edit:hover { background-color: #F59E0B; color: white;}
        .btn-delete { background-color: #EF4444; }
        .btn-delete:hover { background-color: #DC2626; color: white;}

        .data-card-footer {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top: 1px solid var(--border-light);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .pagination-info {
            color: var(--text-gray);
            font-size: 0.9rem;
        }

        .pagination {
            margin: 0;
        }

        .page-item .page-link {
            border: 1px solid var(--border-light);
            color: var(--text-gray);
            padding: 6px 12px;
            font-size: 0.9rem;
        }

        .page-item.active .page-link {
            background-color: var(--brand-green);
            border-color: var(--brand-green);
            color: white;
        }

        /* FOOTER */
        .footer {
            text-align: center;
            padding: 1.5rem;
            color: var(--text-gray);
            font-size: 0.85rem;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
            }
            .main-wrapper {
                margin-left: 0;
            }
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100vw;
                height: 100vh;
                background: rgba(0,0,0,0.5);
                z-index: 999;
                display: none;
            }
            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="login_page/img/musholla_logo.png" alt="Logo" class="sidebar-logo-img">
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-title">Sistem Informasi<br>Musholla</span>
                <span class="sidebar-brand-subtitle">SMK Negeri 1 Kraksaan</span>
            </div>
        </div>
        
        <div class="sidebar-menu">
            <a href="#" class="sidebar-link active">
                <i class="bi bi-house-door-fill"></i>
                <span>Dashboard</span>
            </a>

            <div class="nav-category">KEUANGAN</div>
            <a href="#" class="sidebar-link">
                <i class="bi bi-box2-heart"></i>
                <span>Data Infaq</span>
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-people"></i>
                <span>Data Shodaqoh</span>
            </a>

            <div class="nav-category">KEGIATAN</div>
            <a href="#" class="sidebar-link">
                <i class="bi bi-calendar4-event"></i>
                <span>Data Kegiatan</span>
            </a>

            <div class="nav-category">LAPORAN</div>
            <a href="#" class="sidebar-link">
                <i class="bi bi-file-earmark-text"></i>
                <span>Laporan Infaq</span>
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-file-earmark-text"></i>
                <span>Laporan Shodaqoh</span>
            </a>
            <a href="#" class="sidebar-link">
                <i class="bi bi-file-earmark-text"></i>
                <span>Laporan Kegiatan</span>
            </a>
        </div>

        <div class="sidebar-footer">
            <a href="login_page/sign_in.php" class="sidebar-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Keluar</span>
            </a>
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        
        <!-- Topbar -->
        <nav class="topbar">
            <button class="topbar-toggler d-lg-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <div class="d-none d-lg-block">
                <!-- Empty div to keep flex space-between intact on desktop -->
                <i class="bi bi-list fs-4 text-success" style="cursor: pointer;"></i>
            </div>

            <div class="topbar-right">
                <button class="notification-btn">
                    <i class="bi bi-bell"></i>
                    <span class="notification-badge">2</span>
                </button>
                <div class="user-profile" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-avatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div class="user-info">
                        <span>Petugas</span>
                        <i class="bi bi-chevron-down ms-1" style="font-size: 0.8rem;"></i>
                    </div>
                </div>
                <!-- Optional Dropdown menu for user profile -->
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                    <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="login_page/sign_in.php"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                </ul>
            </div>
        </nav>

        <!-- Content Area -->
        <main class="content-area">
            
            <!-- Page Header -->
            <h2 class="page-title">Selamat Datang, Petugas</h2>
            <p class="page-subtitle">Di Sistem Informasi Musholla SMK Negeri 1 Kraksaan</p>

            <!-- Summary Cards Row -->
            <div class="row g-4">
                <!-- Card 1 -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="summary-card">
                        <div class="summary-icon icon-infaq">
                            <i class="bi bi-bag-heart-fill"></i>
                        </div>
                        <div class="summary-details">
                            <div class="summary-title">Total Infaq</div>
                            <div class="summary-amount text-infaq">Rp 8.450.000</div>
                            <div class="summary-footer">
                                <span>Semua waktu</span>
                                <i class="bi bi-graph-up-arrow chart-icon text-infaq"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 2 -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="summary-card">
                        <div class="summary-icon icon-shodaqoh">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="summary-details">
                            <div class="summary-title">Total Shodaqoh</div>
                            <div class="summary-amount text-shodaqoh">Rp 6.350.000</div>
                            <div class="summary-footer">
                                <span>Semua waktu</span>
                                <i class="bi bi-graph-up-arrow chart-icon text-shodaqoh"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 3 -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="summary-card">
                        <div class="summary-icon icon-kegiatan">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="summary-details">
                            <div class="summary-title">Total Kegiatan</div>
                            <div class="summary-amount text-kegiatan">12</div>
                            <div class="summary-footer">
                                <span>Semua waktu</span>
                                <i class="bi bi-graph-up-arrow chart-icon text-kegiatan"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 4 -->
                <div class="col-12 col-md-6 col-xl-3">
                    <div class="summary-card">
                        <div class="summary-icon icon-saldo">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="summary-details">
                            <div class="summary-title">Saldo Kas</div>
                            <div class="summary-amount text-saldo">Rp 14.800.000</div>
                            <div class="summary-footer">
                                <span>Per hari ini</span>
                                <i class="bi bi-graph-up-arrow chart-icon text-saldo"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table Section -->
            <div class="data-card">
                <div class="data-card-header">
                    <h3 class="data-card-title">
                        <i class="bi bi-card-checklist fs-4"></i> Data Infaq Terbaru
                    </h3>
                    <div class="d-flex gap-2">
                        <button class="btn btn-add">
                            <i class="bi bi-plus-lg me-1"></i> Tambah Data Infaq
                        </button>
                        <button class="btn btn-outline-custom">
                            Lihat Semua
                        </button>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-custom">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Donatur</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>25-07-2026</td>
                                <td>Ahmad Fauzi</td>
                                <td class="text-nominal-green">Rp 100.000</td>
                                <td>Infaq Jumat</td>
                                <td>
                                    <button class="btn-action btn-edit"><i class="bi bi-pencil-fill"></i></button>
                                    <button class="btn-action btn-delete ms-1"><i class="bi bi-trash3-fill"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>25-07-2026</td>
                                <td>Guru & Staff</td>
                                <td class="text-nominal-green">Rp 250.000</td>
                                <td>Kotak Infaq</td>
                                <td>
                                    <button class="btn-action btn-edit"><i class="bi bi-pencil-fill"></i></button>
                                    <button class="btn-action btn-delete ms-1"><i class="bi bi-trash3-fill"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>24-07-2026</td>
                                <td>Budi Santoso</td>
                                <td class="text-nominal-green">Rp 50.000</td>
                                <td>Infaq Harian</td>
                                <td>
                                    <button class="btn-action btn-edit"><i class="bi bi-pencil-fill"></i></button>
                                    <button class="btn-action btn-delete ms-1"><i class="bi bi-trash3-fill"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>24-07-2026</td>
                                <td>Siti Aisyah</td>
                                <td class="text-nominal-green">Rp 75.000</td>
                                <td>Infaq Harian</td>
                                <td>
                                    <button class="btn-action btn-edit"><i class="bi bi-pencil-fill"></i></button>
                                    <button class="btn-action btn-delete ms-1"><i class="bi bi-trash3-fill"></i></button>
                                </td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>23-07-2026</td>
                                <td>Kotak Infaq</td>
                                <td class="text-nominal-green">Rp 300.000</td>
                                <td>Kotak Infaq</td>
                                <td>
                                    <button class="btn-action btn-edit"><i class="bi bi-pencil-fill"></i></button>
                                    <button class="btn-action btn-delete ms-1"><i class="bi bi-trash3-fill"></i></button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="data-card-footer">
                    <div class="pagination-info">
                        Menampilkan 1 sampai 5 dari 5 data
                    </div>
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm">
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                            </li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>

        </main>

        <!-- Footer -->
        <footer class="footer">
            &copy; 2026 Sistem Informasi Musholla - <strong>SMK Negeri 1 Kraksaan</strong>. All rights reserved.
        </footer>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Sidebar Toggle Script for Mobile -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebarOverlay');

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            }

            if(sidebarToggle) {
                sidebarToggle.addEventListener('click', toggleSidebar);
            }
            if(sidebarOverlay) {
                sidebarOverlay.addEventListener('click', toggleSidebar);
            }
        });
    </script>
</body>
</html>