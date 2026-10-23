<?php
$pageTitle = 'Data Jadwal Imam';
session_start();
require_once __DIR__ . '/../template/header.php';

$bulanSekarang = (int) date('n');
$tahunSekarang = (int) date('Y');

$bulanDipilih = isset($_GET['bulan'])
    ? (int) $_GET['bulan']
    : $bulanSekarang;

// Validasi pilihan bulan
if ($bulanDipilih < 0 || $bulanDipilih > 12) {
    $bulanDipilih = $bulanSekarang;
}

// Nama bulan
$namaBulan = [
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'April',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December'
];

// Filter bulan
// Bulan 0 berarti semua bulan dan semua tahun.
$whereBulan = '';

if ($bulanDipilih >= 1 && $bulanDipilih <= 12) {
    $whereBulan = "WHERE MONTH(jadwal_imam.tanggal) = $bulanDipilih
        AND YEAR(jadwal_imam.tanggal) = $tahunSekarang";
}

// Statistik jumlah imam
$totalImam = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(DISTINCT id_guru) AS total
    FROM jadwal_imam
"));

// Statistik total jadwal
$totalJadwal = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(*) AS total
    FROM jadwal_imam
"));

// Jadwal hari ini
$totalHariIni = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(*) AS total
    FROM jadwal_imam
    WHERE tanggal = CURDATE()
"));

// Jadwal bulan ini
$totalBulan = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(*) AS total
    FROM jadwal_imam
    WHERE MONTH(tanggal) = MONTH(CURDATE())
    AND YEAR(tanggal) = YEAR(CURDATE())
"));

// Data jadwal imam berdasarkan filter bulan
$limit = 10;
$page = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
if ($page < 1) {
    $page = 1;
}

$queryCountImam = mysqli_query($connect, "SELECT COUNT(*) AS total FROM jadwal_imam $whereBulan");
$totalDataAll = mysqli_fetch_assoc($queryCountImam);
$totalHalaman = (int) ceil(($totalDataAll['total'] ?? 0) / $limit);
if ($totalHalaman < 1) {
    $totalHalaman = 1;
}
if ($page > $totalHalaman) {
    $page = $totalHalaman;
}

$offset = ($page - 1) * $limit;
$data = mysqli_query($connect, "
    SELECT
        jadwal_imam.id_imam,
        jadwal_imam.tanggal,
        jadwal_imam.waktu_sholat,
        guru.nama_guru
    FROM jadwal_imam
    LEFT JOIN guru ON jadwal_imam.id_guru = guru.id_guru
    $whereBulan
    ORDER BY jadwal_imam.tanggal ASC, jadwal_imam.id_imam ASC
    LIMIT $limit OFFSET $offset
");

// Jadwal Imam Hari Ini
$jadwalImamHariIni = mysqli_query($connect, "
    SELECT
        jadwal_imam.id_imam,
        jadwal_imam.tanggal,
        jadwal_imam.waktu_sholat,
        guru.nama_guru
    FROM jadwal_imam
    LEFT JOIN guru ON jadwal_imam.id_guru = guru.id_guru
    WHERE DATE(jadwal_imam.tanggal) = CURDATE()
    ORDER BY jadwal_imam.id_imam ASC
    LIMIT 1
");

// Jadwal Imam Besok
$jadwalImamBesok = mysqli_query($connect, "
    SELECT
        jadwal_imam.id_imam,
        jadwal_imam.tanggal,
        jadwal_imam.waktu_sholat,
        guru.nama_guru
    FROM jadwal_imam
    LEFT JOIN guru ON jadwal_imam.id_guru = guru.id_guru
    WHERE DATE(jadwal_imam.tanggal) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)
    ORDER BY jadwal_imam.id_imam ASC
    LIMIT 1
");
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <!-- Pesan Notifikasi -->
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

    <!-- Header Halaman -->
    <div class="page-header">
        <div>
            <h3>Data Jadwal Imam</h3>
            <p>Kelola seluruh data jadwal imam musholla.</p>
        </div>

        <?php if ($isPetugas OR $isAdmin): ?>
            <a href="tambah.php" class="btn-add">
                <i class="fas fa-plus-circle"></i>
                Tambah Jadwal Imam
            </a>
        <?php endif; ?>
    </div>

    <!-- Statistik -->
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

    <!-- Tabel Data -->
    <div class="data-card">

        <div class="data-toolbar">

            <div class="search-box">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari nama, waktu sholat, atau tanggal..."
                    autocomplete="off"
                >
            </div>

            <!-- Filter Bulan -->
            <form method="GET" class="filter-box">
                <select name="bulan" id="bulan" onchange="this.form.submit()">

                    <option value="0" <?= $bulanDipilih == 0 ? 'selected' : ''; ?>>
                        Semua Bulan
                    </option>

                    <?php foreach ($namaBulan as $nomor => $bulan): ?>
                        <option
                            value="<?= $nomor; ?>"
                            <?= $bulanDipilih == $nomor ? 'selected' : ''; ?>
                        >
                            <?= $bulan; ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </form>

        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Imam</th>
                        <th>Waktu Sholat</th>
                        <th>Tanggal</th>

                        <?php if ($isPetugas OR $isAdmin): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($data && mysqli_num_rows($data) > 0): ?>

                        <?php $no = $offset + 1; ?>

                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><?= $no++; ?></td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row['nama_guru'] ?? '-'); ?>
                                    </strong>
                                </td>

                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row['waktu_sholat']); ?>
                                    </strong>
                                </td>

                                <td>
                                    <?= date('F d Y', strtotime($row['tanggal'])); ?>
                                </td>

                                <?php if ($isPetugas OR $isAdmin): ?>
                                    <td>
                                        <div class="action-group">

                                            <a
                                                href="edit.php?id=<?= $row['id_imam']; ?>"
                                                class="btn-edit"
                                            >
                                                <i class="bi bi-pencil-fill"></i>
                                                Edit
                                            </a>

                                            <a
                                                href="hapus.php?id=<?= $row['id_imam']; ?>"
                                                class="btn-delete"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus jadwal imam ini?');"
                                            >
                                                <i class="bi bi-trash-fill"></i>
                                                Hapus
                                            </a>

                                        </div>
                                    </td>
                                <?php endif; ?>

                            </tr>
                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="<?= ($isPetugas OR $isAdmin) ? 5 : 4; ?>">
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

        <!-- Hasil Pencarian Kosong -->
        <div id="noSearchResult" class="text-center py-4" style="display: none;">
            <i class="fas fa-search mb-2"></i>
            <h5>Data tidak ditemukan</h5>
            <p class="text-muted mb-0">Coba gunakan kata kunci yang berbeda.</p>
        </div>

        <!-- Footer Tabel -->
        <div class="table-footer">
            <div class="table-info">
                Total Data:
                <strong id="totalData"><?= $totalDataAll['total'] ?? 0; ?></strong>
            </div>
            <?php require __DIR__ . '/../template/pagination.php'; ?>
        </div>

    </div>

</div>

<!-- Pencarian -->
<script src="../assets/js/script.js"></script>

<?php require_once __DIR__ . '/../template/footer.php'; ?>