<?php
require_once __DIR__ . '/../auth/auth.php';
requireRole(['admin', 'petugas']);

$rawId = $_GET['id'] ?? null;
$id = is_scalar($rawId) ? (int) $rawId : 0;

if ($id <= 0) {
    redirect('jadwal_sholat/index.php');
}

$stmt = $connect->prepare(
    "SELECT id_jadwal, tanggal, waktu_sholat, id_kelas
     FROM jadwal_sholat
     WHERE id_jadwal = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result ? $result->fetch_assoc() : null;

if (!$row) {
    redirect('jadwal_sholat/index.php');
}

$kelas = mysqli_query(
    $connect,
    "SELECT id_kelas, nama_kelas, tingkat, bagian
     FROM kelas
     ORDER BY FIELD(tingkat, 'X', 'XI', 'XII'), nama_kelas ASC, id_kelas ASC"
);

$pageTitle = 'Edit Jadwal Sholat';
require_once __DIR__ . '/../template/header.php';
?>

<link rel="stylesheet" href="<?= asset('css/data.css') ?>">

<div class="container-fluid">
    <a href="<?= url('jadwal_sholat/index.php') ?>" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">
        <h3 class="form-title">Edit Jadwal Sholat</h3>
        <p class="form-subtitle">Ubah data jadwal sholat.</p>

        <?php if (($_GET['pesan'] ?? '') === 'gagal'): ?>
            <div class="alert alert-danger">Jadwal sholat tidak dapat diperbarui. Periksa kembali isian data.</div>
        <?php endif; ?>

        <form action="<?= url('jadwal_sholat/update.php') ?>" method="POST">
            <input type="hidden" name="id_jadwal" value="<?= (int) $row['id_jadwal'] ?>">

            <div class="form-group">
                <label for="tanggal">Tanggal</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-event"></i>
                    </span>
                    <input type="date" id="tanggal" name="tanggal" class="form-control"
                        value="<?= htmlspecialchars((string) ($row['tanggal'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
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
                        <option value="Ashar" <?= $row['waktu_sholat'] === 'Ashar' ? 'selected' : ''; ?>>Ashar</option>
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
                        <?php while ($kelasRow = mysqli_fetch_assoc($kelas)): ?>
                            <?php
                            $labelKelas = $kelasRow['nama_kelas'] . ' - ' . $kelasRow['tingkat'];
                            if ($kelasRow['bagian'] !== null && $kelasRow['bagian'] !== '') {
                                $labelKelas .= ' (Bagian ' . $kelasRow['bagian'] . ')';
                            }
                            ?>
                            <option value="<?= (int) $kelasRow['id_kelas'] ?>"
                                <?= (int) $row['id_kelas'] === (int) $kelasRow['id_kelas'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($labelKelas, ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-footer">
                <a href="<?= url('jadwal_sholat/index.php') ?>" class="btn-cancel">
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

<?php require_once __DIR__ . '/../template/footer.php'; ?>
