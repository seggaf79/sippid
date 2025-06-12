<?php
require_once '../includes/auth_check.php';
require_once '../config/database.php';
require_once '../includes/functions.php';

$status_filter = $_GET['status'] ?? 'Semua';
$search = $_GET['search'] ?? '';

$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$count_sql = "SELECT COUNT(*) FROM permohonan WHERE 1=1";
$data_sql = "SELECT * FROM permohonan WHERE 1=1";
$params = [];

if ($status_filter !== 'Semua') {
    $count_sql .= " AND status = ?";
    $data_sql .= " AND status = ?";
    $params[] = $status_filter;
}

if (!empty($search)) {
    $count_sql .= " AND (nama LIKE ? OR kontak LIKE ?)";
    $data_sql .= " AND (nama LIKE ? OR kontak LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$bulan = $_GET['bulan'] ?? '';
$tahun = $_GET['tahun'] ?? '';

if ($bulan && $tahun) {
    $count_sql .= " AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
    $data_sql .= " AND MONTH(tanggal) = ? AND YEAR(tanggal) = ?";
    $params[] = $bulan;
    $params[] = $tahun;
} elseif ($bulan) {
    $count_sql .= " AND MONTH(tanggal) = ?";
    $data_sql .= " AND MONTH(tanggal) = ?";
    $params[] = $bulan;
} elseif ($tahun) {
    $count_sql .= " AND YEAR(tanggal) = ?";
    $data_sql .= " AND YEAR(tanggal) = ?";
    $params[] = $tahun;
}

$data_sql .= " ORDER BY tanggal DESC LIMIT $start, $limit";

$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute(array_slice($params, 0, count($params)));
$total_rows = $count_stmt->fetchColumn();
$total_pages = ceil($total_rows / $limit);

$data_stmt = $pdo->prepare($data_sql);
$data_stmt->execute($params);
$permohonans = $data_stmt->fetchAll(PDO::FETCH_ASSOC);

$total = $pdo->query("SELECT COUNT(*) FROM permohonan")->fetchColumn();
$belum = $pdo->query("SELECT COUNT(*) FROM permohonan WHERE status = 'Belum Diproses'")->fetchColumn();
$proses = $pdo->query("SELECT COUNT(*) FROM permohonan WHERE status = 'Sedang Diproses'")->fetchColumn();
$selesai = $pdo->query("SELECT COUNT(*) FROM permohonan WHERE status = 'Selesai'")->fetchColumn();
$ditolak = $pdo->query("SELECT COUNT(*) FROM permohonan WHERE status = 'Ditolak'")->fetchColumn();
?>

<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard - PPID Bulungan</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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

<div class="container">
  <h4 class="mb-4">Dashboard Permohonan Informasi</h4>

  <div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card text-white bg-dark h-100"><div class="card-body">
  <h6><i class="fas fa-database me-1"></i> Total</h6>
  <h3><?= $total ?></h3>
</div></div></div>
    <div class="col-md-3">
  <div class="card text-white bg-warning h-100">
    <div class="card-body">
      <h6><i class="fas fa-clock me-1"></i> Belum Diproses</h6>
      <h3><?= $belum ?></h3>
    </div>
  </div>
</div>
    <div class="col-md-3">
  <div class="card text-white bg-info h-100">
    <div class="card-body">
      <h6><i class="fas fa-spinner me-1"></i> Sedang Diproses</h6>
      <h3><?= $proses ?></h3>
    </div>
  </div>
</div>
    <div class="col-md-3">
  <div class="card text-white bg-success h-100">
    <div class="card-body">
      <h6><i class="fas fa-check-circle me-1"></i> Selesai</h6>
      <h3><?= $selesai ?></h3>
    </div>
  </div>
</div>
    <div class="col-md-3">
  <div class="card text-white bg-danger h-100">
    <div class="card-body">
      <h6><i class="fas fa-times-circle me-1"></i> Ditolak</h6>
      <h3><?= $ditolak ?></h3>
    </div>
  </div>
</div>
  </div>

  <form class="row g-2 mb-3" method="GET">
    <div class="col-md-3">
      <input type="text" name="search" class="form-control" placeholder="Cari nama/kontak" value="<?= htmlspecialchars($search) ?>">
    </div>
    <div class="col-md-3">
      <select name="status" class="form-select">
        <option <?= $status_filter === 'Semua' ? 'selected' : '' ?>>Semua</option>
        <option <?= $status_filter === 'Belum Diproses' ? 'selected' : '' ?>>Belum Diproses</option>
        <option <?= $status_filter === 'Sedang Diproses' ? 'selected' : '' ?>>Sedang Diproses</option>
        <option <?= $status_filter === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
        <option <?= $status_filter === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
      </select>
    </div>
    <div class="col-md-2"><button type="submit" class="btn btn-primary w-100">Filter</button></div>
    <div class="col-md-2"><a href="export.php?status=<?= urlencode($status_filter) ?>&bulan=<?= urlencode($bulan) ?>&tahun=<?= urlencode($tahun) ?>" class="btn btn-success w-100">Export PDF</a></div>
    <div class="col-md-2"><a href="tambah.php" class="btn btn-warning w-100">+ Tambah</a></div>

     <div class="col">
    <select name="bulan" class="form-select">
      <option value="">Semua Bulan</option>
      <?php
        for ($m = 1; $m <= 12; $m++) {
            $selected = ($m == ($_GET['bulan'] ?? '')) ? 'selected' : '';
            echo "<option value='$m' $selected>" . date('F', mktime(0, 0, 0, $m, 10)) . "</option>";
        }
      ?>
    </select>
  </div>

  <!-- Tahun -->
  <div class="col">
    <select name="tahun" class="form-select">
      <option value="">Semua Tahun</option>
      <?php
        $currentYear = date('Y');
        for ($y = $currentYear; $y >= 2022; $y--) {
            $selected = ($y == ($_GET['tahun'] ?? '')) ? 'selected' : '';
            echo "<option value='$y' $selected>$y</option>";
        }
      ?>
    </select>
  </div>
  </form>

  <div class="table-responsive mb-3">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr><th>No</th><th>Nama</th><th>Kontak</th><th>Status</th><th>Tanggal</th><th>Aksi</th></tr>
      </thead>
      <tbody>
        <?php if (count($permohonans) > 0): $no = $start + 1; foreach ($permohonans as $row): ?>
        <tr>
          <td><?= $no++ ?></td>
          <td><?= htmlspecialchars($row['nama']) ?></td>
          <td><?= htmlspecialchars($row['kontak']) ?></td>
          <td>
            <span class="badge 
              <?= match($row['status']) {
                'Belum Diproses' => 'bg-warning',
                'Sedang Diproses' => 'bg-info',
                'Selesai' => 'bg-success',
                'Ditolak' => 'bg-danger',
                default => 'bg-secondary'
              } ?>">
              <?= $row['status'] ?>
            </span>
          </td>
          <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
          <td>
            <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-secondary">Detail</a>
            <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
            <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus data ini?')">Hapus</a>
          </td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="6" class="text-center">Tidak ada data ditemukan.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <nav>
    <ul class="pagination justify-content-center">
      <?php for ($i = 1; $i <= $total_pages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&status=<?= urlencode($status_filter) ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
    </ul>
  </nav>
</div>

<footer class="text-center py-3 text-muted" style="background-color: #f8f9fa;">
    © 2025 - PPID Bulungan by Seggaf
</footer>

</body>
</html>
