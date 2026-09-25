<?php
require_once __DIR__ . '/../auth/auth.php';
requireRole(['admin', 'petugas']);

$pageTitle = 'Tambah Data Kelas';
require_once __DIR__ . '/../template/header.php';
?>

<link rel="stylesheet" href="<?= asset('css/data.css') ?>">

<div class="container-fluid">
    <a href="<?= url('kelas/index.php') ?>" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">
        <h3 class="form-title">Tambah Data Kelas</h3>
        <p class="form-subtitle">Tambahkan data kelas baru ke dalam sistem.</p>

        <?php if (($_GET['pesan'] ?? '') === 'gagal'): ?>
            <div class="alert alert-danger">Data kelas tidak dapat disimpan. Periksa kembali isian data.</div>
        <?php endif; ?>

        <form action="<?= url('kelas/simpan.php') ?>" method="POST">
            <div class="form-group">
                <label for="nama_kelas">Nama Kelas <span class="required">*</span></label>
                <div class="input-group">
                    <input type="text" id="nama_kelas" name="nama_kelas" class="form-control"
                        placeholder="Masukkan nama kelas" maxlength="30" required>
                </div>
            </div>

            <div class="form-group">
                <label for="tingkat">Tingkat <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <select id="tingkat" name="tingkat" class="form-select" required>
                        <option value="">-- Pilih Tingkat --</option>
                        <option value="X">X</option>
                        <option value="XI">XI</option>
                        <option value="XII">XII</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="bagian">Bagian</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-diagram-3"></i>
                    </span>
                    <select id="bagian" name="bagian" class="form-select">
                        <option value="">-- Tidak dipilih --</option>
                        <option value="01">01</option>
                        <option value="02">02</option>
                    </select>
                </div>
            </div>

            <div class="form-footer">
                <a href="<?= url('kelas/index.php') ?>" class="btn-cancel">
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

<?php require_once __DIR__ . '/../template/footer.php'; ?>
