<?php
require_once '../../config/connect.php';
require_once '../dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');

$dompdf = new Dompdf($options);

$data = mysqli_query (
    $connect, 
    "SELECT shodaqoh.*, kelas.nama_kelas
    FROM shodaqoh
    JOIN kelas
    ON shodaqoh.id_kelas = kelas.id_kelas
    ORDER BY shodaqoh.tanggal DESC, shodaqoh.id_shodaqoh DESC
");
$totalShodaqoh = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COALESCE(SUM(nominal),0) AS total FROM shodaqoh"));
$totalKelas = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(DISTINCT id_kelas) AS total FROM shodaqoh"));
$totalTransaksi = mysqli_fetch_assoc(mysqli_query($connect, "SELECT COUNT(*) AS total FROM shodaqoh"));

$jumlahKelas = $totalKelas['total'] ?? 0;
$jumlahTransaksi = $totalTransaksi['total'] ?? 0;
$totalNominal = $totalShodaqoh['total'] ?? 0;

$bulan = [
    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
];

$logoPath = __DIR__ . '/../../assets/img/musholla_logo.png';
$logo = '';

if (file_exists($logoPath)) {
    $logoData = base64_encode(file_get_contents($logoPath));
    $logo = 'data:image/png;base64,' . $logoData;
}

$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
    @page { margin: 35px 45px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1f2937; margin: 0; }
    
    .header { posisition: relative; text-align: center; padding-bottom: 14px; border-bottom: 3px solid #118848; margin-bottom: 22px; }
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

<div class="header">
    ' . ($logo ? '<img src="' . $logo . '" class="logo">' : '') . '
    <h1>SISTEM INFORMASI MUSHOLLA</h1>
    <h2>SMK NEGERI 1 KRAKSAAN</h2>
    <p>Laporan Keuangan Musholla</p>
</div>

<div class="title">
    <h3>LAPORAN SHODAQOH JUMAT</h3>
    <p>seluruh data penerimaan shodaqoh Jumat dari setiap kelas</p>
</div>

<table class="summary">
    <tr>
        <td>
            <span class="summary-label">JUMLAH KELAS</span>
            <span class="summary-value">' . $jumlahKelas . ' Kelas</span>
        </td>
        <td>
            <span class="summary-label">TOTAL SHODAQOH</span>
            <span class="summary-value">Rp ' . number_format($totalNominal, 0, ',', '.') . '</span>
        </td>
        <td>
            <span class="summary-label">TOTAL TRANSAKSI</span>
            <span class="summary-value">' . $jumlahTransaksi . ' Data</span>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th width="8%" class="center">No</th>
            <th width="25%">Tanggal</th>
            <th width="42%">Nama Kelas</th>
            <th width="25%">Nominal</th>
        </tr>
    </thead>
    <tbody>
';

$no = 1;

if (mysqli_num_rows($data) > 0) {
    while ($row = mysqli_fetch_assoc($data)) {
        $timestamp = strtotime($row['tanggal']);
        $tanggal = date('d', $timestamp) . ' ' . $bulan[(int)date('m', $timestamp)] . ' ' . date('Y', $timestamp);

        $html .= '
        <tr>
            <td class="center">' . $no++ . '</td>
            <td>' . $tanggal . '</td>
            <td>' . htmlspecialchars($row['nama_kelas']) . '</td>
            <td class="nominal">Rp ' . number_format($row['nominal'], 0, ',', '.') . '</td>
        </tr>';
    }
} else {
    $html .= '
        <tr>
            <td colspan="4" class="empty">Belum ada data shodaqoh Jumat.</td>
        </tr>';
}

$html .= '
    </tbody>
</table>

<table class="signature-table">
    <tr>
        <td></td>
        <td class="signature">
            Kraksaan, ' . date('d') . ' ' . $bulan[(int)date('m')] . ' ' . date('Y') . '<br>
            Pengurus Musholla
            <div class="signature-space"></div>
            <strong>______________________________</strong>
        </td>
    </tr>
</table>

<div class="footer">
    Sistem Informasi Musholla &middot; SMK Negeri 1 Kraksaan &middot; ' . date('Y') . '
</div>

</body>
</html>
';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('Laporan-Shodaqoh-Jumat.pdf', ['Attachment' => false]);