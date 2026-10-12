```php
<?php
require_once "../config/connect.php";
require_once "../auth/auth.php";
requireRole(['admin','petugas']);

if (isset($_POST['nama_guru'])) {
    $nama = mysqli_real_escape_string($connect, $_POST['nama_guru']);
    $no_hp = mysqli_real_escape_string($connect, $_POST['no_hp']);

    $queryNip = mysqli_query($connect, "
        SELECT MAX(CAST(nip AS UNSIGNED)) AS nip_terakhir
        FROM guru
    ");
    $dataNip = mysqli_fetch_assoc($queryNip);
    $nip = str_pad(($dataNip['nip_terakhir'] ?? 0) + 1, 5, '0', STR_PAD_LEFT);

    $query = mysqli_query($connect, "
        INSERT INTO guru (nama_guru, nip, no_hp)
        VALUES ('$nama', '$nip', '$no_hp')
    ");

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
```