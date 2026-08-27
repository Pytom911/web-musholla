<?php
require_once "../auth/auth.php";
requireRole(['admin']);
$pageTitle = 'Data Guru';
require_once '../template/header.php';

$totalGuru = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(*) AS total FROM guru
"));

$data = mysqli_query($connect, "
    SELECT * FROM guru
    ORDER BY id_guru DESC
");
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <?php if (isset($_GET['pesan'])): ?>
        <?php if ($_GET['pesan'] == "simpan"): ?>
            <div class="alert alert-success">Data guru berhasil ditambahkan.</div>
        <?php elseif ($_GET['pesan'] == "update"): ?>
            <div class="alert alert-success">Data guru berhasil diperbarui.</div>
        <?php elseif ($_GET['pesan'] == "hapus"): ?>
            <div class="alert alert-success">Data guru berhasil dihapus.</div>
        <?php elseif ($_GET['pesan'] == "gagal"): ?>
            <div class="alert alert-danger">Terjadi kesalahan.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <h3>Data Guru</h3>
            <p>Kelola seluruh data guru.</p>
        </div>
        <a href="tambah.php" class="btn-add">
            <i class="fas fa-plus-circle"></i>
            Tambah Guru
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-12 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-info">
                    <small>Total Guru</small>
                    <h2><?= $totalGuru['total']; ?></h2>
                    <span>Orang</span>
                </div>
            </div>
        </div>
    </div>

    <div class="data-card">
        <div class="data-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama guru...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Guru</th>
                        <th>NIP</th>
                        <th>No HP</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_guru']); ?></td>
                                <td><?= htmlspecialchars($row['nip']); ?></td>
                                <td><?= htmlspecialchars($row['no_hp']); ?></td>
                                <td>
                                    <div class="action-group">
                                        <a href="edit.php?id=<?= $row['id_guru']; ?>" class="btn-edit">
                                            <i class="fas fa-pen"></i>
                                            Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $row['id_guru']; ?>"
                                           class="btn-delete"
                                           onclick="return confirm('Yakin ingin menghapus data?')">
                                            <i class="fas fa-trash"></i>
                                            Hapus
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
                                    <h4>Belum Ada Data Guru</h4>
                                    <p>Silakan klik tombol <b>Tambah Guru</b>.</p>
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
        </div>
    </div>

</div>

<script src="../assets/js/data.js"></script>

<?php require_once '../template/footer.php'; ?>
```
