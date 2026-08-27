<?php
require_once '../config/connect.php';

if (isset($_POST['id_kelas'])) {

    $tanggal = $_POST['tanggal'];
    $id_kelas = $_POST['id_kelas'];
    $nominal = $_POST['nominal'];

    $query = mysqli_query($connect, "INSERT INTO shodaqoh_jumat (tanggal, id_kelas, nominal)
    VALUES ('$tanggal','$id_kelas','$nominal')");

    if ($query) {
        header('Location: index.php?pesan=simpan');
    } else {
        header('Location: tambah.php?pesan=gagal');
    }
} else {
    header('Location: index.php');
}