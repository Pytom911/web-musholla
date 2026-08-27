<?php
require_once "../config/connect.php";

if (isset($_POST['id_guru'])) {

    $id     = mysqli_real_escape_string($connect, $_POST['id_guru']);
    $nama   = mysqli_real_escape_string($connect, $_POST['nama_guru']);
    $nip    = mysqli_real_escape_string($connect, $_POST['nip']);
    $no_hp  = mysqli_real_escape_string($connect, $_POST['no_hp']);

    $query = mysqli_query($connect, "
        UPDATE guru
        SET
            nama_guru = '$nama',
            nip = '$nip',
            no_hp = '$no_hp'
        WHERE id_guru = '$id'
    ");

    if ($query) {
        header("Location: index.php?pesan=update");
        exit;
    } else {
        echo "Gagal mengupdate data: " . mysqli_error($connect);
    }

} else {
    header("Location: index.php");
    exit;
}