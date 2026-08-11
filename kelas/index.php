<?php
$pageTitle = 'Data kelas';
require_once '../template/header.php';

$totalkelas = mysqli_fetch_assoc(mysqli_query($connect,"SELECT COUNT(DISTINCT nama_kelas) AS total FROM kelas"));
$data = mysqli_query($connect,"SELECT * FROM kelas ORDER BY nama_kelas DESC,id_kelas DESC");
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

<?php if(isset($_GET['pesan'])): ?>

<?php if($_GET['pesan']=="simpan"): ?>
<div class="alert alert-success">Data kelas berhasil ditambahkan.</div>
<?php elseif($_GET['pesan']=="update"): ?>
<div class="alert alert-success">Data kelas berhasil diperbarui.</div>
<?php elseif($_GET['pesan']=="hapus"): ?>
<div class="alert alert-success">Data kelas berhasil dihapus.</div>
<?php elseif($_GET['pesan']=="gagal"): ?>
<div class="alert alert-danger">Terjadi kesalahan.</div>
<?php endif; ?>

<?php endif; ?>

<div class="page-header">

    <div>
        <h3>Data kelas</h3>
        <p>Kelola seluruh data kelas musholla.</p>
    </div>

    <a href="tambah.php" class="btn-add">
        <i class="fas fa-plus-circle"></i>
        Tambah kelas
    </a>

</div>

<div class="row g-4 mb-4">

    <div class="col-xl-12 col-md-6">
        <div class="stats-card">

            <div class="icon icon-green">
                <i class="bi bi-backpack3"></i>
            </div>

            <div class="stats-info">
                <small>Total kelas</small>
                <h2><?= $totalkelas['total'] ?? 0 ?></h2>
                <span>Orang</span>
            </div>

        </div>
    </div>

</div>
<div class="data-card">
    <div class="data-toolbar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" placeholder="Cari nama kelas...">
        </div>
    </div>

    <div class="table-responsive">
        <table class="table-modern" id="dataTable">
            <thead>
                <tr>
                    <th width="70">No</th>
                    <th>Nama kelas</th>
                    <th width="220">Tingkat</th>
                    <th width="220">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($data) > 0): ?>
                    <?php $no=1; while($row=mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td><strong><?= htmlspecialchars($row['nama_kelas']); ?></strong></td>
                        <td><strong><?= htmlspecialchars($row['tingkat']); ?></strong></td>
                        <td>
                            <div class="action-group">
                                <a href="edit.php?id=<?= $row['id_kelas']; ?>" class="btn-edit">
                                    <i class="fas fa-pen"></i> Edit
                                </a>
                                <a href="hapus.php?id=<?= $row['id_kelas']; ?>" class="btn-delete">
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
                                <h4>Belum Ada Data kelas</h4>
                                <p>Silakan klik tombol <b>Tambah kelas</b> untuk menambahkan data.</p>
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

