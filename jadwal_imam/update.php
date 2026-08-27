<?php

require_once '../config/connect.php';

$id = $_POST['id_imam'];
$tanggal = $_POST['tanggal'];
$waktu = $_POST['waktu_sholat'];
$idGuru = $_POST['id_guru'];

$cek = mysqli_query($connect, "
    SELECT *
    FROM jadwal_imam
    WHERE tanggal = '$tanggal'
    AND waktu_sholat = '$waktu'
    AND id_imam != '$id'
");

if (mysqli_num_rows($cek) > 0) {

    echo "<script>
        alert('Jadwal sudah digunakan!');
        history.back();
    </script>";

    exit;
}

$query = mysqli_query($connect, "
    UPDATE jadwal_imam
    SET
        tanggal = '$tanggal',
        waktu_sholat = '$waktu',
        id_guru = '$idGuru'
    WHERE id_imam = '$id'
");

if ($query) {
    header("Location: index.php?pesan=update");
    exit;
}

echo "<script>
    alert('Data gagal diperbarui!');
    history.back();
</script>";