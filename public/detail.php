<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

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
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Permohonan - PPID Bulungan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
<link rel="icon" href="/favicon.ico" type="image/x-icon">

</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
  <div class="container-fluid">
    <a class="navbar-brand d-flex align-items-center" href="dashboard.php#">
      <img src="../assets/img/logo_ppid.png" alt="Logo" height="40" class="me-2">APLIKASI MANAJEMEN PPID BULUNGAN
          </a>
    <div>
    <div>
      <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
    </div>
  </div>
</nav>

<div class="container">
  <h4 class="mb-4">Detail Permohonan Informasi</h4>

  <table class="table table-bordered">
    <tr>
      <th width="30%">No. Pendaftaran</th>
      <td><?= e($data['no_pendaftaran']) ?></td>
    </tr>
    <tr>
      <th>Nama</th>
      <td><?= e($data['nama']) ?></td>
    </tr>
    <tr>
      <th>Alamat</th>
      <td><?= nl2br(e($data['alamat'])) ?></td>
    </tr>
    <tr>
      <th>Kontak</th>
      <td><?= e($data['kontak']) ?></td>
    </tr>
    <tr>
      <th>Rincian Informasi</th>
      <td><?= nl2br(e($data['rincian_informasi'])) ?></td>
    </tr>
    <tr>
      <th>Tujuan Penggunaan</th>
      <td><?= nl2br(e($data['tujuan_informasi'])) ?></td>
    </tr>
    <tr>
      <th>Cara Memperoleh</th>
      <td><?= e($data['cara_memperoleh']) ?></td>
    </tr>
    <tr>
      <th>Cara Salinan</th>
      <td><?= e($data['cara_salinan']) ?></td>
    </tr>
    <tr>
      <th>Status</th>
      <td>
        <span class="badge
          <?= match($data['status']) {
              'Belum Diproses' => 'bg-warning',
              'Sedang Diproses' => 'bg-info',
              'Selesai' => 'bg-success',
              default => 'bg-secondary'
          } ?>">
          <?= $data['status'] ?>
        </span>
      </td>
    </tr>
    <tr>
      <th>Tanggal Permohonan</th>
      <td><?= formatTanggalIndo($data['tanggal']) ?></td>
    </tr>
    <tr>
      <th>File KTP</th>
      <td>
        <?php if ($data['file_ktp']): ?>
          <a href="../<?= e($data['file_ktp']) ?>" target="_blank" class="btn btn-sm btn-outline-primary">Lihat KTP</a>
        <?php else: ?>
          <span class="text-muted">Tidak ada</span>
        <?php endif; ?>
      </td>
    </tr>
    <tr>
      <th>File Surat Kuasa</th>
      <td>
        <?php if ($data['file_surat_kuasa']): ?>
          <a href="../<?= e($data['file_surat_kuasa']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">Lihat Surat Kuasa</a>
        <?php else: ?>
          <span class="text-muted">Tidak ada</span>
        <?php endif; ?>
      </td>
    </tr>
    <?php if (!empty($data['file_hasil'])): ?>
<tr>
  <th>File Hasil</th>
  <td>
    <a href="../<?= htmlspecialchars($data['file_hasil']) ?>" target="_blank" class="btn btn-sm btn-success">
      <i class="bi bi-download"></i> Download File Hasil
    </a>
  </td>
</tr>
<?php endif; ?>

  </table>

  <div class="text-end">
    <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
    <a href="edit.php?id=<?= $data['id'] ?>" class="btn btn-warning">Edit</a>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<footer class="text-center py-3 text-muted" style="background-color: #f8f9fa;">
    © 2025 - SIPPID Bulungan by Seggaf
</footer>

</body>
</html>