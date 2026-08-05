<?php
$pageTitle = 'Tambah Kegiatan';
require_once __DIR__ . '/../template/header.php';
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <div class="breadcrumb-wrapper">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= url() ?>">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= url('kegiatan/index.php') ?>">Kegiatan</a>
                </li>
                <li class="breadcrumb-item active">
                    Tambah Kegiatan
                </li>
            </ol>
        </nav>
    </div>

    <a href="<?= url('kegiatan/index.php') ?>" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">

        <div class="form-title">
            Tambah Kegiatan
        </div>

        <div class="form-subtitle">
            Silakan lengkapi data kegiatan musholla pada form di bawah ini.
        </div>

        <form action="simpan.php" method="POST">

    <div class="form-group">
        <label>
            Nama Kegiatan
            <span class="required">*</span>
        </label>

        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-calendar-event"></i>
            </span>

            <input
                type="text"
                name="nama_kegiatan"
                class="form-control"
                placeholder="Masukkan nama kegiatan"
                required>
        </div>
    </div>

    <div class="form-group">
        <label>
            Pengeluaran
            <span class="required">*</span>
        </label>

        <div class="input-group">
            <span class="input-group-text">
                Rp
            </span>

            <input
                type="number"
                name="pengeluaran"
                class="form-control"
                placeholder="Masukkan nominal pengeluaran"
                min="0"
                required>
        </div>
    </div>

    <div class="form-group">
        <label>
            Tanggal
            <span class="required">*</span>
        </label>

        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-calendar-date"></i>
            </span>

            <input
                type="date"
                name="tanggal"
                class="form-control"
                value="<?= date('Y-m-d'); ?>"
                required>
        </div>
    </div>

    <div class="form-group">
        <label>Deskripsi</label>

        <div class="input-group">
            <span class="input-group-text">
                <i class="bi bi-card-text"></i>
            </span>

            <textarea
                name="deskripsi"
                class="form-control"
                rows="4"
                placeholder="Masukkan deskripsi kegiatan (opsional)"></textarea>
        </div>
    </div>

    <div class="form-footer">

        <a href="<?= url('kegiatan/index.php') ?>" class="btn-cancel">
            <i class="bi bi-arrow-counterclockwise"></i>
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