<?php
$pageTitle = 'Edit Kegiatan';
require_once __DIR__ . '/../template/header.php';

$id = $_GET['id'] ?? 0;

$data = mysqli_query($connect,"SELECT * FROM kegiatan WHERE id_kegiatan='$id'");

if(mysqli_num_rows($data)==0){
    echo "<script>
            alert('Data kegiatan tidak ditemukan!');
            window.location='index.php';
          </script>";
    exit;
}

$row = mysqli_fetch_assoc($data);
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
                    <a href="index.php">Kegiatan</a>
                </li>
                <li class="breadcrumb-item active">
                    Edit Kegiatan
                </li>
            </ol>
        </nav>
    </div>

    <a href="index.php" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">

        <div class="form-title">
            Edit Kegiatan
        </div>

        <div class="form-subtitle">
            Ubah data kegiatan musholla.
        </div>

        <form action="update.php" method="POST">

            <input type="hidden" name="id_kegiatan" value="<?= $row['id_kegiatan']; ?>">

            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <label>
                            Nama Kegiatan
                            <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            name="nama_kegiatan"
                            class="form-control"
                            value="<?= htmlspecialchars($row['nama_kegiatan']); ?>"
                            required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>
                            Pengeluaran
                            <span class="required">*</span>
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">Rp</span>

                            <input
                                type="number"
                                name="pengeluaran"
                                class="form-control"
                                value="<?= $row['pengeluaran']; ?>"
                                min="0"
                                required>
                        </div>
                    </div>
                </div>
                                <div class="col-md-6">
                    <div class="form-group">
                        <label>
                            Tanggal
                            <span class="required">*</span>
                        </label>

                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            value="<?= $row['tanggal']; ?>"
                            required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label>Deskripsi</label>

                        <textarea
                            name="deskripsi"
                            class="form-control"
                            rows="4"
                            placeholder="Masukkan deskripsi kegiatan"><?= htmlspecialchars($row['deskripsi']); ?></textarea>
                    </div>
                </div>

            </div>

            <div class="form-footer">

                <a href="index.php" class="btn-cancel">
                    Batal
                </a>

                <button type="submit" class="btn-update">
                    <i class="bi bi-check-circle-fill"></i>
                    Update Data
                </button>

            </div>

        </form>

    </div>

</div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>