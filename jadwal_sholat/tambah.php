<?php
require_once "../auth/auth.php";
requireRole(['admin','petugas']);
$pageTitle = 'Tambah Jadwal Sholat';
require_once '../template/header.php';

// Ambil semua data kelas
$kelas = mysqli_query(
    $connect,
    "SELECT * FROM kelas ORDER BY nama_kelas ASC"
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
            Tambah Jadwal Sholat
        </h3>

        <p class="form-subtitle">
            Tambahkan jadwal sholat baru ke dalam sistem.
        </p>


        <form action="simpan.php" method="POST">


            <!-- Hari -->
            <div class="form-group">

                <label>
                    Hari
                    <span class="required">*</span>
                </label>

                <div class="input-group">

                    <span class="input-group-text">

                        <i class="bi bi-calendar-week"></i>

                    </span>


                    <select name="hari" class="form-select" required>

                        <option value="">
                            -- Pilih Hari --
                        </option>

                        <option value="Senin">
                            Senin
                        </option>

                        <option value="Selasa">
                            Selasa
                        </option>

                        <option value="Rabu">
                            Rabu
                        </option>

                        <option value="Kamis">
                            Kamis
                        </option>

                        <option value="Jumat">
                            Jumat
                        </option>

                    </select>

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


                        <!-- HARUS SAMA DENGAN ENUM DATABASE -->

                        <option value="Dzuhur">
                            Dzuhur
                        </option>


                        <option value="Ashar">
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
                            $row = mysqli_fetch_assoc($kelas)
                        ): ?>

                            <option value="<?= $row['id_kelas']; ?>">

                                <?= htmlspecialchars(
                                    $row['nama_kelas']
                                ); ?>

                                -

                                <?= htmlspecialchars(
                                    $row['tingkat']
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


                <button type="submit" class="btn-save">

                    <i class="bi bi-check-circle-fill"></i>

                    Simpan Data

                </button>

            </div>


        </form>

    </div>

</div>


<?php require_once '../template/footer.php'; ?>