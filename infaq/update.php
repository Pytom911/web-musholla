<?php
require_once '../config/connect.php';

if (isset($_POST['id_infaq'])) {

    $id = (int)$_POST['id_infaq'];
    $nama = mysqli_real_escape_string($connect, $_POST['nama_donatur']);
    $nominal = $_POST['nominal'];
    $tanggal = $_POST['tanggal'];

    $query = mysqli_query($connect, "UPDATE infaq SET
        nama_donatur='$nama',
        nominal='$nominal',
        tanggal='$tanggal'
        WHERE id_infaq='$id'");

    if ($query) {
        header('Location: index.php?pesan=update');
    } else {
        header('Location: edit.php?id='.$id.'&pesan=gagal');
    }

} else {
    header('Location: index.php');
}