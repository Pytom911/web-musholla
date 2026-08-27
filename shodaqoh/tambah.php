<?php
$pageTitle = 'Tambah Data Shodaqoh Jumat';
require_once '../template/header.php';

$kelas = mysqli_query($connect, "SELECT * FROM kelas ORDER BY nama_kelas ASC");
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">
    <a href="index.php" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">
        <h3 class="form-title">Tambah Data Shodaqoh Jumat</h3>
        <p class="form-subtitle">Tambahkan data shodaqoh Jumat baru ke dalam sistem.</p>

        <form action="simpan.php" method="POST">
            <div class="form-group">
                <label>Tanggal <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-event"></i>
                    </span>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
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
                        <?php while($row = mysqli_fetch_assoc($kelas)): ?>
                            <option value="<?= $row['id_kelas']; ?>">
                                <?= htmlspecialchars($row['nama_kelas']); ?>
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
                    <input type="number" name="nominal" class="form-control" placeholder="Masukkan nominal shodaqoh" min="1000" required>
                </div>
            </div>

            <div class="form-footer">
                <a href="index.php" class="btn-cancel">
                    <i class="bi bi-arrow-left-circle"></i>
                    Batal
                </a>
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-circle-fill"></i>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once '../template/footer.php'; ?>