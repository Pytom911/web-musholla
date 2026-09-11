<?php
require_once '../config/connect.php';
require_once "../auth/auth.php";
requireRole(['admin','petugas']);

if (isset($_POST['id_kelas'])) {

    $tanggal = $_POST['tanggal'];
    $id_kelas = $_POST['id_kelas'];
    $nominal = $_POST['nominal'];

    $query = mysqli_query($connect, "INSERT INTO shodaqoh (tanggal, id_kelas, nominal)
    VALUES ('$tanggal','$id_kelas','$nominal')");

    if ($query) {
        header('Location: index.php?pesan=simpan');
    } else {
        header('Location: tambah.php?pesan=gagal');
    }
} else {
    header('Location: index.php');
}