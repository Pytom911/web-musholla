<?php
$pageTitle = 'Beranda - Sistem Informasi Musholla';
require_once __DIR__ . '/template/header.php';

$qTotalInfaq = mysqli_query($connect, "
    SELECT COALESCE(SUM(nominal),0) AS total
    FROM infaq
    WHERE MONTH(tanggal)=MONTH(CURDATE())
    AND YEAR(tanggal)=YEAR(CURDATE())
");

$dataTotalInfaq = mysqli_fetch_assoc($qTotalInfaq);
$totalInfaq = $dataTotalInfaq['total'];

$qTotalshodaqoh = mysqli_query($connect, "
    SELECT COALESCE(SUM(nominal),0) AS total
    FROM shodaqoh_jumat
    WHERE MONTH(tanggal)=MONTH(CURDATE())
    AND YEAR(tanggal)=YEAR(CURDATE())
");

$dataTotalshodaqoh = mysqli_fetch_assoc($qTotalshodaqoh);
$totalshodaqoh = $dataTotalshodaqoh['total'];

$qTotalKegiatan = mysqli_query($connect,"
    SELECT COUNT(*) AS total
    FROM kegiatan
");

$dataTotalKegiatan = mysqli_fetch_assoc($qTotalKegiatan);
$totalKegiatan = $dataTotalKegiatan['total'];

$qTotalPengeluaran = mysqli_query($connect,"
    SELECT COALESCE(SUM(pengeluaran),0) AS total
    FROM kegiatan
");

$dataTotalPengeluaran = mysqli_fetch_assoc($qTotalPengeluaran);
$totalPengeluaran = $dataTotalPengeluaran['total'];

// Saldo Keuangan (sementara Infaq - Pengeluaran)
$saldoKeuangan = $totalInfaq + $totalshodaqoh - $totalPengeluaran;

$qKegiatanTerbaru = mysqli_query($connect,"
    SELECT *
    FROM kegiatan
    ORDER BY tanggal DESC,id_kegiatan DESC
    LIMIT 2
");
?>

<!-- Hero Section (Ringkas) -->
<section class="hero-banner">
    <div class="hero-content">
        <p class="hero-subtitle">Selamat Datang di</p>
        <h1 class="hero-title">Sistem Informasi Musholla SMK Negeri 1 Kraksaan</h1>
        <p class="hero-desc">Informasi kegiatan, jadwal, dan laporan musholla sekolah dalam satu sistem yang mudah
            diakses.</p>
    </div>
    <img src="<?= asset('img/musholla_logo.png') ?>" alt="Logo Musholla" class="hero-logo-large">
</section>

<!-- Baris Pertama: Total Infaq, Total Shodaqoh, Total Kegiatan, Total Kelas -->
<div class="row g-3">
    <!-- Card 1: Total Infaq -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon green"><i class="bi bi-box2-heart"></i></div>
                    <h3 class="stat-title">Total Infaq <span class="fw-normal"></span></h3>
                </div>
                <div class="stat-value">
                    Rp<?= number_format($totalInfaq,0,',','.') ?>
                </div>
            </div>
            <a href="<?= url('infaq/index.php') ?>" class="stat-link">Lihat detail &rarr;</a>
        </div>
    </div>

    <!-- Card 2: Total Shodaqoh -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon blue"><i class="bi bi-coin"></i></div>
                    <h3 class="stat-title">Total Shodaqoh <span class="fw-normal"></span></h3>
                </div>
                <div class="stat-value">
                    Rp<?= number_format($totalshodaqoh,0,',','.') ?>
                </div>
            </div>
            <a href="<?= url('shodaqoh/index.php') ?>" class="stat-link">Lihat detail &rarr;</a>
        </div>
    </div>

    <!-- Card 4: Total Kelas
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon cyan"><i class="bi bi-wallet2"></i></div>
                    <h3 class="stat-title">Total Infaq dan Shodaqoh</h3>
                </div>
                <div class="stat-value">Rp -</div>
            </div>
        </div>
    </div> -->

    <!-- Card 3: Total Kegiatan -->
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon orange"><i class="bi bi-card-checklist"></i></div>
                    <h3 class="stat-title">Total Kegiatan</h3>
                </div>
                <div class="stat-value">
                    <?= $totalKegiatan ?>
                </div>
            </div>
            <a href="<?= url('kegiatan/index.php') ?>" class="stat-link">Lihat detail &rarr;</a>
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
                    <div class="stat-icon purple"><i class="bi bi-wallet2"></i></div>
                    <h3 class="stat-title">Saldo Keuangan</h3>
                </div>
                <div class="stat-value">
                    Rp<?= number_format($saldoKeuangan,0,',','.') ?>
                </div>
                <span class="jadwal-kelas">Total Dari Infaq Dan Shodaqoh</span>
            </div>
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
                <div class="stat-value">
                    Rp<?= number_format($totalPengeluaran,0,',','.') ?>
                </div>
            </div>
            <a href="<?= url('kegiatan/index.php') ?>" class="stat-link">Lihat detail &rarr;</a>
        </div>
    </div>
</div>

<!-- Baris Bawah: Jadwal Hari Ini & Kegiatan Terbaru (satu baris) -->
<div class="row g-4 mt-1 mb-4">
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

    <!-- Kolom Kanan: Kegiatan Terbaru (SUDAH DI-REDESAIN) -->
    <div class="col-12 col-lg-6">
        <div class="section-header">
            <h4 class="section-title">Kegiatan Terbaru</h4>
            <a href="<?= url('kegiatan/index.php') ?>" class="section-link">Lihat Semua &rarr;</a>
        </div>
        <div class="col-12">
            <div class="info-card">
                <?php if(mysqli_num_rows($qKegiatanTerbaru)>0): ?>
                    <?php while($kegiatan=mysqli_fetch_assoc($qKegiatanTerbaru)): ?>
                        <div class="kegiatan-item">
                            <div class="kegiatan-date-box-green">
                                <div class="clock-icon">
                                    <i class="bi bi-clock-fill"></i>
                                </div>
                                <span class="date-number">
                                    <?= date('d',strtotime($kegiatan['tanggal'])) ?>
                                </span>
                                <span class="date-month-year">
                                    <?= date('M',strtotime($kegiatan['tanggal'])) ?><br>
                                    <?= date('Y',strtotime($kegiatan['tanggal'])) ?>
                                </span>
                            </div>

                            <div class="kegiatan-info">
                                <h6><?= htmlspecialchars($kegiatan['nama_kegiatan']) ?></h6>
                            </div>

                            <div class="text-secondary">
                                <i class="bi bi-chevron-right"></i>
                            </div>
                        </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-calendar-event fs-1"></i>
                    <p class="mt-3 mb-0">Belum ada kegiatan.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</div>

<?php require_once __DIR__ . '/template/footer.php'; ?>