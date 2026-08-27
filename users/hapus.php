<?php
require_once '../config/connect.php';
require_once "../auth/auth.php";
requireRole(['admin']);

if (isset($_GET['id'])) {

    $id = (int)$_GET['id'];

    $query = mysqli_query($connect, "DELETE FROM users WHERE id_user='$id'");

    if ($query) {
        header('Location: index.php?pesan=hapus');
    } else {
        header('Location: index.php?pesan=gagal');
    }

} else {
    header('Location: index.php');
}