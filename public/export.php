<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../lib/dompdf/autoload.inc.php';

use Dompdf\Dompdf;

// Ambil parameter filter dari URL
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';
$status = $_GET['status'] ?? '';

// Siapkan query
$params = [];
$sql = "SELECT * FROM permohonan WHERE 1=1";

// Filter status, abaikan jika status = "Semua"
if (!empty($status) && $status !== 'Semua') {
    $sql .= " AND LOWER(status) = LOWER(?)";
    $params[] = $status;
}

if (!empty($bulan) && $bulan !== 'Semua') {
    $sql .= " AND MONTH(tanggal) = ?";
    $params[] = $bulan;
}

if (!empty($tahun) && $tahun !== 'Semua') {
    $sql .= " AND YEAR(tanggal) = ?";
    $params[] = $tahun;
}

$sql .= " ORDER BY tanggal DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Siapkan kop surat
$kopPath = '../assets/img/kopsurat.png';
$kopImg = '';
if (file_exists($kopPath)) {
    $kopBase64 = base64_encode(file_get_contents($kopPath));
    $kopImg = '<img src="data:image/png;base64,' . $kopBase64 . '" style="width:100%; margin-bottom:20px;">';
}

// Mulai buat HTML untuk PDF
$html = '<html><body>';
$html .= $kopImg;
$html .= '<h3 style="text-align:center; margin-bottom: 20px;">LAPORAN PERMOHONAN INFORMASI PUBLIK</h3><br>';

if (count($data) === 0) {
    $html .= '<p style="text-align:center;">Tidak ada data yang ditemukan berdasarkan filter.</p>';
} else {
    $html .= '<table border="1" cellspacing="0" cellpadding="6" width="100%">
    <thead>
        <tr style="background:#eee;">
            <th>No</th>
            <th>Nama</th>
            <th>Kontak</th>
            <th>Tanggal</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>';

    foreach ($data as $i => $row) {
        $html .= '<tr>
            <td>' . ($i + 1) . '</td>
            <td>' . htmlspecialchars($row['nama']) . '</td>
            <td>' . htmlspecialchars($row['kontak']) . '</td>
            <td>' . date('d/m/Y', strtotime($row['tanggal'])) . '</td>
            <td>' . htmlspecialchars($row['status']) . '</td>
        </tr>';
    }

    $html .= '</tbody></table>';
}

$html .= '</body></html>';

// Render PDF dengan Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('laporan_ppid.pdf', ['Attachment' => false]);
exit;
