<?php
require_once '../config/connect.php';
require_once "../auth/auth.php";
requireRole(['admin','petugas']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id_infaq', FILTER_VALIDATE_INT);
$nama = trim($_POST['nama_donatur'] ?? '');
$nominal = filter_input(INPUT_POST, 'nominal', FILTER_VALIDATE_INT);
$tanggal = trim($_POST['tanggal'] ?? '');

if (!$id || $nama === '' || !$nominal || $nominal < 1000 || $tanggal === '') {
    header('Location: ' . ($id ? "edit.php?id=$id&pesan=invalid" : 'index.php'));
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
    "UPDATE infaq SET
        nama_donatur = ?,
        nominal = ?,
        tanggal = ?
     WHERE id_infaq = ?"
);

mysqli_stmt_bind_param($stmt, 'sisi', $nama, $nominal, $tanggal, $id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: index.php?pesan=update');
    exit;
}

mysqli_stmt_close($stmt);

header("Location: edit.php?id=$id&pesan=gagal");
exit;
