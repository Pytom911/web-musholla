<?php
$pageTitle='Edit Data Infaq';
require_once '../template/header.php';

$id=mysqli_real_escape_string($connect,$_GET['id']);
$data=mysqli_query($connect,"SELECT * FROM infaq WHERE id_infaq='$id'");
$row=mysqli_fetch_assoc($data);

if(!$row){
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

        <h3 class="form-title">Edit Data Infaq</h3>
        <p class="form-subtitle">Perbarui data infaq yang telah tersimpan.</p>

        <form action="update.php" method="POST">

            <input type="hidden" name="id_infaq" value="<?= $row['id_infaq']; ?>">

            <div class="form-group">
                <label>Nama Donatur <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="nama_donatur" class="form-control" value="<?= htmlspecialchars($row['nama_donatur']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Nominal <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-cash-stack"></i>
                    </span>

                    <input type="number" name="nominal" class="form-control" value="<?= $row['nominal']; ?>" min="1000" required>
                </div>
            </div>
                        <div class="form-group">
                <label>Tanggal <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-event"></i>
                    </span>

                    <input type="date" name="tanggal" class="form-control" value="<?= $row['tanggal']; ?>" required>
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