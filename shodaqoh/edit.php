<?php
$pageTitle = 'Edit Data Shodaqoh';
require_once '../template/header.php';
require_once "../auth/auth.php";
requireRole(['admin', 'petugas']);

$id = mysqli_real_escape_string($connect, $_GET['id']);

$data = mysqli_query($connect, "SELECT * FROM shodaqoh WHERE id_shodaqoh='$id'");
$row = mysqli_fetch_assoc($data);

if (!$row) {
    echo "<script>alert('Data tidak ditemukan!');window.location='index.php';</script>";
    exit;
}

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
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">
    <a href="index.php" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">
        <h3 class="form-title">Edit Data Shodaqoh Jumat</h3>
        <p class="form-subtitle">Perbarui data shodaqoh Jumat yang telah tersimpan.</p>

        <form action="update.php" method="POST">
            <input type="hidden" name="id_shodaqoh" value="<?= $row['id_shodaqoh']; ?>">

            <div class="form-group">
                <label>Tanggal <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-event"></i>
                    </span>
                    <input type="date" name="tanggal" class="form-control" value="<?= $row['tanggal']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Kelas <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <select id="id_kelas" name="id_kelas" class="form-select" required>
                        <option value="">-- Pilih Kelas --</option>

                        <?php
                        $jurusanAktif = '';

                        while ($kelasRow = mysqli_fetch_assoc($kelas)):
                            $jurusan = $kelasRow['nama_kelas'];

                            if ($jurusanAktif !== $jurusan) {
                                if ($jurusanAktif !== '') {
                                    echo '</optgroup>';
                                }

                                echo '<optgroup label="' . htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') . '">';
                                $jurusanAktif = $jurusan;
                            }

                            $labelKelas = $kelasRow['nama_kelas'] . ' - ' . $kelasRow['tingkat'];

                            if ($kelasRow['bagian'] !== null && $kelasRow['bagian'] !== '') {
                                $labelKelas .= ' (' . $kelasRow['bagian'] . ')';
                            }
                        ?>

                            <option
                                value="<?= (int) $kelasRow['id_kelas']; ?>"
                                <?= (int) $row['id_kelas'] === (int) $kelasRow['id_kelas'] ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($labelKelas, ENT_QUOTES, 'UTF-8'); ?>
                            </option>

                        <?php endwhile; ?>

                        <?php if ($jurusanAktif !== ''): ?>
                            </optgroup>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Nominal <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        Rp
                    </span>
                    <input type="number" name="nominal" class="form-control" value="<?= $row['nominal']; ?>" min="1000" required>
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