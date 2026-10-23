<?php
$pageTitle = 'Tambah Data Shodaqoh Jumat';
require_once '../template/header.php';
require_once "../auth/auth.php";
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
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">
    <?php if (isset($_GET['pesan'])): ?>
        <?php
        $peringatan = [
            'duplikat' => [
                    'class' => 'alert-warning',
                    'teks' => 'Data shodaqoh untuk kelas dan tanggal tersebut sudah tersedia.'
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
                    'teks' => 'Data shodaqoh gagal disimpan. Silakan coba kembali.'
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
        <h3 class="form-title">Tambah Data Shodaqoh Jumat</h3>
        <p class="form-subtitle">Tambahkan data shodaqoh Jumat baru ke dalam sistem.</p>

        <form action="simpan.php" method="POST">
            <div class="form-group">
                <label>Tanggal <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-event"></i>
                    </span>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Kelas <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-mortarboard-fill"></i>
                    </span>
                    <select name="id_kelas" class="form-select" required>
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

            <div class="form-group">
                <label>Nominal <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        Rp
                    </span>
                    <input type="number" name="nominal" class="form-control" placeholder="Masukkan nominal shodaqoh" min="1000" data-nominal-input="previewNominal" required>
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
                <button type="submit" class="btn-save">
                    <i class="bi bi-check-circle-fill"></i>
                    Simpan Data
                </button>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/script.js"></script>

<?php require_once '../template/footer.php'; ?>