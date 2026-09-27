<?php
require_once __DIR__ . '/../auth/auth.php';
requireRole(['admin', 'petugas']);

$kelas = mysqli_query(
    $connect,
    "SELECT * FROM kelas
    ORDER BY nama_kelas ASC,
    CASE tingkat
        WHEN 'X' THEN 1
        WHEN 'XI' THEN 2
        WHEN 'XII' THEN 3
        ELSE 4
    END ASC,
    bagian ASC"
);

$pageTitle = 'Tambah Jadwal Sholat';
require_once __DIR__ . '/../template/header.php';
?>

<link rel="stylesheet" href="<?= asset('css/data.css') ?>">

<div class="container-fluid">
    <a href="<?= url('jadwal_sholat/index.php') ?>" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">
        <h3 class="form-title">Tambah Jadwal Sholat</h3>
        <p class="form-subtitle">Tambahkan jadwal sholat baru ke dalam sistem.</p>

        <?php if (($_GET['pesan'] ?? '') === 'gagal'): ?>
            <div class="alert alert-danger">Jadwal sholat tidak dapat disimpan. Periksa kembali isian data.</div>
        <?php endif; ?>

        <form action="<?= url('jadwal_sholat/simpan.php') ?>" method="POST">
            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-event"></i>
                    </span>
                    <input type="date" id="tanggal" name="tanggal" class="form-control">
                </div>
            </div>

            <div class="form-group">
                <label for="waktu_sholat">Waktu Sholat <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-clock"></i>
                    </span>
                    <select id="waktu_sholat" name="waktu_sholat" class="form-select" required>
                        <option value="">-- Pilih Waktu Sholat --</option>
                        <option value="Ashar">Ashar</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="id_kelas">Kelas <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-mortarboard"></i>
                    </span>
                    <select id="id_kelas" name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>

                        <?php
                        $jurusanAktif = '';

                        while ($row = mysqli_fetch_assoc($kelas)):
                            $jurusan = $row['nama_kelas'];

                            if ($jurusanAktif !== $jurusan) {
                                if ($jurusanAktif !== '') {
                                    echo '</optgroup>';
                                }

                                echo '<optgroup label="' . htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') . '">';
                                $jurusanAktif = $jurusan;
                            }

                            $labelKelas = $row['nama_kelas'] . ' - ' . $row['tingkat'];

                            if ($row['bagian'] !== null && $row['bagian'] !== '') {
                                $labelKelas .= ' (' . $row['bagian'] . ')';
                            }
                        ?>
                            <option value="<?= (int) $row['id_kelas']; ?>">
                                <?= htmlspecialchars($labelKelas, ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endwhile; ?>

                        <?php if ($jurusanAktif !== ''): ?>
                            </optgroup>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-footer">
                <a href="<?= url('jadwal_sholat/index.php') ?>" class="btn-cancel">
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

<?php require_once __DIR__ . '/../template/footer.php'; ?>