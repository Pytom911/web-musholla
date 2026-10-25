<?php
require_once "../auth/auth.php";
requireRole(['admin','petugas']);

$pageTitle = 'Laporan Imam';
require_once '../template/header.php';

$bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$tahunSekarang = (int) date('Y');

/* =========================
   PARAMETER FILTER
========================= */

// Filter bulan (0 = semua bulan)
$bulanDipilih = isset($_GET['bulan']) ? (int) $_GET['bulan'] : 0;

if ($bulanDipilih < 0 || $bulanDipilih > 12) {
    $bulanDipilih = 0;
}

/* =========================
   SUSUN WHERE (prepared)
========================= */

$conditions = [];
$jenisBind = '';
$nilaiBind = [];

if ($bulanDipilih >= 1 && $bulanDipilih <= 12) {
    $conditions[] = "jadwal_imam.tanggal IS NOT NULL
        AND MONTH(jadwal_imam.tanggal) = ?
        AND YEAR(jadwal_imam.tanggal) = ?";
    $jenisBind .= 'ii';
    $nilaiBind[] = $bulanDipilih;
    $nilaiBind[] = $tahunSekarang;
}

$whereSql = $conditions !== []
    ? 'WHERE ' . implode(' AND ', $conditions)
    : '';

/* =========================
   STATISTIK
========================= */

// Jumlah imam (sesuai filter)
$stmtTotalImam = $connect->prepare(
    "SELECT COUNT(DISTINCT id_guru) AS total FROM jadwal_imam $whereSql"
);

if ($jenisBind !== '') {
    $stmtTotalImam->bind_param($jenisBind, ...$nilaiBind);
}

$stmtTotalImam->execute();
$totalImam = $stmtTotalImam->get_result()->fetch_assoc();

// Total jadwal (sesuai filter)
$stmtTotalJadwal = $connect->prepare(
    "SELECT COUNT(*) AS total FROM jadwal_imam $whereSql"
);

if ($jenisBind !== '') {
    $stmtTotalJadwal->bind_param($jenisBind, ...$nilaiBind);
}

$stmtTotalJadwal->execute();
$totalJadwal = $stmtTotalJadwal->get_result()->fetch_assoc();

// Total jadwal hari ini (tetap berarti hari ini, tidak ikut filter bulan)
$totalJadwalHariIni = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(*) AS total
    FROM jadwal_imam
    WHERE tanggal = CURDATE()
"));

/* =========================
   DATA SESUAI FILTER
========================== */

$limit = 10;
$page = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
if ($page < 1) {
    $page = 1;
}

$totalDataAll = (int) ($totalJadwal['total'] ?? 0);
$totalHalaman = (int) ceil($totalDataAll / $limit);
if ($totalHalaman < 1) {
    $totalHalaman = 1;
}
if ($page > $totalHalaman) {
    $page = $totalHalaman;
}

$offset = ($page - 1) * $limit;

// Saat bulan tertentu dipilih, urutkan per tanggal (ASC).
// Saat "Semua Bulan", urutkan terbaru dahulu seperti biasa.
$urutanSql = ($bulanDipilih >= 1 && $bulanDipilih <= 12)
    ? "jadwal_imam.tanggal ASC, jadwal_imam.id_imam ASC"
    : "jadwal_imam.tanggal DESC, jadwal_imam.id_imam DESC";

$stmtData = $connect->prepare(
    "SELECT
        jadwal_imam.id_imam,
        jadwal_imam.tanggal,
        jadwal_imam.waktu_sholat,
        jadwal_imam.id_guru,
        guru.nama_guru
    FROM jadwal_imam
    LEFT JOIN guru
        ON jadwal_imam.id_guru = guru.id_guru
    $whereSql
    ORDER BY $urutanSql
    LIMIT ? OFFSET ?"
);

$jenisBindData = $jenisBind . 'ii';
$nilaiBindData = array_merge($nilaiBind, [$limit, $offset]);
$stmtData->bind_param($jenisBindData, ...$nilaiBindData);

$stmtData->execute();
$data = $stmtData->get_result();

/* =========================
   TAUTAN EXPORT (filter ikut)
========================= */

$tautanExport = url('laporan/export/export-Imam.php')
    . ($bulanDipilih >= 1 && $bulanDipilih <= 12
        ? '?' . http_build_query(['bulan' => $bulanDipilih])
        : '');
?>

<link rel="stylesheet" href="../assets/css/laporan.css">
<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <div class="laporan-header">
        <div>
            <h3>Laporan Imam</h3>
            <p>seluruh data jadwal imam musholla.</p>
        </div>

        <a href="<?= $tautanExport ?>" target="_blank" class="btn-export">
            <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
        </a>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="fas fa-users"></i>
                </div>

                <div class="stats-info">
                    <small>Jumlah Imam</small>
                    <h2><?= $totalImam['total'] ?? 0; ?></h2>
                    <span>Orang</span>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-calendar-check"></i>
                </div>

                <div class="stats-info">
                    <small>Total Jadwal</small>
                    <h2><?= $totalJadwal['total'] ?? 0; ?></h2>
                    <span><?= $whereSql !== '' ? 'Data Jadwal (Sesuai Filter)' : 'Data Jadwal' ?></span>
                </div>
            </div>
        </div>

    </div>

    <div class="table-card">

        <div class="table-header">
            <div class="table-tools">

                <div class="search-box">
                    <i class="fas fa-search"></i>

                    <input
                        type="text"
                        id="searchInput"
                        class="form-control"
                        placeholder="Cari nama imam..."
                    >
                </div>

                <form method="GET" class="filter-box">
                    <select name="bulan" id="bulan" onchange="this.form.submit()">
                        <option value="0" <?= $bulanDipilih == 0 ? 'selected' : ''; ?>>Semua Bulan</option>
                        <?php foreach ($bulan as $nomorBulan => $labelBulan): ?>
                            <option value="<?= $nomorBulan ?>" <?= $bulanDipilih === $nomorBulan ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($labelBulan, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>

            </div>
        </div>

        <div class="table-responsive">

            <table class="table-modern" id="dataTable">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Imam</th>
                        <th>Waktu Sholat</th>
                        <th>Tanggal</th>
                        
                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($data) > 0): ?>

                        <?php $no = $offset + 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($data)): ?>

                            <tr>

                                <td>
                                    <?= $no++; ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars(
                                            $row['nama_guru'] ?? 'Nama guru tidak ditemukan'
                                        ); ?>
                                    </strong>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row['waktu_sholat']); ?>
                                    </strong>
                                </td>

                                <td>

                                    <?php
                                    $tanggal = strtotime($row['tanggal']);

                                    echo  $bulan[(int)date('m', $tanggal)] . ' ' .
                                        date('d', $tanggal) . ' ' .
                                        date('Y', $tanggal);
                                    ?>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="4">

                                <div class="empty-report">

                                    <i class="bi bi-folder2-open"></i>

                                    <h5>Belum Ada Data Imam</h5>

                                    <p>
                                        Belum terdapat data jadwal imam
                                        yang tersimpan dalam sistem.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <div class="table-footer">

            <div class="table-info">
                Menampilkan <strong><?= mysqli_num_rows($data); ?></strong> data dari <strong><?= $totalDataAll; ?></strong> data
                <strong>
                    <?= $totalDataAll; ?>
                </strong>
            </div>

            <?php require __DIR__ . '/../template/pagination.php'; ?>

        </div>

    </div>

</div>

<script src="../assets/js/script.js"></script>

<?php require_once '../template/footer.php'; ?>