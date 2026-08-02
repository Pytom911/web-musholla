<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <!-- Sidebar Header dengan Logo -->
    <div class="sidebar-header">
        <img src="<?= asset('img/musholla_logo.png') ?>" alt="Logo Musholla" class="sidebar-logo">
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-title">Sistem Informasi</span>
            <span class="sidebar-brand-musholla">Musholla</span>
            <span class="sidebar-brand-subtitle">SMK Negeri 1 Kraksaan</span>
        </div>
    </div>

    <!-- Sidebar Navigation Menu -->
    <div class="sidebar-menu">
        <a href="<?= url('/') ?>" class="sidebar-link active">
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
        <a href="<?= url('users/index.php') ?>" class="sidebar-link">
            <div class="sidebar-link-left">
                <i class="bi bi-people"></i>
                <span>Data User</span>
            </div>
        </a>
        <a href="<?= url('guru/index.php') ?>" class="sidebar-link">
            <div class="sidebar-link-left">
                <i class="bi bi-people"></i>
                <span>Data Guru</span>
            </div>
        </a>
        <a href="<?= url('kelas/index.php') ?>" class="sidebar-link">
            <div class="sidebar-link-left">
                <i class="bi bi-book"></i>
                <span>Data Kelas</span>
            </div>
        </a>
        <div class="brand-divider">
            <span></span>
            <i class="bi bi-asterisk"></i>
            <span></span>
        </div>
        <a href="<?= url('jadwal_sholat/jadwal_sholat.php') ?>" class="sidebar-link">
            <div class="sidebar-link-left">
                <i class="bi bi-calendar-event"></i>
                <span>Jadwal Sholat</span>
            </div>
        </a>
        <a href="<?= url('jadwal_imam/jadwal_imam.php') ?>" class="sidebar-link">
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
        <a href="<?= url('kegiatan/index.php') ?>" class="sidebar-link">
            <div class="sidebar-link-left">
                <i class="bi bi-card-checklist"></i>
                <span>Kegiatan</span>
            </div>
        </a>
        <a href="<?= url('infaq/index.php') ?>" class="sidebar-link">
            <div class="sidebar-link-left">
                <i class="bi bi-cash"></i>
                <span>Data Infaq</span>
            </div>
        </a>
        <a href="<?= url('shodaqoh/index.php') ?>" class="sidebar-link">
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

        <!-- Dropdown Laporan -->
        <a href="#" class="sidebar-link" data-bs-toggle="collapse" data-bs-target="#menuLaporan" aria-expanded="false">
            <div class="sidebar-link-left">
                <i class="bi bi-file-earmark-text"></i>
                <span>Laporan</span>
            </div>
            <i class="bi bi-chevron-down" style="font-size: 0.75rem;"></i>
        </a>
        <div class="collapse ps-3" id="menuLaporan">
            <a href="<?= url('laporan/laporan.php') ?>" class="sidebar-link py-2"><span style="font-size: 0.85rem;">Laporan Infaq</span></a>
            <a href="<?= url('laporan/laporan-Shodaqoh.php') ?>" class="sidebar-link py-2"><span style="font-size: 0.85rem;">Laporan Shodaqoh</span></a>
            <a href="<?= url('laporan/laporan-Kegiatan.php') ?>" class="sidebar-link py-2"><span style="font-size: 0.85rem;">Laporan Kegiatan</span></a>
            <a href="<?= url('laporan/laporan-Jadwal.php') ?>" class="sidebar-link py-2"><span style="font-size: 0.85rem;">Laporan Jadwal</span></a>
            <a href="<?= url('laporan/laporan-Imam.php') ?>" class="sidebar-link py-2"><span style="font-size: 0.85rem;">Laporan Imam</span></a>
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
