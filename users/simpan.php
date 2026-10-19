<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../auth/auth.php';
requireRole(['admin']);

if (!isset($_POST['nama'])) {
    redirect('users/index.php');
}

$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$role = $_POST['role'] ?? '';
$password = $_POST['password'] ?? '';

if (
    $nama === ''
    || $username === ''
    || !in_array($role, ['admin', 'petugas'], true)
    || $password === ''
) {
    redirect('users/tambah.php?pesan=gagal');
}

try {
    // Cek duplikat username
    $stmt = $connect->prepare(
        "SELECT id_user FROM users WHERE username = ? LIMIT 1"
    );
    $stmt->bind_param('s', $username);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        redirect('users/tambah.php?pesan=duplikat_username');
    }
    $stmt->close();

    // Cek duplikat nama
    $stmt = $connect->prepare(
        "SELECT id_user FROM users WHERE nama = ? LIMIT 1"
    );
    $stmt->bind_param('s', $nama);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        redirect('users/tambah.php?pesan=duplikat_nama');
    }
    $stmt->close();

    // Simpan data user baru (password di-hash)
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $connect->prepare(
        "INSERT INTO users (nama, username, role, password)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param('ssss', $nama, $username, $role, $hash);
    $stmt->execute();
    $stmt->close();
} catch (Throwable $e) {
    error_log('User create failed: ' . $e->getMessage());
    redirect('users/tambah.php?pesan=gagal');
}

redirect('users/index.php?pesan=simpan');
