<?php
require_once '../config/connect.php';

if (isset($_POST['nama_kelas'])) {

    $nama = mysqli_real_escape_string($connect, $_POST['nama_kelas']);
    $tingkat = $_POST['tingkat'];

    $query = mysqli_query($connect, "INSERT INTO kelas (nama_kelas, tingkat)
    VALUES ('$nama','$tingkat')");

    if ($query) {
        header('Location: index.php?pesan=simpan');
    } else {
        header('Location: tambah.php?pesan=gagal');
    }

} else {
    header('Location: index.php');
}