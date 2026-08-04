<?php
$pageTitle = 'Data Infaq';
require_once '../template/header.php';

$totalDonatur = mysqli_fetch_assoc(mysqli_query($connect,"SELECT COUNT(DISTINCT nama_donatur) AS total FROM infaq"));
$totalInfaq = mysqli_fetch_assoc(mysqli_query($connect,"SELECT SUM(nominal) AS total FROM infaq"));
$totalBulan = mysqli_fetch_assoc(mysqli_query($connect,"SELECT SUM(nominal) AS total FROM infaq WHERE MONTH(tanggal)=MONTH(CURDATE()) AND YEAR(tanggal)=YEAR(CURDATE())"));
$totalTransaksi = mysqli_fetch_assoc(mysqli_query($connect,"SELECT COUNT(*) AS total FROM infaq"));
$data = mysqli_query($connect,"SELECT * FROM infaq ORDER BY tanggal DESC,id_infaq DESC");
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

<?php if(isset($_GET['pesan'])): ?>

<?php if($_GET['pesan']=="simpan"): ?>
<div class="alert alert-success">Data infaq berhasil ditambahkan.</div>
<?php elseif($_GET['pesan']=="update"): ?>
<div class="alert alert-success">Data infaq berhasil diperbarui.</div>
<?php elseif($_GET['pesan']=="hapus"): ?>
<div class="alert alert-success">Data infaq berhasil dihapus.</div>
<?php elseif($_GET['pesan']=="gagal"): ?>
<div class="alert alert-danger">Terjadi kesalahan.</div>
<?php endif; ?>

<?php endif; ?>

<div class="page-header">

    <div>
        <h3>Data Infaq</h3>
        <p>Kelola seluruh data infaq musholla.</p>
    </div>

    <a href="tambah.php" class="btn-add">
        <i class="fas fa-plus-circle"></i>
        Tambah Infaq
    </a>

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
                <h2>Rp<?= number_format($totalInfaq['total'] ?? 0,0,',','.') ?></h2>
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
                <h2>Rp<?= number_format($totalBulan['total'] ?? 0,0,',','.') ?></h2>
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
<div class="data-card">
    <div class="data-toolbar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari nama donatur...">
        </div>

        <div class="filter-box">
            <select id="filterBulan">
                <option value="">All Months</option>
                <option value="01">January</option>
                <option value="02">February</option>
                <option value="03">March</option>
                <option value="04">April</option>
                <option value="05">May</option>
                <option value="06">June</option>
                <option value="07">July</option>
                <option value="08">August</option>
                <option value="09">September</option>
                <option value="10">October</option>
                <option value="11">November</option>
                <option value="12">December</option>
            </select>
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
                    <th width="220">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($data) > 0): ?>
                    <?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
                    <tr data-tanggal="<?= $row['tanggal']; ?>">
                        <td><?= $no++; ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_donatur']); ?></strong></td>
                        <td><span class="nominal">Rp <?= number_format($row['nominal'],0,',','.'); ?></span></td>
                        <td><?= date('d F Y',strtotime($row['tanggal'])); ?></td>
                        <td>
                            <div class="action-group">
                                <a href="edit.php?id=<?= $row['id_infaq']; ?>" class="btn-edit">
                                    <i class="fas fa-pen"></i> Edit
                                </a>
                                <a href="hapus.php?id=<?= $row['id_infaq']; ?>" class="btn-delete">
                                    <i class="fas fa-trash"></i> Hapus
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-data">
                                <i class="fas fa-folder-open"></i>
                                <h4>Belum Ada Data Infaq</h4>
                                <p>Silakan klik tombol <b>Tambah Infaq</b> untuk menambahkan data.</p>
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
    </div>
</div>

</div>

<script src="../assets/js/data.js"></script>

<?php require_once '../template/footer.php'; ?>