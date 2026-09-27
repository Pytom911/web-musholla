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

// Saldo Keuangan
$saldoKeuangan = $totalInfaq + $totalshodaqoh - $totalPengeluaran;

$tanggalHariIni = date('Y-m-d');

/* =========================
   JADWAL IMAM
========================= */

$jadwalImam = mysqli_query($connect, "
    SELECT 
        jadwal_imam.id_imam,
        jadwal_imam.tanggal,
        jadwal_imam.waktu_sholat,
        guru.nama_guru
    FROM jadwal_imam
    LEFT JOIN guru 
        ON jadwal_imam.id_guru = guru.id_guru
    WHERE jadwal_imam.tanggal >= CURDATE()
    ORDER BY jadwal_imam.tanggal ASC,
             jadwal_imam.id_imam ASC
    LIMIT 2
");


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
     JOIN kelas 
        ON jadwal_sholat.id_kelas = kelas.id_kelas
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
         JOIN kelas 
            ON jadwal_sholat.id_kelas = kelas.id_kelas
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
        'data' => $jadwalHariIni,
    ],
    [
        'judul' => 'Jadwal Berikutnya',
        'tanggal' => $tanggalBerikutnya,
        'data' => $jadwalBerikutnya,
    ],
];

?>

<!-- Hero Section -->
<section class="hero-banner">

    <div class="hero-content">

        <p class="hero-subtitle">
            Selamat Datang di
        </p>

        <h1 class="hero-title">
            Sistem Informasi Musholla SMK Negeri 1 Kraksaan
        </h1>

        <p class="hero-desc">
            mengelola seluruh informasi data guru agama dan data seluruh kelas
            <u>SMKN 1 Kraksaan</u>
        </p>

        <p class="hero-desc">
            Jadwal Sholat dan imam, Kegiatan Keagamaan, keuangan infaq dan shodaqoh,
            dan laporan musholla dalam satu sistem yang mudah di akses.
        </p>

    </div>

    <img
        src="<?= asset('img/musholla_logo.png') ?>"
        alt="Logo Musholla"
        class="hero-logo-large"
    >

</section>


<!-- =========================
     BARIS PERTAMA
========================= -->

<div class="row g-3">

    <!-- Total Infaq -->

    <div class="col-12 col-sm-6 col-xl-4">

        <div class="stat-card">

            <div>

                <div class="stat-header">

                    <div class="stat-icon green">
                        <i class="bi bi-box2-heart"></i>
                    </div>

                    <h3 class="stat-title">
                        Total Infaq
                    </h3>

                </div>

                <div class="stat-value">
                    Rp<?= number_format($totalInfaq, 0, ',', '.') ?>
                </div>

            </div>

            <a
                href="<?= url('infaq/index.php') ?>"
                class="stat-link"
            >
                Lihat detail &rarr;
            </a>

        </div>

    </div>


    <!-- Total Shodaqoh -->

    <div class="col-12 col-sm-6 col-xl-4">

        <div class="stat-card">

            <div>

                <div class="stat-header">

                    <div class="stat-icon blue">
                        <i class="bi bi-coin"></i>
                    </div>

                    <h3 class="stat-title">
                        Total Shodaqoh
                    </h3>

                </div>

                <div class="stat-value">
                    Rp<?= number_format($totalshodaqoh, 0, ',', '.') ?>
                </div>

            </div>

            <a
                href="<?= url('shodaqoh/index.php') ?>"
                class="stat-link"
            >
                Lihat detail &rarr;
            </a>

        </div>

    </div>


    <!-- Total Kegiatan -->

    <div class="col-12 col-sm-6 col-xl-4">

        <div class="stat-card">

            <div>

                <div class="stat-header">

                    <div class="stat-icon orange">
                        <i class="bi bi-card-checklist"></i>
                    </div>

                    <h3 class="stat-title">
                        Total Kegiatan
                    </h3>

                </div>

                <div class="stat-value">
                    <?= $totalKegiatan ?>
                </div>

            </div>

            <a
                href="<?= url('kegiatan/index.php') ?>"
                class="stat-link"
            >
                Lihat detail &rarr;
            </a>

        </div>

    </div>

</div>


<!-- =========================
     BARIS KEDUA
========================= -->

<div class="row g-3 mt-1">

    <!-- Saldo Keuangan -->

    <div class="col-12 col-sm-6 col-xl-6">

        <div class="stat-card">

            <div>

                <div class="stat-header">

                    <div class="stat-icon purple">
                        <i class="bi bi-wallet2"></i>
                    </div>

                    <h3 class="stat-title">
                        Saldo Keuangan
                    </h3>

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


    <!-- Total Pengeluaran -->

    <div class="col-12 col-sm-6 col-xl-6">

        <div class="stat-card">

            <div>

                <div class="stat-header">

                    <div class="stat-icon red">
                        <i class="bi bi-cash-stack"></i>
                    </div>

                    <h3 class="stat-title">
                        Total Pengeluaran
                    </h3>

                </div>

                <div class="stat-value">
                    Rp<?= number_format($totalPengeluaran, 0, ',', '.') ?>
                </div>

            </div>

            <a
                href="<?= url('kegiatan/index.php') ?>"
                class="stat-link"
            >
                Lihat detail &rarr;
            </a>

        </div>

    </div>

</div>


<!-- =========================
     JADWAL SHOLAT & JADWAL IMAM
========================= -->

<div class="row g-4 mt-1 mb-4">


    <!-- =====================
         JADWAL SHOLAT
    ====================== -->

    <div class="col-12 col-lg-6">

        <div class="section-header">

            <h4 class="section-title">
                Jadwal Sholat
            </h4>

        </div>


        <div class="row g-3">

            <?php foreach ($jadwalDashboard as $panel): ?>

                <div class="col-12 col-sm-6">

                    <div class="info-card">

                        <div class="jadwal-name">

                            <?= htmlspecialchars($panel['judul']) ?>

                        </div>


                        <?php if ($panel['tanggal'] !== null): ?>

                            <div class="jadwal-kelas mb-2">

                                <?= htmlspecialchars(
                                    hari_indonesia($panel['tanggal'])
                                ) ?>,

                                <?= htmlspecialchars(
                                    date(
                                        'd/m/Y',
                                        strtotime($panel['tanggal'])
                                    )
                                ) ?>

                            </div>

                        <?php endif; ?>


                        <?php if (
                            $panel['tanggal'] !== null &&
                            $panel['data'] !== null &&
                            mysqli_num_rows($panel['data']) > 0
                        ): ?>

                            <div class="jadwal-name">
                                Ashar
                            </div>

                            <div class="jadwal-time">

                                <?= htmlspecialchars($waktuAshar) ?>

                            </div>


                            <?php while (
                                $barisJadwal =
                                mysqli_fetch_assoc($panel['data'])
                            ): ?>

                                <p class="border-kelas">

                                    Kelas:

                                    <?= htmlspecialchars(
                                        $barisJadwal['tingkat']
                                    ) ?>

                                    <?= htmlspecialchars(
                                        $barisJadwal['nama_kelas']
                                    ) ?>

                                    <?= htmlspecialchars(
                                        $barisJadwal['bagian']
                                    ) ?>

                                </p>

                            <?php endwhile; ?>


                        <?php else: ?>

                            <div class="jadwal-name">

                                <?= $panel['judul'] === 'Jadwal Hari Ini'
                                    ? 'Belum ada jadwal hari ini'
                                    : 'Belum ada jadwal terjadwal berikutnya' ?>

                            </div>

                        <?php endif; ?>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>

    </div>


    <!-- =====================
         JADWAL IMAM
    ====================== -->

    <div class="col-12 col-lg-6">

        <div class="section-header">

            <h4 class="section-title">
                Jadwal Imam
            </h4>

            <a
                href="<?= url('jadwal_imam/index.php') ?>"
                class="section-link"
            >
                Lihat Semua &rarr;
            </a>

        </div>


        <div class="col-12">

            <div class="info-card">


                <?php if (
                    $jadwalImam &&
                    mysqli_num_rows($jadwalImam) > 0
                ): ?>


                    <?php
                    $bulan = [
                        1 => 'Jan',
                        2 => 'Feb',
                        3 => 'Mar',
                        4 => 'Apr',
                        5 => 'Mei',
                        6 => 'Jun',
                        7 => 'Jul',
                        8 => 'Agu',
                        9 => 'Sep',
                        10 => 'Okt',
                        11 => 'Nov',
                        12 => 'Des'
                    ];
                    ?>


                    <?php while (
                        $imam = mysqli_fetch_assoc($jadwalImam)
                    ): ?>

                        <?php
                        $tanggalImam = strtotime(
                            $imam['tanggal']
                        );
                        ?>


                        <div class="kegiatan-item">


                            <!-- TANGGAL -->

                            <div class="kegiatan-date-box-green">

                                <div class="clock-icon">

                                    <i class="bi bi-calendar-check-fill"></i>

                                </div>


                                <span class="date-number">

                                    <?= date(
                                        'd',
                                        $tanggalImam
                                    ) ?>

                                </span>


                                <span class="date-month-year">

                                    <?= $bulan[
                                        (int)date(
                                            'm',
                                            $tanggalImam
                                        )
                                    ] ?>

                                    <br>

                                    <?= date(
                                        'Y',
                                        $tanggalImam
                                    ) ?>

                                </span>

                            </div>


                            <!-- DATA IMAM -->

                            <div class="kegiatan-info">

                                <h6>

                                    <?= htmlspecialchars(
                                        $imam['nama_guru']
                                        ?? 'Nama imam tidak ditemukan'
                                    ) ?>

                                </h6>


                                <p class="mb-2">

                                    <?= htmlspecialchars(
                                        $imam['waktu_sholat']
                                    ) ?>

                                </p>


                                <span class="imam-badge">

                                    Imam Sholat

                                </span>

                            </div>


                            <!-- ARROW -->

                            <div class="text-secondary">

                                <i class="bi bi-chevron-right"></i>

                            </div>


                        </div>


                    <?php endwhile; ?>


                <?php else: ?>


                    <div class="text-center py-5 text-muted">

                        <i class="bi bi-calendar-event fs-1"></i>

                        <p class="mt-3 mb-0">

                            Belum ada jadwal imam.

                        </p>

                    </div>


                <?php endif; ?>


            </div>

        </div>

    </div>

</div>


<?php require_once __DIR__ . '/template/footer.php'; ?>