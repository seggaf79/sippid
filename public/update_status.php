<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM permohonan WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    die("Data tidak ditemukan.");
}

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];

    $update = $pdo->prepare("UPDATE permohonan SET status = ? WHERE id = ?");
    $update->execute([$status, $id]);

    $success = "Status berhasil diperbarui.";
    // reload data
    $stmt = $pdo->prepare("SELECT * FROM permohonan WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Update Status - PPID Bulungan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">
  </head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">PPID Bulungan</a>
    <div>
      <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container">
  <h4 class="mb-4">Ubah Status Permohonan</h4>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
  <?php endif; ?>

  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Nama</label>
      <input type="text" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" disabled>
    </div>
    <div class="col-md-6">
      <label class="form-label">No. Pendaftaran</label>
      <input type="text" class="form-control" value="<?= htmlspecialchars($data['no_pendaftaran']) ?>" disabled>
    </div>

    <div class="col-md-6">
      <label class="form-label">Status</label>
      <select name="status" class="form-select" required>
        <option <?= $data['status'] === 'Belum Diproses' ? 'selected' : '' ?>>Belum Diproses</option>
        <option <?= $data['status'] === 'Sedang Diproses' ? 'selected' : '' ?>>Sedang Diproses</option>
        <option <?= $data['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
      </select>
    </div>

    <div class="col-12 text-end">
      <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
      <button type="submit" class="btn btn-success">Update Status</button>
    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
