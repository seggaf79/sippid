<?php
require_once '../config/database.php';

$status = '';
$found = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomor = trim($_POST['nomor']);
    $stmt = $pdo->prepare("SELECT status FROM permohonan WHERE no_pendaftaran = ?");
    $stmt->execute([$nomor]);
    $data = $stmt->fetch();

    if ($data) {
        $status = $data['status'];
        $found = true;
    } else {
        $status = "Nomor permohonan tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cek Status Permohonan - PPID Bulungan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #1e293b;
            color: #f8fafc;
        }
        .logo {
            width: 120px;
            margin-bottom: 20px;
        }
        .card {
            background-color: #334155;
        }
        footer {
            margin-top: 50px;
            color: #cbd5e1;
            font-size: 0.9rem;
        }
    </style>
</head>
<body class="d-flex flex-column justify-content-center align-items-center vh-100">

    <div class="text-center mb-4">
        <img src="../assets/img/logo_ppid.png" alt="Logo PPID" class="logo">
        <h5>Silahkan Masukkan Nomor Permohonan Informasi Di sini dan Tekan Tombol <strong>CEK PERMOHONAN</strong></h5>
    </div>

    <div class="card shadow p-4 text-center" style="width: 100%; max-width: 450px;">
        <form method="post">
            <div class="mb-3">
                <label for="nomor" class="form-label"><i class="fas fa-id-card"></i> Nomor Pendaftaran</label>
                <input type="text" name="nomor" id="nomor" class="form-control text-center" placeholder="Contoh: PPID-1718205000" required>
            </div>
            <button type="submit" class="btn btn-success w-100"><i class="fas fa-search"></i> CEK PERMOHONAN</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="mt-4">
                <?php if ($found): ?>
                    <h6><i class="fas fa-circle-check text-info me-2"></i>Status Permohonan Anda:</h6>
                    <h4 class="text-warning"><?= htmlspecialchars($status) ?></h4>
                <?php else: ?>
                    <div class="alert alert-danger mt-3"><i class="fas fa-exclamation-circle"></i> <?= $status ?></div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer class="text-center mt-5">
        <p>© 2025 - PPID Bulungan by Seggaf</p>
    </footer>

</body>
</html>
