<?php
$pageTitle = 'Edit Jadwal Sholat';
require_once '../template/header.php';

$id = mysqli_real_escape_string($connect, $_GET['id']);

$data = mysqli_query($connect, "SELECT * FROM jadwal_sholat WHERE id_jadwal='$id'");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan!');window.location='index.php';</script>";
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

        <h3 class="form-title">Edit Jadwal Sholat</h3>
        <p class="form-subtitle">Perbarui data jadwal sholat.</p>

        <form action="update.php" method="POST">

            <input type="hidden" name="id_jadwal" value="<?= $row['id_jadwal']; ?>">

            <div class="form-group">
                <label>Tanggal <span class="required">*</span></label>
                <input type="date" name="tanggal" class="form-control"
                    value="<?= $row['tanggal']; ?>" required>
            </div>

            <div class="form-group">
                <label>Waktu Sholat <span class="required">*</span></label>

                <select name="waktu_sholat" class="form-select" required>
                    <option value="dzuhur" <?= ($row['waktu_sholat']=="dzuhur")?"selected":""; ?>>Dzuhur</option>
                    <option value="ashar" <?= ($row['waktu_sholat']=="ashar")?"selected":""; ?>>Ashar</option>
                </select>
            </div>

            <div class="form-group">
                <label>ID Kelas <span class="required">*</span></label>

                <input type="number" name="id_kelas" class="form-control"
                    value="<?= $row['id_kelas']; ?>" required>
            </div>

            <div class="form-footer">
                <a href="index.php" class="btn-cancel">
                    <i class="bi bi-arrow-left-circle"></i>
                    Batal
                </a>

                <button type="submit" class="btn-add">
                    <i class="bi bi-pencil-square"></i>
                    Update Data
                </button>
            </div>

        </form>

    </div>

</div>

<?php require_once '../template/footer.php'; ?>