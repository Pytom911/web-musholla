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
    FROM shodaqoh
    WHERE MONTH(tanggal)=MONTH(CURDATE())
    AND YEAR(tanggal)=YEAR(CURDATE())
");
$dataTotalshodaqoh = mysqli_fetch_assoc($qTotalshodaqoh);
$totalshodaqoh = $dataTotalshodaqoh['total'];

$qTotalKegiatan = mysqli_query($connect, "
    SELECT COUNT(*) AS total
    FROM kegiatan
");
$dataTotalKegiatan = mysqli_fetch_assoc($qTotalKegiatan);
$totalKegiatan = $dataTotalKegiatan['total'];

$qTotalPengeluaran = mysqli_query($connect, "
    SELECT COALESCE(SUM(pengeluaran),0) AS total
    FROM kegiatan
");
$dataTotalPengeluaran = mysqli_fetch_assoc($qTotalPengeluaran);
$totalPengeluaran = $dataTotalPengeluaran['total'];

$saldoKeuangan = $totalInfaq + $totalshodaqoh;
$tanggalHariIni = date('Y-m-d');

/* =========================
   JADWAL IMAM HARI INI
========================= */

$stmtJadwalImamHariIni = $connect->prepare(
    "SELECT
        jadwal_imam.id_imam,
        jadwal_imam.tanggal,
        jadwal_imam.waktu_sholat,
        guru.nama_guru
     FROM jadwal_imam
     JOIN guru ON jadwal_imam.id_guru = guru.id_guru
     WHERE jadwal_imam.tanggal = ?
     ORDER BY FIELD(jadwal_imam.waktu_sholat, 'Dzuhur', 'Ashar'),
              jadwal_imam.id_imam ASC"
);
$stmtJadwalImamHariIni->bind_param('s', $tanggalHariIni);
$stmtJadwalImamHariIni->execute();
$jadwalImamHariIni = $stmtJadwalImamHariIni->get_result();

/* =========================
   JADWAL IMAM BERIKUTNYA
========================= */

$stmtTanggalImamBerikutnya = $connect->prepare(
    "SELECT MIN(tanggal) AS tanggal
     FROM jadwal_imam
     WHERE tanggal > ?"
);
$stmtTanggalImamBerikutnya->bind_param('s', $tanggalHariIni);
$stmtTanggalImamBerikutnya->execute();
$resultTanggalImamBerikutnya = $stmtTanggalImamBerikutnya->get_result();
$rowTanggalImamBerikutnya = $resultTanggalImamBerikutnya
    ? $resultTanggalImamBerikutnya->fetch_assoc()
    : null;
$tanggalImamBerikutnya = $rowTanggalImamBerikutnya['tanggal'] ?? null;
$jadwalImamBerikutnya = null;

if ($tanggalImamBerikutnya !== null) {
    $stmtJadwalImamBerikutnya = $connect->prepare(
        "SELECT
            jadwal_imam.id_imam,
            jadwal_imam.tanggal,
            jadwal_imam.waktu_sholat,
            guru.nama_guru
         FROM jadwal_imam
         JOIN guru ON jadwal_imam.id_guru = guru.id_guru
         WHERE jadwal_imam.tanggal = ?
         ORDER BY FIELD(jadwal_imam.waktu_sholat, 'Dzuhur', 'Ashar'),
                  jadwal_imam.id_imam ASC"
    );
    $stmtJadwalImamBerikutnya->bind_param('s', $tanggalImamBerikutnya);
    $stmtJadwalImamBerikutnya->execute();
    $jadwalImamBerikutnya = $stmtJadwalImamBerikutnya->get_result();
}

$jadwalImamDashboard = [
    [
        'judul' => 'Jadwal Hari Ini',
        'tanggal' => $tanggalHariIni,
        'data' => $jadwalImamHariIni
    ],
    [
        'judul' => 'Jadwal Berikutnya',
        'tanggal' => $tanggalImamBerikutnya,
        'data' => $jadwalImamBerikutnya
    ]
];

/* =========================
   JADWAL SHOLAT HARI INI
========================= */

$stmtJadwalHariIni = $connect->prepare(
    "SELECT
        jadwal_sholat.id_jadwal,
        jadwal_sholat.tanggal,
        kelas.nama_kelas,
        kelas.tingkat,
        kelas.bagian
     FROM jadwal_sholat
     JOIN kelas ON jadwal_sholat.id_kelas = kelas.id_kelas
     WHERE jadwal_sholat.tanggal = ?
       AND jadwal_sholat.waktu_sholat = 'Ashar'
     ORDER BY FIELD(kelas.tingkat, 'X', 'XI', 'XII'),
              kelas.nama_kelas ASC,
              kelas.id_kelas ASC"
);
$stmtJadwalHariIni->bind_param('s', $tanggalHariIni);
$stmtJadwalHariIni->execute();
$jadwalHariIni = $stmtJadwalHariIni->get_result();

/* =========================
   JADWAL SHOLAT BERIKUTNYA
========================= */

$stmtTanggalBerikutnya = $connect->prepare(
    "SELECT MIN(tanggal) AS tanggal
     FROM jadwal_sholat
     WHERE tanggal > ?
       AND waktu_sholat = 'Ashar'"
);
$stmtTanggalBerikutnya->bind_param('s', $tanggalHariIni);
$stmtTanggalBerikutnya->execute();
$resultTanggalBerikutnya = $stmtTanggalBerikutnya->get_result();
$rowTanggalBerikutnya = $resultTanggalBerikutnya
    ? $resultTanggalBerikutnya->fetch_assoc()
    : null;
$tanggalBerikutnya = $rowTanggalBerikutnya['tanggal'] ?? null;
$jadwalBerikutnya = null;

if ($tanggalBerikutnya !== null) {
    $stmtJadwalBerikutnya = $connect->prepare(
        "SELECT
            jadwal_sholat.id_jadwal,
            jadwal_sholat.tanggal,
            kelas.nama_kelas,
            kelas.tingkat,
            kelas.bagian
         FROM jadwal_sholat
         JOIN kelas ON jadwal_sholat.id_kelas = kelas.id_kelas
         WHERE jadwal_sholat.tanggal = ?
           AND jadwal_sholat.waktu_sholat = 'Ashar'
         ORDER BY FIELD(kelas.tingkat, 'X', 'XI', 'XII'),
                  kelas.nama_kelas ASC,
                  kelas.id_kelas ASC"
    );
    $stmtJadwalBerikutnya->bind_param('s', $tanggalBerikutnya);
    $stmtJadwalBerikutnya->execute();
    $jadwalBerikutnya = $stmtJadwalBerikutnya->get_result();
}

$waktuAshar = '14:50 - 15:10';

$jadwalDashboard = [
    [
        'judul' => 'Jadwal Hari Ini',
        'tanggal' => $tanggalHariIni,
        'data' => $jadwalHariIni
    ],
    [
        'judul' => 'Jadwal Berikutnya',
        'tanggal' => $tanggalBerikutnya,
        'data' => $jadwalBerikutnya
    ]
];
?>

<!-- Hero Section -->
<section class="hero-banner">
    <div class="hero-content">
        <p class="hero-subtitle">Selamat Datang di</p>
        <h1 class="hero-title">Sistem Informasi Musholla SMK Negeri 1 Kraksaan</h1>
        <p class="hero-desc">
            mengelola seluruh informasi data guru agama dan data seluruh kelas
            <u>SMKN 1 Kraksaan</u>
        </p>
        <p class="hero-desc">
            Jadwal Sholat dan imam, Kegiatan Keagamaan, keuangan infaq dan shodaqoh,
            dan laporan musholla dalam satu sistem yang mudah di akses.
        </p>
    </div>
    <img src="<?= asset('img/musholla_logo.png') ?>" alt="Logo Musholla" class="hero-logo-large">
</section>

<!-- Total -->
<div class="row g-3">
    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon green">
                        <i class="bi bi-box2-heart"></i>
                    </div>
                    <h3 class="stat-title">Total Infaq</h3>
                </div>
                <div class="stat-value">
                    Rp<?= number_format($totalInfaq, 0, ',', '.') ?>
                </div>
            </div>
            <a href="<?= url('infaq/index.php') ?>" class="stat-link">
                Lihat detail &rarr;
            </a>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon blue">
                        <i class="bi bi-coin"></i>
                    </div>
                    <h3 class="stat-title">Total Shodaqoh</h3>
                </div>
                <div class="stat-value">
                    Rp<?= number_format($totalshodaqoh, 0, ',', '.') ?>
                </div>
            </div>
            <a href="<?= url('shodaqoh/index.php') ?>" class="stat-link">
                Lihat detail &rarr;
            </a>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-4">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon orange">
                        <i class="bi bi-card-checklist"></i>
                    </div>
                    <h3 class="stat-title">Total Kegiatan</h3>
                </div>
                <div class="stat-value">
                    <?= $totalKegiatan ?>
                </div>
            </div>
            <a href="<?= url('kegiatan/index.php') ?>" class="stat-link">
                Lihat detail &rarr;
            </a>
        </div>
    </div>
</div>

<!-- Keuangan -->
<div class="row g-3 mt-1">
    <div class="col-12 col-sm-6 col-xl-6">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon purple">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <h3 class="stat-title">Saldo Keuangan</h3>
                </div>
                <div class="stat-value">
                    Rp<?= number_format($saldoKeuangan, 0, ',', '.') ?>
                </div>
                <span class="jadwal-kelas">
                    Total Dari Infaq Dan Shodaqoh
                </span>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-6">
        <div class="stat-card">
            <div>
                <div class="stat-header">
                    <div class="stat-icon red">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <h3 class="stat-title">Total Pengeluaran</h3>
                </div>
                <div class="stat-value">
                    Rp<?= number_format($totalPengeluaran, 0, ',', '.') ?>
                </div>
            </div>
            <a href="<?= url('kegiatan/index.php') ?>" class="stat-link">
                Lihat detail &rarr;
            </a>
        </div>
    </div>
</div>

<!-- =========================================================
     JADWAL SHOLAT & IMAM
========================================================= -->

<div class="schedule-section">

    <!-- Header -->
    <div class="section-header">
        <h4 class="section-title">
            Jadwal Sholat & Imam
        </h4>

        <a href="<?= url('jadwal_imam/index.php') ?>" class="section-link">
            Lihat Semua →
        </a>
    </div>


    <!-- 4 CARD DALAM SATU GRID -->
    <div class="schedule-grid">


        <!-- =====================================================
             1. JADWAL SHOLAT HARI INI
        ====================================================== -->
        <div class="schedule-card">

            <div class="schedule-card-header">

                <div class="schedule-icon green">
                    <i class="bi bi-clock"></i>
                </div>

                <div class="schedule-card-heading">
                    <span>Jadwal Sholat</span>
                    <h5>Hari Ini</h5>
                </div>

            </div>


            <?php if ($tanggalHariIni !== null): ?>

                <div class="schedule-date">
                    <i class="bi bi-calendar3"></i>

                    <?= htmlspecialchars(hari_indonesia($tanggalHariIni)) ?>,
                    <?= htmlspecialchars(date('d/m/Y', strtotime($tanggalHariIni))) ?>

                </div>

            <?php endif; ?>


            <?php if (
                $tanggalHariIni !== null &&
                $jadwalHariIni !== null &&
                mysqli_num_rows($jadwalHariIni) > 0
            ): ?>

                <div class="schedule-time-box">

                    <span class="schedule-label">
                        Waktu Sholat Ashar
                    </span>

                    <strong class="schedule-time">
                        <?= htmlspecialchars($waktuAshar) ?>
                    </strong>

                </div>


                <div class="schedule-divider"></div>


                <div class="schedule-list">

                    <?php while ($barisJadwal = mysqli_fetch_assoc($jadwalHariIni)): ?>

                        <div class="schedule-item">

                            <div class="schedule-item-icon green">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>

                            <div class="schedule-item-content">

                                <span>Kelas</span>

                                <strong>
                                    <?= htmlspecialchars($barisJadwal['tingkat']) ?>
                                    <?= htmlspecialchars($barisJadwal['nama_kelas']) ?>
                                    <?= htmlspecialchars($barisJadwal['bagian']) ?>
                                </strong>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>


            <?php else: ?>

                <div class="schedule-empty">

                    <div class="schedule-empty-icon">
                        <i class="bi bi-calendar-x"></i>
                    </div>

                    <strong>
                        Belum ada jadwal
                    </strong>

                    <span>
                        Belum ada jadwal sholat untuk hari ini.
                    </span>

                </div>

            <?php endif; ?>

        </div>



        <!-- =====================================================
             2. JADWAL SHOLAT BERIKUTNYA
        ====================================================== -->
        <div class="schedule-card">

            <div class="schedule-card-header">

                <div class="schedule-icon green">
                    <i class="bi bi-calendar-event"></i>
                </div>

                <div class="schedule-card-heading">
                    <span>Jadwal Sholat</span>
                    <h5>Jadwal Berikutnya</h5>
                </div>

            </div>


            <?php if ($tanggalBerikutnya !== null): ?>

                <div class="schedule-date">
                    <i class="bi bi-calendar3"></i>

                    <?= htmlspecialchars(hari_indonesia($tanggalBerikutnya)) ?>,
                    <?= htmlspecialchars(date('d/m/Y', strtotime($tanggalBerikutnya))) ?>

                </div>

            <?php endif; ?>


            <?php if (
                $tanggalBerikutnya !== null &&
                $jadwalBerikutnya !== null &&
                mysqli_num_rows($jadwalBerikutnya) > 0
            ): ?>

                <div class="schedule-time-box">

                    <span class="schedule-label">
                        Waktu Sholat Ashar
                    </span>

                    <strong class="schedule-time">
                        <?= htmlspecialchars($waktuAshar) ?>
                    </strong>

                </div>


                <div class="schedule-divider"></div>


                <div class="schedule-list">

                    <?php while ($barisJadwal = mysqli_fetch_assoc($jadwalBerikutnya)): ?>

                        <div class="schedule-item">

                            <div class="schedule-item-icon green">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>

                            <div class="schedule-item-content">

                                <span>Kelas</span>

                                <strong>
                                    <?= htmlspecialchars($barisJadwal['tingkat']) ?>
                                    <?= htmlspecialchars($barisJadwal['nama_kelas']) ?>
                                    <?= htmlspecialchars($barisJadwal['bagian']) ?>
                                </strong>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>


            <?php else: ?>

                <div class="schedule-empty">

                    <div class="schedule-empty-icon">
                        <i class="bi bi-calendar-x"></i>
                    </div>

                    <strong>
                        Belum ada jadwal
                    </strong>

                    <span>
                        Belum ada jadwal sholat berikutnya.
                    </span>

                </div>

            <?php endif; ?>

        </div>



        <!-- =====================================================
             3. JADWAL IMAM HARI INI
        ====================================================== -->
        <div class="schedule-card">

            <div class="schedule-card-header">

                <div class="schedule-icon blue">
                    <i class="bi bi-person-badge"></i>
                </div>

                <div class="schedule-card-heading">
                    <span>Jadwal Imam</span>
                    <h5>Hari Ini</h5>
                </div>

            </div>


            <?php if ($tanggalHariIni !== null): ?>

                <div class="schedule-date">
                    <i class="bi bi-calendar3"></i>

                    <?= htmlspecialchars(hari_indonesia($tanggalHariIni)) ?>,
                    <?= htmlspecialchars(date('d/m/Y', strtotime($tanggalHariIni))) ?>

                </div>

            <?php endif; ?>


            <?php if (
                $tanggalHariIni !== null &&
                $jadwalImamHariIni !== null &&
                mysqli_num_rows($jadwalImamHariIni) > 0
            ): ?>

                <div class="schedule-list">

                    <?php while ($barisImam = mysqli_fetch_assoc($jadwalImamHariIni)): ?>

                        <div class="schedule-item imam">

                            <div class="schedule-item-icon blue">
                                <i class="bi bi-person-fill"></i>
                            </div>

                            <div class="schedule-item-content">

                                <span>
                                    <?= htmlspecialchars($barisImam['waktu_sholat']) ?>
                                </span>

                                <strong>
                                    <?= htmlspecialchars($barisImam['nama_guru']) ?>
                                </strong>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>


            <?php else: ?>

                <div class="schedule-empty">

                    <div class="schedule-empty-icon">
                        <i class="bi bi-person-x"></i>
                    </div>

                    <strong>
                        Belum ada jadwal imam
                    </strong>

                    <span>
                        Belum ada jadwal imam untuk hari ini.
                    </span>

                </div>

            <?php endif; ?>

        </div>



        <!-- =====================================================
             4. JADWAL IMAM BERIKUTNYA
        ====================================================== -->
        <div class="schedule-card">

            <div class="schedule-card-header">

                <div class="schedule-icon blue">
                    <i class="bi bi-person-lines-fill"></i>
                </div>

                <div class="schedule-card-heading">
                    <span>Jadwal Imam</span>
                    <h5>Jadwal Berikutnya</h5>
                </div>

            </div>


            <?php if ($tanggalImamBerikutnya !== null): ?>

                <div class="schedule-date">
                    <i class="bi bi-calendar3"></i>

                    <?= htmlspecialchars(hari_indonesia($tanggalImamBerikutnya)) ?>,
                    <?= htmlspecialchars(date('d/m/Y', strtotime($tanggalImamBerikutnya))) ?>

                </div>

            <?php endif; ?>


            <?php if (
                $tanggalImamBerikutnya !== null &&
                $jadwalImamBerikutnya !== null &&
                mysqli_num_rows($jadwalImamBerikutnya) > 0
            ): ?>

                <div class="schedule-list">

                    <?php while ($barisImam = mysqli_fetch_assoc($jadwalImamBerikutnya)): ?>

                        <div class="schedule-item imam">

                            <div class="schedule-item-icon blue">
                                <i class="bi bi-person-fill"></i>
                            </div>

                            <div class="schedule-item-content">

                                <span>
                                    <?= htmlspecialchars($barisImam['waktu_sholat']) ?>
                                </span>

                                <strong>
                                    <?= htmlspecialchars($barisImam['nama_guru']) ?>
                                </strong>

                            </div>

                        </div>

                    <?php endwhile; ?>

                </div>


            <?php else: ?>

                <div class="schedule-empty">

                    <div class="schedule-empty-icon">
                        <i class="bi bi-person-x"></i>
                    </div>

                    <strong>
                        Belum ada jadwal imam
                    </strong>

                    <span>
                        Belum ada jadwal imam berikutnya.
                    </span>

                </div>

            <?php endif; ?>

        </div>


    </div>

</div>

<?php require_once __DIR__ . '/template/footer.php'; ?>