<?php
// Authentication handler for login
session_start();

// Include database configuration
require_once 'db_config.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validate input
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Email dan password harus diisi!';
        header('Location: login.php');
        exit;
    }

    // Check database connection
    $db_available = isDatabaseConnected();

    if ($db_available) {
        // Login dari database
        try {
            $user = getQueryRow("SELECT * FROM users WHERE email = ? AND is_active = 1", [$email]);
            
            if ($user && password_verify($password, $user['password'])) {
                // Login berhasil
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_phone'] = $user['phone'];
                $_SESSION['user_address'] = $user['address'];
                $_SESSION['logged_in'] = true;

                // Update last login
                executeQuery("UPDATE users SET updated_at = NOW() WHERE id = ?", [$user['id']]);

                unset($_SESSION['login_error']);
                header('Location: dashboard.php');
                exit;
            } else {
                $_SESSION['login_error'] = 'Email atau password salah!';
                header('Location: login.php');
                exit;
            }
        } catch (Exception $e) {
            $_SESSION['login_error'] = 'Terjadi kesalahan database. Silakan coba lagi.';
            header('Location: login.php');
            exit;
        }
    } else {
        // Fallback ke file JSON jika database tidak tersedia
        $users_file = __DIR__ . '/users.json';
        $default_users = [
            ['id' => 1, 'email' => 'demo@mebel.com', 'password' => password_hash('demo123', PASSWORD_DEFAULT), 'name' => 'Admin Demo'],
            ['id' => 2, 'email' => 'user@mebel.com', 'password' => password_hash('user123', PASSWORD_DEFAULT), 'name' => 'User Biasa']
        ];

        $users = $default_users;

        if (file_exists($users_file)) {
            $file_users = json_decode(file_get_contents($users_file), true);
            if (is_array($file_users)) {
                $users = array_merge($default_users, $file_users);
            }
        }

        $user = null;
        foreach ($users as $u) {
            if ($u['email'] === $email) {
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
            session_regenerate_id(true);

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['logged_in'] = true;

            unset($_SESSION['login_error']);
            header('Location: dashboard.php');
            exit;
        } else {
            $_SESSION['login_error'] = 'Email atau password salah!';
            header('Location: login.php');
            exit;
        }
    }
} else {
    header('Location: login.php');
    exit;
}
?>
