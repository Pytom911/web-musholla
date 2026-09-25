<?php
require_once __DIR__ . '/../auth/auth.php';
requireRole(['admin', 'petugas']);

$rawId = $_POST['id_jadwal'] ?? null;
$rawTanggal = $_POST['tanggal'] ?? null;
$rawWaktu = $_POST['waktu_sholat'] ?? null;
$rawIdKelas = $_POST['id_kelas'] ?? null;

$id = is_scalar($rawId) ? (int) $rawId : 0;
$tanggal = is_string($rawTanggal) ? trim($rawTanggal) : '';
$waktu = is_string($rawWaktu) ? trim($rawWaktu) : '';
$idKelas = is_scalar($rawIdKelas) ? (int) $rawIdKelas : 0;

$tanggalValid = $tanggal === '';
if ($tanggal !== '') {
    $parsedTanggal = DateTime::createFromFormat('!Y-m-d', $tanggal);
    $tanggalValid = $parsedTanggal !== false
        && $parsedTanggal->format('Y-m-d') === $tanggal;
}

if ($id <= 0) {
    redirect('jadwal_sholat/index.php');
}

if (
    !$tanggalValid
    || !in_array($waktu, ['Dzuhur', 'Ashar'], true)
    || $idKelas <= 0
) {
    redirect('jadwal_sholat/edit.php?id=' . $id . '&pesan=gagal');
}

try {
    $classStmt = $connect->prepare("SELECT id_kelas FROM kelas WHERE id_kelas = ?");
    $classStmt->bind_param('i', $idKelas);
    $classStmt->execute();
    $classStmt->store_result();

    if ($classStmt->num_rows !== 1) {
        redirect('jadwal_sholat/edit.php?id=' . $id . '&pesan=gagal');
    }

    if ($tanggal === '') {
        $stmt = $connect->prepare(
            "UPDATE jadwal_sholat
             SET tanggal = NULL, waktu_sholat = ?, id_kelas = ?
             WHERE id_jadwal = ?"
        );
        $stmt->bind_param('sii', $waktu, $idKelas, $id);
    } else {
        $stmt = $connect->prepare(
            "UPDATE jadwal_sholat
             SET tanggal = ?, waktu_sholat = ?, id_kelas = ?
             WHERE id_jadwal = ?"
        );
        $stmt->bind_param('ssii', $tanggal, $waktu, $idKelas, $id);
    }

    $stmt->execute();
} catch (Throwable $e) {
    error_log('Prayer schedule update failed: ' . $e->getMessage());
    redirect('jadwal_sholat/edit.php?id=' . $id . '&pesan=gagal');
}

redirect('jadwal_sholat/index.php?pesan=update');
