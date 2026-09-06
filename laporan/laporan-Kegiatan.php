<?php
$pageTitle = 'Laporan Kegiatan';
require_once '../template/header.php';

$totalKegiatan = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM kegiatan"));
$totalPengeluaran = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COALESCE(SUM(pengeluaran),0) AS total FROM kegiatan"));
$data = mysqli_query($connect, "SELECT * FROM kegiatan ORDER BY tanggal DESC, id_kegiatan DESC");

$bulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November',    12 => 'Desember'
];
?>

<link rel="stylesheet" href="../assets/css/laporan.css">
<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">
    <div class="laporan-header">
        <div>
            <h3>Laporan Kegiatan</h3>
            <p>seluruh data kegiatan pengeluaran musholla.</p>
        </div>
        <a href="export/export-Kegiatan.php" target="_blank" class="btn-export">
            <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>
                <div class="stats-info">
                    <small>Total Kegiatan</small>
                    <h2><?= $totalKegiatan['total'] ?? 0; ?></h2>
                    <span>Seluruh Kegiatan</span>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stats-info">
                    <small>Total Pengeluaran</small>
                    <h2>Rp<?= number_format($totalPengeluaran['total'] ?? 0, 0, ',', '.'); ?></h2>
                    <span>Seluruh Kegiatan</span>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <div class="table-tools">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama kegiatan...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kegiatan</th>
                        <th>Pengeluaran</th>
                        <th>Tanggal</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($row['nama_kegiatan']); ?></strong>
                                </td>
                                <td>
                                    <span class="nominal-red">
                                        Rp <?= number_format($row['pengeluaran'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $tanggal = strtotime($row['tanggal']);
                                    echo date('d', $tanggal) . ' ' . $bulan[(int)date('m', $tanggal)] . ' ' . date('Y', $tanggal);
                                    ?>
                                </td>
                                <td>
                                    <?= nl2br(htmlspecialchars($row['deskripsi'])); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-report">
                                    <i class="bi bi-folder2-open"></i>
                                    <h5>Belum Ada Data Kegiatan</h5>
                                    <p>Belum terdapat data kegiatan yang tersimpan dalam sistem.</p>
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
                <strong><?= $totalKegiatan['total'] ?? 0; ?></strong>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/laporan.js"></script>
<?php require_once '../template/footer.php'; ?>