<?php
require_once '../config/connect.php';
require_once "../auth/auth.php";
requireRole(['admin','petugas']);



if (isset($_GET['id'])) {


    $id = (int) $_GET['id'];


    $query = mysqli_query(
        $connect,
        "
        DELETE FROM jadwal_sholat
        WHERE id_jadwal = $id
        "
    );


    if ($query) {

        header(
            'Location: index.php?pesan=hapus'
        );

        exit;

    }


    header(
        'Location: index.php?pesan=gagal'
    );

    exit;

}


header(
    'Location: index.php'
);

exit;

?>