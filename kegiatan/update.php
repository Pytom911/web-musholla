
<?php
require_once __DIR__ . '/../config/connect.php';
require_once __DIR__ . '/../auth/auth.php';

requireRole(['admin', 'petugas']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id_kegiatan', FILTER_VALIDATE_INT);
$nama = trim($_POST['nama_kegiatan'] ?? '');
$deskripsi = trim($_POST['deskripsi'] ?? '');
$pengeluaranInput = $_POST['pengeluaran'] ?? '';
$tanggal = $_POST['tanggal'] ?? '';

if (!$id || $id < 1) {
    header('Location: index.php?pesan=invalid');
    exit;
}

$kembaliEdit = static function (string $pesan) use ($id): void {
    header(
        'Location: edit.php?id=' . $id .
        '&pesan=' . urlencode($pesan) . '&kosong=1'
    );
    exit;
};

// Validasi input.
if (
    $nama === '' ||
    mb_strlen($nama) > 50 ||
    $deskripsi === '' ||
    $pengeluaranInput === '' ||
    !is_numeric($pengeluaranInput) ||
    !is_finite((float) $pengeluaranInput) ||
    (float) $pengeluaranInput < 0
) {
    $kembaliEdit('invalid');
}

$nominal = (float) $pengeluaranInput;

// Validasi tanggal.
$tanggalValid = DateTime::createFromFormat('!Y-m-d', $tanggal);
$kesalahanTanggal = DateTime::getLastErrors();

if (
    !$tanggalValid ||
    ($kesalahanTanggal !== false &&
        (
            $kesalahanTanggal['warning_count'] > 0 ||
            $kesalahanTanggal['error_count'] > 0
        )) ||
    $tanggalValid->format('Y-m-d') !== $tanggal
) {
    $kembaliEdit('invalid');
}

if ($tanggalValid->format('Y-m') !== date('Y-m')) {
    $kembaliEdit('bulan_tidak_valid');
}

// Ambil pengeluaran lama dari kegiatan yang diedit.
$stmtLama = $connect->prepare(
    'SELECT pengeluaran
     FROM kegiatan
     WHERE id_kegiatan = ?'
);

if (!$stmtLama) {
    $kembaliEdit('gagal');
}

$stmtLama->bind_param('i', $id);
$stmtLama->execute();

$dataLama = $stmtLama->get_result()->fetch_assoc();
$stmtLama->close();

if (!$dataLama) {
    header('Location: index.php?pesan=invalid');
    exit;
}

$pengeluaranLama = (float) $dataLama['pengeluaran'];

// Hitung saldo setelah semua pengeluaran saat ini.
$querySaldo = $connect->query(
    "SELECT
        (
            (SELECT COALESCE(SUM(nominal), 0) FROM infaq)
            +
            (SELECT COALESCE(SUM(nominal), 0) FROM shodaqoh)
            -
            (SELECT COALESCE(SUM(pengeluaran), 0) FROM kegiatan)
        ) AS saldo"
);

if (!$querySaldo) {
    $kembaliEdit('gagal');
}

$dataSaldo = $querySaldo->fetch_assoc();
$saldoSaatIni = (float) ($dataSaldo['saldo'] ?? 0);
$querySaldo->free();

// Proyeksikan saldo setelah mengganti pengeluaran lama.
$saldoSetelahUpdate = $saldoSaatIni
    + $pengeluaranLama
    - $nominal;

// Jika saldo masih minus, jangan izinkan pengeluaran bertambah.
// Pengeluaran yang dikurangi tetap boleh disimpan untuk membantu
// memperbaiki saldo minus yang sudah ada sebelumnya.
if (
    $saldoSetelahUpdate < 0 &&
    $nominal > $pengeluaranLama
) {
    $kembaliEdit('saldo_tidak_cukup');
}

// Periksa duplikat selain data yang sedang diedit.
$cekDuplikat = $connect->prepare(
    'SELECT id_kegiatan
     FROM kegiatan
     WHERE nama_kegiatan = ?
       AND pengeluaran = ?
       AND tanggal = ?
       AND id_kegiatan <> ?
     LIMIT 1'
);

if (!$cekDuplikat) {
    $kembaliEdit('gagal');
}

$cekDuplikat->bind_param(
    'sdsi',
    $nama,
    $nominal,
    $tanggal,
    $id
);

$cekDuplikat->execute();
$duplikat = $cekDuplikat->get_result()->num_rows > 0;
$cekDuplikat->close();

if ($duplikat) {
    $kembaliEdit('duplikat');
}

// Perbarui data.
$stmtUpdate = $connect->prepare(
    'UPDATE kegiatan
     SET nama_kegiatan = ?,
         deskripsi = ?,
         pengeluaran = ?,
         tanggal = ?
     WHERE id_kegiatan = ?'
);

if (!$stmtUpdate) {
    $kembaliEdit('gagal');
}

$stmtUpdate->bind_param(
    'ssdsi',
    $nama,
    $deskripsi,
    $nominal,
    $tanggal,
    $id
);

$berhasil = $stmtUpdate->execute();
$stmtUpdate->close();

if ($berhasil) {
    header('Location: index.php?pesan=update');
    exit;
}

$kembaliEdit('gagal');
