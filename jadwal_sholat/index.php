<?php
$pageTitle = 'Data Jadwal Sholat';
require_once '../template/header.php';

$totalJadwal = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM jadwal_sholat"));
$totalkelas = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM kelas"));

$data = mysqli_query($connect, "
    SELECT jadwal_sholat.*, kelas.nama_kelas
    FROM jadwal_sholat
    JOIN kelas ON jadwal_sholat.id_kelas = kelas.id_kelas
    ORDER BY jadwal_sholat.tanggal DESC, jadwal_sholat.id_jadwal DESC
");
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <?php if (isset($_GET['pesan'])): ?>

        <?php if ($_GET['pesan'] == "simpan"): ?>
            <div class="alert alert-success">Data jadwal sholat berhasil ditambahkan.</div>
        <?php elseif ($_GET['pesan'] == "update"): ?>
            <div class="alert alert-success">Data jadwal sholat berhasil diperbarui.</div>
        <?php elseif ($_GET['pesan'] == "hapus"): ?>
            <div class="alert alert-success">Data jadwal sholat berhasil dihapus.</div>
        <?php elseif ($_GET['pesan'] == "gagal"): ?>
            <div class="alert alert-danger">Terjadi kesalahan.</div>
        <?php endif; ?>

    <?php endif; ?>

    <div class="page-header">

        <div>
            <h3>Data Jadwal Sholat</h3>
            <p>Kelola seluruh data jadwal sholat musholla.</p>
        </div>
        <?php if ($isPetugas || $isAdmin): ?>
            <a href="tambah.php" class="btn-add">
                <i class="fas fa-plus-circle"></i>
                Tambah Jadwal
            </a>
        <?php endif; ?>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-6 col-md-6">
            <div class="stats-card">

                <div class="icon icon-green">
                    <i class="bi bi-calendar-event"></i>
                </div>

                <div class="stats-info">
                    <small>Total Jadwal</small>
                    <h2><?= $totalJadwal['total']; ?></h2>
                    <span>Data</span>
                </div>

            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="stats-card">

                <div class="icon icon-red">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>

                <div class="stats-info">
                    <small>Total kelas</small>
                    <h2><?= $totalkelas['total'] ?? 0 ?></h2>
                    <span>Kelas</span>
                </div>

            </div>
        </div>

    </div>

    <div class="data-card">

        <div class="data-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari jadwal...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th width="70">No</th>
                        <th>Tanggal</th>
                        <th>Waktu Sholat</th>
                        <th>Kelas</th>
                        <th width="220">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($data) > 0): ?>

                        <?php $no = 1;
                        while ($row = mysqli_fetch_assoc($data)): ?>

                            <tr>

                                <td><?= $no++; ?></td>

                                <td><?= htmlspecialchars($row['tanggal']); ?></td>

                                <td><?= htmlspecialchars($row['waktu_sholat']); ?></td>

                                <td><?= htmlspecialchars($row['nama_kelas']); ?></td>

                                <td>
                                    <div class="action-group">

                                        <a href="edit.php?id=<?= $row['id_jadwal']; ?>" class="btn-edit">
                                            <i class="fas fa-pen"></i> Edit
                                        </a>

                                        <a href="hapus.php?id=<?= $row['id_jadwal']; ?>"
                                            class="btn-delete"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')">
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
                                    <h4>Belum Ada Data Jadwal Sholat</h4>
                                    <p>Silakan klik tombol <b>Tambah Jadwal</b> untuk menambahkan data.</p>
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