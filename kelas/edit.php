<?php
require_once __DIR__ . '/../auth/auth.php';
requireRole(['admin', 'petugas']);

$rawId = $_GET['id'] ?? null;
$id = is_scalar($rawId) ? (int) $rawId : 0;

if ($id <= 0) {
    redirect('kelas/index.php');
}

$stmt = $connect->prepare(
    "SELECT id_kelas, nama_kelas, tingkat, bagian
     FROM kelas
     WHERE id_kelas = ?"
);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result ? $result->fetch_assoc() : null;

if (!$row) {
    redirect('kelas/index.php');
}

$pageTitle = 'Edit Data Kelas';
require_once __DIR__ . '/../template/header.php';
?>

<link rel="stylesheet" href="<?= asset('css/data.css') ?>">

<div class="container-fluid">
    <a href="<?= url('kelas/index.php') ?>" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">
        <h3 class="form-title">Edit Data Kelas</h3>
        <p class="form-subtitle">Perbarui data kelas yang telah tersimpan.</p>

        <?php if (($_GET['pesan'] ?? '') === 'gagal'): ?>
            <div class="alert alert-danger">Data kelas tidak dapat diperbarui. Periksa kembali isian data.</div>
        <?php endif; ?>

        <form action="<?= url('kelas/update.php') ?>" method="POST">
            <input type="hidden" name="id_kelas" value="<?= (int) $row['id_kelas'] ?>">

            <div class="form-group">
                <label for="nama_kelas">Nama Kelas <span class="required">*</span></label>
                <input type="text" id="nama_kelas" name="nama_kelas" class="form-control"
                    value="<?= htmlspecialchars((string) $row['nama_kelas'], ENT_QUOTES, 'UTF-8') ?>"
                    maxlength="30" required>
            </div>

            <div class="form-group">
                <label for="tingkat">Tingkat <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <select id="tingkat" name="tingkat" class="form-select" required>
                        <option value="">-- Pilih Tingkat --</option>
                        <option value="X" <?= $row['tingkat'] === 'X' ? 'selected' : ''; ?>>X</option>
                        <option value="XI" <?= $row['tingkat'] === 'XI' ? 'selected' : ''; ?>>XI</option>
                        <option value="XII" <?= $row['tingkat'] === 'XII' ? 'selected' : ''; ?>>XII</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="bagian">Bagian</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-diagram-3"></i>
                    </span>
                    <select id="bagian" name="bagian" class="form-select">
                        <option value="">-- Tidak dipilih --</option>
                        <option value="01" <?= $row['bagian'] === '01' ? 'selected' : ''; ?>>01</option>
                        <option value="02" <?= $row['bagian'] === '02' ? 'selected' : ''; ?>>02</option>
                    </select>
                </div>
            </div>

            <div class="form-footer">
                <a href="<?= url('kelas/index.php') ?>" class="btn-cancel">
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
