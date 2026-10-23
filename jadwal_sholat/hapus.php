<?php
require_once __DIR__ . '/../auth/auth.php';
requireRole(['admin', 'petugas']);

$rawId = $_GET['id'] ?? null;
$id = is_scalar($rawId) ? (int) $rawId : 0;

if ($id <= 0) {
    redirect('jadwal_sholat/index.php');
}

try {
    $stmt = $connect->prepare("DELETE FROM jadwal_sholat WHERE id_jadwal = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
} catch (Throwable $e) {
    error_log('Prayer schedule delete failed: ' . $e->getMessage());
    redirect('jadwal_sholat/index.php?pesan=gagal');
}

redirect('jadwal_sholat/index.php?pesan=hapus');
