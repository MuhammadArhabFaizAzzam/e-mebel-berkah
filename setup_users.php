<?php
// Script untuk setup user demo di database
session_start();
require_once 'db_config.php';

$db_available = isDatabaseConnected();

if (!$db_available) {
    die("Database tidak tersedia!");
}

// Data user demo
$demo_users = [
    [
        'name' => 'Admin Demo',
        'email' => 'demo@mebel.com',
        'phone' => '08123456789',
        'password' => 'demo123',
        'address' => 'Jl. Furniture No. 1, Jakarta',
        'city' => 'Jakarta',
        'province' => 'DKI Jakarta',
        'postal_code' => '12345'
    ],
    [
        'name' => 'User Biasa',
        'email' => 'user@mebel.com',
        'phone' => '08987654321',
        'password' => 'user123',
        'address' => 'Jl. Kayu Jati No. 99, Bandung',
        'city' => 'Bandung',
        'province' => 'Jawa Barat',
        'postal_code' => '40123'
    ]
];

try {
    $count = 0;
    foreach ($demo_users as $user) {
        // Check if user already exists
        $existing = getQueryRow("SELECT id FROM users WHERE email = ?", [$user['email']]);
        
        if (!$existing) {
            $hashed_password = password_hash($user['password'], PASSWORD_DEFAULT);
            
            executeQuery(
                "INSERT INTO users (name, email, phone, password, address, city, province, postal_code, is_active, created_at, updated_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())",
                [
                    $user['name'],
                    $user['email'],
                    $user['phone'],
                    $hashed_password,
                    $user['address'],
                    $user['city'],
                    $user['province'],
                    $user['postal_code']
                ]
            );
            $count++;
        }
    }

    echo "<div style='padding: 20px; background: #d4edda; border: 1px solid #c3e6cb; border-radius: 5px; color: #155724;'>";
    echo "<h2>✅ Setup Berhasil!</h2>";
    echo "<p>User demo yang ditambahkan: <strong>$count</strong></p>";
    echo "<p><strong>Akun Demo:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Email:</strong> demo@mebel.com | <strong>Password:</strong> demo123</li>";
    echo "<li><strong>Email:</strong> user@mebel.com | <strong>Password:</strong> user123</li>";
    echo "</ul>";
    echo "<p><a href='login.php' style='color: #155724; text-decoration: underline; font-weight: bold;'>Klik di sini untuk login</a></p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<div style='padding: 20px; background: #f8d7da; border: 1px solid #f5c6cb; border-radius: 5px; color: #721c24;'>";
    echo "<h2>❌ Error!</h2>";
    echo "<p>Pesan: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>
