<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $alamat = trim($_POST['alamat']);
    $kontak = trim($_POST['kontak']);
    $rincian = trim($_POST['rincian']);
    $tujuan = trim($_POST['tujuan']);
    $cara_memperoleh = $_POST['cara_memperoleh'];
    $cara_salinan = $_POST['cara_salinan'];
    $no_pendaftaran = 'PPID-' . time(); // Sementara auto dari timestamp

    // Buat folder uploads berdasarkan tahun dan bulan
    $tahun = date('Y');
    $bulan = date('m');
    $uploadDir = "../uploads/$tahun/$bulan/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Upload KTP
    $ktp_path = '';
    if ($_FILES['ktp']['error'] === 0) {
        $ext = pathinfo($_FILES['ktp']['name'], PATHINFO_EXTENSION);
        $ktp_name = 'ktp_' . time() . '_' . uniqid() . '.' . $ext;
        $ktp_full_path = $uploadDir . $ktp_name;
        move_uploaded_file($_FILES['ktp']['tmp_name'], $ktp_full_path);
        $ktp_path = "uploads/$tahun/$bulan/$ktp_name"; // path relatif ke database
    }

    // Upload Surat Kuasa (optional)
    $kuasa_path = '';
    if ($_FILES['kuasa']['error'] === 0) {
        $ext = pathinfo($_FILES['kuasa']['name'], PATHINFO_EXTENSION);
        $kuasa_name = 'kuasa_' . time() . '_' . uniqid() . '.' . $ext;
        $kuasa_full_path = $uploadDir . $kuasa_name;
        move_uploaded_file($_FILES['kuasa']['tmp_name'], $kuasa_full_path);
        $kuasa_path = "uploads/$tahun/$bulan/$kuasa_name"; // path relatif ke database
    }

  
    // Simpan ke DB
    $stmt = $pdo->prepare("INSERT INTO permohonan (no_pendaftaran, nama, alamat, kontak, rincian_informasi, tujuan_informasi, cara_memperoleh, cara_salinan, file_ktp, file_surat_kuasa, status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Belum Diproses')");
    $stmt->execute([
        $no_pendaftaran, $nama, $alamat, $kontak, $rincian, $tujuan,
        $cara_memperoleh, $cara_salinan, $ktp_path, $kuasa_path
    ]);

    $success = "Permohonan berhasil ditambahkan!";
}

?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Permohonan - PPID Bulungan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
      <img src="../assets/img/logo_ppid.png" alt="Logo" height="40" class="me-2">APLIKASI MANAJEMEN PPID BULUNGAN
          </a>
    <div>
</nav>

<div class="container">
    <h4 class="mb-4">Form Tambah Permohonan Informasi</h4>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="mb-3"><label class="form-label">Nama</label><input type="text" name="nama" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Alamat</label><textarea name="alamat" class="form-control" required></textarea></div>
        <div class="mb-3"><label class="form-label">Kontak</label><input type="text" name="kontak" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Rincian Informasi</label><textarea name="rincian" class="form-control" required></textarea></div>
        <div class="mb-3"><label class="form-label">Tujuan Penggunaan</label><textarea name="tujuan" class="form-control" required></textarea></div>
        <div class="mb-3">
            <label class="form-label">Cara Memperoleh Informasi</label>
            <select name="cara_memperoleh" class="form-select" id="cara_memperoleh" required>
                <option disabled selected value="">-- Pilih Cara --</option>
                <option value="Melihat/membaca">Melihat / Membaca / Mendengarkan / Mencatat </option>
                <option value="Mendapatkan Salinan">Mendapatkan Salinan</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Cara Mendapatkan Salinan</label>
            <select name="cara_salinan" class="form-select" id="cara_salinan" required>
                <option disabled selected value="">-- Pilih Cara --</option>
                <option value="Langsung">Mengambil Langsung</option>
                <option value="Kurir">Kurir</option>
                <option value="Pos">Pos</option>
                <option value="Email">Email</option>
                <option value="Fax">Fax</option>
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Upload KTP (jpg/jpeg/png)</label><input type="file" name="ktp" class="form-control" accept="image/*" required></div>
        <div class="mb-3"><label class="form-label">Upload Surat Kuasa (PDF) <small>(Opsional)</small></label><input type="file" name="kuasa" class="form-control" accept="application/pdf"></div>
        <div class="col-12 text-end">
      <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
      <button type="submit" class="btn btn-primary">Simpan</button>
    </div>

    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<footer class="text-center py-3 text-muted" style="background-color: #f8f9fa;">
    © 2025 - PPID Bulungan by Seggaf
</footer>

</body>
</html>
