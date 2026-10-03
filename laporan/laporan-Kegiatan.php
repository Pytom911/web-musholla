<?php
$pageTitle = 'Laporan Kegiatan';
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

$totalKegiatan = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM kegiatan $whereBulan"));
$totalPengeluaran = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COALESCE(SUM(pengeluaran), 0) AS total FROM kegiatan $whereBulan"));

$data = mysqli_query($connect, "SELECT * FROM kegiatan $whereBulan ORDER BY tanggal DESC, id_kegiatan DESC");
?>

<link rel="stylesheet" href="../assets/css/laporan.css">
<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">
    <div class="laporan-header">
        <div>
            <h3>Laporan Kegiatan</h3>
            <p>Seluruh data kegiatan dan pengeluaran musholla.</p>
        </div>
        <a href="export/export-Kegiatan.php<?= $bulanDipilih > 0 ? '?bulan=' . $bulanDipilih : ''; ?>" target="_blank" class="btn-export">
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
                    <small>
                        <?php if ($bulanDipilih == 0): ?>
                            Total Kegiatan
                        <?php else: ?>
                            Total Kegiatan <?= $namaBulan[$bulanDipilih]; ?>
                        <?php endif; ?>
                    </small>
                    <h2><?= $totalKegiatan['total'] ?? 0; ?></h2>
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

        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stats-info">
                    <small>
                        <?php if ($bulanDipilih == 0): ?>
                            Total Pengeluaran
                        <?php else: ?>
                            Pengeluaran <?= $namaBulan[$bulanDipilih]; ?>
                        <?php endif; ?>
                    </small>
                    <h2>Rp<?= number_format($totalPengeluaran['total'] ?? 0, 0, ',', '.'); ?></h2>
                    <span>
                        <?php if ($bulanDipilih == 0): ?>
                            Semua Kegiatan
                        <?php else: ?>
                            <?= $namaBulan[$bulanDipilih]; ?> <?= date('Y'); ?>
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
                    <input type="text" id="searchInput" class="form-control" placeholder="Cari nama kegiatan...">
                </div>
                <form method="GET" class="filter-box">
                    <select name="bulan" id="bulan" onchange="this.form.submit()">
                        <option value="0" <?= $bulanDipilih == 0 ? 'selected' : ''; ?>>Semua Bulan</option>
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
                        <th>Nama Kegiatan</th>
                        <th>Pengeluaran</th>
                        <th>Deskripsi</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <?php $tanggal = date('F d Y', strtotime($row['tanggal'])); ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><strong><?= htmlspecialchars($row['nama_kegiatan']); ?></strong></td>
                                <td><span class="nominal-red">Rp <?= number_format($row['pengeluaran'], 0, ',', '.'); ?></span></td>
                                <td><strong><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></strong></td>
                                <td><?= $tanggal; ?></td>
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
                Total Data : <strong><?= $totalTransaksi['total'] ?? 0; ?></strong>
            </div>
        </div>
    </div>
</div>

<script src="../assets/js/script.js"></script>
<?php require_once '../template/footer.php'; ?>