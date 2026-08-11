<?php
$pageTitle = 'Tambah Data Infaq';
require_once '../template/header.php';
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <a href="index.php" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">

        <h3 class="form-title">Tambah Data Infaq</h3>
        <p class="form-subtitle">Tambahkan data infaq baru ke dalam sistem.</p>

        <form action="simpan.php" method="POST">

            <div class="form-group">
                <label>Nama Donatur <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="nama_donatur" class="form-control" placeholder="Masukkan nama donatur" required>
                </div>
            </div>

            <div class="form-group">
                <label>Nominal <span class="required">*</span></label>

                <div class="input-group">

                    <span class="input-group-text">
                        <i class="bi bi-cash-stack"></i>
                    </span>

                    <input type="number" name="nominal" class="form-control" placeholder="Masukkan nominal infaq" min="1000" required>

                </div>

            </div>
            <div class="form-group">
            <label>Tanggal <span class="required">*</span></label>

            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-calendar-event"></i>
                </span>

                <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
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