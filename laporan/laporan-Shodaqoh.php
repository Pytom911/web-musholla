<?php
$pageTitle = 'Laporan Shodaqoh';
require_once '../template/header.php';

$bulanDipilih = isset($_GET['bulan']) ? (int) $_GET['bulan'] : 0;

$namaBulan = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December'
];

$whereBulan = '';

if ($bulanDipilih >= 1 && $bulanDipilih <= 12) {
    $whereBulan = "WHERE MONTH(tanggal) = $bulanDipilih AND YEAR(tanggal) = YEAR(CURDATE())";
}

$totalShodaqoh = mysqli_fetch_assoc(mysqli_query(
    $connect,
    "SELECT COALESCE(SUM(nominal), 0) AS total FROM shodaqoh $whereBulan"
));

$totalKelas = mysqli_fetch_assoc(mysqli_query(
    $connect,
    "SELECT COUNT(DISTINCT id_kelas) AS total FROM shodaqoh $whereBulan"
));

$totalTransaksi = mysqli_fetch_assoc(mysqli_query(
    $connect,
    "SELECT COUNT(*) AS total FROM shodaqoh $whereBulan"
));

$data = mysqli_query(
    $connect,
    "SELECT 
        shodaqoh.*, 
        kelas.nama_kelas, 
        kelas.tingkat, 
        kelas.bagian 
    FROM shodaqoh 
    JOIN kelas ON shodaqoh.id_kelas = kelas.id_kelas 
    $whereBulan 
    ORDER BY shodaqoh.tanggal DESC, shodaqoh.id_shodaqoh DESC"
);
?>

<link rel="stylesheet" href="../assets/css/laporan.css">
<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">
    <div class="laporan-header">
        <div>
            <h3>Laporan Shodaqoh Jumat</h3>
            <p>
                <?php if ($bulanDipilih == 0): ?>
                    Seluruh data shodaqoh Jumat dari setiap kelas.
                <?php else: ?>
                    Data shodaqoh Jumat bulan <?= $namaBulan[$bulanDipilih]; ?>.
                <?php endif; ?>
            </p>
        </div>

        <a href="export/export-Shodaqoh.php<?= $bulanDipilih > 0 ? '?bulan=' . $bulanDipilih : ''; ?>" target="_blank" class="btn-export">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            Export PDF
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="bi bi-coin"></i>
                </div>
                <div class="stats-info">
                    <small>
                        <?php if ($bulanDipilih == 0): ?>
                            Total Shodaqoh
                        <?php else: ?>
                            Total Shodaqoh <?= $namaBulan[$bulanDipilih]; ?>
                        <?php endif; ?>
                    </small>
                    <h2>
                        Rp<?= number_format($totalShodaqoh['total'] ?? 0, 0, ',', '.'); ?>
                    </h2>
                    <span>
                        <?php if ($bulanDipilih == 0): ?>
                            Keseluruhan
                        <?php else: ?>
                            <?= $namaBulan[$bulanDipilih]; ?> <?= date('Y'); ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>
                <div class="stats-info">
                    <small>Jumlah Kelas</small>
                    <h2>
                        <?= $totalKelas['total'] ?? 0; ?>
                    </h2>
                    <span>
                        <?php if ($bulanDipilih == 0): ?>
                            Kelas
                        <?php else: ?>
                            Kelas <?= $namaBulan[$bulanDipilih]; ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-blue">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stats-info">
                    <small>Total Transaksi</small>
                    <h2>
                        <?= $totalTransaksi['total'] ?? 0; ?>
                    </h2>
                    <span>
                        <?php if ($bulanDipilih == 0): ?>
                            Data Shodaqoh
                        <?php else: ?>
                            Data <?= $namaBulan[$bulanDipilih]; ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <div class="table-tools">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama kelas...">
                </div>

                <form method="GET" class="filter-box">
                    <select name="bulan" id="bulan" onchange="this.form.submit()">
                        <option value="0" <?= $bulanDipilih == 0 ? 'selected' : ''; ?>>
                            Semua Bulan
                        </option>
                        <?php foreach ($namaBulan as $nomor => $nama): ?>
                            <option value="<?= $nomor; ?>" <?= $bulanDipilih == $nomor ? 'selected' : ''; ?>>
                                <?= $nama; ?>
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
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Bagian</th>
                        <th>Nominal</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td>
                                    <?= $no++; ?>
                                </td>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row['nama_kelas']); ?>
                                    </strong>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['tingkat']); ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['bagian']); ?>
                                </td>
                                <td>
                                    <span class="nominal">
                                        Rp <?= number_format($row['nominal'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('F d Y', strtotime($row['tanggal'])); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6">
                                <div class="empty-report">
                                    <i class="bi bi-folder2-open"></i>
                                    <h5>Belum Ada Data Shodaqoh</h5>
                                    <p>
                                        <?php if ($bulanDipilih == 0): ?>
                                            Belum terdapat data shodaqoh Jumat yang tersimpan dalam sistem.
                                        <?php else: ?>
                                            Belum terdapat data shodaqoh Jumat pada bulan <b><?= $namaBulan[$bulanDipilih]; ?></b>.
                                        <?php endif; ?>
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
                Total Data : 
                <strong>
                    <?= $totalTransaksi['total'] ?? 0; ?>
                </strong>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/script.js"></script>

<?php require_once '../template/footer.php'; ?>