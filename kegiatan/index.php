<?php
$pageTitle = 'Data Kegiatan';
require_once __DIR__ . '/../template/header.php';

$cari = $_GET['cari'] ?? '';

$totalKegiatan = 0;
$totalPengeluaran = 0;
$pengeluaranBulan = 0;

$qStat = mysqli_query($connect, "
    SELECT
        COUNT(*) AS total_kegiatan,
        COALESCE(SUM(pengeluaran),0) AS total_pengeluaran,
        COALESCE(SUM(
            CASE
                WHEN MONTH(tanggal)=MONTH(CURDATE())
                AND YEAR(tanggal)=YEAR(CURDATE())
                THEN pengeluaran
                ELSE 0
            END
        ),0) AS bulan_ini
    FROM kegiatan
");

if ($qStat && mysqli_num_rows($qStat) > 0) {
    $stat = mysqli_fetch_assoc($qStat);
    $totalKegiatan = $stat['total_kegiatan'];
    $totalPengeluaran = $stat['total_pengeluaran'];
    $pengeluaranBulan = $stat['bulan_ini'];
}

$sql = "SELECT * FROM kegiatan";

if ($cari != '') {
    $keyword = mysqli_real_escape_string($connect, $cari);
    $sql .= " WHERE nama_kegiatan LIKE '%$keyword%'";
}

$sql .= " ORDER BY tanggal DESC,id_kegiatan DESC";

$data = mysqli_query($connect, $sql);
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <?php if (isset($_GET['pesan'])): ?>

        <?php if ($_GET['pesan'] == "simpan"): ?>
            <div class="alert alert-success">Data kegiatan berhasil ditambahkan.</div>

        <?php elseif ($_GET['pesan'] == "update"): ?>
            <div class="alert alert-success">Data kegiatan berhasil diperbarui.</div>

        <?php elseif ($_GET['pesan'] == "hapus"): ?>
            <div class="alert alert-success">Data kegiatan berhasil dihapus.</div>

        <?php elseif ($_GET['pesan'] == "gagal"): ?>
            <div class="alert alert-danger">Terjadi kesalahan.</div>
        <?php endif; ?>

    <?php endif; ?>

    <div class="page-header">
        <div>
            <h3>Data Kegiatan</h3>
            <p>Kelola seluruh data kegiatan dan pengeluaran musholla.</p>
        </div>
        <?php if ($isPetugas OR $isAdmin): ?>
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
                    <small>Total Kegiatan</small>
                    <h2><?= $totalKegiatan; ?></h2>
                    <span>Seluruh Kegiatan</span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-red">
                    <i class="bi bi-cash-stack"></i>
                </div>

                <div class="stats-info">
                    <small>Total Pengeluaran</small>
                    <h2>Rp<?= number_format($totalPengeluaran, 0, ',', '.'); ?></h2>
                    <span>Semua Kegiatan</span>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6">
            <div class="stats-card">
                <div class="icon icon-yellow">
                    <i class="bi bi-calendar2-week-fill"></i>
                </div>

                <div class="stats-info">
                    <small>Pengeluaran Bulan Ini</small>
                    <h2>Rp<?= number_format($pengeluaranBulan, 0, ',', '.'); ?></h2>
                    <span><?= date('F Y'); ?></span>
                </div>
            </div>
        </div>

    </div>

    <div class="data-card">

        <div class="data-toolbar">

            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="Cari nama kegiatan..."
                    value="<?= htmlspecialchars($cari); ?>">
            </div>
            <!-- <div class="filter-box">
                <select id="showData" class="form-select">
                    <option value="10">10 Data</option>
                    <option value="25">25 Data</option>
                    <option value="50">50 Data</option>
                    <option value="100">100 Data</option>
                </select>
            </div> -->

        </div>

        <div class="table-responsive">

            <table class="table-modern" id="dataTable">

                <thead>
                    <tr>
                        <th width="70">No</th>
                        <th>Nama Kegiatan</th>
                        <th width="180">Pengeluaran</th>
                        <th width="170">Tanggal</th>
                        <th>Deskripsi</th>
                        <?php if ($isPetugas OR $isAdmin): ?>
                            <th width="180" class="text-center">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>

                    <?php if (mysqli_num_rows($data) > 0): ?>

                        <?php $no = 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>

                            <tr>
                                <td><?= $no++; ?></td>
                                <td><strong><?= htmlspecialchars($row['nama_kegiatan']); ?></strong></td>
                                <td><span class="nominal-red">Rp <?= number_format($row['pengeluaran'], 0, ',', '.'); ?></span>
                                </td>
                                <td><?= date('d F Y', strtotime($row['tanggal'])); ?></td>
                                <td><?= nl2br(htmlspecialchars($row['deskripsi'])); ?></td>
                                <?php if ($isPetugas OR $isAdmin): ?>
                                    <td>
                                        <div class="action-group">

                                            <a href="edit.php?id=<?= $row['id_kegiatan']; ?>" class="btn-edit">
                                                <i class="bi bi-pencil-fill"></i>
                                                Edit
                                            </a>

                                            <a href="hapus.php?id=<?= $row['id_kegiatan']; ?>" class="btn-delete"
                                                onclick="return confirm('Yakin ingin menghapus data kegiatan ini?')">
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
                            <td colspan="6">
                                <div class="empty-data">
                                    <i class="bi bi-folder2-open"></i>
                                    <h4>Belum Ada Data Kegiatan</h4>
                                    <p>Silakan tambahkan kegiatan baru untuk mulai mengelola data.</p>
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

            <!-- <nav>
                <ul class="pagination mb-0">

                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="bi bi-chevron-left"></i>
                        </span>
                    </li>

                    <li class="page-item active">
                        <span class="page-link">1</span>
                    </li>

                    <li class="page-item disabled">
                        <span class="page-link">
                            <i class="bi bi-chevron-right"></i>
                        </span>
                    </li>

                </ul>
            </nav> -->

        </div>

    </div>

</div>

<script src="../assets/js/data.js"></script>

<?php require_once __DIR__ . '/../template/footer.php'; ?>