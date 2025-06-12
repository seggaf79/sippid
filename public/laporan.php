<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

// Ambil data filter dari URL atau default
$bulan = $_GET['bulan'] ?? date('m');
$tahun = $_GET['tahun'] ?? date('Y');
$status = $_GET['status'] ?? 'Semua';

// Ambil tahun unik dari database untuk dropdown
$tahun_stmt = $pdo->query("SELECT DISTINCT YEAR(tanggal) AS tahun FROM permohonan ORDER BY tahun DESC");
$tahun_opsi = $tahun_stmt->fetchAll(PDO::FETCH_COLUMN);

// Siapkan query
$params = [];
$sql = "SELECT * FROM permohonan WHERE 1=1";

if ($status !== 'Semua') {
    $sql .= " AND status = ?";
    $params[] = $status;
}
if ($bulan !== 'Semua') {
    $sql .= " AND MONTH(tanggal) = ?";
    $params[] = $bulan;
}
if ($tahun !== 'Semua') {
    $sql .= " AND YEAR(tanggal) = ?";
    $params[] = $tahun;
}

$sql .= " ORDER BY tanggal DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Permohonan - PPID Bulungan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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
  <h4 class="mb-4">Laporan Permohonan Informasi</h4>

  <form class="row g-2 mb-4" method="GET">
    <div class="col-md-2">
      <select name="bulan" class="form-select">
        <option value="Semua">Semua Bulan</option>
        <?php for ($i = 1; $i <= 12; $i++): ?>
          <option value="<?= sprintf('%02d', $i) ?>" <?= $bulan == sprintf('%02d', $i) ? 'selected' : '' ?>>
            <?= date('F', mktime(0, 0, 0, $i, 1)) ?>
          </option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="col-md-2">
      <select name="tahun" class="form-select">
        <option value="Semua">Semua Tahun</option>
        <?php foreach ($tahun_opsi as $th): ?>
          <option value="<?= $th ?>" <?= $tahun == $th ? 'selected' : '' ?>><?= $th ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option <?= $status === 'Semua' ? 'selected' : '' ?>>Semua</option>
        <option <?= $status === 'Belum Diproses' ? 'selected' : '' ?>>Belum Diproses</option>
        <option <?= $status === 'Sedang Diproses' ? 'selected' : '' ?>>Sedang Diproses</option>
        <option <?= $status === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
      </select>
    </div>
    <div class="col-md-2">
      <button class="btn btn-primary w-100">Tampilkan</button>
    </div>
    <div class="col-md-3">
      <a href="export.php?bulan=<?= $bulan ?>&tahun=<?= $tahun ?>&status=<?= urlencode($status) ?>" class="btn btn-success w-100">
        Export ke PDF
      </a>
    </div>
  </form>

  <div class="table-responsive">
    <table class="table table-bordered table-hover">
      <thead class="table-secondary">
        <tr>
          <th>No</th>
          <th>Nama</th>
          <th>Kontak</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th>Tujuan</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($data) > 0): $no = 1; foreach ($data as $row): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= e($row['nama']) ?></td>
          <td><?= e($row['kontak']) ?></td>
          <td>
            <span class="badge 
              <?= match($row['status']) {
                'Belum Diproses' => 'bg-warning',
                'Sedang Diproses' => 'bg-info',
                'Selesai' => 'bg-success',
                default => 'bg-secondary'
              } ?>">
              <?= $row['status'] ?>
            </span>
          </td>
          <td><?= formatTanggalIndo($row['tanggal']) ?></td>
          <td><?= e($row['tujuan_informasi']) ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="6" class="text-center">Tidak ada data ditemukan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
