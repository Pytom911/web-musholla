<?php
$pageTitle = 'Data Shodaqoh Jumat';
require_once '../template/header.php';

$totalData = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM shodaqoh_jumat"));
$totalShodaqoh = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COALESCE(SUM(nominal),0) AS total FROM shodaqoh_jumat"));
$totalBulan = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COALESCE(SUM(nominal),0) AS total FROM shodaqoh_jumat WHERE MONTH(tanggal)=MONTH(CURDATE()) AND YEAR(tanggal)=YEAR(CURDATE())"));
$totalKelas = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(DISTINCT id_kelas) AS total FROM shodaqoh_jumat"));

$data = mysqli_query (
    $connect, 
    "SELECT shodaqoh_jumat.*, kelas.nama_kelas
    FROM shodaqoh_jumat
    JOIN kelas
    ON shodaqoh_jumat.id_kelas = kelas.id_kelas
    ORDER BY shodaqoh_jumat.tanggal DESC, shodaqoh_jumat.id_shodaqoh DESC
")
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">
    <?php if(isset($_GET['pesan'])): ?>
        <?php if($_GET['pesan']=="simpan"): ?>
            <div class="alert alert-success">Data shodaqoh berhasil ditambahkan.</div>
        <?php elseif($_GET['pesan']=="update"): ?>
            <div class="alert alert-success">Data shodaqoh berhasil diperbarui.</div>
        <?php elseif($_GET['pesan']=="hapus"): ?>
            <div class="alert alert-success">Data shodaqoh berhasil dihapus.</div>
        <?php elseif($_GET['pesan']=="gagal"): ?>
            <div class="alert alert-danger">Terjadi kesalahan.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <h3>Data Shodaqoh Jumat</h3>
            <p>Kelola seluruh data shodaqoh Jumat dari setiap kelas.</p>
        </div>
        <?php if($isPetugas || $isAdmin): ?>
            <a href="tambah.php" class="btn-add">
                <i class="bi bi-plus-circle-fill"></i>
                Tambah Shodaqoh
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="stats-info">
                    <small>Total Shodaqoh</small>
                    <h2>Rp<?= number_format($totalShodaqoh['total'] ?? 0,0,',','.'); ?></h2>
                    <span>Keseluruhan</span>
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
                    <h2><?= $totalKelas['total'] ?? 0; ?></h2>
                    <span>Kelas</span>
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
                    <h2><?= $totalData['total'] ?? 0; ?></h2>
                    <span>Total</span>
                </div>
            </div>
        </div>
    </div>
        <div class="data-card">
        <div class="data-toolbar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama kelas...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th width="70">No</th>
                        <th>Tanggal</th>
                        <th>Nama Kelas</th>
                        <th width="220">Nominal</th>
                        <?php if($isPetugas || $isAdmin): ?>
                            <th width="180" class="text-center">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($data) > 0): ?>
                        <?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
                        <tr data-tanggal="<?= htmlspecialchars($row['tanggal']); ?>">
                            <td><?= $no++; ?></td>
                            <td><?= date('d F Y',strtotime($row['tanggal'])); ?></td>
                            <td><strong><?= htmlspecialchars($row['nama_kelas']); ?></strong></td>
                            <td>
                                <span class="nominal">
                                    Rp <?= number_format($row['nominal'],0,',','.'); ?>
                                </span>
                            </td>
                            <?php if($isPetugas || $isAdmin): ?>
                                <td>
                                    <div class="action-group">
                                        <a href="edit.php?id=<?= $row['id_shodaqoh']; ?>" class="btn-edit">
                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $row['id_shodaqoh']; ?>" class="btn-delete">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= ($isPetugas || $isAdmin) ? '5' : '4'; ?>">
                                <div class="empty-data">
                                    <i class="bi bi-folder2-open"></i>
                                    <h4>Belum Ada Data Shodaqoh</h4>
                                    <p>Silakan tambahkan data shodaqoh Jumat untuk mulai mengelola data.</p>
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