<?php
$pageTitle = 'Edit Data Pengguna';
require_once '../template/header.php';

$id = mysqli_real_escape_string($connect, $_GET['id']);
$data = mysqli_query($connect, "SELECT * FROM users WHERE id_user='$id'");
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

        <h3 class="form-title">Edit Data Pengguna</h3>
        <p class="form-subtitle">Perbarui data pengguna yang telah tersimpan.</p>

        <form action="update.php" method="POST">

            <input type="hidden" name="id_user" value="<?= $row['id_user']; ?>">

            <div class="form-group">
                <label>Username <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>

                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($row['username']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Nama <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person-vcard"></i>
                    </span>

                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($row['nama']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Role <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person-badge"></i>
                    </span>
                    <select name="role" class="form-select" required>
                        <option value="">Pilih Role</option>
                        <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="petugas" <?= $row['role'] == 'petugas' ? 'selected' : ''; ?>>Petugas</option>
                        <option value="siswa" <?= $row['role'] == 'siswa' ? 'selected' : ''; ?>>Siswa</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Password</label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-lock"></i>
                    </span>

                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password">
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
