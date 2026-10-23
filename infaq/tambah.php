<?php
$pageTitle = 'Tambah Data Infaq';
require_once '../template/header.php';
require_once "../auth/auth.php";
requireRole(['admin','petugas']);
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">

    <?php if (isset($_GET['pesan'])): ?>
        <?php
        $peringatan = [
            'bulan_tidak_valid' => [
                'class' => 'alert-warning',
                'teks' => 'Tanggal infaq harus sesuai bulan dan tahun sekarang.'
            ],
            'invalid' => [
                'class' => 'alert-danger',
                'teks' => 'Data tidak valid. Periksa kembali tanggal dan nominal.'
            ],
            'gagal' => [
                'class' => 'alert-danger',
                'teks' => 'Data infaq gagal disimpan. Silakan coba kembali.'
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

        <h3 class="form-title">Tambah Data Infaq</h3>
        <p class="form-subtitle">Tambahkan data infaq baru ke dalam sistem.</p>

        <form action="simpan.php" method="POST">

            <div class="form-group">
                <label>Nama Donatur <span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-person"></i>
                    </span>
                    <input type="text" name="nama_donatur" class="form-control" placeholder="Masukkan nama donatur" required>
                </div>
            </div>

            <div class="form-group">
                <label>Nominal <span class="required">*</span></label>

                <div class="input-group">

                    <span class="input-group-text">
                        Rp
                    </span>

                    <input type="number" name="nominal" class="form-control" placeholder="Masukkan nominal infaq" min="1000" data-nominal-input="previewNominal" required>
                </div>

                <span class="nominal-preview">
                    Nominal: <span id="previewNominal">0</span>
                </span>
            </div>
            <div class="form-group">
                <label>Tanggal <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-event"></i>
                    </span>

                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
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

<script src="../assets/js/script.js"></script>

<?php require_once '../template/footer.php'; ?>