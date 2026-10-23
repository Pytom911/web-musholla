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
    <?php if (isset($_GET['pesan'])): ?>
        <?php
        $peringatan = [
            'duplikat' => [
                'class' => 'alert-warning',
                'teks' => 'Data shodaqoh dengan tanggal, kelas, dan nominal yang sama sudah tersedia.'
            ],
            'bulan_tidak_valid' => [
                'class' => 'alert-warning',
                'teks' => 'Tanggal shodaqoh harus sesuai bulan dan tahun sekarang.'
            ],
            'invalid' => [
                'class' => 'alert-danger',
                'teks' => 'Data tidak valid. Periksa kembali tanggal, kelas, dan nominal.'
            ],
            'gagal' => [
                'class' => 'alert-danger',
                'teks' => 'Data shodaqoh gagal diperbarui. Silakan coba kembali.'
            ]
        ];

        $pesan = $peringatan[$_GET['pesan']] ?? null;
        ?>

        <?php if ($pesan): ?>
            <div class="alert <?= $pesan['class']; ?>" role="alert">
                <?= htmlspecialchars($pesan['teks'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
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
                    <input type="number" name="nominal" class="form-control" value="<?= $row['nominal']; ?>" min="1000" data-nominal-input="previewNominal" required>
                </div>

                <span class="nominal-preview">
                    Nominal: <span id="previewNominal">0</span>
                </span>
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

<script src="../assets/js/script.js"></script>

<?php require_once '../template/footer.php'; ?>