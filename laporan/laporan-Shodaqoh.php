<?php
$pageTitle = 'Laporan Shodaqoh Jumat';
require_once '../template/header.php';

$totalData = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM shodaqoh_jumat"));
$totalShodaqoh = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COALESCE(SUM(nominal),0) AS total FROM shodaqoh_jumat"));
$totalKelas = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(DISTINCT id_kelas) AS total FROM shodaqoh_jumat"));

$data = mysqli_query($connect, "
    SELECT shodaqoh_jumat.*, kelas.nama_kelas 
    FROM shodaqoh_jumat 
    INNER JOIN kelas ON shodaqoh_jumat.id_kelas = kelas.id_kelas 
    ORDER BY shodaqoh_jumat.tanggal DESC, shodaqoh_jumat.id_shodaqoh DESC
");

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
            <h3>Laporan Shodaqoh Jumat</h3>
            <p>Rekapitulasi seluruh data shodaqoh Jumat dari setiap kelas.</p>
        </div>
        <a href="export/export-Shodaqoh.php" target="_blank" class="btn-export">
            <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green"><i class="bi bi-cash-stack"></i></div>
                <div class="stats-info">
                    <small>Total Shodaqoh</small>
                    <h2>Rp<?= number_format($totalShodaqoh['total'] ?? 0, 0, ',', '.'); ?></h2>
                    <span>Keseluruhan</span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red"><i class="bi bi-mortarboard-fill"></i></div>
                <div class="stats-info">
                    <small>Jumlah Kelas</small>
                    <h2><?= $totalKelas['total'] ?? 0; ?></h2>
                    <span>Kelas</span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-blue"><i class="bi bi-receipt"></i></div>
                <div class="stats-info">
                    <small>Total Transaksi</small>
                    <h2><?= $totalData['total'] ?? 0; ?></h2>
                    <span>Data Shodaqoh</span>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <div>
                <h4>Data Laporan Shodaqoh Jumat</h4>
                <p class="text-muted mb-0">Seluruh transaksi shodaqoh Jumat yang telah tercatat.</p>
            </div>
            <div class="table-tools">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama kelas...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th width="70">No</th>
                        <th width="200">Tanggal</th>
                        <th>Nama Kelas</th>
                        <th width="220">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
                            <?php
                            $timestamp = strtotime($row['tanggal']);
                            $tanggal = date('d', $timestamp) . ' ' . $bulan[(int)date('m', $timestamp)] . ' ' . date('Y', $timestamp);
                            ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= $tanggal; ?></td>
                                <td><strong><?= htmlspecialchars($row['nama_kelas']); ?></strong></td>
                                <td>
                                    <span class="nominal-red">
                                        Rp <?= number_format($row['nominal'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">
                                <div class="empty-report">
                                    <i class="bi bi-folder2-open"></i>
                                    <h5>Belum Ada Data Shodaqoh</h5>
                                    <p>Belum terdapat data shodaqoh Jumat yang tersimpan dalam sistem.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="table-info">
                Total Data : <strong><?= $totalData['total'] ?? 0; ?></strong>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/laporan.js"></script>

<?php require_once '../template/footer.php'; ?>