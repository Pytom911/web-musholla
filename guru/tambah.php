<?php
session_start();

$pageTitle = 'Tambah Guru';
require_once "../auth/auth.php";
requireRole(['admin','petugas']);

require_once "../config/connect.php";

require_once "../template/header.php";
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid px-4">

    <!-- Tombol Kembali -->
    <div class="mb-4">
        <a href="index.php" class="btn btn-light border rounded-3 px-4 py-3">
            <i class="bi bi-arrow-left"></i>
            Kembali
        </a>
    </div>

    <!-- Pesan Validasi -->
    <?php
    $pesan = [
        'nama_ada' => 'Nama guru sudah terdaftar!',
        'nip_ada' => 'NIP sudah terdaftar!',
        'no_hp_ada' => 'Nomor HP sudah terdaftar!',
        'data_ada' => 'Data guru sudah terdaftar!',
        'gagal' => 'Data guru gagal disimpan!'
    ];

    if (isset($_GET['pesan']) && isset($pesan[$_GET['pesan']])):
    ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($pesan[$_GET['pesan']]) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Card Form -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">

            <div class="mb-4">
                <h2 class="fw-bold mb-2" style="border-left: 5px solid #198754; padding-left: 12px;">
                    Tambah Guru
                </h2>

                <p class="text-muted mb-0">
                    Tambahkan data guru baru ke dalam sistem.
                </p>
            </div>

            <form action="simpan.php" method="POST">

                <!-- Nama Guru -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        Nama Guru <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>

                        <input
                            type="text"
                            id="nama_guru"
                            name="nama_guru"
                            class="form-control"
                            placeholder="Masukkan nama guru"
                            maxlength="30"
                            required
                        >
                    </div>
                </div>

                <!-- NIP Manual -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        NIP <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-card-text"></i>
                        </span>

                        <input
                            type="text"
                            id="nip"
                            name="nip"
                            class="form-control"
                            placeholder="Masukkan NIP guru"
                            maxlength="30"
                            required
                        >
                    </div>
                </div>

                <!-- No HP -->
                <div class="mb-4">
                    <label class="form-label fw-semibold">
                        No HP <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-telephone"></i>
                        </span>

                        <input
                            type="tel"
                            name="no_hp"
                            class="form-control"
                            placeholder="Masukkan nomor HP"
                            maxlength="13"
                            pattern="[0-9]{1,13}"
                            inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 13)"
                            required
                        >
                    </div>

                    <small class="text-muted">
                        Nomor HP hanya boleh berupa angka, maksimal 13 digit.
                    </small>
                </div>

                <!-- Tombol -->
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

</div>

<?php require_once "../template/footer.php"; ?>