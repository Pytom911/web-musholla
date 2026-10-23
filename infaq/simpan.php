<?php
require_once '../config/connect.php';
require_once "../auth/auth.php";
requireRole(['admin','petugas']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$nama = trim($_POST['nama_donatur'] ?? '');
$nominal = filter_input(INPUT_POST, 'nominal', FILTER_VALIDATE_INT);
$tanggal = trim($_POST['tanggal'] ?? '');

if ($nama === '' || !$nominal || $nominal < 1000 || $tanggal === '') {
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
