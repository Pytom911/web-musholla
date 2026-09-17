<?php

require_once '../config/connect.php';
require_once "../auth/auth.php";
requireRole(['admin','petugas']);


if (
    isset(
    $_POST['id_jadwal'],
    $_POST['hari'],
    $_POST['waktu_sholat'],
    $_POST['id_kelas']
)
) {


    $id = (int) $_POST['id_jadwal'];


    $hari = mysqli_real_escape_string(
        $connect,
        $_POST['hari']
    );


    $waktu_sholat = mysqli_real_escape_string(
        $connect,
        $_POST['waktu_sholat']
    );


    $id_kelas = (int) $_POST['id_kelas'];


    /*
     * VALIDASI SESUAI DATABASE
     *
     * ENUM('Dzuhur','Ashar')
     */

    if (
        $waktu_sholat !== 'Dzuhur'
        &&
        $waktu_sholat !== 'Ashar'
    ) {

        echo "<script>

            alert('Waktu sholat tidak valid!');

            window.history.back();

        </script>";

        exit;

    }


    /*
     * Validasi hari
     *
     * ENUM('Senin','Selasa','Rabu','Kamis','Jumat')
     */

    $hariValid = [
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat'
    ];

    if (!in_array($hari, $hariValid, true)) {

        echo "<script>

            alert('Hari tidak valid!');

            window.history.back();

        </script>";

        exit;

    }


    /*
     * Validasi kelas
     */

    if ($id_kelas <= 0) {

        echo "<script>

            alert('Kelas harus dipilih!');

            window.history.back();

        </script>";

        exit;

    }


    /*
     * Update data
     */

    $query = mysqli_query(
        $connect,
        "
        UPDATE jadwal_sholat
        SET
            hari = '$hari',
            waktu_sholat = '$waktu_sholat',
            id_kelas = $id_kelas
        WHERE
            id_jadwal = $id
        "
    );


    if ($query) {

        header(
            'Location: index.php?pesan=update'
        );

        exit;

    }


    echo "Gagal update: " .
        mysqli_error($connect);


} else {

    header(
        'Location: index.php'
    );

    exit;

}

?>