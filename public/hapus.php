<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];

// Cek apakah data ada
$stmt = $pdo->prepare("SELECT * FROM permohonan WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data tidak ditemukan.");
}

// Hapus file KTP jika ada
if (!empty($data['file_ktp']) && file_exists("../" . $data['file_ktp'])) {
    unlink("../" . $data['file_ktp']);
}

// Hapus file surat kuasa jika ada
if (!empty($data['file_surat_kuasa']) && file_exists("../" . $data['file_surat_kuasa'])) {
    unlink("../" . $data['file_surat_kuasa']);
}

// Hapus data dari database
$delete = $pdo->prepare("DELETE FROM permohonan WHERE id = ?");
$delete->execute([$id]);

// Redirect kembali ke dashboard
header("Location: dashboard.php");
exit();
?>
