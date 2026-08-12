<?php
$pageTitle = 'Edit Data kelas';
require_once '../template/header.php';

$id = mysqli_real_escape_string($connect, $_GET['id']);
$data = mysqli_query($connect, "SELECT * FROM kelas WHERE id_kelas='$id'");
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

        <h3 class="form-title">Edit Data kelas</h3>
        <p class="form-subtitle">Perbarui data kelas yang telah tersimpan.</p>

        <form action="update.php" method="POST">

            <input type="hidden" name="id_kelas" value="<?= $row['id_kelas']; ?>">

            <div class="form-group">
                <label>Nama Kelas <span class="required">*</span></label>
                <input type="text" name="nama_kelas" class="form-control" value="<?= htmlspecialchars($row['nama_kelas']); ?>" required>
            </div>

            <div class="form-group">
                <label>Tingkat <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-cash-stack"></i>
                    </span>
                    <select id="tingkat" name="tingkat" class="form-select">
                        <option value="X" <?= ($row['tingkat'] == "X") ? "selected" : ""; ?>>X</option>
                        <option value="XI" <?= ($row['tingkat'] == "XI") ? "selected" : ""; ?>>XI</option>
                        <option value="XII" <?= ($row['tingkat'] == "XII") ? "selected" : ""; ?>>XII</option>
                    </select>
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