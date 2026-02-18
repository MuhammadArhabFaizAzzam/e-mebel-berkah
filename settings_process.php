<?php
session_start();

// Include config
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    header('Location: index.php');
    exit;
}

// Verify CSRF token
if (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
    $_SESSION['settings_error'] = 'Token keamanan tidak valid!';
    header('Location: settings.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'change_password') {
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate inputs
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['settings_error'] = 'Semua field password harus diisi!';
            header('Location: settings.php');
            exit;
        }

        if (strlen($new_password) < 6) {
            $_SESSION['settings_error'] = 'Password baru minimal 6 karakter!';
            header('Location: settings.php');
            exit;
        }

        if ($new_password !== $confirm_password) {
            $_SESSION['settings_error'] = 'Konfirmasi password tidak cocok!';
            header('Location: settings.php');
            exit;
        }

        // Get user data
        $users = loadUsers();
        $userFound = false;
        $userId = $_SESSION['user_id'];

        foreach ($users as &$user) {
            if ($user['id'] == $userId) {
                // Verify current password
                if (!password_verify($current_password, $user['password'])) {
                    $_SESSION['settings_error'] = 'Password saat ini salah!';
                    header('Location: settings.php');
                    exit;
                }

                // Update password
                $user['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                $userFound = true;
                break;
            }
        }

        if ($userFound) {
            saveUsers($users);
            $_SESSION['settings_success'] = 'Password berhasil diubah!';
        } else {
            $_SESSION['settings_error'] = 'User tidak ditemukan!';
        }

    } elseif ($action === 'update_notifications') {
        // Handle notification preferences
        $email_notifications = isset($_POST['email_notifications']) ? 1 : 0;
        $sms_notifications = isset($_POST['sms_notifications']) ? 1 : 0;
        $newsletter = isset($_POST['newsletter']) ? 1 : 0;

        // Save to user data
        $users = loadUsers();
        $userId = $_SESSION['user_id'];

        foreach ($users as &$user) {
            if ($user['id'] == $userId) {
                $user['notifications'] = [
                    'email' => $email_notifications,
                    'sms' => $sms_notifications,
                    'newsletter' => $newsletter
                ];
                break;
            }
        }

        saveUsers($users);
        $_SESSION['settings_success'] = 'Preferensi notifikasi berhasil disimpan!';

    } elseif ($action === 'update_privacy') {
        // Handle privacy settings
        $public_profile = isset($_POST['public_profile']) ? 1 : 0;
        $private_history = isset($_POST['private_history']) ? 1 : 0;

        // Save to user data
        $users = loadUsers();
        $userId = $_SESSION['user_id'];

        foreach ($users as &$user) {
            if ($user['id'] == $userId) {
                $user['privacy'] = [
                    'public_profile' => $public_profile,
                    'private_history' => $private_history
                ];
                break;
            }
        }

        saveUsers($users);
        $_SESSION['settings_success'] = 'Pengaturan privasi berhasil disimpan!';

    } elseif ($action === 'delete_account') {
        // Handle account deletion
        $confirm_delete = $_POST['confirm_delete'] ?? '';

        if ($confirm_delete !== 'DELETE') {
            $_SESSION['settings_error'] = 'Konfirmasi penghapusan akun tidak valid!';
            header('Location: settings.php');
            exit;
        }

        // Delete user
        $users = loadUsers();
        $userId = $_SESSION['user_id'];

        $users = array_filter($users, function($user) use ($userId) {
            return $user['id'] != $userId;
        });

        saveUsers($users);

        // Destroy session
        session_destroy();

        header('Location: index.php?message=account_deleted');
        exit;
    }

    header('Location: settings.php');
    exit;
} else {
    header('Location: settings.php');
    exit;
}
?>
