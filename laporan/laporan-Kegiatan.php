<?php
$pageTitle='Laporan Kegiatan';
require_once '../template/header.php';

$dari=$_GET['dari'] ?? '';
$sampai=$_GET['sampai'] ?? '';

$where='';
if($dari!='' && $sampai!=''){
    $where="WHERE tanggal BETWEEN '$dari' AND '$sampai'";
}

$totalKegiatan=mysqli_fetch_assoc(mysqli_query($connect,"
SELECT COUNT(*) total
FROM kegiatan
$where
"));

$totalPengeluaran=mysqli_fetch_assoc(mysqli_query($connect,"
SELECT SUM(pengeluaran) total
FROM kegiatan
$where
"));

$totalTransaksi=mysqli_fetch_assoc(mysqli_query($connect,"
SELECT COUNT(*) total
FROM kegiatan
$where
"));

$bulanIni=mysqli_fetch_assoc(mysqli_query($connect,"
SELECT SUM(pengeluaran) total
FROM kegiatan
WHERE MONTH(tanggal)=MONTH(CURDATE())
AND YEAR(tanggal)=YEAR(CURDATE())
"));

$data=mysqli_query($connect,"
SELECT *
FROM kegiatan
$where
ORDER BY tanggal DESC,id_kegiatan DESC
");
?>

<link rel="stylesheet" href="../assets/css/data.css">
<link rel="stylesheet" href="../assets/css/laporan.css">

<div class="container-fluid">

<div class="laporan-header">

    <div>
        <h3>Laporan Kegiatan</h3>
        <p>Kelola dan cetak laporan data kegiatan musholla.</p>
    </div>

    <a href="pdf/laporan-kegiatan-pdf.php?dari=<?= $dari ?>&sampai=<?= $sampai ?>" class="btn-export" target="_blank">
        <i class="bi bi-file-earmark-pdf-fill"></i>
        Export PDF
    </a>

</div>

<div class="laporan-card">

    <div class="filter-title">
        <i class="bi bi-funnel-fill"></i>
        Filter Laporan
    </div>

    <form method="GET">

        <div class="row g-3">

            <div class="col-lg-3">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?= $dari ?>">
            </div>

            <div class="col-lg-3">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?= $sampai ?>">
            </div>

            <div class="col-lg-6 d-flex align-items-end gap-2">

                <button class="btn btn-filter" type="submit">
                    <i class="bi bi-search"></i>
                    Tampilkan
                </button>

                <a href="laporan-kegiatan.php" class="btn btn-reset">
                    <i class="bi bi-arrow-clockwise"></i>
                    Reset
                </a>

            </div>

        </div>

    </form>

</div>
<div class="row g-4 mb-4">

    <div class="col-xl-4 col-md-6">
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

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-cash-stack"></i>
                </div>

                <div class="stats-info">
                    <small>Total Pengeluaran</small>
                    <h2>Rp<?= number_format($totalPengeluaran['total'] ?? 0,0,',','.'); ?></h2>
                    <span>Semua Kegiatan</span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-yellow">
                    <i class="bi bi-calendar2-week-fill"></i>
                </div>

                <div class="stats-info">
                    <small>Pengeluaran Bulan Ini</small>
                    <h2>Rp<?= number_format($bulanIni['total'] ?? 0,0,',','.'); ?></h2>
                    <span><?= date('F Y'); ?></span>
                </div>
            </div>
        </div>

</div>
    <div class="data-card">

        <div class="data-toolbar">

            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama kegiatan...">
            </div>
            <!-- <div class="filter-box">
                <select id="showData" class="form-select">
                    <option value="10">10 Data</option>
                    <option value="25">25 Data</option>
                    <option value="50">50 Data</option>
                    <option value="100">100 Data</option>
                </select>
            </div> -->

        </div>

        <div class="table-responsive">

            <table class="table-modern" id="dataTable">

                <thead>
                    <tr>
                        <th width="70">No</th>
                        <th>Nama Kegiatan</th>
                        <th width="180">Pengeluaran</th>
                        <th width="170">Tanggal</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>

                <tbody>

                <?php if(mysqli_num_rows($data) > 0): ?>

                    <?php $no = 1; ?>
                    <?php while($row = mysqli_fetch_assoc($data)): ?>

                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_kegiatan']); ?></strong></td>
                        <td><span class="nominal-red">Rp <?= number_format($row['pengeluaran'],0,',','.'); ?></span></td>
                        <td><?= date('d F Y',strtotime($row['tanggal'])); ?></td>
                        <td><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></td>
                    </tr>

                <?php endwhile; ?>
                <?php else: ?>
                                        <tr>
                        <td colspan="6">
                            <div class="empty-data">
                                <i class="bi bi-folder2-open"></i>
                                <h4>Belum Ada Data Kegiatan</h4>
                                <p>Silakan tambahkan kegiatan baru untuk mulai mengelola data.</p>
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
                <strong><?= mysqli_num_rows($data); ?></strong>
            </div>

            <!-- <nav>
                <ul class="pagination mb-0">

                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    </li>

                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>

                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>

                </ul>
            </nav> -->

        </div>

    </div>

</div>

<script src="../assets/js/data.js"></script>

<?php require_once __DIR__ . '/../template/footer.php'; ?>