<?php
$pageTitle = 'Tambah Data User';
require_once '../template/header.php';
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <a href="index.php" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">

        <h3 class="form-title">Tambah Data Pengguna</h3>
        <p class="form-subtitle">Tambahkan data pengguna baru ke dalam sistem.</p>

        <form action="simpan.php" method="POST">

            <div class="form-group">
                <label>Username <span class="required">*</span></label>
                <input type="text" name="username" class="form-control" placeholder="Masukkan Username" required>
            </div>

            <div class="form-group">
                <label>Nama <span class="required">*</span></label>
                <input type="text" name="nama" class="form-control" placeholder="Masukkan Nama" required>
            </div>

            <div class="form-group">
                <label>Role <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person-badge"></i>
                    </span>
                    <select name="role" class="form-select" required>
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="petugas">Petugas</option>
                        <option value="siswa">Siswa</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Password <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>

                    <input type="password" name="password" class="form-control" placeholder="Masukan Password" required>
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