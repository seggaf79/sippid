<?php
require '../config/database.php';

$id = $_POST['id'] ?? null;
if (!$id || !isset($_FILES['file_hasil'])) {
    die("Permintaan tidak valid.");
}

$file = $_FILES['file_hasil'];
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);

// Validasi file
if ($file['error'] !== UPLOAD_ERR_OK || $ext !== 'pdf') {
    die("Gagal upload file.");
}

if ($file['size'] > 2 * 1024 * 1024) {
    die("Ukuran file melebihi batas (2MB).");
}

// Folder simpan
$tahun = date('Y');
$bulan = date('m');
$uploadDir = "../uploads/hasil/$tahun/$bulan/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Buat nama file unik
$namaBaru = uniqid('hasil_') . '.pdf';
$uploadPath = $uploadDir . $namaBaru;

// Simpan file
if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
    // Simpan ke database (path relatif)
    $relPath = "uploads/hasil/$tahun/$bulan/$namaBaru";
    $stmt = $pdo->prepare("UPDATE permohonan SET file_hasil = ? WHERE id = ?");
    $stmt->execute([$relPath, $id]);

    header("Location: dashboard.php?upload=success");
    exit;
} else {
    die("Gagal menyimpan file.");
}