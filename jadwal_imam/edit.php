<?php
$pageTitle = 'Edit Jadwal Imam';

session_start();

require_once '../template/header.php';

$id = mysqli_real_escape_string($connect, $_GET['id'] ?? '');

$data = mysqli_query($connect, "
    SELECT *
    FROM jadwal_imam
    WHERE id_imam = '$id'
");

$row = mysqli_fetch_assoc($data);

if(!$row){
    echo "<script>alert('Data tidak ditemukan!');window.location='index.php';</script>";
    exit;
}


$guru = mysqli_query($connect, "
    SELECT *
    FROM guru
    ORDER BY nama_guru ASC
");
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <a href="index.php" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">

        <h3 class="form-title">Edit Jadwal Imam</h3>
        <p class="form-subtitle">
            Perbarui data jadwal imam musholla.
        </p>

        <form action="update.php" method="POST">

            <input
                type="hidden"
                name="id_imam"
                value="<?= $row['id_imam']; ?>"
            >

            <div class="form-group">
                <label>
                    Tanggal <span class="required">*</span>
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-event"></i>
                    </span>

                    <input
                        type="date"
                        name="tanggal"
                        class="form-control"
                        value="<?= $row['tanggal']; ?>"
                        required
                    >
                </div>
            </div>

            <div class="form-group">
                <label>
                    Waktu Sholat <span class="required">*</span>
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-clock"></i>
                    </span>

                    <select
                        name="waktu_sholat"
                        class="form-control"
                        required
                    >
                        <option value="">-- Pilih Waktu Sholat --</option>

                        <option
                            value="Dzuhur"
                            <?= $row['waktu_sholat'] == 'Dzuhur' ? 'selected' : ''; ?>
                        >
                            Dzuhur
                        </option>

                        <option
                            value="Ashar"
                            <?= $row['waktu_sholat'] == 'Ashar' ? 'selected' : ''; ?>
                        >
                            Ashar
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>
                    Nama Guru <span class="required">*</span>
                </label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>

                    <select
                        name="id_guru"
                        class="form-control"
                        required
                    >
                        <option value="">-- Pilih Guru --</option>

                        <?php while ($g = mysqli_fetch_assoc($guru)): ?>

                            <option
                                value="<?= $g['id_guru']; ?>"
                                <?= $row['id_guru'] == $g['id_guru'] ? 'selected' : ''; ?>
                            >
                                <?= htmlspecialchars($g['nama_guru']); ?>
                            </option>

                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-footer">

                <a href="index.php" class="btn-cancel">
                    <i class="bi bi-x-circle"></i>
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