<?php
require_once __DIR__ . '/../auth/auth.php';
requireRole(['admin', 'petugas']);

$rawTanggal = $_POST['tanggal'] ?? null;
$rawWaktu = $_POST['waktu_sholat'] ?? null;
$rawIdKelas = $_POST['id_kelas'] ?? null;

$tanggal = is_string($rawTanggal) ? trim($rawTanggal) : '';
$waktu = is_string($rawWaktu) ? trim($rawWaktu) : '';
$idKelas = is_scalar($rawIdKelas) ? (int) $rawIdKelas : 0;

$tanggalValid = $tanggal === '';
if ($tanggal !== '') {
    $parsedTanggal = DateTime::createFromFormat('!Y-m-d', $tanggal);
    $tanggalValid = $parsedTanggal !== false
        && $parsedTanggal->format('Y-m-d') === $tanggal;
}

if (
    !$tanggalValid
    || !in_array($waktu, ['Dzuhur', 'Ashar'], true)
    || $idKelas <= 0
) {
    redirect('jadwal_sholat/tambah.php?pesan=gagal');
}

try {
    $classStmt = $connect->prepare("SELECT id_kelas FROM kelas WHERE id_kelas = ?");
    $classStmt->bind_param('i', $idKelas);
    $classStmt->execute();
    $classStmt->store_result();

    if ($classStmt->num_rows !== 1) {
        redirect('jadwal_sholat/tambah.php?pesan=gagal');
    }

    if ($tanggal === '') {
        $stmt = $connect->prepare(
            "INSERT INTO jadwal_sholat (tanggal, waktu_sholat, id_kelas)
             VALUES (NULL, ?, ?)"
        );
        $stmt->bind_param('si', $waktu, $idKelas);
    } else {
        $stmt = $connect->prepare(
            "INSERT INTO jadwal_sholat (tanggal, waktu_sholat, id_kelas)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param('ssi', $tanggal, $waktu, $idKelas);
    }

    $stmt->execute();
} catch (Throwable $e) {
    error_log('Prayer schedule create failed: ' . $e->getMessage());
    redirect('jadwal_sholat/tambah.php?pesan=gagal');
}

redirect('jadwal_sholat/index.php?pesan=simpan');
