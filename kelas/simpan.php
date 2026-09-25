<?php
require_once __DIR__ . '/../auth/auth.php';
requireRole(['admin', 'petugas']);

$rawNama = $_POST['nama_kelas'] ?? null;
$rawTingkat = $_POST['tingkat'] ?? null;
$rawBagian = $_POST['bagian'] ?? null;

$nama = is_string($rawNama) ? trim($rawNama) : '';
$tingkat = is_string($rawTingkat) ? trim($rawTingkat) : '';
$bagian = is_string($rawBagian) ? trim($rawBagian) : '';

if (
    $nama === ''
    || strlen($nama) > 30
    || !in_array($tingkat, ['X', 'XI', 'XII'], true)
    || ($bagian !== '' && !in_array($bagian, ['01', '02'], true))
) {
    redirect('kelas/tambah.php?pesan=gagal');
}

try {
    if ($bagian === '') {
        $stmt = $connect->prepare(
            "INSERT INTO kelas (nama_kelas, tingkat, bagian)
             VALUES (?, ?, NULL)"
        );
        $stmt->bind_param('ss', $nama, $tingkat);
    } else {
        $stmt = $connect->prepare(
            "INSERT INTO kelas (nama_kelas, tingkat, bagian)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param('ssi', $nama, $tingkat, $bagian);
    }

    $stmt->execute();
} catch (Throwable $e) {
    error_log('Class create failed: ' . $e->getMessage());
    redirect('kelas/tambah.php?pesan=gagal');
}

redirect('kelas/index.php?pesan=simpan');
