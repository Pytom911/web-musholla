<?php
$pageTitle = 'Data Jadwal Sholat';
require_once __DIR__ . '/../template/header.php';

$tahunSekarang = (int) date('Y');

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

/* =========================
   PARAMETER FILTER
========================= */

// Filter bulan (0 = semua bulan)
$bulanDipilih = isset($_GET['bulan']) ? (int) $_GET['bulan'] : 0;

if ($bulanDipilih < 0 || $bulanDipilih > 12) {
    $bulanDipilih = 0;
}

// Opsi waktu sholat diambil dari enum database
$waktuSholatTersedia = ['Dzuhur', 'Ashar'];
$enumStmt = $connect->prepare(
    "SELECT COLUMN_TYPE
     FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'jadwal_sholat'
       AND COLUMN_NAME = 'waktu_sholat'"
);
$enumStmt->execute();
$enumRow = $enumStmt->get_result()->fetch_assoc();

if ($enumRow && preg_match("/^enum\('(.*)'\)$/i", $enumRow['COLUMN_TYPE'], $m)) {
    $waktuSholatTersedia = explode("','", $m[1]);
}

// Filter waktu sholat ('' = semua waktu)
$waktuDipilih = isset($_GET['waktu_sholat']) ? (string) $_GET['waktu_sholat'] : '';

if (!in_array($waktuDipilih, $waktuSholatTersedia, true)) {
    $waktuDipilih = '';
}

// Daftar kelas untuk dropdown (dikelompokkan per jurusan)
$daftarKelas = [];
$stmtDaftarKelas = $connect->prepare(
    "SELECT id_kelas, nama_kelas, tingkat, bagian
     FROM kelas
     ORDER BY nama_kelas ASC,
         CASE tingkat
             WHEN 'X' THEN 1
             WHEN 'XI' THEN 2
             WHEN 'XII' THEN 3
             ELSE 4
         END ASC,
         bagian ASC"
);
$stmtDaftarKelas->execute();
$resultDaftarKelas = $stmtDaftarKelas->get_result();

while ($rowKelas = $resultDaftarKelas->fetch_assoc()) {
    $daftarKelas[] = $rowKelas;
}

// Filter kelas (0 = semua kelas)
$idKelasDipilih = isset($_GET['id_kelas']) ? (int) $_GET['id_kelas'] : 0;
$idKelasValid = array_map('intval', array_column($daftarKelas, 'id_kelas'));

if (!in_array($idKelasDipilih, $idKelasValid, true)) {
    $idKelasDipilih = 0;
}

/* =========================
   SUSUN WHERE (prepared)
========================= */

$conditions = [];
$jenisBind = '';
$nilaiBind = [];

if ($bulanDipilih >= 1 && $bulanDipilih <= 12) {
    $conditions[] = "jadwal_sholat.tanggal IS NOT NULL
        AND MONTH(jadwal_sholat.tanggal) = ?
        AND YEAR(jadwal_sholat.tanggal) = ?";
    $jenisBind .= 'ii';
    $nilaiBind[] = $bulanDipilih;
    $nilaiBind[] = $tahunSekarang;
}

if ($waktuDipilih !== '') {
    $conditions[] = "jadwal_sholat.waktu_sholat = ?";
    $jenisBind .= 's';
    $nilaiBind[] = $waktuDipilih;
}

if ($idKelasDipilih > 0) {
    $conditions[] = "jadwal_sholat.id_kelas = ?";
    $jenisBind .= 'i';
    $nilaiBind[] = $idKelasDipilih;
}

$whereSql = $conditions !== []
    ? 'WHERE ' . implode(' AND ', $conditions)
    : '';

/* =========================
   TOTAL SESUAI FILTER
========================= */

$stmtTotal = $connect->prepare(
    "SELECT COUNT(*) AS total FROM jadwal_sholat $whereSql"
);

if ($jenisBind !== '') {
    $stmtTotal->bind_param($jenisBind, ...$nilaiBind);
}

$stmtTotal->execute();
$totalJadwal = $stmtTotal->get_result()->fetch_assoc();

/* =========================
   DATA SESUAI FILTER
========================== */

$limit = 10;
$page = isset($_GET['halaman']) ? (int) $_GET['halaman'] : 1;
if ($page < 1) {
    $page = 1;
}

$totalDataAll = (int) ($totalJadwal['total'] ?? 0);
$totalHalaman = (int) ceil($totalDataAll / $limit);
if ($totalHalaman < 1) {
    $totalHalaman = 1;
}
if ($page > $totalHalaman) {
    $page = $totalHalaman;
}

$offset = ($page - 1) * $limit;

// Saat bulan tertentu dipilih, urutkan per tanggal.
// Saat "Semua Bulan", urutkan: hari ini -> mendatang -> lampau.
$urutanSql = ($bulanDipilih >= 1 && $bulanDipilih <= 12)
    ? "jadwal_sholat.tanggal ASC,
       FIELD(jadwal_sholat.waktu_sholat, 'Dzuhur', 'Ashar'),
       jadwal_sholat.id_jadwal DESC"
    : "CASE
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
       jadwal_sholat.id_jadwal DESC";

$stmtData = $connect->prepare(
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
     $whereSql
     ORDER BY $urutanSql
     LIMIT ? OFFSET ?"
);

$jenisBindData = $jenisBind . 'ii';
$nilaiBindData = array_merge($nilaiBind, [$limit, $offset]);
$stmtData->bind_param($jenisBindData, ...$nilaiBindData);

$stmtData->execute();
$data = $stmtData->get_result();
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
                    <span><?= $whereSql !== '' ? 'Data (Sesuai Filter)' : 'Data' ?></span>
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
            <form method="GET" class="filter-box">
                <select name="bulan" id="bulan" onchange="this.form.submit()">
                    <option value="0" <?= $bulanDipilih == 0 ? 'selected' : ''; ?>>Semua Bulan</option>
                    <?php foreach ($namaBulan as $nomorBulan => $labelBulan): ?>
                        <option value="<?= $nomorBulan ?>" <?= $bulanDipilih === $nomorBulan ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($labelBulan, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="waktu_sholat" id="waktu_sholat" onchange="this.form.submit()">
                    <option value="">Semua Waktu</option>
                    <?php foreach ($waktuSholatTersedia as $waktu): ?>
                        <option value="<?= htmlspecialchars($waktu, ENT_QUOTES, 'UTF-8') ?>"
                            <?= $waktuDipilih === $waktu ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($waktu, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="id_kelas" id="id_kelas" onchange="this.form.submit()">
                    <option value="0">Semua Kelas</option>
                    <?php
                    $jurusanAktif = '';
                    foreach ($daftarKelas as $rowKelasFilter):
                        $jurusan = $rowKelasFilter['nama_kelas'];

                        if ($jurusanAktif !== $jurusan) {
                            if ($jurusanAktif !== '') {
                                echo '</optgroup>';
                            }

                            echo '<optgroup label="' . htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') . '">';
                            $jurusanAktif = $jurusan;
                        }

                        $labelKelas = $rowKelasFilter['nama_kelas'] . ' - ' . $rowKelasFilter['tingkat'];

                        if ($rowKelasFilter['bagian'] !== null && $rowKelasFilter['bagian'] !== '') {
                            $labelKelas .= ' (' . $rowKelasFilter['bagian'] . ')';
                        }
                    ?>
                        <option value="<?= (int) $rowKelasFilter['id_kelas']; ?>"
                            <?= $idKelasDipilih === (int) $rowKelasFilter['id_kelas'] ? 'selected' : ''; ?>>
                            <?= htmlspecialchars($labelKelas, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>

                    <?php if ($jurusanAktif !== ''): ?>
                        </optgroup>
                    <?php endif; ?>
                </select>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table-modern" id="dataTable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Jurusan</th>
                        <th>Tingkat</th>
                        <th>Bagian</th>
                        <th>Hari</th>
                        <th>Waktu Sholat</th>
                        <th>Tanggal</th>
                        <?php if ($canManage): ?>
                            <th>Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dataCount > 0): ?>
                        <?php $no = $offset + 1; ?>
                        <?php while ($row = mysqli_fetch_assoc($data)): ?>
                            <?php
                            $timestampTanggal = !empty($row['tanggal'])
                                ? strtotime($row['tanggal'])
                                : false;
                            $tanggal = $timestampTanggal
                                ? date('F d Y', $timestampTanggal)
                                : '-';
                            $hari = $timestampTanggal
                                ? hari_indonesia($row['tanggal'])
                                : '-';

                            $badgeClassJs = preg_replace(
                                '/[^a-zA-Z0-9_-]/',
                                '-',
                                (string) $row['nama_kelas']
                            );
                            $badgeClassJs = $badgeClassJs !== '' ? $badgeClassJs : 'kelas';
                            ?>
                                <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <span class="role-badge JURUSAN-<?= htmlspecialchars($badgeClassJs, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= htmlspecialchars((string) $row['nama_kelas'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars((string) $row['tingkat'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td>
                                    <?= $row['bagian'] !== null && $row['bagian'] !== ''
                                        ? htmlspecialchars((string) $row['bagian'], ENT_QUOTES, 'UTF-8')
                                        : '-' ?>
                                </td>
                                <td><strong><?= htmlspecialchars($hari, ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><strong><?= htmlspecialchars((string) $row['waktu_sholat'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><strong><?= htmlspecialchars($tanggal, ENT_QUOTES, 'UTF-8') ?></strong></td>
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
                <strong><?= $totalDataAll ?></strong>
            </div>
            <?php require __DIR__ . '/../template/pagination.php'; ?>
        </div>
    </div>
</div>

<script src="<?= asset('js/script.js') ?>"></script>
<?php require_once __DIR__ . '/../template/footer.php'; ?>
