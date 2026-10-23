<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../auth/auth.php';
requireRole(['admin']);

if (!isset($_POST['id_user'])) {
    redirect('users/index.php');
}

$id = (int)$_POST['id_user'];
$nama = trim($_POST['nama'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = strtolower(trim($_POST['email'] ?? ''));
$role = $_POST['role'] ?? '';
$password = $_POST['password'] ?? '';

if (
    $nama === ''
    || $username === ''
    || !in_array($role, ['admin', 'petugas'], true)
) {
    redirect('users/edit.php?id=' . $id . '&pesan=gagal');
}

if ($email === '') {
    redirect('users/edit.php?id=' . $id . '&pesan=email_kosong');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
    redirect('users/edit.php?id=' . $id . '&pesan=email_invalid');
}

try {
    // Pastikan user masih ada
    $stmt = $connect->prepare(
        "SELECT id_user FROM users WHERE id_user = ? LIMIT 1"
    );
    $stmt->bind_param('i', $id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows === 0) {
        redirect('users/index.php');
    }
    $stmt->close();

    // Cek duplikat username (kecuali user ini sendiri)
    $stmt = $connect->prepare(
        "SELECT id_user FROM users WHERE username = ? AND id_user != ? LIMIT 1"
    );
    $stmt->bind_param('si', $username, $id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        redirect('users/edit.php?id=' . $id . '&pesan=duplikat_username');
    }
    $stmt->close();

    // Cek duplikat nama (kecuali user ini sendiri)
    $stmt = $connect->prepare(
        "SELECT id_user FROM users WHERE nama = ? AND id_user != ? LIMIT 1"
    );
    $stmt->bind_param('si', $nama, $id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        redirect('users/edit.php?id=' . $id . '&pesan=duplikat_nama');
    }
    $stmt->close();

    // Cek duplikat email (kecuali user ini sendiri)
    $stmt = $connect->prepare(
        "SELECT id_user FROM users WHERE email = ? AND id_user != ? LIMIT 1"
    );
    $stmt->bind_param('si', $email, $id);
    $stmt->execute();

    if ($stmt->get_result()->num_rows > 0) {
        redirect('users/edit.php?id=' . $id . '&pesan=duplikat_email');
    }
    $stmt->close();

    // Perbarui data user
    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $connect->prepare(
            "UPDATE users SET nama = ?, email = ?, username = ?, role = ?, password = ?
             WHERE id_user = ?"
        );
        $stmt->bind_param('sssssi', $nama, $email, $username, $role, $hash, $id);
    } else {
        $stmt = $connect->prepare(
            "UPDATE users SET nama = ?, email = ?, username = ?, role = ?
             WHERE id_user = ?"
        );
        $stmt->bind_param('ssssi', $nama, $email, $username, $role, $id);
    }

    $stmt->execute();
    $stmt->close();
} catch (Throwable $e) {
    error_log('User update failed: ' . $e->getMessage());
    redirect('users/edit.php?id=' . $id . '&pesan=gagal');
}

redirect('users/index.php?pesan=update');
