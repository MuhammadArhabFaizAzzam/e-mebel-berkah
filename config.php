<?php
/**
 * Berkah Mebel Ayu - Configuration File
 * Centralisasi setting dan konfigurasi aplikasi
 */

// Brand & Company Info
define('APP_NAME', 'Berkah Mebel Ayu');
define('APP_TAGLINE', 'Furniture Berkualitas Premium');
define('APP_YEAR', '2026');

// Colors
define('COLOR_PRIMARY', '#8B4513');      // Wood Brown
define('COLOR_SECONDARY', '#D4A76A');   // Gold Wood
define('COLOR_ACCENT', '#E6C9A8');      // Light Wood
define('COLOR_DARK', '#5C4033');        // Dark Wood
define('COLOR_LIGHT', '#F5E6D3');       // Cream

// File Paths
define('USERS_FILE', __DIR__ . '/users.json');
define('IMG_PATH', 'img/');

// Session Settings
define('SESSION_TIMEOUT', 3600); // 1 hour

// Product Categories
$PRODUCT_CATEGORIES = [
    'Semua',
    'Kursi',
    'Meja',
    'Lemari',
    'Tempat Tidur',
    'Rak',
    'Sofa'
];

// Demo Account
$DEMO_ACCOUNT = [
    'email' => 'demo@mebel.com',
    'password' => 'demo123',
    'name' => 'Demo User'
];

// Helper Functions

/**
 * Format currency to Indonesian Rupiah
 */
function formatRupiah($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Get user data from session
 */
function getLoggedInUser() {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'email' => $_SESSION['user_email'] ?? null,
            'name' => $_SESSION['user_name'] ?? null
        ];
    }
    return null;
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Redirect to login if not authenticated
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

/**
 * Load users from JSON file
 */
function loadUsers() {
    if (!file_exists(USERS_FILE)) {
        return [];
    }
    $json = file_get_contents(USERS_FILE);
    return json_decode($json, true) ?? [];
}

/**
 * Save users to JSON file
 */
function saveUsers($users) {
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Find user by email
 */
function findUserByEmail($email) {
    $users = loadUsers();
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            return $user;
        }
    }
    return null;
}
?>
