<?php
$pageTitle = 'Laporan Infaq';
require_once '../template/header.php';

$totalDonatur = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(DISTINCT nama_donatur) AS total FROM infaq"));
$totalInfaq = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COALESCE(SUM(nominal),0) AS total FROM infaq"));
$totalTransaksi = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM infaq"));
$data = mysqli_query($connect, "SELECT * FROM infaq ORDER BY tanggal DESC, id_infaq DESC");

$bulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];
?>

<link rel="stylesheet" href="../assets/css/laporan.css">
<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <div class="laporan-header">
        <div>
            <h3>Laporan Infaq</h3>
            <p>seluruh data penerimaan infaq musholla.</p>
        </div>
        <a href="export/export-Infaq.php" target="_blank" class="btn-export">
            <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-info">
                    <small>Jumlah Donatur</small>
                    <h2><?= $totalDonatur['total'] ?? 0; ?></h2>
                    <span>Orang</span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="bi bi-box2-heart"></i>
                </div>
                <div class="stats-info">
                    <small>Total Infaq</small>
                    <h2>Rp<?= number_format($totalInfaq['total'] ?? 0, 0, ',', '.'); ?></h2>
                    <span>Keseluruhan</span>
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
                    <h2><?= $totalTransaksi['total'] ?? 0; ?></h2>
                    <span>Data Infaq</span>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <div class="table-tools">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama donatur...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Donatur</th>
                        <th>Nominal</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_donatur']); ?></strong>
                                </td>
                                <td>
                                    <span class="nominal">
                                        Rp <?= number_format($row['nominal'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $tanggal = strtotime($row['tanggal']);
                                    echo date('d', $tanggal) . ' ' .
                                         $bulan[(int)date('m', $tanggal)] . ' ' .
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
                                    <h5>Belum Ada Data Infaq</h5>
                                    <p>Belum terdapat data infaq yang tersimpan dalam sistem.</p>
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
                <strong><?= $totalTransaksi['total'] ?? 0; ?></strong>
            </div>
        </div>
    </div>

</div>

<script src="../assets/js/laporan.js"></script>
<?php require_once '../template/footer.php'; ?>