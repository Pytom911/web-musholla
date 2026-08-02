<?php
$pageTitle = 'Beranda - Sistem Informasi Musholla';
require_once __DIR__ . '/template/header.php';
?>

<!-- Hero Section (Ringkas) -->
<section class="hero-banner">
    <div class="hero-content">
        <p class="hero-subtitle">Selamat Datang di</p>
        <h1 class="hero-title">Sistem Informasi Musholla SMK Negeri 1 Kraksaan</h1>
        <p class="hero-desc">Informasi kegiatan, jadwal, dan laporan musholla sekolah dalam satu sistem yang mudah diakses.</p>
    </div>
    <img src="<?= asset('img/musholla_logo.png') ?>" alt="Logo Musholla" class="hero-logo-large">
</section>

<!-- Baris Pertama: Total Infaq, Total Shodaqoh, Total Kegiatan, Total Kelas -->
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
            <a href="<?= url('infaq/index.php') ?>" class="stat-link">Lihat detail &rarr;</a>
        </div>
    </div>

    <!-- Card 2: Total Shodaqoh -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon blue"><i class="bi bi-coin"></i></div>
                    <h3 class="stat-title">Total Shodaqoh <span class="fw-normal">(Bulan ini)</span></h3>
                </div>
                <div class="stat-value">Rp 8.000.000</div>
            </div>
            <a href="<?= url('shodaqoh/index.php') ?>" class="stat-link">Lihat detail &rarr;</a>
        </div>
    </div>

    <!-- Card 3: Total Kegiatan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon orange"><i class="bi bi-card-checklist"></i></div>
                    <h3 class="stat-title">Total Kegiatan</h3>
                </div>
                <div class="stat-value">25</div>
            </div>
            <a href="<?= url('kegiatan/index.php') ?>" class="stat-link">Lihat detail &rarr;</a>
        </div>
    </div>

    <!-- Card 4: Total Kelas -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon purple"><i class="bi bi-backpack3"></i></div>
                    <h3 class="stat-title">Total Kelas</h3>
                </div>
                <div class="stat-value">39</div>
            </div>
            <a href="<?= url('kelas/index.php') ?>" class="stat-link">Lihat detail &rarr;</a>
        </div>
    </div>
</div>

<!-- Baris Kedua: Total Infaq & Shodaqoh, Total Pengeluaran -->
<div class="row g-3 mt-1">
    <!-- Card 5: Total Infaq & Shodaqoh -->
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon cyan"><i class="bi bi-wallet2"></i></div>
                    <h3 class="stat-title">Total Infaq dan Shodaqoh</h3>
                </div>
                <div class="stat-value">Rp 20.500.000</div>
            </div>
            <a href="<?= url('infaq/index.php') ?>" class="stat-link">Lihat detail &rarr;</a>
        </div>
    </div>

    <!-- Card 6: Total Pengeluaran -->
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon red"><i class="bi bi-cash-stack"></i></div>
                    <h3 class="stat-title">Total Pengeluaran</h3>
                </div>
                <div class="stat-value">Rp 5.250.000</div>
            </div>
            <a href="<?= url('laporan/index.php') ?>" class="stat-link">Lihat detail &rarr;</a>
        </div>
    </div>
</div>

<!-- Baris Bawah: Jadwal Hari Ini & Kegiatan Terbaru (satu baris) -->
<div class="row g-4 mt-1 mb-3">
    <!-- Kolom Kiri: Jadwal Hari Ini -->
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

    <!-- Kolom Kanan: Kegiatan Terbaru -->
    <div class="col-12 col-lg-6">
        <div class="section-header">
            <h4 class="section-title">Kegiatan Terbaru</h4>
            <a href="<?= url('kegiatan/index.php') ?>" class="section-link">Lihat Semua &rarr;</a>
        </div>
        <div class="info-card">
            <div class="kegiatan-list">
                <!-- Item 1 -->
                <div class="kegiatan-item">
                    <img src="<?= asset('img/img1.png') ?>" alt="Kajian Islam" class="kegiatan-img">
                    <div class="kegiatan-info">
                        <h6>Kajian Islam</h6>
                        <p><i class="bi bi-clock"></i> 20 Jul 2026</p>
                    </div>
                </div>
                <!-- Item 2 -->
                <div class="kegiatan-item">
                    <img src="<?= asset('img/img1.png') ?>" alt="Pesantren Ramadhan" class="kegiatan-img">
                    <div class="kegiatan-info">
                        <h6>Pesantren Ramadhan</h6>
                        <p><i class="bi bi-clock"></i> 10 Mar 2026</p>
                    </div>
                </div>
                <!-- Item 3 -->
                <div class="kegiatan-item">
                    <img src="<?= asset('img/img1.png') ?>" alt="Isra Mi'raj" class="kegiatan-img">
                    <div class="kegiatan-info">
                        <h6>Isra Mi'raj</h6>
                        <p><i class="bi bi-clock"></i> 27 Feb 2026</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/template/footer.php'; ?>
