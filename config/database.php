<?php
$host = "localhost";        // Ganti jika pakai host/database eksternal
$dbname = "ppid_bulungan"; // Nama database
$username = "root";        // Username database
$password = "";            // Password database (kosong di localhost XAMPP)

// Koneksi menggunakan PDO
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set error mode ke exception biar lebih mudah debugging
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
