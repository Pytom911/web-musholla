<?php
session_start();

// Menghapus semua variabel session
session_unset();

// Menghancurkan session
session_destroy();

// Redirect ke halaman utama
header("Location: ../index.php");
exit;
?>