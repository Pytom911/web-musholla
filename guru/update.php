<?php
require_once "../config/connect.php";
require_once "../auth/auth.php";
requireRole(['admin','petugas']);

if (isset($_POST['id_guru'])) {

    $id     = mysqli_real_escape_string($connect, $_POST['id_guru']);
    $nama   = mysqli_real_escape_string($connect, trim($_POST['nama_guru']));
    $nip    = mysqli_real_escape_string($connect, $_POST['nip']);
    $no_hp  = mysqli_real_escape_string($connect, trim($_POST['no_hp']));

    // Cek nama guru duplikat, kecuali data milik sendiri
    $cekNama = mysqli_query($connect, "
        SELECT id_guru
        FROM guru
        WHERE LOWER(TRIM(nama_guru)) = LOWER(TRIM('$nama'))
        AND id_guru != '$id'
    ");

    if (mysqli_num_rows($cekNama) > 0) {
        echo "<script>
            alert('Nama guru sudah terdaftar!');
            history.back();
        </script>";
        exit;
    }

    // Cek No HP duplikat, kecuali data milik sendiri
    $cekHp = mysqli_query($connect, "
        SELECT id_guru
        FROM guru
        WHERE no_hp = '$no_hp'
        AND id_guru != '$id'
    ");

    if (mysqli_num_rows($cekHp) > 0) {
        echo "<script>
            alert('Nomor HP sudah terdaftar!');
            history.back();
        </script>";
        exit;
    }

    $query = mysqli_query($connect, "
        UPDATE guru
        SET
            nama_guru = '$nama',
            nip = '$nip',
            no_hp = '$no_hp'
        WHERE id_guru = '$id'
    ");

    if ($query) {
        header("Location: index.php?pesan=update");
        exit;
    } else {
        echo "Gagal mengupdate data: " . mysqli_error($connect);
    }

} else {
    header("Location: index.php");
    exit;
}