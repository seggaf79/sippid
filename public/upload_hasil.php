<?php
require '../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID permohonan tidak ditemukan.");
}

// Ambil data permohonan (opsional, untuk ditampilkan)
$stmt = $pdo->prepare("SELECT * FROM permohonan WHERE id = ?");
$stmt->execute([$id]);
$permohonan = $stmt->fetch();
if (!$permohonan) {
    die("Data permohonan tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload File Hasil Permohonan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">
      <img src="../assets/img/logo_ppid.png" alt="Logo" height="40" class="me-2">APLIKASI MANAJEMEN PPID BULUNGAN
          </a>
    <div>
      <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container py-5">
    <h3 class="mb-4">Upload File Hasil Permohonan</h3>
    <div class="card shadow-sm">
        <div class="card-body">
            <form action="proses_upload_hasil.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                <div class="mb-3">
                    <label for="file_hasil" class="form-label">File Hasil (PDF)</label>
                    <input type="file" name="file_hasil" class="form-control" accept="application/pdf" required>
                    <small class="form-text text-muted">Maks. 2MB, format .pdf</small>
                </div>
                <button type="submit" class="btn btn-success">Upload</button>
                <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
            </form>
        </div>
    </div>
</div>

<footer class="text-center py-3 text-muted" style="background-color: #f8f9fa;">
    © 2025 - PPID Bulungan by Seggaf
</footer>
</body>
</html>