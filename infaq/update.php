
<?php
require_once '../config/connect.php';
require_once '../auth/auth.php';

requireRole(['admin', 'petugas']);

if (
    !isset($_POST['id_infaq'], $_POST['nama_donatur'], $_POST['nominal'], $_POST['tanggal'])
) {
    header('Location: index.php');
    exit;
}

$id = (int) $_POST['id_infaq'];
$nama = trim($_POST['nama_donatur']);
$nominal = (int) $_POST['nominal'];
$tanggal = $_POST['tanggal'];

if ($id <= 0 || $nama === '' || strlen($nama) > 50 || $nominal < 1000) {
    header('Location: edit.php?id=' . $id . '&pesan=invalid');
    exit;
}

// Validasi format tanggal.
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
    header('Location: edit.php?id=' . $id . '&pesan=invalid');
    exit;
}

[$tahun, $bulan, $hari] = array_map('intval', explode('-', $tanggal));

if (!checkdate($bulan, $hari, $tahun)) {
    header('Location: edit.php?id=' . $id . '&pesan=invalid');
    exit;
}

// Pastikan tanggal sesuai bulan dan tahun sekarang.
if (date('Y-m', strtotime($tanggal)) !== date('Y-m')) {
    header('Location: edit.php?id=' . $id . '&pesan=bulan_tidak_valid');
    exit;
}

// Periksa duplikat, kecuali data yang sedang diedit.
$stmt = mysqli_prepare(
    $connect,
    "SELECT id_infaq
     FROM infaq
     WHERE nama_donatur = ?
       AND nominal = ?
       AND tanggal = ?
       AND id_infaq != ?
     LIMIT 1"
);

mysqli_stmt_bind_param($stmt, 'sisi', $nama, $nominal, $tanggal, $id);
mysqli_stmt_execute($stmt);

$hasil = mysqli_stmt_get_result($stmt);
$duplikat = mysqli_num_rows($hasil) > 0;

mysqli_stmt_close($stmt);

if ($duplikat) {
    header('Location: edit.php?id=' . $id . '&pesan=duplikat');
    exit;
}

// Simpan perubahan.
$stmt = mysqli_prepare(
    $connect,
    "UPDATE infaq
     SET nama_donatur = ?, nominal = ?, tanggal = ?
     WHERE id_infaq = ?"
);

mysqli_stmt_bind_param($stmt, 'sisi', $nama, $nominal, $tanggal, $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: index.php?pesan=update');
    exit;
}

mysqli_stmt_close($stmt);

header('Location: edit.php?id=' . $id . '&pesan=gagal');
exit;
