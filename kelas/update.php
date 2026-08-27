<?php
require_once '../config/connect.php';

if (isset($_POST['id_kelas'])) {

    $id = (int)$_POST['id_kelas'];
    $nama = mysqli_real_escape_string($connect, $_POST['nama_kelas']);
    $tingkat = $_POST['tingkat'];


    $query = mysqli_query($connect, "UPDATE kelas SET
        nama_kelas='$nama',
        tingkat='$tingkat'
        WHERE id_kelas='$id'");

    if ($query) {
        header('Location: index.php?pesan=update');
    } else {
        header('Location: edit.php?id=' . $id . '&pesan=gagal');
    }
} else {
    header('Location: index.php');
}
