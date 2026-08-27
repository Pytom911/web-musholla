```php
<?php
$pageTitle = 'Edit Data Guru';
require_once '../template/header.php';

$id = mysqli_real_escape_string($connect, $_GET['id']);
$data = mysqli_query($connect, "SELECT * FROM guru WHERE id_guru='$id'");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data guru tidak ditemukan!');window.location='index.php';</script>";
    exit;
}
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <a href="index.php" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">

        <h3 class="form-title">Edit Data Guru</h3>
        <p class="form-subtitle">Perbarui data guru yang telah tersimpan.</p>

        <form action="update.php" method="POST">

            <input type="hidden" name="id_guru" value="<?= $row['id_guru']; ?>">

            <div class="form-group">
                <label>Nama Guru <span class="required">*</span></label>
                <input type="text" name="nama_guru" class="form-control"
                       value="<?= htmlspecialchars($row['nama_guru']); ?>" required>
            </div>

            <div class="form-group">
                <label>NIP <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person-vcard"></i>
                    </span>

                    <input type="text" name="nip" class="form-control"
                           value="<?= htmlspecialchars($row['nip']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>No HP <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-telephone"></i>
                    </span>

                    <input type="text" name="no_hp" class="form-control"
                           value="<?= htmlspecialchars($row['no_hp']); ?>" required>
                </div>
            </div>

            <div class="form-footer">

                <a href="index.php" class="btn-cancel">
                    <i class="bi bi-arrow-left-circle"></i>
                    Batal
                </a>

                <button type="submit" class="btn-update">
                    <i class="bi bi-pencil-square"></i>
                    Update Data
                </button>

            </div>

        </form>

    </div>

</div>

<?php require_once '../template/footer.php'; ?>
```
