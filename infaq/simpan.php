
<?php
require_once '../config/connect.php';
require_once '../auth/auth.php';

requireRole(['admin', 'petugas']);

if (
    !isset($_POST['nama_donatur'], $_POST['nominal'], $_POST['tanggal'])
) {
    header('Location: tambah.php');
    exit;
}

$nama = trim($_POST['nama_donatur']);
$nominal = (int) $_POST['nominal'];
$tanggal = $_POST['tanggal'];

if (
    $nama === '' ||
    strlen($nama) > 50 ||
    $nominal < 1000 ||
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)
) {
    header('Location: tambah.php?pesan=invalid');
    exit;
}

// Pastikan tanggal benar-benar valid.
[$tahun, $bulan, $hari] = array_map('intval', explode('-', $tanggal));

if (!checkdate($bulan, $hari, $tahun)) {
    header('Location: tambah.php?pesan=invalid');
    exit;
}

// Batasi input hanya untuk bulan dan tahun sekarang.
if (date('Y-m', strtotime($tanggal)) !== date('Y-m')) {
    header('Location: tambah.php?pesan=bulan_tidak_valid');
    exit;
}

// Periksa apakah data yang sama sudah tersedia.
$stmt = mysqli_prepare(
    $connect,
    "SELECT id_infaq
     FROM infaq
     WHERE nama_donatur = ?
       AND nominal = ?
       AND tanggal = ?
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, 'sis', $nama, $nominal, $tanggal);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$duplikat = mysqli_num_rows($hasil) > 0;

mysqli_stmt_close($stmt);

if ($duplikat) {
    header('Location: tambah.php?pesan=duplikat');
    exit;
}

// Simpan data jika tanggal dan data sudah valid.
$stmt = mysqli_prepare(
    $connect,
    "INSERT INTO infaq (nama_donatur, nominal, tanggal)
     VALUES (?, ?, ?)"
);

mysqli_stmt_bind_param($stmt, 'sis', $nama, $nominal, $tanggal);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: index.php?pesan=simpan');
    exit;
}

mysqli_stmt_close($stmt);

header('Location: tambah.php?pesan=gagal');
exit;
