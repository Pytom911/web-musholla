<?php
require_once '../config/connect.php';
require_once "../auth/auth.php";
requireRole(['admin']);

if (isset($_POST['nama'])) {

    $nama = mysqli_real_escape_string($connect, $_POST['nama']);
    $username = $_POST['username'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    $query = mysqli_query($connect, "INSERT INTO users (nama, username, role, password)
    VALUES ('$nama','$username','$role', '$password')");

    if ($query) {
        header('Location: index.php?pesan=simpan');
    } else {
        header('Location: tambah.php?pesan=gagal');
    }

} else {
    header('Location: index.php');
}