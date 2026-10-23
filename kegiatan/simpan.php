
<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../auth/auth.php';

requireRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$nama = trim($_POST['nama_kegiatan'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$pengeluaranInput = $_POST['pengeluaran'] ?? '';
$tanggal = $_POST['tanggal'] ?? '';

if (
    $nama === '' ||
    $deskripsi === '' ||
    $pengeluaranInput === '' ||
    $tanggal === '' ||
    mb_strlen($nama) > 50 ||
    !is_numeric($pengeluaranInput) ||
    !is_finite((float) $pengeluaranInput) ||
    (float) $pengeluaranInput < 0
) {
    header('Location: tambah.php?pesan=invalid');
    exit;
}

$nominal = (float) $pengeluaranInput;

// Validasi format tanggal.
$tanggalValid = DateTime::createFromFormat('!Y-m-d', $tanggal);
$kesalahanTanggal = DateTime::getLastErrors();

if (
    !$tanggalValid ||
    ($kesalahanTanggal !== false &&
        (
            $kesalahanTanggal['warning_count'] > 0 ||
            $kesalahanTanggal['error_count'] > 0
        )) ||
    $tanggalValid->format('Y-m-d') !== $tanggal
) {
    header('Location: tambah.php?pesan=invalid');
    exit;
}

// Tanggal harus berada pada bulan dan tahun sekarang.
if ($tanggalValid->format('Y-m') !== date('Y-m')) {
    header('Location: tambah.php?pesan=bulan_tidak_valid');
    exit;
}

// Arus kas bulan berjalan, sama dengan saldo keuangan di dashboard.
$querySaldo = $connect->query(
    "SELECT
        (
            (SELECT COALESCE(SUM(nominal), 0) FROM infaq WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()))
            +
            (SELECT COALESCE(SUM(nominal), 0) FROM shodaqoh WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()))
            -
            (SELECT COALESCE(SUM(pengeluaran), 0) FROM kegiatan WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()))
        ) AS saldo"
);

if (!$querySaldo) {
    header('Location: tambah.php?pesan=gagal');
    exit;
}

$dataSaldo = $querySaldo->fetch_assoc();
$saldoTersedia = (float) ($dataSaldo['saldo'] ?? 0);
$querySaldo->free();

// Tolak jika saldo tidak cukup.
if ($nominal > $saldoTersedia) {
    header('Location: tambah.php?pesan=saldo_tidak_cukup');
    exit;
}

// Periksa duplikat.
$cek = $connect->prepare(
    'SELECT id_kegiatan
     FROM kegiatan
     WHERE nama_kegiatan = ?
       AND pengeluaran = ?
       AND tanggal = ?
     LIMIT 1'
);

if (!$cek) {
    header('Location: tambah.php?pesan=gagal');
    exit;
}

$cek->bind_param('sds', $nama, $nominal, $tanggal);
$cek->execute();

$duplikat = $cek->get_result()->num_rows > 0;
$cek->close();

if ($duplikat) {
    header('Location: tambah.php?pesan=duplikat');
    exit;
}

// Simpan setelah semua validasi berhasil.
$stmt = $connect->prepare(
    'INSERT INTO kegiatan
        (nama_kegiatan, deskripsi, pengeluaran, tanggal)
     VALUES (?, ?, ?, ?)'
);

if (!$stmt) {
    header('Location: tambah.php?pesan=gagal');
    exit;
}

$stmt->bind_param('ssds', $nama, $deskripsi, $nominal, $tanggal);

$berhasil = $stmt->execute();
$stmt->close();

if ($berhasil) {
    header('Location: index.php?pesan=simpan');
    exit;
}

header('Location: tambah.php?pesan=gagal');
exit;
