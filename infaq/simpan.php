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
$idKegiatanInput = trim($_POST['id_kegiatan'] ?? '');

if (!$nominal || $nominal < 1000 || $tanggal === '') {
    header('Location: tambah.php?pesan=invalid');
    exit;
}

$idKegiatan = null;

if ($idKegiatanInput !== '') {
    $idKegiatan = filter_var($idKegiatanInput, FILTER_VALIDATE_INT);

    if (!$idKegiatan || $idKegiatan < 1) {
        header('Location: tambah.php?pesan=invalid');
        exit;
    }

    $cekKegiatan = $connect->prepare(
        'SELECT id_kegiatan FROM kegiatan WHERE id_kegiatan = ? LIMIT 1'
    );

    if (!$cekKegiatan) {
        header('Location: tambah.php?pesan=gagal');
        exit;
    }

    $cekKegiatan->bind_param('i', $idKegiatan);
    $cekKegiatan->execute();
    $kegiatanAda = $cekKegiatan->get_result()->num_rows > 0;
    $cekKegiatan->close();

    if (!$kegiatanAda) {
        header('Location: tambah.php?pesan=invalid');
        exit;
    }
}

if ($idKegiatan === null && $nama === '') {
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

$stmt = $connect->prepare(
    "INSERT INTO infaq (nama_donatur, nominal, tanggal, id_kegiatan) VALUES (?, ?, ?, ?)"
);
if (!$stmt) {
    header('Location: tambah.php?pesan=gagal');
    exit;
}
$stmt->bind_param('sisi', $nama, $nominal, $tanggal, $idKegiatan);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: index.php?pesan=simpan');
    exit;
}

$stmt->close();
header('Location: tambah.php?pesan=gagal');
exit;
