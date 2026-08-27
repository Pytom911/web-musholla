<?php
require_once "../config/connect.php";

if (isset($_GET['id'])) {

    $id = mysqli_real_escape_string($connect, $_GET['id']);

    $query = mysqli_query($connect, "DELETE FROM guru WHERE id_guru='$id'");

    if ($query) {
        header("Location: index.php?pesan=hapus");
        exit;
    } else {
        header("Location: index.php?pesan=gagal");
        exit;
    }

} else {
    header("Location: index.php");
    exit;
}
?>