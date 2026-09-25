<?php
$pageTitle = 'Data Jadwal Sholat';
require_once __DIR__ . '/../template/header.php';

$totalJadwal = mysqli_fetch_assoc(
    mysqli_query($connect, "SELECT COUNT(*) AS total FROM jadwal_sholat")
);
$data = mysqli_query(
    $connect,
    "SELECT
        jadwal_sholat.id_jadwal,
        jadwal_sholat.tanggal,
        jadwal_sholat.waktu_sholat,
        jadwal_sholat.id_kelas,
        kelas.nama_kelas,
        kelas.tingkat,
        kelas.bagian
     FROM jadwal_sholat
     JOIN kelas ON jadwal_sholat.id_kelas = kelas.id_kelas
     ORDER BY
         CASE
             WHEN jadwal_sholat.tanggal IS NULL THEN 3
             WHEN jadwal_sholat.tanggal = CURDATE() THEN 0
             WHEN jadwal_sholat.tanggal > CURDATE() THEN 1
             ELSE 2
         END ASC,
         CASE
             WHEN jadwal_sholat.tanggal > CURDATE() THEN jadwal_sholat.tanggal
             ELSE NULL
         END ASC,
         CASE
             WHEN jadwal_sholat.tanggal < CURDATE() THEN jadwal_sholat.tanggal
             ELSE NULL
         END DESC,
         jadwal_sholat.id_jadwal DESC"
);
$dataCount = mysqli_num_rows($data);
$canManage = $isPetugas || $isAdmin;
?>

<link rel="stylesheet" href="<?= asset('css/data.css') ?>">

<div class="container-fluid">
    <?php if (($_GET['pesan'] ?? '') === 'simpan'): ?>
        <div class="alert alert-success">Data jadwal sholat berhasil ditambahkan.</div>
    <?php elseif (($_GET['pesan'] ?? '') === 'update'): ?>
        <div class="alert alert-success">Data jadwal sholat berhasil diperbarui.</div>
    <?php elseif (($_GET['pesan'] ?? '') === 'hapus'): ?>
        <div class="alert alert-success">Data jadwal sholat berhasil dihapus.</div>
    <?php elseif (($_GET['pesan'] ?? '') === 'gagal'): ?>
        <div class="alert alert-danger">Terjadi kesalahan.</div>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <h3>Data Jadwal Sholat</h3>
            <p>Kelola seluruh data jadwal sholat musholla.</p>
        </div>

        <?php if ($canManage): ?>
            <a href="<?= url('jadwal_sholat/tambah.php') ?>" class="btn-add">
                <i class="fas fa-plus-circle"></i>
                Tambah Jadwal Sholat
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-12 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div class="stats-info">
                    <small>Total Jadwal</small>
                    <h2><?= (int) ($totalJadwal['total'] ?? 0) ?></h2>
                    <span>Data</span>
                </div>
            </div>
        </div>
    </div>

    <div class="data-card">
        <div class="data-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari hari, tanggal, waktu, kelas, atau bagian...">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Hari</th>
                        <th>Tanggal</th>
                        <th>Waktu Sholat</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Bagian</th>
                        <?php if ($canManage): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dataCount > 0): ?>
                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <?php
                            $tanggal = !empty($row['tanggal'])
                                ? date('d/m/Y', strtotime($row['tanggal']))
                                : '-';
                            $hari = hari_indonesia($row['tanggal'] ?? null);
                            ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><strong><?= htmlspecialchars($hari, ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><strong><?= htmlspecialchars($tanggal, ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><strong><?= htmlspecialchars((string) $row['waktu_sholat'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><strong><?= htmlspecialchars((string) $row['nama_kelas'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><?= htmlspecialchars((string) $row['tingkat'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?= $row['bagian'] !== null && $row['bagian'] !== ''
                                        ? htmlspecialchars((string) $row['bagian'], ENT_QUOTES, 'UTF-8')
                                        : '-' ?>
                                </td>
                                <?php if ($canManage): ?>
                                    <td>
                                        <div class="action-group">
                                            <a href="<?= url('jadwal_sholat/edit.php?id=' . (int) $row['id_jadwal']) ?>" class="btn-edit">
                                                <i class="fas fa-pen"></i> Edit
                                            </a>
                                            <a href="<?= url('jadwal_sholat/hapus.php?id=' . (int) $row['id_jadwal']) ?>" class="btn-delete"
                                                onclick="return confirm('Apakah kamu yakin ingin menghapus jadwal ini?');">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $canManage ? 8 : 7 ?>">
                                <div class="empty-data">
                                    <i class="fas fa-folder-open"></i>
                                    <h4>Belum Ada Data Jadwal Sholat</h4>
                                    <p>Silakan tambahkan jadwal sholat pertama.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="table-info">
                Total Data:
                <strong><?= $dataCount ?></strong>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/script.js') ?>"></script>
<?php require_once __DIR__ . '/../template/footer.php'; ?>
