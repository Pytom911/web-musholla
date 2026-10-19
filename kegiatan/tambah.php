<?php
$pageTitle = 'Tambah Kegiatan';
require_once __DIR__ . '/../template/header.php';
require_once "../auth/auth.php";
requireRole(['admin','petugas']);

// Arus kas bulan berjalan, sama dengan saldo keuangan di dashboard.
$querySaldo = $connect->query(
    "SELECT
        (
            (SELECT COALESCE(SUM(nominal), 0) FROM infaq WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()))
            +
            (SELECT COALESCE(SUM(nominal), 0) FROM shodaqoh WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()))
            -
            (SELECT COALESCE(SUM(pengeluaran), 0) FROM kegiatan WHERE MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()))
        ) AS saldo"
);

$saldoTersedia = $querySaldo
    ? (float) ($querySaldo->fetch_assoc()['saldo'] ?? 0)
    : 0.0;
?>

<link rel="stylesheet" href="../assets/css/data.css">

<div class="container-fluid">
    <?php if (isset($_GET['pesan'])): ?>
        <?php
            $peringatan = [
                'duplikat' => [
                    'class' => 'alert-warning',
                    'teks' => 'Kegiatan dengan nama, nominal pengeluaran, dan tanggal tersebut sudah tersedia.'
                ],
                'bulan_tidak_valid' => [
                    'class' => 'alert-warning',
                    'teks' => 'Tanggal kegiatan harus sesuai bulan dan tahun sekarang.'
                ],
                'invalid' => [
                    'class' => 'alert-danger',
                    'teks' => 'Data kegiatan tidak valid. Periksa kembali nama, deskripsi, pengeluaran, dan tanggal.'
                ],
                'gagal' => [
                    'class' => 'alert-danger',
                    'teks' => 'Data kegiatan gagal disimpan. Silakan coba kembali.'
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
    <div class="breadcrumb-wrapper">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?= url() ?>">Dashboard</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="<?= url('kegiatan/index.php') ?>">Kegiatan</a>
                </li>
                <li class="breadcrumb-item active">
                    Tambah Kegiatan
                </li>
            </ol>
        </nav>
    </div>

    <a href="<?= url('kegiatan/index.php') ?>" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Kembali
    </a>

    <div class="form-card">

        <div class="form-title">
            Tambah Kegiatan
        </div>

        <div class="form-subtitle">
            Silakan lengkapi data kegiatan musholla pada form di bawah ini.
        </div>

        <form action="simpan.php" method="POST">
            <div class="form-group">
                <label>Nama Kegiatan<span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-event"></i>
                    </span>
                    <input type="text" name="nama_kegiatan" class="form-control" placeholder="Masukkan nama kegiatan" maxlength="50" required>
                </div>
            </div>

            <div class="form-group">
                <label>Pengeluaran<span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        Rp
                    </span>
                    <input type="number" name="pengeluaran" id="inputPengeluaran" class="form-control" placeholder="Masukkan nominal pengeluaran" min="0" required>
                </div>
                <small class="<?= $saldoTersedia < 0 ? 'text-danger' : 'text-muted'; ?>">
                    Saldo kas bulan ini:
                    <strong>Rp<?= number_format($saldoTersedia, 0, ',', '.'); ?></strong>
                </small>
                <div class="alert alert-danger mt-2 py-2 small" id="peringatanSaldo" role="alert" hidden>
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <span id="teksPeringatanSaldo"></span>
                </div>
            </div>

            <div class="form-group">
                <label>Tanggal<span class="required">*</span></label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-date"></i>
                    </span>
                    <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d'); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-card-text"></i>
                    </span>
                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Masukkan deskripsi kegiatan" required></textarea>
                </div>
            </div>

            <div class="form-footer">

                <a href="<?= url('kegiatan/index.php') ?>" class="btn-cancel">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    Batal
                </a>

                <button type="submit" id="tombolSimpan" class="btn-save">
                    <i class="bi bi-check-circle-fill"></i>
                    Simpan Data
                </button>

            </div>

        </form>
    </div>

</div>

<script>
(function () {
    // Kunci tombol saat nominal melebihi saldo; sama dengan blokir di simpan.php.
    const saldoTersedia = <?= json_encode($saldoTersedia); ?>;
    const inputPengeluaran = document.getElementById('inputPengeluaran');
    const tombolSimpan = document.getElementById('tombolSimpan');
    const peringatan = document.getElementById('peringatanSaldo');
    const teksPeringatan = document.getElementById('teksPeringatanSaldo');

    const formatRupiah = (nilai) => new Intl.NumberFormat('id-ID').format(nilai);

    function periksaSaldo() {
        const nominal = Number(inputPengeluaran.value);
        const melebihi = inputPengeluaran.value !== '' && nominal > saldoTersedia;

        if (melebihi) {
            teksPeringatan.textContent =
                'Pengeluaran melebihi saldo tersedia — kurangi minimal Rp' +
                formatRupiah(nominal - saldoTersedia) + '.';
            peringatan.hidden = false;
            tombolSimpan.disabled = true;
        } else {
            peringatan.hidden = true;
            tombolSimpan.disabled = false;
        }
    }

    inputPengeluaran.addEventListener('input', periksaSaldo);

    tombolSimpan.form.addEventListener('submit', function (event) {
        periksaSaldo();
        if (tombolSimpan.disabled) {
            event.preventDefault();
        }
    });

    periksaSaldo();
})();
</script>

<?php require_once __DIR__ . '/../template/footer.php'; ?>