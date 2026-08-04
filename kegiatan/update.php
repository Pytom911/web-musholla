<?php
require_once '../config/connect.php';

if(isset($_POST['id_kegiatan'])){

    $id = (int)$_POST['id_kegiatan'];
    $nama = mysqli_real_escape_string($connect,$_POST['nama_kegiatan']);
    $deskripsi = mysqli_real_escape_string($connect,$_POST['deskripsi']);
    $pengeluaran = $_POST['pengeluaran'];
    $tanggal = $_POST['tanggal'];

    $query = mysqli_query($connect,"UPDATE kegiatan SET
        nama_kegiatan='$nama',
        deskripsi='$deskripsi',
        pengeluaran='$pengeluaran',
        tanggal='$tanggal'
        WHERE id_kegiatan='$id'");

    if($query){
        header('Location: index.php?pesan=update');
    }else{
        header('Location: edit.php?id='.$id.'&pesan=gagal');
    }

}else{
    header('Location: index.php');
}