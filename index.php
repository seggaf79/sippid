<?php
session_start();

// Jika user sudah login, arahkan ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: public/dashboard.php");
    exit();
}

// Jika belum login, arahkan ke halaman login
header("Location: public/login.php");
exit();
?>
