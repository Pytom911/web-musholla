<?php
require_once "../auth/auth.php";
requireRole(['admin']);
$pageTitle = 'Data Pengguna';
require_once '../template/header.php';

$totalUser = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM users"));
$totalPetugas = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM users WHERE role='petugas'"));
$totalAdmin = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM users WHERE role='admin'"));
$data = mysqli_query($connect, "SELECT * FROM users ORDER BY id_user ASC");
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <?php if (isset($_GET['pesan'])): ?>

        <?php if ($_GET['pesan'] == "simpan"): ?>
            <div class="alert alert-success">Data pengguna berhasil ditambahkan.</div>
        <?php elseif ($_GET['pesan'] == "update"): ?>
            <div class="alert alert-success">Data pengguna berhasil diperbarui.</div>
        <?php elseif ($_GET['pesan'] == "hapus"): ?>
            <div class="alert alert-success">Data pengguna berhasil dihapus.</div>
        <?php elseif ($_GET['pesan'] == "gagal"): ?>
            <div class="alert alert-danger">Terjadi kesalahan.</div>
        <?php endif; ?>

    <?php endif; ?>

    <div class="page-header">

        <div>
            <h3>Data Pengguna</h3>
            <p>Kelola seluruh data pengguna musholla.</p>
        </div>

        <a href="tambah.php" class="btn-add">
            <i class="fas fa-plus-circle"></i>
            Tambah Pengguna
        </a>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">

                <div class="icon icon-red">
                    <i class="fas fa-users"></i>
                </div>

                <div class="stats-info">
                    <small>Jumlah Pengguna</small>
                    <h2><?= $totalUser['total'] ?? 0 ?></h2>
                    <span>Orang</span>
                </div>

            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">

                <div class="icon icon-yellow">
                    <i class="bi bi-person-badge"></i>
                </div>

                <div class="stats-info">
                    <small>Total Petugas</small>
                    <h2><?= $totalPetugas['total'] ?? 0 ?></h2>
                    <span>Orang</span>
                </div>

            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">

                <div class="icon icon-blue">
                    <i class="bi bi-shield-lock-fill"></i>
                </div>

                <div class="stats-info">
                    <small>Total Admin</small>
                    <h2><?= $totalAdmin['total'] ?? 0 ?></h2>
                    <span>Orang</span>
                </div>

            </div>
        </div>

    </div>
    <div class="data-card">
        <div class="data-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama atau username...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th width="70">No</th>
                        <th>Username</th>
                        <th>Nama</th>
                        <th width="180">Role</th>
                        <th width="180">Password</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = 1;
                        while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><strong><?= htmlspecialchars($row['username']); ?></strong></td>
                                <td><?= htmlspecialchars($row['nama']); ?></td>
                                <td><span
                                        class="role-badge role-<?= htmlspecialchars($row['role']); ?>"><?= htmlspecialchars(ucfirst($row['role'])); ?></span>
                                </td>
                                <td><?= htmlspecialchars($row['password']); ?></td>
                                <td>
                                    <div class="action-group">
                                        <a href="edit.php?id=<?= $row['id_user']; ?>" class="btn-edit">
                                            <i class="fas fa-pen"></i> Edit
                                        </a>
                                        <a href="hapus.php?id=<?= $row['id_user']; ?>" class="btn-delete">
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
                                    <h4>Belum Ada Data Pengguna</h4>
                                    <p>Silakan klik tombol <b>Tambah Pengguna</b> untuk menambahkan data.</p>
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