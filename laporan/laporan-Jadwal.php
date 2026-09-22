<?php
$pageTitle = 'Laporan Jadwal Sholat';
require_once '../template/header.php';

/*
|--------------------------------------------------------------------------
| DATA STATISTIK
|--------------------------------------------------------------------------
*/

// Total jadwal
$totalJadwal = mysqli_fetch_assoc(mysqli_query(
    $connect,
    "SELECT COUNT(*) AS total FROM jadwal_sholat"
));

// Total hari
$totalHari = mysqli_fetch_assoc(mysqli_query(
    $connect,
    "SELECT COUNT(DISTINCT hari) AS total FROM jadwal_sholat"
));

// Total kelas yang memiliki jadwal
$totalKelas = mysqli_fetch_assoc(mysqli_query(
    $connect,
    "SELECT COUNT(DISTINCT id_kelas) AS total FROM jadwal_sholat"
));


/*
|--------------------------------------------------------------------------
| DATA JADWAL SHOLAT
|--------------------------------------------------------------------------
|
| JOIN dengan tabel kelas agar id_kelas ditampilkan sebagai nama kelas.
|
*/

$data = mysqli_query(
    $connect,
    "SELECT 
        jadwal_sholat.id_jadwal,
        jadwal_sholat.hari,
        jadwal_sholat.waktu_sholat,
        jadwal_sholat.id_kelas,
        kelas.nama_kelas,
        kelas.tingkat
    FROM jadwal_sholat
    LEFT JOIN kelas 
        ON jadwal_sholat.id_kelas = kelas.id_kelas
    ORDER BY FIELD(jadwal_sholat.hari, 'Senin','Selasa','Rabu','Kamis','Jumat'), jadwal_sholat.id_jadwal DESC"
);


?>

<link rel="stylesheet" href="../assets/css/laporan.css">
<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <!-- HEADER -->
    <div class="laporan-header">
        <div>
            <h3>Laporan Jadwal Sholat</h3>
            <p>Seluruh data jadwal sholat berdasarkan hari dan kelas.</p>
        </div>

        <a href="export/export-Jadwal.php"
           target="_blank"
           class="btn-export">
            <i class="bi bi-file-earmark-pdf-fill"></i>
            Export PDF
        </a>
    </div>


    <!-- STATISTIK -->
    <div class="row g-4 mb-4">

        <!-- TOTAL JADWAL -->
        <div class="col-xl-12 col-md-6">
            <div class="stats-card">

                <div class="icon icon-green">
                    <i class="bi bi-calendar-check"></i>
                </div>

                <div class="stats-info">
                    <small>Total Jadwal</small>

                    <h2>
                        <?= $totalJadwal['total'] ?? 0; ?>
                    </h2>

                    <span>Jadwal Sholat</span>
                </div>

            </div>
        </div>

    </div>


    <!-- TABLE -->
    <div class="table-card">

        <div class="table-header">

            <div class="table-tools">

                <div class="search-box">

                    <i class="fas fa-search"></i>

                    <input
                        type="text"
                        id="searchInput"
                        class="form-control"
                        placeholder="Cari jadwal, kelas, atau hari..."
                    >

                </div>

            </div>

        </div>


        <div class="table-responsive">

            <table class="table-modern" id="dataTable">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Hari</th>
                        <th>Waktu Sholat</th>
                        <th>Jurusan</th>
                        <th>Tingkat</th>
                    </tr>
                </thead>


                <tbody>

                    <?php if (mysqli_num_rows($data) > 0): ?>

                        <?php $no = 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($data)): ?>

                            <tr>

                                <!-- NO -->
                                <td>
                                    <?= $no++; ?>
                                </td>


                                <!-- HARI -->
                                <td>
                                    <strong>
                                        <?= htmlspecialchars(
                                            $row['hari']
                                        ); ?>
                                    </strong>

                                </td>


                                <!-- WAKTU SHOLAT -->
                                <td>
                                    <strong>
                                    <?php
                                    $waktu = strtolower($row['waktu_sholat']);

                                    $namaSholat = [
                                        'dzhuhur' => 'Dzuhur',
                                        'dzuhur'  => 'Dzuhur',
                                        'ashar'   => 'Ashar',
                                        'maghrib' => 'Maghrib',
                                        'isya'    => 'Isya',
                                        'subuh'   => 'Subuh'
                                    ];

                                    echo htmlspecialchars(
                                        $namaSholat[$waktu] ?? ucfirst($row['waktu_sholat'])
                                    );
                                    ?>
                                    </strong>

                                </td>


                                <!-- KELAS -->
                                <td>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $row['nama_kelas'] ?? 'Kelas Tidak Ditemukan'
                                        ); ?>
                                    </strong>

                                </td>


                                <!-- TINGKAT -->
                                <td>

                                    <strong>
                                        <?= htmlspecialchars(
                                            $row['tingkat'] ?? '-'
                                        ); ?>
                                    </strong>

                                </td>


                            </tr>

                        <?php endwhile; ?>


                    <?php else: ?>

                        <tr>

                            <td colspan="6">

                                <div class="empty-report">

                                    <i class="bi bi-calendar-x"></i>

                                    <h5>Belum Ada Jadwal Sholat</h5>

                                    <p>
                                        Belum terdapat data jadwal sholat
                                        yang tersimpan dalam sistem.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>


        <!-- FOOTER TABLE -->
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