<?php
session_start();

// Simulasi: Mengambil nama provider (google/facebook)
$provider = isset($_GET['provider']) ? $_GET['provider'] : 'guest';

// Di sini biasanya ada proses verifikasi token dari Google/FB
// Untuk sekarang, kita anggap login berhasil:
$_SESSION['user_id'] = 999; // ID dummy
$_SESSION['user_name'] = "User " . ucfirst($provider);
$_SESSION['role'] = 'user'; // Peran sebagai user biasa

// Arahkan ke dashboard user
header("Location: dashboard.php");
exit();
?>