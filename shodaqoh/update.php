
<?php
require_once '../config/connect.php';
require_once '../auth/auth.php';

requireRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id_shodaqoh', FILTER_VALIDATE_INT);
$id_kelas = filter_input(INPUT_POST, 'id_kelas', FILTER_VALIDATE_INT);
$nominal = filter_input(INPUT_POST, 'nominal', FILTER_VALIDATE_INT);
$tanggal = trim($_POST['tanggal'] ?? '');

if (!$id || !$id_kelas || !$nominal || $nominal < 1000 || $tanggal === '') {
    header('Location: ' . ($id ? "edit.php?id=$id&pesan=invalid" : 'index.php'));
    exit;
}

$stmt = mysqli_prepare(
    $connect,
    "SELECT id_shodaqoh FROM shodaqoh WHERE id_shodaqoh = ?"
);
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$hasilData = mysqli_stmt_get_result($stmt);
$dataAda = mysqli_num_rows($hasilData) > 0;
mysqli_stmt_close($stmt);

if (!$dataAda) {
    header('Location: index.php?pesan=gagal');
    exit;
}

$formatTanggal = DateTime::createFromFormat('!Y-m-d', $tanggal);
$validTanggal = $formatTanggal
    && $formatTanggal->format('Y-m-d') === $tanggal;

if (!$validTanggal) {
    header("Location: edit.php?id=$id&pesan=invalid");
    exit;
}

if ($formatTanggal->format('Y-m') !== date('Y-m')) {
    header("Location: edit.php?id=$id&pesan=bulan_tidak_valid");
    exit;
}

$stmt = mysqli_prepare(
    $connect,
    "SELECT id_shodaqoh
     FROM shodaqoh
     WHERE tanggal = ?
       AND id_kelas = ?
       AND nominal = ?
       AND id_shodaqoh != ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    'siii',
    $tanggal,
    $id_kelas,
    $nominal,
    $id
);

mysqli_stmt_execute($stmt);
$hasilDuplikat = mysqli_stmt_get_result($stmt);
$duplikat = mysqli_num_rows($hasilDuplikat) > 0;
mysqli_stmt_close($stmt);

if ($duplikat) {
    header("Location: edit.php?id=$id&pesan=duplikat");
    exit;
}

$stmt = mysqli_prepare(
    $connect,
    "UPDATE shodaqoh
     SET tanggal = ?, id_kelas = ?, nominal = ?
     WHERE id_shodaqoh = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    'siii',
    $tanggal,
    $id_kelas,
    $nominal,
    $id
);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: index.php?pesan=update');
    exit;
}

mysqli_stmt_close($stmt);

header("Location: edit.php?id=$id&pesan=gagal");
exit;
