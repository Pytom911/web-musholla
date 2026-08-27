<?php

require_once "../config/config.php";


$guru = mysqli_query(
    $connect,
    "SELECT * FROM guru ORDER BY nama_guru ASC"
);

$pageTitle = "Tambah Jadwal Imam";

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

    <!-- Form -->
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-body p-4 p-md-5">

            <div class="mb-4">

                <h2
                    class="fw-bold mb-2"
                    style="border-left: 5px solid #198754; padding-left: 12px;"
                >
                    Tambah Jadwal Imam
                </h2>

                <p class="text-muted mb-0">
                    Tambahkan jadwal imam baru ke dalam sistem.
                </p>

            </div>

            <form action="simpan.php" method="POST">

                <!-- Tanggal -->
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Tanggal <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-calendar3"></i>
                        </span>

                        <input
                            type="date"
                            name="tanggal"
                            class="form-control"
                            required
                        >

                    </div>

                </div>

                <!-- Waktu Sholat -->
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Waktu Sholat <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-clock"></i>
                        </span>

                        <select
                            name="waktu_sholat"
                            class="form-select"
                            required
                        >
                            <option value="">
                                -- Pilih Waktu Sholat --
                            </option>
                            <option value="Dzuhur">Dzuhur</option>
                            <option value="Ashar">Ashar</option>
                        </select>

                    </div>

                </div>

                <!-- Nama Guru -->
                <div class="mb-4">

                    <label class="form-label fw-semibold">
                        Nama Guru <span class="text-danger">*</span>
                    </label>

                    <div class="input-group">

                        <span class="input-group-text">
                            <i class="bi bi-person"></i>
                        </span>

                        <select
                            name="id_guru"
                            class="form-select"
                            required
                        >
                            <option value="">
                                -- Pilih Guru --
                            </option>

                            <?php while ($g = mysqli_fetch_assoc($guru)) : ?>

                                <option value="<?= $g['id_guru']; ?>">
                                    <?= htmlspecialchars($g['nama_guru']); ?>
                                </option>

                            <?php endwhile; ?>

                        </select>

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

</div>

<?php require_once "../template/footer.php"; ?>