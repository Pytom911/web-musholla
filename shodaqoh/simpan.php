
<?php
require_once '../config/connect.php';
require_once '../auth/auth.php';

requireRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$tanggal = trim($_POST['tanggal'] ?? '');
$id_kelas = filter_input(INPUT_POST, 'id_kelas', FILTER_VALIDATE_INT);
$nominal = filter_input(INPUT_POST, 'nominal', FILTER_VALIDATE_INT);

if (
    $tanggal === '' ||
    !$id_kelas ||
    !$nominal ||
    $nominal < 1000
) {
    header('Location: tambah.php?pesan=invalid');
    exit;
}

$formatTanggal = DateTime::createFromFormat('!Y-m-d', $tanggal);
$validTanggal = $formatTanggal
    && $formatTanggal->format('Y-m-d') === $tanggal;

if (!$validTanggal) {
    header('Location: tambah.php?pesan=invalid');
    exit;
}

if ($formatTanggal->format('Y-m') !== date('Y-m')) {
    header('Location: tambah.php?pesan=bulan_tidak_valid');
    exit;
}

$stmt = mysqli_prepare(
    $connect,
    "SELECT id_shodaqoh
     FROM shodaqoh
     WHERE tanggal = ?
       AND id_kelas = ?
       AND nominal = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    'sii',
    $tanggal,
    $id_kelas,
    $nominal
);

mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$duplikat = mysqli_num_rows($hasil) > 0;

mysqli_stmt_close($stmt);

if ($duplikat) {
    header('Location: tambah.php?pesan=duplikat');
    exit;
}

$stmt = mysqli_prepare(
    $connect,
    "INSERT INTO shodaqoh (tanggal, id_kelas, nominal)
     VALUES (?, ?, ?)"
);

mysqli_stmt_bind_param(
    $stmt,
    'sii',
    $tanggal,
    $id_kelas,
    $nominal
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: index.php?pesan=simpan');
    exit;
}

mysqli_stmt_close($stmt);

header('Location: tambah.php?pesan=gagal');
exit;
