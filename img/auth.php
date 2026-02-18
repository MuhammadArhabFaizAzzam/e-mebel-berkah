<?php
session_start();

// 1. DATABASE USER (Gunakan ini untuk login)
$users = [
    [
        'id' => 1, 
        'email' => 'admin@mebel.com', 
        'password' => 'admin123', 
        'name' => 'Admin Berkah Mebel',
        'role' => 'admin'
    ],
    [
        'id' => 2, 
        'email' => 'user@mebel.com', 
        'password' => 'user123', 
        'name' => 'Pelanggan',
        'role' => 'user'
    ]
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Email dan password harus diisi!';
        header('Location: login.php'); // Perbaikan Path
        exit;
    }

    $found_user = null;
    foreach ($users as $u) {
        if ($u['email'] === $email && $u['password'] === $password) {
            $found_user = $u;
            break;
        }
    }

    if ($found_user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $found_user['id'];
        $_SESSION['user_email'] = $found_user['email'];
        $_SESSION['user_name'] = $found_user['name'];
        $_SESSION['role'] = $found_user['role']; // Penting untuk admin_dasboard.php
        $_SESSION['logged_in'] = true;

        unset($_SESSION['login_error']);

        // Arahkan berdasarkan role
        if ($found_user['role'] === 'admin') {
            header('Location: admin_dasboard.php');
        } else {
            header('Location: dashboard.php');
        }
        exit;
    } else {
        $_SESSION['login_error'] = 'Email atau password salah!';
        header('Location: login.php');
        exit;
    }
} else {
    header('Location: login.php');
    exit;
}