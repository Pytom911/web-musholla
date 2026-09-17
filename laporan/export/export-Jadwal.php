<?php

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
| DATA STATISTIK
|--------------------------------------------------------------------------
*/

// Total jadwal
$totalJadwal = mysqli_fetch_assoc(
    mysqli_query(
        $connect,
        "SELECT COUNT(*) AS total FROM jadwal_sholat"
    )
);

// Total hari
$totalHari = mysqli_fetch_assoc(
    mysqli_query(
        $connect,
        "SELECT COUNT(DISTINCT hari) AS total FROM jadwal_sholat"
    )
);

// Total kelas
$totalKelas = mysqli_fetch_assoc(
    mysqli_query(
        $connect,
        "SELECT COUNT(DISTINCT id_kelas) AS total FROM jadwal_sholat"
    )
);

$jumlahJadwal = $totalJadwal['total'] ?? 0;
$jumlahHari = $totalHari['total'] ?? 0;
$jumlahKelas = $totalKelas['total'] ?? 0;


/*
|--------------------------------------------------------------------------
| DATA JADWAL SHOLAT
|--------------------------------------------------------------------------
|
| JOIN tabel jadwal_sholat dengan tabel kelas
|
*/

$data = mysqli_query(
    $connect,
    "SELECT
        jadwal_sholat.id_jadwal,
        jadwal_sholat.hari,
        jadwal_sholat.waktu_sholat,
        jadwal_sholat.id_kelas,
        kelas.nama_kelas
    FROM jadwal_sholat
    LEFT JOIN kelas
        ON jadwal_sholat.id_kelas = kelas.id_kelas
    ORDER BY
        FIELD(jadwal_sholat.hari, 'Senin','Selasa','Rabu','Kamis','Jumat'),
        jadwal_sholat.id_jadwal DESC"
);


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
    .summary td { width: 33.33%; text-align: center; padding: 12px 8px; background: #f0fdf4; border: 1px solid #d1fae5; }
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
        Seluruh data jadwal sholat berdasarkan hari dan kelas
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
                TOTAL HARI
            </span>

            <span class="summary-value">
                ' . $jumlahHari . ' Hari
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

            <th width="8%" class="center">
                No
            </th>

            <th width="25%">
                Hari
            </th>

            <th width="22%">
                Waktu Sholat
            </th>

            <th width="25%">
                Jurusan
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

        /*
        |--------------------------------------------------------------------------
        | HARI
        |--------------------------------------------------------------------------
        */

        $hari = $row['hari'];


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

        <tr>

            <td class="center">
                ' . $no++ . '
            </td>

            <td class="bold">
                ' . htmlspecialchars($hari) . '
            </td>

            <td class="sholat">
                ' . htmlspecialchars($sholat) . '
            </td>

            <td class="bold">
                ' . htmlspecialchars($namaKelas) . '
            </td>

        </tr>

        ';
    }

} else {

    $html .= '

        <tr>

            <td colspan="5" class="empty">

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
            ' . date('d') . '
            ' . $bulan[(int)date('m')] . '
            ' . date('Y') . '

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
