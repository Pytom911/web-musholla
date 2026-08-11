<?php
$pageTitle = 'Tambah Jadwal Sholat';
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

        <h3 class="form-title">Tambah Jadwal Sholat</h3>
        <p class="form-subtitle">Tambahkan jadwal sholat baru ke dalam sistem.</p>

        <form action="simpan.php" method="POST">

            <div class="form-group">
                <label>Tanggal <span class="required">*</span></label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>

            <div class="form-group">
                <label>Waktu Sholat <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-clock"></i>
                    </span>

                    <select name="waktu_sholat" class="form-select" required>
                        <option value="">-- Pilih Waktu Sholat --</option>
                        <option value="dzuhur">Dzuhur</option>
                        <option value="ashar">Ashar</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Kelas <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-mortarboard"></i>
                    </span>

                    <select name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>

                        <?php while($row = mysqli_fetch_assoc($kelas)) : ?>
                            <option value="<?= $row['id_kelas']; ?>">
                                <?= htmlspecialchars($row['nama_kelas']); ?> - <?= htmlspecialchars($row['tingkat']); ?>
                            </option>
                        <?php endwhile; ?>

                    </select>
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