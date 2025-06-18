<?php
require '../lib/dompdf/vendor/autoload.php';
require '../config/database.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$status = $_GET['status'] ?? '';
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

$where = [];
$params = [];

if ($status && $status !== 'Semua') {
    $where[] = 'status = ?';
    $params[] = $status;
}

if ($bulan) {
    $where[] = 'MONTH(tanggal) = ?';
    $params[] = $bulan;
}

if ($tahun) {
    $where[] = 'YEAR(tanggal) = ?';
    $params[] = $tahun;
}

$sql = 'SELECT * FROM permohonan';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY tanggal DESC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);
$dompdf->setPaper('A4', 'landscape');

// Encode logo sebagai base64
$logo_path = '../assets/img/kopsurat.jpg';
$type = pathinfo($logo_path, PATHINFO_EXTENSION);
$data_logo = file_get_contents($logo_path);
$base64_logo = 'data:image/' . $type . ';base64,' . base64_encode($data_logo);

$bulanNama = [
    '', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
];
$judulBulan = $bulan ? $bulanNama[(int)$bulan] : 'Semua Bulan';
$judulTahun = $tahun ?: 'Semua Tahun';

$html = "
<style>
    body { font-family: Arial, sans-serif; font-size: 12px; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #000; padding: 6px; vertical-align: top; }
    th { background-color: #f2f2f2; }
    .kop { width: 100%; margin-bottom: 10px; }
    .kop img { height: 80px; float: left; }
    .judul-container {
        text-align: center;
        margin-left: 100px;
    }
</style>

<div class='kop'>
    <img src='{$base64_logo}' />
    <div class='judul-container'>
        <h3>LAPORAN PERMOHONAN INFORMASI PUBLIK</h3>
        <h4>BULAN {$judulBulan} TAHUN {$judulTahun}</h4>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor Pendaftaran</th>
            <th>Nama</th>
            <th>Alamat</th>
            <th>Kontak</th>
            <th>Rincian Informasi</th>
            <th>Tujuan Penggunaan</th>
            <th>Cara Memperoleh</th>
            <th>Cara Salinan</th>
            <th>Status</th>
            <th>Tanggal Permohonan</th>
        </tr>
    </thead>
    <tbody>";

if ($data) {
    $no = 1;
    foreach ($data as $row) {
        $html .= "<tr>
            <td>{$no}</td>
            <td>{$row['no_pendaftaran']}</td>
            <td>{$row['nama']}</td>
            <td>{$row['alamat']}</td>
            <td>{$row['kontak']}</td>
            <td>{$row['rincian_informasi']}</td>
            <td>{$row['tujuan_informasi']}</td>
            <td>{$row['cara_memperoleh']}</td>
            <td>{$row['cara_salinan']}</td>
            <td>{$row['status']}</td>
            <td>" . date('d/m/Y', strtotime($row['tanggal'])) . "</td>
        </tr>";
        $no++;
    }
} else {
    $html .= "<tr><td colspan='11' style='text-align:center;'>Data tidak tersedia</td></tr>";
}

$html .= "</tbody></table>";

$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream('laporan_permohonan.pdf', ['Attachment' => false]);
exit;
?>