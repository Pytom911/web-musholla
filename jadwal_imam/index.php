<?php
$pageTitle = 'Data Jadwal Imam';
session_start();
require_once '../template/header.php';

$totalImam = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(DISTINCT id_guru) AS total
    FROM jadwal_imam
"));

$totalJadwal = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(*) AS total
    FROM jadwal_imam
"));

$totalHariIni = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(*) AS total
    FROM jadwal_imam
    WHERE tanggal = CURDATE()
"));

$totalBulan = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(*) AS total
    FROM jadwal_imam
    WHERE MONTH(tanggal) = MONTH(CURDATE())
    AND YEAR(tanggal) = YEAR(CURDATE())
"));

$data = mysqli_query($connect, "
    SELECT
        jadwal_imam.id_imam,
        jadwal_imam.tanggal,
        jadwal_imam.waktu_sholat,
        jadwal_imam.id_guru,
        guru.nama_guru AS nama_guru
    FROM jadwal_imam
    LEFT JOIN guru ON jadwal_imam.id_guru = guru.id_guru
    ORDER BY jadwal_imam.tanggal DESC, jadwal_imam.id_imam DESC
");
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <?php if (isset($_GET['pesan'])): ?>
        <?php if ($_GET['pesan'] == 'simpan'): ?>
            <div class="alert alert-success">Jadwal imam berhasil ditambahkan.</div>
        <?php elseif ($_GET['pesan'] == 'update'): ?>
            <div class="alert alert-success">Jadwal imam berhasil diperbarui.</div>
        <?php elseif ($_GET['pesan'] == 'hapus'): ?>
            <div class="alert alert-success">Jadwal imam berhasil dihapus.</div>
        <?php elseif ($_GET['pesan'] == 'gagal'): ?>
            <div class="alert alert-danger">Terjadi kesalahan.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <h3>Data Jadwal Imam</h3>
            <p>Kelola seluruh data jadwal imam musholla.</p>
        </div>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
            <a href="tambah.php" class="btn-add">
                <i class="fas fa-plus-circle"></i>
                Tambah Jadwal Imam
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stats-info">
                    <small>Jumlah Imam</small>
                    <h2><?= $totalImam['total'] ?? 0; ?></h2>
                    <span>Orang</span>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stats-info">
                    <small>Total Jadwal</small>
                    <h2><?= $totalJadwal['total'] ?? 0; ?></h2>
                    <span>Data</span>
                </div>
            </div>
        </div>

    </div>

    <div class="data-card">

        <div class="data-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama imam...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th width="70">No</th>
                        <th>Nama Imam</th>
                        <th width="180">Tanggal</th>
                        <th width="220">Waktu Sholat</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row['nama_guru'] ?? 'Nama guru tidak ditemukan'); ?>
                                    </strong>
                                </td>
                                <td><?= date('d F Y', strtotime($row['tanggal'])); ?></td>
                                <td><?= htmlspecialchars($row['waktu_sholat']); ?></td>
                                <td>
                                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                                        <div class="action-group">
                                            <a href="edit.php?id=<?= $row['id_imam']; ?>" class="btn-edit">
                                                <i class="fas fa-pen"></i>
                                                Edit
                                            </a>
                                            <a href="hapus.php?id=<?= $row['id_imam']; ?>"
                                               class="btn-delete"
                                               onclick="return confirm('Yakin ingin menghapus jadwal ini?')">
                                                <i class="fas fa-trash"></i>
                                                Hapus
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-data">
                                    <i class="fas fa-folder-open"></i>
                                    <h4>Belum Ada Jadwal Imam</h4>
                                    <p>Belum ada data jadwal imam.</p>
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
