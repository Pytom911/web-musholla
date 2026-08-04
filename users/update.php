<?php
require_once '../config/connect.php';

if (isset($_POST['id_user'])) {

    $id = (int)$_POST['id_user'];
    $username = mysqli_real_escape_string($connect, $_POST['username']);
    $nama = mysqli_real_escape_string($connect, $_POST['nama']);
    $role = mysqli_real_escape_string($connect, $_POST['role']);

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = mysqli_query($connect, "UPDATE users SET
            username='$username',
            nama='$nama',
            role='$role',
            password='$password'
            WHERE id_user='$id'");
    } else {
        $query = mysqli_query($connect, "UPDATE users SET
            username='$username',
            nama='$nama',
            role='$role'
            WHERE id_user='$id'");
    }

    if ($query) {
        header('Location: index.php?pesan=update');
    } else {
        header('Location: edit.php?id='.$id.'&pesan=gagal');
    }

} else {
    header('Location: index.php');
}
