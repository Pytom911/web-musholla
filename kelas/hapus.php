<?php
require_once __DIR__ . '/../auth/auth.php';
requireRole(['admin', 'petugas']);

$rawId = $_GET['id'] ?? null;
$id = is_scalar($rawId) ? (int) $rawId : 0;

if ($id <= 0) {
    redirect('kelas/index.php');
}

try {
    $stmt = $connect->prepare("DELETE FROM kelas WHERE id_kelas = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
} catch (Throwable $e) {
    error_log('Class delete failed: ' . $e->getMessage());
    redirect('kelas/index.php?pesan=gagal');
}

redirect('kelas/index.php?pesan=hapus');
