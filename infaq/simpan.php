<?php
require_once '../config/connect.php';

if (isset($_POST['nama_donatur'])) {

    $nama = mysqli_real_escape_string($connect, $_POST['nama_donatur']);
    $nominal = $_POST['nominal'];
    $tanggal = $_POST['tanggal'];

    $query = mysqli_query($connect, "INSERT INTO infaq (nama_donatur, nominal, tanggal)
    VALUES ('$nama','$nominal','$tanggal')");

    if ($query) {
        header('Location: index.php?pesan=simpan');
    } else {
        header('Location: tambah.php?pesan=gagal');
    }

} else {
    header('Location: index.php');
}