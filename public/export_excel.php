<?php
require '../vendor/autoload.php';
require '../config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;

// Ambil parameter filter
$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? date('Y');

// Ambil data dari database berdasarkan filter
$sql = "SELECT * FROM permohonan WHERE 1=1";
$params = [];

if ($bulan != '') {
    $sql .= " AND MONTH(tanggal) = ?";
    $params[] = $bulan;
}
if ($tahun != '') {
    $sql .= " AND YEAR(tanggal) = ?";
    $params[] = $tahun;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buat spreadsheet baru
$spreadsheet = new Spreadsheet();
$spreadsheet->getActiveSheet()->getPageSetup()
    ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);

// Set judul dua baris
$bulanText = $bulan ? date("F", mktime(0, 0, 0, $bulan, 10)) : 'SEMUA BULAN';
$judul1 = "LAPORAN PERMOHONAN INFORMASI PUBLIK";
$judul2 = "BULAN " . strtoupper($bulanText) . " TAHUN " . $tahun;

// Tulis judul ke sel
$sheet = $spreadsheet->getActiveSheet();
$sheet->mergeCells('A1:J1')->setCellValue('A1', $judul1);
$sheet->mergeCells('A2:J2')->setCellValue('A2', $judul2);
$sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1:A2')->getAlignment()->setHorizontal('center');

// Header kolom
$headers = ['No', 'Nama', 'Alamat', 'Kontak', 'Rincian Informasi', 'Tujuan Penggunaan', 'Cara Memperoleh', 'Cara Salinan', 'Status', 'Tanggal Permohonan'];
$sheet->fromArray($headers, null, 'A4');

// Isi data
$row = 5;
$no = 1;
foreach ($data as $item) {
    $sheet->fromArray([
        $no++,
        $item['nama'],
        $item['alamat'],
        $item['kontak'],
        $item['rincian_informasi'],
        $item['tujuan_informasi'],
        $item['cara_memperoleh'],
        $item['cara_salinan'],
        $item['status'],
        date('d/m/Y', strtotime($item['tanggal']))
    ], null, 'A' . $row);
    $row++;
}

// Set auto width untuk kolom
foreach (range('A', 'J') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

// Set nama file dan header untuk download
$filename = 'laporan_permohonan_' . date('YmdHis') . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

// Output file ke browser
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
