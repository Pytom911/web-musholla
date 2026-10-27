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
$idKegiatanInput = trim($_POST['id_kegiatan'] ?? '');

if (!$id || !$nominal || $nominal < 1000 || $tanggal === '') {
    header('Location: ' . ($id ? "edit.php?id=$id&pesan=invalid" : 'index.php'));
    exit;
}

$idKegiatan = null;

if ($idKegiatanInput !== '') {
    $idKegiatan = filter_var($idKegiatanInput, FILTER_VALIDATE_INT);

    if (!$idKegiatan || $idKegiatan < 1) {
        header("Location: edit.php?id=$id&pesan=invalid");
        exit;
    }

    $cekKegiatan = $connect->prepare(
        'SELECT id_kegiatan FROM kegiatan WHERE id_kegiatan = ? LIMIT 1'
    );

    if (!$cekKegiatan) {
        header("Location: edit.php?id=$id&pesan=gagal");
        exit;
    }

    $cekKegiatan->bind_param('i', $idKegiatan);
    $cekKegiatan->execute();
    $kegiatanAda = $cekKegiatan->get_result()->num_rows > 0;
    $cekKegiatan->close();

    if (!$kegiatanAda) {
        header("Location: edit.php?id=$id&pesan=invalid");
        exit;
    }
}

if ($idKegiatan === null && $nama === '') {
    header("Location: edit.php?id=$id&pesan=invalid");
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

$stmt = $connect->prepare(
    "UPDATE infaq SET nama_donatur = ?, nominal = ?, tanggal = ?, id_kegiatan = ? WHERE id_infaq = ?"
);
if (!$stmt) {
    header("Location: edit.php?id=$id&pesan=gagal");
    exit;
}
$stmt->bind_param('sisii', $nama, $nominal, $tanggal, $idKegiatan, $id);

if ($stmt->execute()) {
    $stmt->close();
    header('Location: index.php?pesan=update');
    exit;
}

$stmt->close();
header("Location: edit.php?id=$id&pesan=gagal");
exit;
