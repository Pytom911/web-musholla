<?php
$pageTitle = 'Edit Kegiatan';
require_once __DIR__ . '/../template/header.php';
require_once "../auth/auth.php";
requireRole(['admin','petugas']);

$id = $_GET['id'] ?? 0;

$data = mysqli_query($connect, "SELECT * FROM kegiatan WHERE id_kegiatan='$id'");

if (mysqli_num_rows($data) == 0) {
    echo "<script>
            alert('Data kegiatan tidak ditemukan!');
            window.location='index.php';
          </script>";
    exit;
}

$row = mysqli_fetch_assoc($data);
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
                'duplikat' => [
                    'class' => 'alert-warning',
                    'teks' => 'Kegiatan dengan nama, nominal pengeluaran, dan tanggal tersebut sudah tersedia.'
                ],
                'invalid' => [
                    'class' => 'alert-danger',
                    'teks' => 'Data tidak valid. Periksa kembali tanggal, kelas, dan nominal.'
                ],
                    'saldo_tidak_cukup' => [
                    'class' => 'alert-danger',
                    'teks' => 'Saldo tidak cukup untuk melanjutkan. Pengeluaran melebihi saldo keuangan yang tersedia.'
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

                    <input type="number" name="pengeluaran" class="form-control" value="<?= $row['pengeluaran']; ?>" min="0" required>
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

                <button type="submit" class="btn-update">
                    <i class="bi bi-check-circle-fill"></i>
                    Update Data
                </button>

            </div>

        </form>

    </div>

</div>

<?php require_once __DIR__ . '/../template/footer.php'; ?>