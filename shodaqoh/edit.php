<?php
$pageTitle = 'Edit Data Shodaqoh Jumat';
require_once '../template/header.php';

$id = mysqli_real_escape_string($connect, $_GET['id']);

$data = mysqli_query($connect, "SELECT * FROM shodaqoh_jumat WHERE id_shodaqoh='$id'");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan!');window.location='index.php';</script>";
    exit;
}

$kelas = mysqli_query($connect, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">
    <a href="index.php" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">
        <h3 class="form-title">Edit Data Shodaqoh Jumat</h3>
        <p class="form-subtitle">Perbarui data shodaqoh Jumat yang telah tersimpan.</p>

        <form action="update.php" method="POST">
            <input type="hidden" name="id_shodaqoh" value="<?= $row['id_shodaqoh']; ?>">

            <div class="form-group">
                <label>Tanggal <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-event"></i>
                    </span>
                    <input type="date" name="tanggal" class="form-control" value="<?= $row['tanggal']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Kelas <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <select name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php while($kelasRow = mysqli_fetch_assoc($kelas)): ?>
                            <option value="<?= $kelasRow['id_kelas']; ?>" <?= $kelasRow['id_kelas'] == $row['id_kelas'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($kelasRow['nama_kelas']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Nominal <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-cash-stack"></i>
                    </span>
                    <input type="number" name="nominal" class="form-control" value="<?= $row['nominal']; ?>" min="1000" required>
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