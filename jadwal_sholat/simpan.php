<?php

require_once '../config/connect.php';
require_once "../auth/auth.php";
requireRole(['admin','petugas']);
if (
    isset(
    $_POST['tanggal'],
    $_POST['waktu_sholat'],
    $_POST['id_kelas']
)
) {

    $tanggal = mysqli_real_escape_string(
        $connect,
        $_POST['tanggal']
    );


    $waktu_sholat = mysqli_real_escape_string(
        $connect,
        $_POST['waktu_sholat']
    );


    $id_kelas = (int) $_POST['id_kelas'];


    /*
     * Sesuai ENUM database:
     *
     * ENUM('Dzuhur','Ashar')
     */

    if (
        $waktu_sholat !== 'Dzuhur'
        &&
        $waktu_sholat !== 'Ashar'
    ) {

        header(
            'Location: tambah.php?pesan=gagal'
        );

        exit;

    }


    if ($id_kelas <= 0) {

        header(
            'Location: tambah.php?pesan=gagal'
        );

        exit;

    }


    $query = mysqli_query(
        $connect,
        "
        INSERT INTO jadwal_sholat
        (
            tanggal,
            waktu_sholat,
            id_kelas
        )
        VALUES
        (
            '$tanggal',
            '$waktu_sholat',
            $id_kelas
        )
        "
    );


    if ($query) {

        header(
            'Location: index.php?pesan=simpan'
        );

        exit;

    }


    header(
        'Location: tambah.php?pesan=gagal'
    );

    exit;

}


header('Location: index.php');

exit;

?>