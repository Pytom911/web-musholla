<?php
require_once '../config/connect.php';

if (isset($_POST['tanggal'])) {

    $tanggal = mysqli_real_escape_string($connect, $_POST['tanggal']);
    $waktu_sholat = mysqli_real_escape_string($connect, $_POST['waktu_sholat']);
    $id_kelas = (int)$_POST['id_kelas'];

    $query = mysqli_query($connect, "INSERT INTO jadwal_sholat (tanggal, waktu_sholat, id_kelas)
    VALUES ('$tanggal', '$waktu_sholat', '$id_kelas')");

    if ($query) {
        header('Location: index.php?pesan=simpan');
    } else {
        header('Location: tambah.php?pesan=gagal');
    }

} else {
    header('Location: index.php');
}