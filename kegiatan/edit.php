<?php
$pageTitle = 'Edit Kegiatan';
require_once __DIR__ . '/../template/header.php';
require_once "../auth/auth.php";
requireRole(['admin','petugas']);

$id = (int) ($_GET['id'] ?? 0);

$data = mysqli_query($connect, "SELECT * FROM kegiatan WHERE id_kegiatan='$id'");

if (mysqli_num_rows($data) == 0) {
    echo "<script>
            alert('Data kegiatan tidak ditemukan!');
            window.location='index.php';
          </script>";
    exit;
}

$row = mysqli_fetch_assoc($data);

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

$saldoSaatIni = $querySaldo
    ? (float) ($querySaldo->fetch_assoc()['saldo'] ?? 0)
    : 0.0;

// Pengeluaran data ini masih terhitung di saldo, jadi kembalikan saat menghitung batas.
$pengeluaranLama = (float) $row['pengeluaran'];
$saldoTersedia = $saldoSaatIni + $pengeluaranLama;
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
            'saldo_tidak_cukup' => [
                'class' => 'alert-danger',
                'teks' => 'Pengeluaran lebih besar dari saldo saat ini. Harap isi ulang nominal pengeluaran.'
            ],
            'gagal' => [
                'class' => 'alert-danger',
                'teks' => 'Data kegiatan gagal diperbarui. Silakan coba kembali.'
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

        <h3 class="form-title">Edit Kegiatan</h3>
        <p class="form-subtitle">
            Perbarui data kegiatan musholla pada form di bawah ini.
        </p>

        <form action="update.php" method="POST">

            <input type="hidden" name="id_kegiatan" value="<?= $row['id_kegiatan']; ?>">

            <div class="form-group">
                <label>Nama Kegiatan <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-event"></i>
                    </span>

                    <input type="text" name="nama_kegiatan" class="form-control" maxlength="50" value="<?= htmlspecialchars($row['nama_kegiatan']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Pengeluaran <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">Rp</span>

                    <input type="number" name="pengeluaran" id="inputPengeluaran" class="form-control" value="<?= $row['pengeluaran']; ?>" min="0" data-nominal-input="previewPengeluaran" required>
                </div>

                <span class="nominal-preview">
                    Nominal: <span id="previewPengeluaran">0</span>
                </span>

                <small class="<?= $saldoTersedia < 0 ? 'text-danger' : 'text-muted'; ?>">
                    Saldo kas bulan ini untuk kegiatan ini:
                    <strong>Rp<?= number_format($saldoTersedia, 0, ',', '.'); ?></strong>
                </small>

                <div class="alert alert-danger mt-2 py-2 small" id="peringatanSaldo" role="alert" hidden>
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <span id="teksPeringatanSaldo"></span>
                </div>
            </div>

            <div class="form-group">
                <label>Tanggal <span class="required">*</span></label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-calendar-date"></i>
                    </span>

                    <input type="date" name="tanggal" class="form-control" value="<?= $row['tanggal']; ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Deskripsi</label>

                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-card-text"></i>
                    </span>

                    <textarea name="deskripsi" class="form-control" rows="4" placeholder="Masukkan deskripsi kegiatan" required><?= htmlspecialchars($row['deskripsi']); ?></textarea>
                </div>
            </div>

            <div class="form-footer">

                <a href="index.php" class="btn-cancel">
                    <i class="bi bi-arrow-left-circle"></i>
                    Batal
                </a>

                <button type="submit" id="tombolSimpan" class="btn-update">
                    <i class="bi bi-check-circle-fill"></i>
                    Update Data
                </button>

            </div>

        </form>

    </div>

</div>

<script src="../assets/js/script.js"></script>
<script>
(function () {
    // Sama dengan update.php: blokir hanya saat saldo minus dan pengeluaran naik.
    // Menurunkan pengeluaran tetap diizinkan agar saldo minus bisa diperbaiki.
    const saldoTersedia = <?= json_encode($saldoTersedia); ?>;
    const pengeluaranLama = <?= json_encode($pengeluaranLama); ?>;
    const inputPengeluaran = document.getElementById('inputPengeluaran');
    const peringatan = document.getElementById('peringatanSaldo');
    const teksPeringatan = document.getElementById('teksPeringatanSaldo');

    const formatRupiah = (nilai) => new Intl.NumberFormat('id-ID').format(nilai);

    function melebihiSaldo() {
        const nominal = Number(inputPengeluaran.value);

        return (
            inputPengeluaran.value !== '' &&
            nominal > saldoTersedia &&
            nominal > pengeluaranLama
        );
    }

    function periksaSaldo() {
        const nominal = Number(inputPengeluaran.value);
        const melebihi = inputPengeluaran.value !== '' && nominal > saldoTersedia;
        const nominalNaik = nominal > pengeluaranLama;

        if (melebihi && nominalNaik) {
            teksPeringatan.textContent =
                'Pengeluaran melebihi saldo tersedia — kurangi minimal Rp' +
                formatRupiah(nominal - saldoTersedia) + '.';
            peringatan.classList.remove('alert-warning');
            peringatan.classList.add('alert-danger');
            peringatan.hidden = false;
        } else if (melebihi) {
            teksPeringatan.textContent =
                'Pengeluaran masih melebihi saldo tersedia (Rp' +
                formatRupiah(saldoTersedia) +
                '), tetapi tetap dapat disimpan karena nominal berkurang untuk memperbaiki saldo.';
            peringatan.classList.remove('alert-danger');
            peringatan.classList.add('alert-warning');
            peringatan.hidden = false;
        } else {
            peringatan.hidden = true;
        }
    }

    inputPengeluaran.addEventListener('input', periksaSaldo);

    inputPengeluaran.form.addEventListener('submit', function (event) {
        if (melebihiSaldo()) {
            event.preventDefault();

            teksPeringatan.textContent =
                'Pengeluaran lebih besar dari saldo saat ini. Harap isi ulang nominal pengeluaran.';
            peringatan.classList.remove('alert-warning');
            peringatan.classList.add('alert-danger');
            peringatan.hidden = false;
            peringatan.scrollIntoView({ behavior: 'smooth', block: 'center' });
            inputPengeluaran.focus();
        }
    });

    periksaSaldo();
})();
</script>

<?php require_once __DIR__ . '/../template/footer.php'; ?>