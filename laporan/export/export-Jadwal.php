<?php

require_once '../../auth/auth.php';
requireRole(['admin', 'petugas']);

require_once '../../config/connect.php';
require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

/*
|--------------------------------------------------------------------------
| KONFIGURASI DOMPDF
|--------------------------------------------------------------------------
*/

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);


/*
|--------------------------------------------------------------------------
| PARAMETER FILTER (dikirim dari halaman laporan)
|--------------------------------------------------------------------------
*/

$tahunSekarang = (int) date('Y');

// Nama bulan untuk keterangan di PDF
$bulanNama = [
    1  => 'Januari',
    2  => 'Februari',
    3  => 'Maret',
    4  => 'April',
    5  => 'Mei',
    6  => 'Juni',
    7  => 'Juli',
    8  => 'Agustus',
    9  => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

// Filter bulan (0 = semua bulan)
$bulanDipilih = isset($_GET['bulan']) ? (int) $_GET['bulan'] : 0;

if ($bulanDipilih < 0 || $bulanDipilih > 12) {
    $bulanDipilih = 0;
}

// Opsi waktu sholat diambil dari enum database
$waktuSholatTersedia = ['Dzuhur', 'Ashar'];
$enumStmt = $connect->prepare(
    "SELECT COLUMN_TYPE
     FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = DATABASE()
       AND TABLE_NAME = 'jadwal_sholat'
       AND COLUMN_NAME = 'waktu_sholat'"
);
$enumStmt->execute();
$enumRow = $enumStmt->get_result()->fetch_assoc();

if ($enumRow && preg_match("/^enum\('(.*)'\)$/i", $enumRow['COLUMN_TYPE'], $m)) {
    $waktuSholatTersedia = explode("','", $m[1]);
}

// Filter waktu sholat ('' = semua waktu)
$waktuDipilih = isset($_GET['waktu_sholat']) ? (string) $_GET['waktu_sholat'] : '';

if (!in_array($waktuDipilih, $waktuSholatTersedia, true)) {
    $waktuDipilih = '';
}

// Filter kelas (0 = semua kelas, divalidasi ke tabel kelas)
$idKelasDipilih = isset($_GET['id_kelas']) ? (int) $_GET['id_kelas'] : 0;

if ($idKelasDipilih > 0) {
    $stmtValidKelas = $connect->prepare(
        "SELECT nama_kelas, tingkat, bagian FROM kelas WHERE id_kelas = ?"
    );
    $stmtValidKelas->bind_param('i', $idKelasDipilih);
    $stmtValidKelas->execute();
    $rowValidKelas = $stmtValidKelas->get_result()->fetch_assoc();

    if ($rowValidKelas) {
        $labelKelasTerpilih = $rowValidKelas['nama_kelas'] . ' - ' . $rowValidKelas['tingkat'];

        if ($rowValidKelas['bagian'] !== null && $rowValidKelas['bagian'] !== '') {
            $labelKelasTerpilih .= ' (' . $rowValidKelas['bagian'] . ')';
        }
    } else {
        $idKelasDipilih = 0;
    }
}

/*
|--------------------------------------------------------------------------
| SUSUN WHERE (prepared)
|--------------------------------------------------------------------------
*/

$conditions = [];
$jenisBind = '';
$nilaiBind = [];

if ($bulanDipilih >= 1 && $bulanDipilih <= 12) {
    $conditions[] = "jadwal_sholat.tanggal IS NOT NULL
        AND MONTH(jadwal_sholat.tanggal) = ?
        AND YEAR(jadwal_sholat.tanggal) = ?";
    $jenisBind .= 'ii';
    $nilaiBind[] = $bulanDipilih;
    $nilaiBind[] = $tahunSekarang;
}

if ($waktuDipilih !== '') {
    $conditions[] = "jadwal_sholat.waktu_sholat = ?";
    $jenisBind .= 's';
    $nilaiBind[] = $waktuDipilih;
}

if ($idKelasDipilih > 0) {
    $conditions[] = "jadwal_sholat.id_kelas = ?";
    $jenisBind .= 'i';
    $nilaiBind[] = $idKelasDipilih;
}

$whereSql = $conditions !== []
    ? 'WHERE ' . implode(' AND ', $conditions)
    : '';

// Keterangan filter yang dicetak di PDF
$keteranganFilter = 'Seluruh data jadwal sholat berdasarkan tanggal dan kelas';
$daftarKeterangan = [];

if ($bulanDipilih >= 1 && $bulanDipilih <= 12) {
    $daftarKeterangan[] = 'Periode: ' . $bulanNama[$bulanDipilih] . ' ' . $tahunSekarang;
}

if ($waktuDipilih !== '') {
    $daftarKeterangan[] = 'Waktu: ' . $waktuDipilih;
}

if ($idKelasDipilih > 0) {
    $daftarKeterangan[] = 'Kelas: ' . ($labelKelasTerpilih ?? '');
}

if ($daftarKeterangan !== []) {
    $keteranganFilter = 'Menampilkan data — ' . implode(' · ', $daftarKeterangan);
}


/*
|--------------------------------------------------------------------------
| DATA STATISTIK (sesuai filter)
|--------------------------------------------------------------------------
*/

// Total jadwal
$stmtTotalJadwal = $connect->prepare(
    "SELECT COUNT(*) AS total FROM jadwal_sholat $whereSql"
);

if ($jenisBind !== '') {
    $stmtTotalJadwal->bind_param($jenisBind, ...$nilaiBind);
}

$stmtTotalJadwal->execute();
$totalJadwal = $stmtTotalJadwal->get_result()->fetch_assoc();

// Total kelas
$stmtTotalKelas = $connect->prepare(
    "SELECT COUNT(DISTINCT id_kelas) AS total FROM jadwal_sholat $whereSql"
);

if ($jenisBind !== '') {
    $stmtTotalKelas->bind_param($jenisBind, ...$nilaiBind);
}

$stmtTotalKelas->execute();
$totalKelas = $stmtTotalKelas->get_result()->fetch_assoc();

$jumlahJadwal = $totalJadwal['total'] ?? 0;
$jumlahKelas = $totalKelas['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| DATA JADWAL SHOLAT (sesuai filter)
|--------------------------------------------------------------------------
|
| JOIN tabel jadwal_sholat dengan tabel kelas
|
*/

// Saat bulan tertentu dipilih, urutkan per tanggal.
// Saat "Semua Bulan", urutkan: hari ini -> mendatang -> lampau.
$urutanSql = ($bulanDipilih >= 1 && $bulanDipilih <= 12)
    ? "jadwal_sholat.tanggal ASC,
       FIELD(jadwal_sholat.waktu_sholat, 'Dzuhur', 'Ashar'),
       jadwal_sholat.id_jadwal DESC"
    : "CASE
           WHEN jadwal_sholat.tanggal IS NULL THEN 3
           WHEN jadwal_sholat.tanggal = CURDATE() THEN 0
           WHEN jadwal_sholat.tanggal > CURDATE() THEN 1
           ELSE 2
       END ASC,
       CASE
           WHEN jadwal_sholat.tanggal > CURDATE() THEN jadwal_sholat.tanggal
           ELSE NULL
       END ASC,
       CASE
           WHEN jadwal_sholat.tanggal < CURDATE() THEN jadwal_sholat.tanggal
           ELSE NULL
       END DESC,
       jadwal_sholat.id_jadwal DESC";

$stmtData = $connect->prepare(
    "SELECT
        jadwal_sholat.id_jadwal,
        jadwal_sholat.tanggal,
        jadwal_sholat.waktu_sholat,
        jadwal_sholat.id_kelas,
        kelas.nama_kelas,
        kelas.tingkat,
        kelas.bagian
    FROM jadwal_sholat
    LEFT JOIN kelas
        ON jadwal_sholat.id_kelas = kelas.id_kelas
    $whereSql
    ORDER BY $urutanSql"
);

if ($jenisBind !== '') {
    $stmtData->bind_param($jenisBind, ...$nilaiBind);
}

$stmtData->execute();
$data = $stmtData->get_result();


/*
|--------------------------------------------------------------------------
| NAMA BULAN INDONESIA
|--------------------------------------------------------------------------
*/

$bulan = [
    1  => 'Januari',
    2  => 'Februari',
    3  => 'Maret',
    4  => 'April',
    5  => 'Mei',
    6  => 'Juni',
    7  => 'Juli',
    8  => 'Agustus',
    9  => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

$logoPath = __DIR__ . '/../../assets/img/musholla_logo.png';
$logo = '';

if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
    $logo = 'data:image/png;base64,' . $logoData;
}

/*
|--------------------------------------------------------------------------
| HTML PDF
|--------------------------------------------------------------------------
*/

$html = '
<!DOCTYPE html>
<html>
<head>

<meta charset="UTF-8">

<style>
    @page { margin: 35px 45px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; margin: 0; }
    
    .header { position: relative; text-align: center; padding-bottom: 14px; border-bottom: 3px solid #118848; margin-bottom: 22px; }
    .header h1 { margin: 0; font-size: 20px; color: #118848; font-weight: bold; }
    .header h2 { margin: 5px 0; font-size: 16px; color: #1f2937; }
    .header p { margin: 3px 0 0; color: #6b7280; font-size: 11px; }

    .logo { position: absolute; top: -55px; left: 0; width: 170px; height: 170px; }
    
    .title { text-align: center; margin-bottom: 20px; }
    .title h3 { margin: 0; font-size: 18px; color: #1f2937; }
    .title p { margin: 6px 0 0; font-size: 11px; color: #6b7280; }
    
    .summary { width: 100%; border-collapse: collapse; margin-bottom: 22px; }
    .summary td { width: 50%; text-align: center; padding: 12px 8px; background: #f0fdf4; border: 1px solid #d1fae5; }
    .summary-label { display: block; font-size: 9px; color: #6b7280; margin-bottom: 5px; }
    .summary-value { display: block; font-size: 14px; font-weight: bold; color: #118848; }
    
    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { background: #118848; color: #ffffff; padding: 10px 8px; border: 1px solid #0d6d3a; font-size: 11px; }
    .data-table td { padding: 9px 8px; border: 1px solid #e5e7eb; font-size: 11px; }
    .data-table tr:nth-child(even) td { background: #f9fafb; }
    
    .center { text-align: center; }
    .right { text-align: right; }
    .nominal { font-weight: bold; }
    .empty { text-align: center; padding: 20px; color: #6b7280; }
    
    .signature-table { width: 100%; margin-top: 45px; }
    .signature-table td { width: 50%; vertical-align: top; }
    .signature { text-align: center; }
    .signature-space { height: 65px; }
    
    .footer { text-align: center; margin-top: 25px; padding-top: 10px; border-top: 1px solid #e5e7eb; color: #6b7280; font-size: 9px; }
</style>

</head>

<body>


<!-- HEADER -->

<div class="header">

 ' . ($logo ? '<img src="' . $logo . '" class="logo">' : '') . '
   
    <h1>SISTEM INFORMASI MUSHOLLA</h1>

    <h2>SMK NEGERI 1 KRAKSAAN</h2>

    <p>
        Laporan Jadwal Sholat
    </p>

</div>


<!-- TITLE -->

<div class="title">

    <h3>LAPORAN JADWAL SHOLAT</h3>

    <p>
        ' . $keteranganFilter . '
    </p>

</div>


<!-- SUMMARY -->

<table class="summary">

    <tr>

        <td>

            <span class="summary-label">
                TOTAL JADWAL
            </span>

            <span class="summary-value">
                ' . $jumlahJadwal . ' Jadwal
            </span>

        </td>


        <td>

            <span class="summary-label">
                TOTAL KELAS
            </span>

            <span class="summary-value">
                ' . $jumlahKelas . ' Kelas
            </span>

        </td>

    </tr>

</table>


<!-- DATA TABLE -->

<table class="data-table">

    <thead>

        <tr>

            <th width="6%" class="center">
                No
            </th>

            <th width="14%">
                Nama Kelas
            </th>

            <th width="11%">
                Tingkat
            </th>

            <th width="11%">
                Bagian
            </th>

            <th width="22%">
                Waktu Sholat
            </th>

            <th width="11%">
                Hari
            </th>

            <th width="25%">
                Tanggal
            </th>

        </tr>

    </thead>


    <tbody>
';


/*
|--------------------------------------------------------------------------
| DATA ROW
|--------------------------------------------------------------------------
*/

$no = 1;

if (mysqli_num_rows($data) > 0) {

    while ($row = mysqli_fetch_assoc($data)) {

        $tanggal = !empty($row['tanggal'])
            ? date('F d Y',strtotime($row['tanggal']))
            : '-';
        $hari = hari_indonesia($row['tanggal'] ?? null);
        $bagian = !empty($row['bagian'])
            ? $row['bagian']
            : '-';

        /*
        |--------------------------------------------------------------------------
        | FORMAT NAMA SHOLAT
        |--------------------------------------------------------------------------
        */

        $waktu = strtolower(trim($row['waktu_sholat']));

        $namaSholat = [

            'dzhuhur' => 'Dzuhur',
            'dzuhur'  => 'Dzuhur',
            'zuhur'   => 'Dzuhur',
            'zhuhur'  => 'Dzuhur',

            'ashar'   => 'Ashar',

            'maghrib' => 'Maghrib',

            'isya'    => 'Isya',

            'subuh'   => 'Subuh'

        ];

        $sholat = $namaSholat[$waktu]
            ?? ucfirst($row['waktu_sholat']);


        /*
        |--------------------------------------------------------------------------
        | NAMA KELAS
        |--------------------------------------------------------------------------
        */

        $namaKelas = !empty($row['nama_kelas'])
            ? $row['nama_kelas']
            : 'Kelas Tidak Ditemukan';



        /*
        |--------------------------------------------------------------------------
        | TAMBAHKAN ROW
        |--------------------------------------------------------------------------
        */

        $html .= '

        <tr class="center">

            <td>
                ' . $no++ . '
            </td>

            <td class="bold">
                ' . htmlspecialchars($namaKelas) . '
            </td>

            <td>
                ' . htmlspecialchars($row['tingkat'] ?? '-') . '
            </td>

            <td>
                ' . htmlspecialchars($bagian) . '
            </td>

            <td>
                ' . htmlspecialchars($hari) . '
            </td>

            <td class="bold">
                ' . htmlspecialchars($sholat) . '
            </td>

            <td class="sholat">
                ' . htmlspecialchars($tanggal) . '
            </td>

        </tr>

        ';
    }

} else {

    $html .= '

        <tr>

            <td colspan="7" class="empty">

                Belum ada data jadwal sholat.

            </td>

        </tr>

    ';
}


/*
|--------------------------------------------------------------------------
| PENUTUP TABLE + TANDA TANGAN
|--------------------------------------------------------------------------
*/

$html .= '

    </tbody>

</table>


<!-- SIGNATURE -->

<table class="signature-table">

    <tr>

        <td></td>

        <td class="signature">

            Kraksaan,
            ' . $bulan[(int)date('m')] . ' ' .
            date('d') . ' ' .
            date('Y') . '

            <br>

            Pengurus Musholla

            <div class="signature-space"></div>

            <strong>
                ________________________
            </strong>

        </td>

    </tr>

</table>


<!-- FOOTER -->

<div class="footer">

    Sistem Informasi Musholla
    &middot;
    SMK Negeri 1 Kraksaan
    &middot;
    ' . date('Y') . '

</div>


</body>

</html>
';


/*
|--------------------------------------------------------------------------
| GENERATE PDF
|--------------------------------------------------------------------------
*/

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream(
    'Laporan-Jadwal-Sholat.pdf',
    [
        'Attachment' => false
    ]
);

?>
