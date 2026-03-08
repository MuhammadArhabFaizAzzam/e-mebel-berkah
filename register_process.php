<?php
session_start();

// Include database configuration
require_once 'db_config.php';

// Validate input
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm_password = $_POST['password_confirm'] ?? '';
$terms = $_POST['terms'] ?? '';

// Validation
if (empty($name) || empty($email) || empty($phone) || empty($password) || empty($confirm_password)) {
    $_SESSION['register_error'] = 'Semua field harus diisi!';
    header('Location: register.php');
    exit;
}

if (strlen($password) < 6) {
    $_SESSION['register_error'] = 'Password minimal 6 karakter!';
    header('Location: register.php');
    exit;
}

if ($password !== $confirm_password) {
    $_SESSION['register_error'] = 'Password tidak cocok!';
    header('Location: register.php');
    exit;
}

if (!$terms) {
    $_SESSION['register_error'] = 'Anda harus menerima syarat dan ketentuan!';
    header('Location: register.php');
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['register_error'] = 'Format email tidak valid!';
    header('Location: register.php');
    exit;
}

// Check database connection
$db_available = isDatabaseConnected();

if ($db_available) {
    // Register ke database
    try {
        // Check if email already exists
        $existing_user = getQueryRow("SELECT id FROM users WHERE email = ?", [$email]);
        
        if ($existing_user) {
            $_SESSION['register_error'] = 'Email sudah terdaftar! Gunakan email lain atau login.';
            header('Location: register.php');
            exit;
        }

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user
        executeQuery(
            "INSERT INTO users (name, email, phone, password, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, 1, NOW(), NOW())",
            [$name, $email, $phone, $hashed_password]
        );

        $_SESSION['register_success'] = true;
        $_SESSION['register_email'] = $email;
        
        // Auto login setelah daftar
        $new_user = getQueryRow("SELECT id, name, email, phone FROM users WHERE email = ?", [$email]);
        
        if ($new_user) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $new_user['id'];
            $_SESSION['user_email'] = $new_user['email'];
            $_SESSION['user_name'] = $new_user['name'];
            $_SESSION['user_phone'] = $new_user['phone'];
            $_SESSION['logged_in'] = true;
            
            // Redirect ke dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            // Jika auto-login gagal, redirect ke login
            header('Location: register.php');
            exit;
        }
    } catch (Exception $e) {
        $_SESSION['register_error'] = 'Terjadi kesalahan: ' . $e->getMessage();
        header('Location: register.php');
        exit;
    }
} else {
    // Fallback ke file JSON jika database tidak tersedia
    $users_file = __DIR__ . '/users.json';
    $users = [];

    if (file_exists($users_file)) {
        $users = json_decode(file_get_contents($users_file), true) ?? [];
    }

    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $_SESSION['register_error'] = 'Email sudah terdaftar!';
            header('Location: register.php');
            exit;
        }
    }

    // Generate new user ID
    $new_id = count($users) + 3; // Mulai dari 3 untuk menghindari conflict dengan database default

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Add new user
    $new_user = [
        'id' => $new_id,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'password' => $hashed_password,
        'created_at' => date('Y-m-d H:i:s')
    ];

    $users[] = $new_user;

    // Save to file
    if (file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
        $_SESSION['register_success'] = true;
        $_SESSION['register_email'] = $email;
        
        // Auto login
        session_regenerate_id(true);
        $_SESSION['user_id'] = $new_user['id'];
        $_SESSION['user_email'] = $new_user['email'];
        $_SESSION['user_name'] = $new_user['name'];
        $_SESSION['user_phone'] = $new_user['phone'];
        $_SESSION['logged_in'] = true;
        
        header('Location: dashboard.php');
        exit;
    } else {
        $_SESSION['register_error'] = 'Gagal menyimpan data. Silakan coba lagi.';
        header('Location: register.php');
        exit;
    }
}
?>
