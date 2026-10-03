<?php
$pageTitle = 'Data Shodaqoh Jumat';
require_once '../template/header.php';

$bulanDipilih = isset($_GET['bulan']) ? (int) $_GET['bulan'] : 0;

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

$whereBulan = '';

if ($bulanDipilih >= 1 && $bulanDipilih <= 12) {
    $whereBulan = "WHERE MONTH(tanggal) = $bulanDipilih
    AND YEAR(tanggal) = YEAR(CURDATE())";
}

$totalShodaqoh = mysqli_fetch_assoc(
    mysqli_query(
    $connect,
    "SELECT COALESCE(SUM(nominal), 0) AS total FROM shodaqoh $whereBulan"
));

$totalData = mysqli_fetch_assoc(
    mysqli_query(
    $connect,"SELECT COUNT(*) AS total FROM shodaqoh $whereBulan"
));


$totalKelas = mysqli_fetch_assoc(
    mysqli_query(
    $connect,
    "SELECT COUNT(DISTINCT id_kelas) AS total FROM shodaqoh $whereBulan"
));

$data = mysqli_query(
    $connect,
    "SELECT shodaqoh.*, kelas.nama_kelas, kelas.tingkat, kelas.bagian
    FROM shodaqoh
    JOIN kelas
    ON shodaqoh.id_kelas = kelas.id_kelas
    $whereBulan
    ORDER BY
    CASE
        WHEN shodaqoh.tanggal IS NULL THEN 3
        WHEN shodaqoh.tanggal = CURDATE() THEN 0
        WHEN shodaqoh.tanggal > CURDATE() THEN 1
        ELSE 2
    END ASC,
    CASE
        WHEN shodaqoh.tanggal > CURDATE() THEN shodaqoh.tanggal
        ELSE NULL
    END ASC,
    CASE
        WHEN shodaqoh.tanggal < CURDATE() THEN shodaqoh.tanggal
        ELSE NULL
    END DESC,
    shodaqoh.id_shodaqoh DESC"
);

$dataCount = mysqli_num_rows($data);
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <?php if (isset($_GET['pesan'])): ?>
        <?php if ($_GET['pesan'] == "simpan"): ?>
            <div class="alert alert-success">
                Data shodaqoh berhasil ditambahkan.
            </div>
        <?php elseif ($_GET['pesan'] == "update"): ?>
            <div class="alert alert-success">
                Data shodaqoh berhasil diperbarui.
            </div>
        <?php elseif ($_GET['pesan'] == "hapus"): ?>
            <div class="alert alert-success">
                Data shodaqoh berhasil dihapus.
            </div>
        <?php elseif ($_GET['pesan'] == "gagal"): ?>
            <div class="alert alert-danger">
                Terjadi kesalahan.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <h3>Data Shodaqoh Jumat</h3>
            <p>Kelola seluruh data shodaqoh Jumat dari setiap kelas.</p>
        </div>

        <?php if ($isPetugas || $isAdmin): ?>
            <a href="tambah.php" class="btn-add">
                <i class="bi bi-plus-circle-fill"></i>
                Tambah Shodaqoh
            </a>
        <?php endif; ?>
    </div>

    <!-- STATS CARDS -->
    <div class="row g-4 mb-4">
        <!-- Total Shodaqoh -->
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="bi bi-coin"></i>
                </div>

                <div class="stats-info">
                    <small>
                        <?php if ($bulanDipilih == 0): ?>
                            Total Shodaqoh
                        <?php else: ?>
                            Total Shodaqoh <?= $namaBulan[$bulanDipilih]; ?>
                        <?php endif; ?>
                    </small>

                    <h2>
                        Rp<?= number_format($totalShodaqoh['total'] ?? 0, 0, ',', '.'); ?>
                    </h2>

                    <span>
                        <?php if ($bulanDipilih == 0): ?>
                            Keseluruhan
                        <?php else: ?>
                            <?= $namaBulan[$bulanDipilih]; ?> <?= date('Y'); ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Jumlah Kelas -->
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-mortarboard-fill"></i>
                </div>

                <div class="stats-info">
                    <small>Jumlah Kelas</small>
                    <h2>
                        <?= $totalKelas['total'] ?? 0; ?>
                    </h2>
                    <span>Kelas</span>
                </div>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-blue">
                    <i class="bi bi-receipt"></i>
                </div>

                <div class="stats-info">
                    <small>Total Transaksi</small>
                    <h2>
                        <?= $totalData['total'] ?? 0; ?>
                    </h2>
                    <span>Total</span>
                </div>
            </div>
        </div>
    </div>

    <!-- DATA TABLE SECTION -->
    <div class="data-card">
        <div class="data-toolbar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Cari nama kelas..."
                >
            </div>

            <form method="GET" class="filter-box">
                <select name="bulan" id="bulan" onchange="this.form.submit()">
                    <option value="0" <?= $bulanDipilih == 0 ? 'selected' : ''; ?>>Semua Bulan</option>
                    <option value="1" <?= $bulanDipilih == 1 ? 'selected' : ''; ?>>January</option>
                    <option value="2" <?= $bulanDipilih == 2 ? 'selected' : ''; ?>>February</option>
                    <option value="3" <?= $bulanDipilih == 3 ? 'selected' : ''; ?>>March</option>
                    <option value="4" <?= $bulanDipilih == 4 ? 'selected' : ''; ?>>April</option>
                    <option value="5" <?= $bulanDipilih == 5 ? 'selected' : ''; ?>>May</option>
                    <option value="6" <?= $bulanDipilih == 6 ? 'selected' : ''; ?>>June</option>
                    <option value="7" <?= $bulanDipilih == 7 ? 'selected' : ''; ?>>July</option>
                    <option value="8" <?= $bulanDipilih == 8 ? 'selected' : ''; ?>>August</option>
                    <option value="9" <?= $bulanDipilih == 9 ? 'selected' : ''; ?>>September</option>
                    <option value="10" <?= $bulanDipilih == 10 ? 'selected' : ''; ?>>October</option>
                    <option value="11" <?= $bulanDipilih == 11 ? 'selected' : ''; ?>>November</option>
                    <option value="12" <?= $bulanDipilih == 12 ? 'selected' : ''; ?>>December</option>
                </select>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kelas</th>
                        <th>Tingkat</th>
                        <th>Bagian</th>
                        <th>Nominal</th>
                        <th>Tanggal</th>
                        <?php if ($isPetugas || $isAdmin): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dataCount > 0): ?>
                        <?php 
                        $no = 1; 
                        while ($row = mysqli_fetch_assoc($data)): 
                        ?>
                            <tr>
                                <td>
                                    <?= $no++; ?>
                                </td>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row['nama_kelas']); ?>
                                    </strong>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['tingkat']); ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars($row['bagian']); ?>
                                </td>
                                <td>
                                    <span class="nominal">
                                        Rp <?= number_format($row['nominal'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('F d Y', strtotime($row['tanggal'])); ?>
                                </td>
                                <?php if ($isPetugas || $isAdmin): ?>
                                    <td>
                                        <div class="action-group">
                                            <a
                                                href="edit.php?id=<?= $row['id_shodaqoh']; ?>"
                                                class="btn-edit"
                                            >
                                                <i class="bi bi-pencil-fill"></i>
                                                Edit
                                            </a>
                                            <a
                                                href="hapus.php?id=<?= $row['id_shodaqoh']; ?>"
                                                class="btn-delete"
                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data shodaqoh ini?');"
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
                            <td colspan="<?= ($isPetugas || $isAdmin) ? '7' : '6'; ?>">
                                <div class="empty-data">
                                    <i class="bi bi-folder2-open"></i>
                                    <h4>Belum Ada Data Shodaqoh</h4>
                                    <p>
                                        <?php if ($bulanDipilih > 0): ?>
                                            Tidak ada data shodaqoh pada bulan <?= $namaBulan[$bulanDipilih]; ?>.
                                        <?php else: ?>
                                            Silakan tambahkan data shodaqoh Jumat untuk mulai mengelola data.
                                        <?php endif; ?>
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
                <strong><?= $dataCount; ?></strong>
            </div>
        </div>
    </div>

</div>

<script src="../assets/js/script.js"></script>

<?php require_once '../template/footer.php'; ?>