<?php
$pageTitle = 'Laporan Imam';
require_once '../template/header.php';

$totalImam = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(DISTINCT id_guru) AS total
    FROM jadwal_imam
"));

$totalJadwal = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(*) AS total
    FROM jadwal_imam
"));

$totalJadwalHariIni = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(*) AS total
    FROM jadwal_imam
    WHERE tanggal = CURDATE()
"));

$data = mysqli_query($connect, "
    SELECT
        jadwal_imam.id_imam,
        jadwal_imam.tanggal,
        jadwal_imam.waktu_sholat,
        jadwal_imam.id_guru,
        guru.nama_guru
    FROM jadwal_imam
    LEFT JOIN guru
        ON jadwal_imam.id_guru = guru.id_guru
    ORDER BY jadwal_imam.tanggal DESC, jadwal_imam.id_imam DESC
");

$bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];
?>

<link rel="stylesheet" href="../assets/css/laporan.css">
<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <div class="laporan-header">
        <div>
            <h3>Laporan Imam</h3>
            <p>seluruh data jadwal imam musholla.</p>
        </div>

        <a href="export/export-Imam.php" target="_blank" class="btn-export">
            <i class="bi bi-file-earmark-pdf-fill"></i> Export PDF
        </a>
    </div>

    <div class="row g-4 mb-4">

        <div class="col-xl-4 col-md-6">
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

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-calendar-check"></i>
                </div>

                <div class="stats-info">
                    <small>Total Jadwal</small>
                    <h2><?= $totalJadwal['total'] ?? 0; ?></h2>
                    <span>Data Jadwal</span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-blue">
                    <i class="bi bi-calendar-day"></i>
                </div>

                <div class="stats-info">
                    <small>Jadwal Hari Ini</small>
                    <h2><?= $totalJadwalHariIni['total'] ?? 0; ?></h2>
                    <span>Jadwal Imam</span>
                </div>
            </div>
        </div>

    </div>

    <div class="table-card">

        <div class="table-header">
            <div class="table-tools">

                <div class="search-box">
                    <i class="fas fa-search"></i>

                    <input
                        type="text"
                        id="searchInput"
                        class="form-control"
                        placeholder="Cari nama imam..."
                    >
                </div>

            </div>
        </div>

        <div class="table-responsive">

            <table class="table-modern" id="dataTable">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Imam</th>
                        <th>Waktu Sholat</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($data) > 0): ?>

                        <?php $no = 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($data)): ?>

                            <tr>

                                <td>
                                    <?= $no++; ?>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars(
                                            $row['nama_guru'] ?? 'Nama guru tidak ditemukan'
                                        ); ?>
                                    </strong>
                                </td>

                                <td>
                                    <span class="nominal">
                                        <?= htmlspecialchars($row['waktu_sholat']); ?>
                                    </span>
                                </td>

                                <td>

                                    <?php
                                    $tanggal = strtotime($row['tanggal']);

                                    echo date('d', $tanggal) . ' ' .
                                        $bulan[(int)date('m', $tanggal)] . ' ' .
                                        date('Y', $tanggal);
                                    ?>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="4">

                                <div class="empty-report">

                                    <i class="bi bi-folder2-open"></i>

                                    <h5>Belum Ada Data Imam</h5>

                                    <p>
                                        Belum terdapat data jadwal imam
                                        yang tersimpan dalam sistem.
                                    </p>

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
                <strong>
                    <?= $totalJadwal['total'] ?? 0; ?>
                </strong>
            </div>

        </div>

    </div>

</div>

<script src="../assets/js/laporan.js"></script>

<?php require_once '../template/footer.php'; ?>