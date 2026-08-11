<!-- <!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kelas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h3>Tambah Data Kelas</h3>

    <form action="simpan.php" method="POST">
        <div class="mb-3">
            <label for="nama_kelas" class="form-label">Nama Kelas</label>
            <input type="text" id="nama_kelas" name="nama_kelas" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="tingkat" class="form-label">Tingkat</label>
            <select id="tingkat" name="tingkat" class="form-select">
                <option value="X">X</option>
                <option value="XI">XI</option>
                <option value="XII">XII</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

</body>
</html> -->

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
                <input type="text" name="nama_kelas" class="form-control" placeholder="Masukkan nama kelas" required>
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