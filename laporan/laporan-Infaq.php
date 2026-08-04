<?php
$pageTitle='Laporan Infaq';
require_once '../template/header.php';

$dari=$_GET['dari'] ?? '';
$sampai=$_GET['sampai'] ?? '';

$where='';
if($dari!='' && $sampai!=''){
    $where="WHERE tanggal BETWEEN '$dari' AND '$sampai'";
}

$totalDana=mysqli_fetch_assoc(mysqli_query($connect,"SELECT SUM(nominal) total FROM infaq $where"));
$totalDonatur=mysqli_fetch_assoc(mysqli_query($connect,"SELECT COUNT(DISTINCT nama_donatur) total FROM infaq $where"));
$totalTransaksi=mysqli_fetch_assoc(mysqli_query($connect,"SELECT COUNT(*) total FROM infaq $where"));

$bulanIni=mysqli_fetch_assoc(mysqli_query($connect,"
SELECT SUM(nominal) total
FROM infaq
WHERE MONTH(tanggal)=MONTH(CURDATE())
AND YEAR(tanggal)=YEAR(CURDATE())
"));

$data=mysqli_query($connect,"
SELECT *
FROM infaq
$where
ORDER BY tanggal DESC,id_infaq DESC
");
?>

<link rel="stylesheet" href="../assets/css/data.css">
<link rel="stylesheet" href="../assets/css/laporan.css">

<div class="container-fluid">

<div class="laporan-header">

    <div>
        <h3>Laporan Infaq</h3>
        <p>Kelola dan cetak laporan data infaq musholla.</p>
    </div>

    <a href="pdf/laporan-infaq-pdf.php?dari=<?= $dari ?>&sampai=<?= $sampai ?>" class="btn-export" target="_blank">
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

                <a href="laporan-infaq.php" class="btn btn-reset">
                    <i class="bi bi-arrow-clockwise"></i>
                    Reset
                </a>

            </div>

        </div>

    </form>

</div>
<div class="row g-4 mb-4">

    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon icon-red">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-info">
                <small>Jumlah Donatur</small>
                <h2><?= $totalDonatur['total'] ?? 0 ?></h2>
                <span>Orang</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon icon-green">
                <i class="bi bi-box2-heart"></i>
            </div>
            <div class="stats-info">
                <small>Total Infaq</small>
                <h2>Rp<?= number_format($totalDana['total'] ?? 0,0,',','.') ?></h2>
                <span>Keseluruhan</span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon icon-yellow">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
            <div class="stats-info">
                <small>Bulan Ini</small>
                <h2>Rp<?= number_format($bulanIni['total'] ?? 0,0,',','.') ?></h2>
                <span><?= date('F Y') ?></span>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="stats-card">
            <div class="icon icon-blue">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="stats-info">
                <small>Total Transaksi</small>
                <h2><?= $totalTransaksi['total'] ?? 0 ?></h2>
                <span>Data</span>
            </div>
        </div>
    </div>

</div>
<div class="table-card">

    <div class="table-header">
        <h4>Data Laporan Infaq</h4>

        <div class="table-tools">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama donatur...">
            </div>

            <!-- <select id="showData" class="form-select">
                <option value="10">10 Data</option>
                <option value="25">25 Data</option>
                <option value="50">50 Data</option>
                <option value="100">100 Data</option>
            </select> -->
        </div>
    </div>

    <div class="table-responsive">

        <table class="table-modern" id="dataTable">

            <thead>
                <tr>
                    <th width="70">No</th>
                    <th>Nama Donatur</th>
                    <th width="220">Nominal</th>
                    <th width="180">Tanggal</th>
                </tr>
            </thead>

            <tbody>

            <?php if(mysqli_num_rows($data)>0): ?>
            <?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>

                <tr>
                    <td><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_donatur']); ?></td>
                    <td>
                        <span class="nominal">
                            Rp <?= number_format($row['nominal'],0,',','.'); ?>
                        </span>
                    </td>
                    <td><?= date('d F Y',strtotime($row['tanggal'])); ?></td>
                </tr>

            <?php endwhile; ?>
            <?php else: ?>

                <tr>
                    <td colspan="4">
                        <div class="empty-data">
                            <i class="bi bi-folder2-open"></i>
                            <h4>Belum Ada Data</h4>
                            <p>Tidak ada data infaq yang dapat ditampilkan.</p>
                        </div>
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>
            <div class="table-footer">

            <div class="table-info">
                Total Data : <strong><?= mysqli_num_rows($data); ?></strong>
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

<?php require_once '../template/footer.php'; ?>