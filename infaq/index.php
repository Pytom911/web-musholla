    <?php
    $pageTitle = 'Data Infaq';
    require_once '../template/header.php';

    $bulanSekarang = (int) date('n');
    $tahunSekarang = (int) date('Y');

    $bulanDipilih = isset($_GET['bulan'])
        ? (int) $_GET['bulan']
        : $bulanSekarang;

    $tahunDipilih = isset($_GET['tahun'])
        ? (int) $_GET['tahun']
        : $tahunSekarang;

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
        $whereBulan = "WHERE MONTH(infaq.tanggal) = $bulanDipilih
            AND YEAR(infaq.tanggal) = $tahunDipilih";
    } else {
        $bulanDipilih = 0;
    }

    $totalBulan = mysqli_fetch_assoc(mysqli_query(
        $connect,
        "SELECT COALESCE(SUM(nominal), 0) AS total FROM infaq $whereBulan"
    ));

    $totalDonatur = mysqli_fetch_assoc(mysqli_query(
        $connect,
        "SELECT COUNT(DISTINCT NULLIF(nama_donatur, '')) AS total FROM infaq $whereBulan"
    ));

    $totalTransaksi = mysqli_fetch_assoc(mysqli_query(
        $connect,
        "SELECT COUNT(*) AS total FROM infaq $whereBulan"
    ));

    $limit = 10;
    $page = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
    if ($page < 1) {
        $page = 1;
    }

    $queryCount = mysqli_query($connect, "SELECT COUNT(*) AS total FROM infaq $whereBulan");
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
        "SELECT infaq.*, kegiatan.nama_kegiatan
        FROM infaq
        LEFT JOIN kegiatan ON kegiatan.id_kegiatan = infaq.id_kegiatan
        $whereBulan
        ORDER BY infaq.tanggal DESC, infaq.id_infaq DESC
        LIMIT $limit OFFSET $offset"
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
            <?php if ($isPetugas || $isAdmin): ?>
                <a href="tambah.php" class="btn-add">
                    <i class="bi bi-plus-circle-fill"></i>
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
                        <i class="bi bi-people-fill"></i>
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
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchInput" placeholder="Cari Nama Donatur">
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
                            <th>Kegiatan</th>
                            <th>Nominal</th>
                            <th>Tanggal</th>
                            <?php if ($isPetugas || $isAdmin): ?>
                                <th>Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($data) > 0): ?>
                            <?php 
                            $no = $offset + 1;
                            while ($row = mysqli_fetch_assoc($data)): 
                            ?>
                                <tr data-tanggal="<?= $row['tanggal']; ?>">
                                    <td><?= $no++; ?></td>
                                    <td><strong><?= $row['nama_donatur'] !== '' ? htmlspecialchars($row['nama_donatur']) : '-'; ?></strong></td>
                                    <td>
                                        <?= !empty($row['nama_kegiatan'])
                                            ? htmlspecialchars($row['nama_kegiatan'])
                                            : '<span class="text-muted">-</span>'; ?>
                                    </td>
                                    <td><span class="nominal">Rp <?= number_format($row['nominal'], 0, ',', '.'); ?></span></td>
                                    <td><?= date('F d Y', strtotime($row['tanggal'])); ?></td>
                                    <?php if ($isPetugas || $isAdmin): ?>
                                        <td>
                                            <div class="action-group">
                                                <a href="edit.php?id=<?= $row['id_infaq']; ?>" class="btn-edit">
                                                    <i class="bi bi-pencil-fill"></i> Edit
                                                </a>
                                                <a href="hapus.php?id=<?= $row['id_infaq']; ?>" class="btn-delete"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus data infaq ini?')">
                                                    <i class="bi bi-trash-fill"></i> Hapus
                                                </a>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= ($isPetugas || $isAdmin) ? '6' : '5'; ?>">
                                    <div class="empty-data">
                                        <i class="bi bi-folder2-open"></i>
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
                    <strong><?= $totalDataAll['total'] ?? 0; ?></strong>
                </div>
                <?php require __DIR__ . '/../template/pagination.php'; ?>
            </div>
        </div>

    </div>

    <script src="../assets/js/script.js"></script>

    <?php require_once '../template/footer.php'; ?>