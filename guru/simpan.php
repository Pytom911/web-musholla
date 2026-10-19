<?php
require_once "../config/connect.php";
require_once "../auth/auth.php";
requireRole(['admin','petugas']);

if (isset($_POST['nama_guru'], $_POST['nip'], $_POST['no_hp'])) {

    $nama = mysqli_real_escape_string($connect, trim($_POST['nama_guru']));
    $nip = mysqli_real_escape_string($connect, trim($_POST['nip']));
    $no_hp = trim($_POST['no_hp']);

    // Validasi input tidak boleh kosong
    if ($nama === '' || $nip === '' || $no_hp === '') {
        header("Location: tambah.php?pesan=gagal");
        exit;
    }

    // Validasi nomor HP maksimal 13 digit
    if (!preg_match('/^[0-9]{1,13}$/', $no_hp)) {
        header("Location: tambah.php?pesan=gagal");
        exit;
    }

    $no_hp = mysqli_real_escape_string($connect, $no_hp);

    // Cek nama, NIP, dan nomor HP yang sama
    $cek = mysqli_query($connect, "SELECT nama_guru, nip, no_hp FROM guru
                                   WHERE nama_guru = '$nama'
                                   OR nip = '$nip'
                                   OR no_hp = '$no_hp'");

    if (!$cek) {
        header("Location: tambah.php?pesan=gagal");
        exit;
    }

    if (mysqli_num_rows($cek) > 0) {
        while ($data = mysqli_fetch_assoc($cek)) {
            if ($data['nama_guru'] == $nama) {
                header("Location: tambah.php?pesan=nama_ada");
                exit;
            }

            if ($data['nip'] == $nip) {
                header("Location: tambah.php?pesan=nip_ada");
                exit;
            }

            if ($data['no_hp'] == $no_hp) {
                header("Location: tambah.php?pesan=no_hp_ada");
                exit;
            }
        }
    }

    // Simpan data guru dengan NIP yang diisi manual
    $query = mysqli_query($connect, "INSERT INTO guru (nama_guru, nip, no_hp)
                                     VALUES ('$nama', '$nip', '$no_hp')");

    if ($query) {
        header("Location: index.php?pesan=simpan");
        exit;
    } else {
        header("Location: tambah.php?pesan=gagal");
        exit;
    }

} else {
    header("Location: index.php");
    exit;
}
?>