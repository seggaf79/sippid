<?php

// Format tanggal ke versi Indonesia
function formatTanggalIndo($tanggal) {
    return date('d/m/Y H:i', strtotime($tanggal));
}

// Escape HTML entity (untuk keamanan output)
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

?>
