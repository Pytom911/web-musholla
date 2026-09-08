<?php

require_once '../../config/connect.php';
require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);

/*
|--------------------------------------------------------------------------
| Ambil Data Jadwal Imam
|--------------------------------------------------------------------------
*/

$data = mysqli_query($connect, "
    SELECT
        jadwal_imam.id_imam,
        jadwal_imam.tanggal,
        jadwal_imam.waktu_sholat,
        jadwal_imam.id_guru,
        guru.nama_guru
    FROM jadwal_imam
    LEFT JOIN guru
        ON jadwal_imam.id_guru = guru.id_guru
    ORDER BY
        jadwal_imam.tanggal ASC,
        jadwal_imam.id_imam ASC
");

/*
|--------------------------------------------------------------------------
| Statistik
|--------------------------------------------------------------------------
*/

$totalImam = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(DISTINCT id_guru) AS total
    FROM jadwal_imam
"));

$totalJadwal = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(*) AS total
    FROM jadwal_imam
"));

$totalHariIni = mysqli_fetch_assoc(mysqli_query($connect, "
    SELECT COUNT(*) AS total
    FROM jadwal_imam
    WHERE tanggal = CURDATE()
"));

$jumlahImam = $totalImam['total'] ?? 0;
$jumlahJadwal = $totalJadwal['total'] ?? 0;
$jumlahHariIni = $totalHariIni['total'] ?? 0;

/*
|--------------------------------------------------------------------------
| Nama Bulan
|--------------------------------------------------------------------------
*/

$bulan = [
    1 => 'Januari',
    2 => 'Februari',
    3 => 'Maret',
    4 => 'April',
    5 => 'Mei',
    6 => 'Juni',
    7 => 'Juli',
    8 => 'Agustus',
    9 => 'September',
    10 => 'Oktober',
    11 => 'November',
    12 => 'Desember'
];

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

    @page {
        margin: 35px 45px;
    }

    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 12px;
        color: #1f2937;
        margin: 0;
    }

    /* HEADER */

    .header {
        text-align: center;
        padding-bottom: 14px;
        border-bottom: 3px solid #118848;
        margin-bottom: 22px;
    }

    .header h1 {
        margin: 0;
        font-size: 20px;
        color: #118848;
        font-weight: bold;
    }

    .header h2 {
        margin: 5px 0;
        font-size: 16px;
        color: #1f2937;
    }

    .header p {
        margin: 3px 0 0;
        color: #6b7280;
        font-size: 11px;
    }

    /* TITLE */

    .title {
        text-align: center;
        margin-bottom: 20px;
    }

    .title h3 {
        margin: 0;
        font-size: 18px;
        color: #1f2937;
    }

    .title p {
        margin: 6px 0 0;
        font-size: 11px;
        color: #6b7280;
    }

    /* SUMMARY */

    .summary {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 22px;
    }

    .summary td {
        width: 33.33%;
        text-align: center;
        padding: 12px 8px;
        background: #f0fdf4;
        border: 1px solid #d1fae5;
    }

    .summary-label {
        display: block;
        font-size: 9px;
        color: #6b7280;
        margin-bottom: 5px;
    }

    .summary-value {
        display: block;
        font-size: 14px;
        font-weight: bold;
        color: #118848;
    }

    /* TABLE */

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th {
        background: #118848;
        color: #ffffff;
        padding: 10px 8px;
        border: 1px solid #0d6d3a;
        font-size: 11px;
    }

    .data-table td {
        padding: 9px 8px;
        border: 1px solid #e5e7eb;
        font-size: 11px;
    }

    .data-table tr:nth-child(even) td {
        background: #f9fafb;
    }

    .center {
        text-align: center;
    }

    .empty {
        text-align: center;
        padding: 20px;
        color: #6b7280;
    }

    /* SIGNATURE */

    .signature-table {
        width: 100%;
        margin-top: 45px;
    }

    .signature-table td {
        width: 50%;
        vertical-align: top;
    }

    .signature {
        text-align: center;
    }

    .signature-space {
        height: 65px;
    }

    /* FOOTER */

    .footer {
        text-align: center;
        margin-top: 25px;
        padding-top: 10px;
        border-top: 1px solid #e5e7eb;
        color: #6b7280;
        font-size: 9px;
    }

</style>

</head>

<body>

<!-- HEADER -->

<div class="header">

    <h1>SISTEM INFORMASI MUSHOLLA</h1>

    <h2>SMK NEGERI 1 KRAKSAAN</h2>

    <p>Jadwal Imam Musholla</p>

</div>

<!-- TITLE -->

<div class="title">

    <h3>LAPORAN JADWAL IMAM</h3>

    <p>seluruh data jadwal imam musholla</p>

</div>

<!-- SUMMARY -->

<table class="summary">

    <tr>

        <td>

            <span class="summary-label">
                JUMLAH IMAM
            </span>

            <span class="summary-value">
                ' . $jumlahImam . ' Orang
            </span>

        </td>

        <td>

            <span class="summary-label">
                TOTAL JADWAL
            </span>

            <span class="summary-value">
                ' . $jumlahJadwal . ' Data
            </span>

        </td>

        <td>

            <span class="summary-label">
                JADWAL HARI INI
            </span>

            <span class="summary-value">
                ' . $jumlahHariIni . ' Jadwal
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

            <th width="37%">
                Nama Imam
            </th>

            <th width="25%">
                Tanggal
            </th>

            <th width="30%">
                Waktu Sholat
            </th>

        </tr>

    </thead>

    <tbody>
';

/*
|--------------------------------------------------------------------------
| Isi Data
|--------------------------------------------------------------------------
*/

$no = 1;

if (mysqli_num_rows($data) > 0) {

    while ($row = mysqli_fetch_assoc($data)) {

        $timestamp = strtotime($row['tanggal']);

        $tanggal =
            date('d', $timestamp) . ' ' .
            $bulan[(int) date('m', $timestamp)] . ' ' .
            date('Y', $timestamp);

        $namaGuru = $row['nama_guru'] ?? 'Nama guru tidak ditemukan';

        $html .= '

        <tr>

            <td class="center">
                ' . $no++ . '
            </td>

            <td>
                ' . htmlspecialchars($namaGuru) . '
            </td>

            <td>
                ' . $tanggal . '
            </td>

            <td>
                ' . htmlspecialchars($row['waktu_sholat']) . '
            </td>

        </tr>

        ';
    }

} else {

    $html .= '

        <tr>

            <td colspan="4" class="empty">
                Belum ada data jadwal imam.
            </td>

        </tr>

    ';
}

$html .= '

    </tbody>

</table>

<!-- SIGNATURE -->

<table class="signature-table">

    <tr>

        <td></td>

        <td class="signature">

            Kraksaan,
            ' . date('d') . ' ' .
            $bulan[(int) date('m')] . ' ' .
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
| Generate PDF
|--------------------------------------------------------------------------
*/

$dompdf->loadHtml($html);

$dompdf->setPaper('A4', 'portrait');

$dompdf->render();

$dompdf->stream(
    'Laporan-Jadwal-Imam.pdf',
    [
        'Attachment' => false
    ]
);