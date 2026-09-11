<?php
require_once '../config/connect.php';
require_once "../auth/auth.php";
requireRole(['admin','petugas']);

if(isset($_POST['nama_kegiatan'])){

    $nama = mysqli_real_escape_string($connect,$_POST['nama_kegiatan']);
    $deskripsi = mysqli_real_escape_string($connect,$_POST['deskripsi']);
    $pengeluaran = $_POST['pengeluaran'];
    $tanggal = $_POST['tanggal'];

    $query = mysqli_query($connect,"INSERT INTO kegiatan (
        nama_kegiatan,
        deskripsi,
        pengeluaran,
        tanggal
    ) VALUES (
        '$nama',
        '$deskripsi',
        '$pengeluaran',
        '$tanggal'
    )");

    if($query){
        header('Location: index.php?pesan=simpan');
    }else{
        header('Location: tambah.php?pesan=gagal');
    }

}else{
    header('Location: index.php');
}