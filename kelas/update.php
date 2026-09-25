<?php
require_once __DIR__ . '/../auth/auth.php';
requireRole(['admin', 'petugas']);

$rawId = $_POST['id_kelas'] ?? null;
$rawNama = $_POST['nama_kelas'] ?? null;
$rawTingkat = $_POST['tingkat'] ?? null;
$rawBagian = $_POST['bagian'] ?? null;

$id = is_scalar($rawId) ? (int) $rawId : 0;
$nama = is_string($rawNama) ? trim($rawNama) : '';
$tingkat = is_string($rawTingkat) ? trim($rawTingkat) : '';
$bagian = is_string($rawBagian) ? trim($rawBagian) : '';

if ($id <= 0) {
    redirect('kelas/index.php');
}

if (
    $nama === ''
    || strlen($nama) > 30
    || !in_array($tingkat, ['X', 'XI', 'XII'], true)
    || ($bagian !== '' && !in_array($bagian, ['01', '02'], true))
) {
    redirect('kelas/edit.php?id=' . $id . '&pesan=gagal');
}

try {
    if ($bagian === '') {
        $stmt = $connect->prepare(
            "UPDATE kelas
             SET nama_kelas = ?, tingkat = ?, bagian = NULL
             WHERE id_kelas = ?"
        );
        $stmt->bind_param('ssi', $nama, $tingkat, $id);
    } else {
        $stmt = $connect->prepare(
            "UPDATE kelas
             SET nama_kelas = ?, tingkat = ?, bagian = ?
             WHERE id_kelas = ?"
        );
        $stmt->bind_param('sssi', $nama, $tingkat, $bagian, $id);
    }

    $stmt->execute();
} catch (Throwable $e) {
    error_log('Class update failed: ' . $e->getMessage());
    redirect('kelas/edit.php?id=' . $id . '&pesan=gagal');
}

redirect('kelas/index.php?pesan=update');
