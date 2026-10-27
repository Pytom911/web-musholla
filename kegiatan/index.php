<?php
$pageTitle = 'Data Kegiatan';
require_once '../template/header.php';


$bulanSekarang = (int) date('n');
$tahunSekarang = (int) date('Y');

$bulanDipilih = isset($_GET['bulan'])
    ? (int) $_GET['bulan']
    : $bulanSekarang;

if ($bulanDipilih < 0 || $bulanDipilih > 12) {
    $bulanDipilih = $bulanSekarang;
}

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
        AND YEAR(tanggal) = $tahunSekarang";
}


$totalKegiatan = mysqli_fetch_assoc(mysqli_query(
    $connect,
    "SELECT COUNT(*) AS total FROM kegiatan $whereBulan"
));

$totalPengeluaran = mysqli_fetch_assoc(mysqli_query(
    $connect,
    "SELECT COALESCE(SUM(pengeluaran), 0) AS total FROM kegiatan $whereBulan"
));

// Pemasukan kegiatan = total infaq yang terhubung ke kegiatan.
if (!$whereBulan) {
    $totalPemasukanKegiatan = mysqli_fetch_assoc(mysqli_query(
        $connect,
        "SELECT COALESCE(SUM(nominal), 0) AS total
         FROM infaq
         WHERE id_kegiatan IS NOT NULL"
    ));
} else {
    $totalPemasukanKegiatan = mysqli_fetch_assoc(mysqli_query(
        $connect,
        "SELECT COALESCE(SUM(infaq.nominal), 0) AS total
         FROM infaq
         JOIN kegiatan ON kegiatan.id_kegiatan = infaq.id_kegiatan
         WHERE MONTH(kegiatan.tanggal) = $bulanDipilih AND YEAR(kegiatan.tanggal) = $tahunSekarang"
    ));
}

// Arus kas bulan terpilih, sama dengan saldo keuangan di dashboard.
// Pemasukan kegiatan berasal dari infaq, jadi tidak ditambahkan lagi agar tidak terhitung dua kali.
if (!$whereBulan) {
    $ringkasSaldo = mysqli_fetch_assoc(mysqli_query(
        $connect,
        "SELECT
            (
                (SELECT COALESCE(SUM(nominal), 0) FROM infaq)
                +
                (SELECT COALESCE(SUM(nominal), 0) FROM shodaqoh)
                -
                (SELECT COALESCE(SUM(pengeluaran), 0) FROM kegiatan)
            ) AS saldo"
    ));
} else {
    $ringkasSaldo = mysqli_fetch_assoc(mysqli_query(
        $connect,
        "SELECT
            (
                (SELECT COALESCE(SUM(nominal), 0) FROM infaq WHERE MONTH(tanggal) = $bulanDipilih AND YEAR(tanggal) = $tahunSekarang)
                +
                (SELECT COALESCE(SUM(nominal), 0) FROM shodaqoh WHERE MONTH(tanggal) = $bulanDipilih AND YEAR(tanggal) = $tahunSekarang)
                -
                (SELECT COALESCE(SUM(pengeluaran), 0) FROM kegiatan WHERE MONTH(tanggal) = $bulanDipilih AND YEAR(tanggal) = $tahunSekarang)
            ) AS saldo"
    ));
}

$saldoTersedia = (float) ($ringkasSaldo['saldo'] ?? 0);

$limit = 10;
$page = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
if ($page < 1) {
    $page = 1;
}

$queryCount = mysqli_query($connect, "SELECT COUNT(*) AS total FROM kegiatan $whereBulan");
$totalDataAll = mysqli_fetch_assoc($queryCount);
$totalHalaman = ceil(($totalDataAll['total'] ?? 0) / $limit);
if ($totalHalaman < 1) {
    $totalHalaman = 1;
}
if ($page > $totalHalaman) {
    $page = $totalHalaman;
}

$offset = ($page - 1) * $limit;

$data = mysqli_query(
    $connect,
    "SELECT k.*, COALESCE((SELECT SUM(i.nominal) FROM infaq i WHERE i.id_kegiatan = k.id_kegiatan), 0) AS pemasukan 
     FROM kegiatan k 
     $whereBulan 
     ORDER BY k.tanggal DESC, k.id_kegiatan DESC 
     LIMIT $limit OFFSET $offset"
);
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <?php if (isset($_GET['pesan'])): ?>
        <?php if ($_GET['pesan'] == "simpan"): ?>
            <div class="alert alert-success">
                Data kegiatan berhasil ditambahkan.
            </div>
        <?php elseif ($_GET['pesan'] == "update"): ?>
            <div class="alert alert-success">
                Data kegiatan berhasil diperbarui.
            </div>
        <?php elseif ($_GET['pesan'] == "hapus"): ?>
            <div class="alert alert-success">
                Data kegiatan berhasil dihapus.
            </div>
        <?php elseif ($_GET['pesan'] == "gagal"): ?>
            <div class="alert alert-danger">
                Terjadi kesalahan.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($saldoTersedia < 0): ?>
        <div class="alert alert-danger" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Saldo tidak mencukupi — total pengeluaran kegiatan melebihi total infaq &amp; shodaqoh sebesar
            <strong>Rp<?= number_format(abs($saldoTersedia), 0, ',', '.'); ?></strong>.
        </div>
    <?php endif; ?>

    <div class="page-header">
        <div>
            <h3>Data Kegiatan</h3>
            <p>Kelola seluruh data kegiatan dan pengeluaran musholla.</p>
        </div>

        <?php if ($isPetugas || $isAdmin): ?>
            <a href="tambah.php" class="btn-add">
                <i class="bi bi-plus-circle-fill"></i>
                Tambah Kegiatan
            </a>
        <?php endif; ?>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-green">
                    <i class="bi bi-calendar-event-fill"></i>
                </div>

                <div class="stats-info">
                    <small>
                        <?php if ($bulanDipilih == 0): ?>
                            Total Kegiatan
                        <?php else: ?>
                            Total Kegiatan <?= $namaBulan[$bulanDipilih]; ?>
                        <?php endif; ?>
                    </small>

                    <h2>
                        <?= $totalKegiatan['total'] ?? 0; ?>
                    </h2>

                    <span>
                        <?php if ($bulanDipilih == 0): ?>
                            Seluruh Kegiatan
                        <?php else: ?>
                            <?= $namaBulan[$bulanDipilih]; ?> <?= $tahunSekarang; ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-cash-stack"></i>
                </div>

                <div class="stats-info">
                    <small>
                        <?php if ($bulanDipilih == 0): ?>
                            Total Pengeluaran
                        <?php else: ?>
                            Total Pengeluaran <?= $namaBulan[$bulanDipilih]; ?>
                        <?php endif; ?>
                    </small>

                    <h2>
                        Rp<?= number_format($totalPengeluaran['total'] ?? 0, 0, ',', '.'); ?>
                    </h2>

                    <span>
                        <?php if ($bulanDipilih == 0): ?>
                            Semua Kegiatan
                        <?php else: ?>
                            <?= $namaBulan[$bulanDipilih]; ?> <?= date('Y'); ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-blue">
                    <i class="bi bi-wallet2"></i>
                </div>

                <div class="stats-info">
                    <small>Saldo Tersedia</small>

                    <h2>
                        <?php if ($saldoTersedia < 0): ?>
                            <span class="text-danger">-Rp<?= number_format(abs($saldoTersedia), 0, ',', '.'); ?></span>
                        <?php else: ?>
                            Rp<?= number_format($saldoTersedia, 0, ',', '.'); ?>
                        <?php endif; ?>
                    </h2>

                    <span>
                        <?php if ($bulanDipilih == 0): ?>
                            Keseluruhan
                        <?php else: ?>
                            Arus kas <?= $namaBulan[$bulanDipilih]; ?> <?= $tahunSekarang; ?>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="data-card">
        <div class="data-toolbar">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama kegiatan...">
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
                        <th>Nama Kegiatan</th>
                        <th>Pemasukan</th>
                        <th>Pengeluaran</th>
                        <th>Deskripsi</th>
                        <th>Tanggal</th>
                        <?php if ($isPetugas || $isAdmin): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($data) > 0): ?>
                        <?php $no = $offset + 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr data-tanggal="<?= htmlspecialchars($row['tanggal']); ?>">
                                <td>
                                    <?= $no++; ?>
                                </td>
                                <td>
                                    <strong>
                                        <?= htmlspecialchars($row['nama_kegiatan']); ?>
                                    </strong>
                                </td>
                                <td>
                                    <span class="nominal">
                                        Rp <?= number_format($row['pemasukan'] ?? 0, 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="nominal-red">
                                        Rp <?= number_format($row['pengeluaran'] ?? 0, 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td class="deskripsi-cell">
                                    <div class="deskripsi-text">
                                        <?= nl2br(htmlspecialchars($row['deskripsi'])); ?>
                                    </div>
                                </td>
                                <td>
                                    <?= date('F d Y', strtotime($row['tanggal'])); ?>
                                </td>
                                <?php if ($isPetugas || $isAdmin): ?>
                                    <td>
                                        <div class="action-group">
                                            <a href="edit.php?id=<?= $row['id_kegiatan']; ?>" class="btn-edit">
                                                <i class="bi bi-pencil-fill"></i>
                                                Edit
                                            </a>
                                            <a href="hapus.php?id=<?= $row['id_kegiatan']; ?>" class="btn-delete" onclick="return confirm('Apakah Anda yakin ingin menghapus kegiatan ini?');">
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
                                    <h4>Belum Ada Data Kegiatan</h4>
                                    <p>
                                        <?php if ($bulanDipilih == 0): ?>
                                            Silakan tambahkan kegiatan baru untuk mulai mengelola data.
                                        <?php else: ?>
                                            Tidak ada data kegiatan pada bulan
                                            <b><?= $namaBulan[$bulanDipilih]; ?></b>.
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
                <strong><?= $totalDataAll['total'] ?? 0; ?></strong>
            </div>
            <?php require __DIR__ . '/../template/pagination.php'; ?>
        </div>
    </div>

</div>

<script src="../assets/js/script.js"></script>

<?php require_once '../template/footer.php'; ?>