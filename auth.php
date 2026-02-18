<?php
// Authentication handler for login
session_start();

// Load users from file
$users_file = __DIR__ . '/users.json';
$default_users = [
    ['id' => 1, 'email' => 'demo@mebel.com', 'password' => password_hash('demo123', PASSWORD_DEFAULT), 'name' => 'Admin Demo'],
    ['id' => 2, 'email' => 'user@mebel.com', 'password' => password_hash('user123', PASSWORD_DEFAULT), 'name' => 'User Biasa']
];

$users = $default_users;

// Merge dengan users dari JSON file jika ada
if (file_exists($users_file)) {
    $file_users = json_decode(file_get_contents($users_file), true);
    if (is_array($file_users)) {
        $users = array_merge($default_users, $file_users);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Email dan password harus diisi!';
        header('Location: login.php');
        exit;
    }

    // Find user
    $user = null;
    foreach ($users as $u) {
        if ($u['email'] === $email) {
            // Check password: if hashed, use password_verify, else plain text
            $passwordMatch = false;
            if (strpos($u['password'], '$2y$') === 0) {
                $passwordMatch = password_verify($password, $u['password']);
            } else {
                $passwordMatch = ($u['password'] === $password);
            }

            if ($passwordMatch) {
                $user = $u;
                break;
            }
        }
    }

    if ($user) {
        // Regenerate session ID for security
        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['logged_in'] = true;

        // Clear any error messages
        unset($_SESSION['login_error']);

        header('Location: dashboard.php');
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
?>
