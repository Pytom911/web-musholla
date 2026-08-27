<?php
require_once '../config/connect.php';

if (isset($_POST['id_shodaqoh'])) {
    $id = (int)$_POST['id_shodaqoh'];
    $id_kelas = $_POST['id_kelas'];
    $nominal = $_POST['nominal'];
    $tanggal = $_POST['tanggal'];

    $query = mysqli_query($connect, "UPDATE shodaqoh_jumat SET
        id_kelas='$id_kelas',
        nominal='$nominal',
        tanggal='$tanggal'
        WHERE id_shodaqoh='$id'");

    if ($query) {
        header('Location: index.php?pesan=update');
    } else {
        header('Location: edit.php?id=' . $id . '&pesan=gagal');
    }
} else {
    header('Location: index.php');
}