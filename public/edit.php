<?php
require '../config/database.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    die("ID tidak ditemukan.");
}

// Ambil data permohonan
$stmt = $pdo->prepare("SELECT * FROM permohonan WHERE id = ?");
$stmt->execute([$id]);
$data = $stmt->fetch();

if (!$data) {
    die("Data tidak ditemukan.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $alamat = $_POST['alamat'];
    $kontak = $_POST['kontak'];
    $rincian = $_POST['rincian'];
    $tujuan = $_POST['tujuan'];
    $cara_memperoleh = $_POST['cara_memperoleh'];
    $cara_salinan = $_POST['cara_salinan'];
    $status = $_POST['status'];

    // Update data utama
    $stmt = $pdo->prepare("UPDATE permohonan SET nama=?, alamat=?, kontak=?, rincian_informasi=?, tujuan_informasi=?, cara_memperoleh=?, cara_salinan=?, status=? WHERE id=?");
    $stmt->execute([$nama, $alamat, $kontak, $rincian, $tujuan, $cara_memperoleh, $cara_salinan, $status, $id]);

    // Cek apakah user mengupload file baru
    if (isset($_FILES['file_hasil']) && $_FILES['file_hasil']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['file_hasil'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($ext === 'pdf' && $file['size'] <= 2 * 1024 * 1024) {
            $tahun = date('Y');
            $bulan = date('m');
            $folderPath = "../uploads/hasil/$tahun/$bulan/";
            if (!is_dir($folderPath)) {
                mkdir($folderPath, 0777, true);
            }

            $namaFileBaru = uniqid('hasil_') . '.pdf';
            $targetPath = $folderPath . $namaFileBaru;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $relPath = "uploads/hasil/$tahun/$bulan/$namaFileBaru";
                $stmt = $pdo->prepare("UPDATE permohonan SET file_hasil=? WHERE id=?");
                $stmt->execute([$relPath, $id]);
            }
        }
    }

    header("Location: dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Permohonan</title>
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
    <h3 class="mb-4">Edit Permohonan Informasi</h3>
    <div class="card shadow">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($data['nama']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <input type="text" name="alamat" class="form-control" value="<?= htmlspecialchars($data['alamat']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kontak</label>
                    <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($data['kontak']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Rincian Informasi</label>
                    <textarea name="rincian" class="form-control" required><?= htmlspecialchars($data['rincian_informasi']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tujuan Penggunaan</label>
                    <textarea name="tujuan" class="form-control" required><?= htmlspecialchars($data['tujuan_informasi']) ?></textarea>
                </div>
                <div class="mb-3">
            <label class="form-label">Cara Memperoleh Informasi</label>
            <select name="cara_memperoleh" class="form-select" id="cara_memperoleh" required>
                <option disabled selected value="">-- Pilih Cara --</option>
                <option value="Melihat/membaca">Melihat / Membaca / Mendengarkan / Mencatat</option>
                <option value="Mendapatkan salinan">Mendapatkan Salinan</option>
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
                <div class="mb-3">
                    <label class="form-label">Status Permohonan</label>
                    <select name="status" class="form-select" required>
                        <option value="Belum Diproses" <?= $data['status'] === 'Belum Diproses' ? 'selected' : '' ?>>Belum Diproses</option>
                        <option value="Sedang Diproses" <?= $data['status'] === 'Sedang Diproses' ? 'selected' : '' ?>>Sedang Diproses</option>
                        <option value="Selesai" <?= $data['status'] === 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="Ditolak" <?= $data['status'] === 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">File Hasil (PDF)</label>
                    <?php if (!empty($data['file_hasil'])): ?>
                        <p>File sebelumnya: <a href="../<?= htmlspecialchars($data['file_hasil']) ?>" target="_blank">Lihat File</a></p>
                    <?php endif; ?>
                    <input type="file" name="file_hasil" class="form-control" accept="application/pdf">
                    <small class="text-muted">Biarkan kosong jika tidak ingin mengganti file.</small>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="dashboard.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<footer class="text-center py-3 text-muted" style="background-color: #f8f9fa;">
    © 2025 - PPID Bulungan by Seggaf
</footer>
</body>
</html>
