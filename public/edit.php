<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: dashboard.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM permohonan WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    echo "<div style='padding:20px;'>Data tidak ditemukan. <a href='dashboard.php'>Kembali</a></div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $kontak = $_POST['kontak'];
    $alamat = $_POST['alamat'];
    $rincian = $_POST['rincian_informasi'];
    $tujuan = $_POST['tujuan_informasi'];
    $status = $_POST['status'];

    $update = $pdo->prepare("UPDATE permohonan SET nama=?, kontak=?, alamat=?, rincian_informasi=?, tujuan_informasi=?, status=? WHERE id=?");
    $update->execute([$nama, $kontak, $alamat, $rincian, $tujuan, $status, $id]);

    header("Location: dashboard.php");
    exit;
}
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Permohonan Informasi</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="../assets/img/logo_ppid.png" alt="Logo" height="40" class="me-2">APLIKASI MANAJEMEN PPID BULUNGAN
          </a>
    <div>
      <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>
<div class="container mt-4">
  <h4 class="mb-4">Edit Permohonan Informasi</h4>
  <form method="POST">
    <div class="mb-3">
      <label class="form-label">Nama</label>
      <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Kontak</label>
      <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($data['kontak'] ?? '') ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Alamat</label>
      <textarea name="alamat" class="form-control" required><?= htmlspecialchars($data['alamat'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Rincian Informasi</label>
      <textarea name="rincian_informasi" class="form-control" required><?= htmlspecialchars($data['rincian_informasi'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Tujuan Penggunaan</label>
      <textarea name="tujuan_informasi" class="form-control" required><?= htmlspecialchars($data['tujuan_informasi'] ?? '') ?></textarea>
    </div>
    <div class="mb-3">
      <label class="form-label">Status Permohonan</label>
      <select name="status" class="form-select" required>
        <?php $status = $data['status'] ?? 'Belum Diproses'; ?>
        <option value="Belum Diproses" <?= $status == 'Belum Diproses' ? 'selected' : '' ?>>Belum Diproses</option>
        <option value="Sedang Diproses" <?= $status == 'Sedang Diproses' ? 'selected' : '' ?>>Sedang Diproses</option>
        <option value="Selesai" <?= $status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
        <option value="Ditolak" <?= $status == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
      </select>
    </div>
    <div class="d-flex justify-content-between">
      <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
      <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </div>
  </form>
</div>

<footer class="text-center py-3 text-muted" style="background-color: #f8f9fa;">
    © 2025 - PPID Bulungan by Seggaf
</footer>

</body>
</html>
