<?php
require_once "../config/connect.php";

if(isset($_POST['tanggal'])){

    $tanggal = mysqli_real_escape_string($connect,$_POST['tanggal']);
    $waktu_sholat = mysqli_real_escape_string($connect,$_POST['waktu_sholat']);
    $id_guru = mysqli_real_escape_string($connect,$_POST['id_guru']);

    $query = mysqli_query($connect,"
    INSERT INTO jadwal_imam
    (tanggal,waktu_sholat,id_guru)
    VALUES
    ('$tanggal','$waktu_sholat','$id_guru')
    ");

    if($query){
        header("Location: index.php?pesan=simpan");
    }else{
        header("Location: tambah.php?pesan=gagal");
    }

}else{

    header("Location: index.php");

}