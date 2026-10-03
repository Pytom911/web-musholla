<?php
$pageTitle = 'Data Infaq';
require_once '../template/header.php';

$bulanDipilih = isset($_GET['bulan']) ? (int) $_GET['bulan'] : 0;
$whereBulan = '';
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

if ($bulanDipilih >= 1 && $bulanDipilih <= 12) {
    $whereBulan = "WHERE MONTH(tanggal) = $bulanDipilih
    AND YEAR(tanggal) = YEAR(CURDATE())";
}

$totalBulan = mysqli_fetch_assoc(mysqli_query(
    $connect, 
    "SELECT SUM(nominal) AS total FROM infaq $whereBulan"
));

$totalDonatur = mysqli_fetch_assoc(mysqli_query(
    $connect, 
    "SELECT COUNT(DISTINCT nama_donatur) AS total FROM infaq"
));

$totalTransaksi = mysqli_fetch_assoc(mysqli_query(
    $connect, 
    "SELECT COUNT(*) AS total FROM infaq"
));

$data = mysqli_query(
    $connect, 
    "SELECT * FROM infaq $whereBulan ORDER BY tanggal DESC, id_infaq DESC"
);
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <?php if (isset($_GET['pesan'])): ?>
        <?php if ($_GET['pesan'] == "simpan"): ?>
            <div class="alert alert-success">Data infaq berhasil ditambahkan.</div>
        <?php elseif ($_GET['pesan'] == "update"): ?>
            <div class="alert alert-success">Data infaq berhasil diperbarui.</div>
        <?php elseif ($_GET['pesan'] == "hapus"): ?>
            <div class="alert alert-success">Data infaq berhasil dihapus.</div>
        <?php elseif ($_GET['pesan'] == "gagal"): ?>
            <div class="alert alert-danger">Terjadi kesalahan.</div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <h3>Data Infaq</h3>
            <p>Kelola seluruh data infaq musholla.</p>
        </div>

        <?php if ($isPetugas OR $isAdmin): ?>
            <a href="tambah.php" class="btn-add">
                <i class="fas fa-plus-circle"></i>
                Tambah Infaq
            </a>
        <?php endif; ?>
    </div>

    <!-- STATS CARDS -->
    <div class="row g-4 mb-4">
        <!-- Total Infaq -->
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="bi bi-box2-heart"></i>
                </div>

                <div class="stats-info">
                    <small>
                        <?php if ($bulanDipilih == 0): ?>
                            Total Infaq
                        <?php else: ?>
                            Total Infaq <?= $namaBulan[$bulanDipilih]; ?>
                        <?php endif; ?>
                    </small>

                    <h2>
                        Rp<?= number_format($totalBulan['total'] ?? 0, 0, ',', '.') ?>
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

        <!-- Jumlah Donatur -->
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="fas fa-users"></i>
                </div>

                <div class="stats-info">
                    <small>Jumlah Donatur</small>
                    <h2><?= $totalDonatur['total'] ?? 0 ?></h2>
                    <span>Orang</span>
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
                    <h2><?= $totalTransaksi['total'] ?? 0 ?></h2>
                    <span>Data</span>
                </div>
            </div>
        </div>
    </div>

    <!-- DATA TABLE SECTION -->
    <div class="data-card">
        <div class="data-toolbar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama donatur...">
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
                        <th>Nama Donatur</th>
                        <th>Nominal</th>
                        <th>Tanggal</th>
                        <?php if ($isPetugas OR $isAdmin): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php 
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($data)): 
                        ?>
                            <tr data-tanggal="<?= $row['tanggal']; ?>">
                                <td><?= $no++; ?></td>
                                <td><strong><?= htmlspecialchars($row['nama_donatur']); ?></strong></td>
                                <td><span class="nominal">Rp <?= number_format($row['nominal'], 0, ',', '.'); ?></span></td>
                                <td><?= date('F d Y', strtotime($row['tanggal'])); ?></td>
                                <?php if ($isPetugas OR $isAdmin): ?>
                                    <td>
                                        <div class="action-group">
                                            <a href="edit.php?id=<?= $row['id_infaq']; ?>" class="btn-edit">
                                                <i class="fas fa-pen"></i> Edit
                                            </a>
                                            <a href="hapus.php?id=<?= $row['id_infaq']; ?>" class="btn-delete"
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus data infaq ini?')">
                                                <i class="fas fa-trash"></i> Hapus
                                            </a>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-data">
                                    <i class="fas fa-folder-open"></i>
                                    <h4>Belum Ada Data Infaq</h4>
                                    <p>Silakan klik tombol <b>Tambah Infaq</b> untuk menambahkan data.</p>
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

<script src="../assets/js/script.js"></script>

<?php require_once '../template/footer.php'; ?>