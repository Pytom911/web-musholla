<?php
$pageTitle = 'Laporan Jadwal Sholat';
require_once __DIR__ . '/../template/header.php';

$totalJadwal = mysqli_fetch_assoc(mysqli_query(
    $connect,
    "SELECT COUNT(*) AS total FROM jadwal_sholat"
));
$totalKelas = mysqli_fetch_assoc(mysqli_query(
    $connect,
    "SELECT COUNT(DISTINCT id_kelas) AS total FROM jadwal_sholat"
));
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
     LEFT JOIN kelas ON jadwal_sholat.id_kelas = kelas.id_kelas
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
?>

<link rel="stylesheet" href="<?= asset('css/laporan.css') ?>">
<link rel="stylesheet" href="<?= asset('css/data.css') ?>">

<div class="container-fluid">
    <div class="laporan-header">
        <div>
            <h3>Laporan Jadwal Sholat</h3>
            <p>Seluruh data jadwal sholat berdasarkan tanggal dan kelas.</p>
        </div>
        <a href="<?= url('laporan/export/export-Jadwal.php') ?>" target="_blank" class="btn-export">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            Export PDF
        </a>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stats-info">
                    <small>Total Jadwal</small>
                    <h2><?= (int) ($totalJadwal['total'] ?? 0) ?></h2>
                    <span>Jadwal Sholat</span>
                </div>
            </div>
        </div>
        <div class="col-xl-6 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-mortarboard"></i>
                </div>
                <div class="stats-info">
                    <small>Total Kelas</small>
                    <h2><?= (int) ($totalKelas['total'] ?? 0) ?></h2>
                    <span>Kelas</span>
                </div>
            </div>
        </div>
    </div>

    <div class="table-card">
        <div class="table-header">
            <div class="table-tools">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" class="form-control"
                        placeholder="Cari hari, jadwal, tanggal, kelas, atau bagian...">
                </div>
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
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
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
                                <td><strong><?= htmlspecialchars((string) ($row['nama_kelas'] ?? 'Kelas Tidak Ditemukan'), ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><?= htmlspecialchars((string) ($row['tingkat'] ?? '-'), ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?= !empty($row['bagian'])
                                        ? htmlspecialchars((string) $row['bagian'], ENT_QUOTES, 'UTF-8')
                                        : '-' ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-report">
                                    <i class="bi bi-calendar-x"></i>
                                    <h5>Belum Ada Jadwal Sholat</h5>
                                    <p>Belum terdapat data jadwal sholat yang tersimpan dalam sistem.</p>
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
                <strong><?= (int) ($totalJadwal['total'] ?? 0) ?></strong>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('js/laporan.js') ?>"></script>
<?php require_once __DIR__ . '/../template/footer.php'; ?>
