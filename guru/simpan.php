<?php
require_once "../config/connect.php";

if (isset($_POST['nama_guru'])) {

    $nama  = mysqli_real_escape_string($connect, $_POST['nama_guru']);
    $nip   = mysqli_real_escape_string($connect, $_POST['nip']);
    $no_hp = mysqli_real_escape_string($connect, $_POST['no_hp']);

    $query = mysqli_query($connect, "INSERT INTO guru (nama_guru, nip, no_hp)
                                     VALUES ('$nama', '$nip', '$no_hp')");

    if ($query) {
        header("Location: index.php?pesan=simpan");
        exit;
    } else {
        header("Location: tambah.php?pesan=gagal");
        exit;
    }   

} else {
    header("Location: index.php");
    exit;
}
?>