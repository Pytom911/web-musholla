<?php
$pageTitle = 'Tambah Data kelas';
require_once '../template/header.php';
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <a href="index.php" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">

        <h3 class="form-title">Tambah Data kelas</h3>
        <p class="form-subtitle">Tambahkan data kelas baru ke dalam sistem.</p>

        <form action="simpan.php" method="POST">

            <div class="form-group">
                <label>Nama Kelas <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-card-checklist"></i>
                    </span>
                    <input type="text" name="nama_kelas" class="form-control" placeholder="Masukkan nama kelas" required>
                </div>
            </div>

            <div class="form-group">
                <label>Tingkat <span class="required">*</span></label>

                <div class="input-group">

                    <span class="input-group-text">
                        <i class="bi bi-backpack3"></i>
                    </span>
                    <select id="tingkat" name="tingkat" class="form-select">
                        <option value="X">X</option>
                        <option value="XI">XI</option>
                        <option value="XII">XII</option>
                    </select>

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