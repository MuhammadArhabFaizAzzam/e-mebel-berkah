<?php
session_start();

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

// Check if email already exists
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
$new_id = count($users) + 1;

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
    header('Location: register.php');
    exit;
} else {
    $_SESSION['register_error'] = 'Gagal menyimpan data. Silakan coba lagi.';
    header('Location: register.php');
    exit;
}
?>
