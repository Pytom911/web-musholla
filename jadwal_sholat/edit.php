<?php
require_once "../auth/auth.php";
requireRole(['admin','petugas']);
$pageTitle = 'Edit Jadwal Sholat';

require_once '../template/header.php';


/*
 * Cek ID
 */

if (!isset($_GET['id'])) {

    echo "<script>
        alert('ID jadwal tidak ditemukan!');
        window.location='index.php';
    </script>";

    exit;

}


$id = (int) $_GET['id'];


/*
 * Ambil data jadwal
 */

$query = mysqli_query(
    $connect,
    "
    SELECT *
    FROM jadwal_sholat
    WHERE id_jadwal = $id
    "
);


$row = mysqli_fetch_assoc($query);


/*
 * Jika data tidak ditemukan
 */

if (!$row) {

    echo "<script>
        alert('Data jadwal tidak ditemukan!');
        window.location='index.php';
    </script>";

    exit;

}


/*
 * Ambil data kelas
 */

$kelas = mysqli_query(
    $connect,
    "
    SELECT *
    FROM kelas
    ORDER BY nama_kelas ASC
    "
);

?>

<link rel="stylesheet" href="../assets/css/data.css">


<div class="container-fluid">


    <a href="index.php" class="btn-back">

        <i class="bi bi-arrow-left"></i>

        Kembali

    </a>


    <div class="form-card">


        <h3 class="form-title">
            Edit Jadwal Sholat
        </h3>


        <p class="form-subtitle">
            Ubah data jadwal sholat.
        </p>


        <form action="update.php" method="POST">


            <!-- ID -->
            <input type="hidden" name="id_jadwal" value="<?= $row['id_jadwal']; ?>">


            <!-- Tanggal -->
            <div class="form-group">

                <label>

                    Tanggal

                    <span class="required">*</span>

                </label>


                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-calendar-event"></i>

                    </span>


                    <input type="date" name="tanggal" class="form-control" value="<?= htmlspecialchars(
                        $row['tanggal']
                    ); ?>" required>

                </div>

            </div>


            <!-- Waktu Sholat -->
            <div class="form-group">

                <label>

                    Waktu Sholat

                    <span class="required">*</span>

                </label>


                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-clock"></i>

                    </span>


                    <select name="waktu_sholat" class="form-select" required>

                        <option value="">
                            -- Pilih Waktu Sholat --
                        </option>


                        <option value="Dzuhur" <?= $row['waktu_sholat'] === 'Dzuhur'
                            ? 'selected'
                            : ''; ?>>
                            Dzuhur
                        </option>


                        <option value="Ashar" <?= $row['waktu_sholat'] === 'Ashar'
                            ? 'selected'
                            : ''; ?>>
                            Ashar
                        </option>

                    </select>

                </div>

            </div>


            <!-- Kelas -->
            <div class="form-group">

                <label>

                    Kelas

                    <span class="required">*</span>

                </label>


                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-mortarboard"></i>

                    </span>


                    <select name="id_kelas" class="form-select" required>

                        <option value="">
                            -- Pilih Kelas --
                        </option>


                        <?php while (
                            $kelasRow =
                            mysqli_fetch_assoc($kelas)
                        ): ?>

                            <option value="<?= $kelasRow['id_kelas']; ?>" <?= $row['id_kelas'] == $kelasRow['id_kelas']
                                  ? 'selected'
                                  : ''; ?>>

                                <?= htmlspecialchars(
                                    $kelasRow['nama_kelas']
                                ); ?>

                                -

                                <?= htmlspecialchars(
                                    $kelasRow['tingkat']
                                ); ?>

                            </option>

                        <?php endwhile; ?>

                    </select>

                </div>

            </div>


            <!-- Tombol -->
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