<?php
session_start();

// Include database connection
require_once '../db_config.php';

// --- KEAMANAN: DATABASE LOGIN ADMIN ---
$error_login = '';
$admin_user = null;

// Cek apakah database tersedia
$db_available = isDatabaseConnected();

if ($db_available) {
    // Jika database tersedia, gunakan database untuk login
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $error_login = 'Username dan password harus diisi!';
        } else {
            // Cek username di database
            $admin = getQueryRow("SELECT * FROM admin_users WHERE (username = ? OR email = ?) AND is_active = 1", [$username, $username]);
            
            if ($admin && password_verify($password, $admin['password'])) {
                // Login berhasil
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['name'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_role'] = $admin['role'];
                
                // Update last login
                executeQuery("UPDATE admin_users SET last_login = NOW() WHERE id = ?", [$admin['id']]);
                
                header("Location: admin_dashboard.php");
                exit;
            } else {
                $error_login = 'Username atau password salah!';
            }
        }
    }
} else {
    // Fallback ke login sederhana jika database tidak tersedia
    $admin_password = "admin123";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        if ($_POST['password'] === $admin_password) {
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_name'] = 'Admin (Offline Mode)';
            $_SESSION['admin_role'] = 'admin';
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error_login = "Password salah!";
        }
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_dashboard.php");
    exit();
}

// Jika belum login, tampilkan halaman login saja
if (!isset($_SESSION['is_admin'])) {
    echo '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Berkah Mebel Ayu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #2D1B14 0%, #5D4037 100%); min-height: 100vh; }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-2xl p-8 max-w-md w-full">
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-amber-100 rounded-full mb-4">
                <i class="fas fa-user-shield text-2xl text-amber-700"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Admin Login</h2>
            <p class="text-gray-500 text-sm mt-2">Berkah Mebel Ayu</p>
            ' . (!$db_available ? '<p class="text-orange-500 text-xs mt-2">Mode Offline - Database tidak tersedia</p>' : '') . '
        </div>
        
        ' . ($error_login ? '<div class="mb-6 p-4 bg-red-100 border border-red-300 rounded-xl text-red-700 text-sm"><i class="fas fa-exclamation-circle mr-2"></i> ' . $error_login . '</div>' : '') . '
        
        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-user text-amber-700 mr-2"></i>Username atau Email
                </label>
                <input type="text" name="username" 
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none" 
                       placeholder="admin" required>
            </div>
            
            <div>
                <label class="block text-gray-700 font-semibold mb-2">
                    <i class="fas fa-lock text-amber-700 mr-2"></i>Password
                </label>
                <input type="password" name="password" 
                       class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none" 
                       placeholder="••••••••" required>
            </div>
            
            <button type="submit" name="login" class="w-full bg-amber-700 hover:bg-amber-800 text-white font-bold py-4 rounded-xl transition shadow-lg">
                <i class="fas fa-sign-in-alt mr-2"></i> Masuk
            </button>
        </form>
        
        <div class="mt-6 text-center text-sm text-gray-400">
            <p>Default: admin / admin123</p>
        </div>
    </div>
</body>
</html>';
    exit();
}

// --- INISIALISASI DATA ---
if (!isset($_SESSION['fs_active'])) {
    $_SESSION['fs_active'] = false;
    $_SESSION['fs_end'] = date('Y-m-d H:i', strtotime('+1 day'));
    $_SESSION['fs_discount'] = 0;
}

$success_msg = $_SESSION['success_msg'] ?? '';
$error_msg = $_SESSION['error_msg'] ?? '';
unset($_SESSION['success_msg'], $_SESSION['error_msg']);

// Get current tab
$current_tab = $_GET['tab'] ?? 'dashboard';

// Get statistics
$stats = [
    'products' => 0,
    'orders' => 0,
    'users' => 0,
    'contacts' => 0,
    'total_sales' => 0
];

if ($db_available) {
    $stats['products'] = getQueryRow("SELECT COUNT(*) as total FROM products WHERE status = 'active'")['total'] ?? 0;
    $stats['orders'] = getQueryRow("SELECT COUNT(*) as total FROM orders")['total'] ?? 0;
    $stats['users'] = getQueryRow("SELECT COUNT(*) as total FROM users WHERE is_active = 1")['total'] ?? 0;
    $stats['contacts'] = getQueryRow("SELECT COUNT(*) as total FROM contacts WHERE is_read = 0")['total'] ?? 0;
    $stats['total_sales'] = getQueryRow("SELECT COALESCE(SUM(total_amount), 0) as total FROM orders WHERE status = 'delivered'")['total'] ?? 0;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add_product') {
        // ADD PRODUCT
        $name = trim($_POST['p_name'] ?? '');
        $price = intval($_POST['p_price'] ?? 0);
        $stock = intval($_POST['p_stock'] ?? 0);
        $category = $_POST['p_category'] ?? 'Kursi';
        $description = htmlspecialchars($_POST['p_description'] ?? '');
        
        if (empty($name) || $price <= 0) {
            $error_msg = "Nama produk dan harga harus diisi dengan benar!";
        } else {
            // Handle image upload
            $image_url = 'https://placehold.co/300x300?text=' . urlencode($name);
            if (isset($_FILES['p_image']) && $_FILES['p_image']['error'] == 0) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
                
                if (in_array($_FILES['p_image']['type'], $allowed_types)) {
                    $target_dir = "../uploads/";
                    if (!file_exists($target_dir)) { 
                        mkdir($target_dir, 0777, true); 
                    }
                    $file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["p_image"]["name"])); 
                    $target_file = $target_dir . $file_name;
                    
                    if (move_uploaded_file($_FILES["p_image"]["tmp_name"], $target_file)) {
                        $image_url = $target_file;
                    }
                } else {
                    $error_msg = "Format file tidak didukung! Gunakan JPG/PNG/WebP.";
                }
            }
            
            if (empty($error_msg)) {
                if ($db_available) {
                    try {
                        executeQuery(
                            "INSERT INTO products (name, price, stock, category, description, image, is_flash_sale, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                            [$name, $price, $stock, $category, $description, $image_url, 0, 'active']
                        );
                        $success_msg = "Produk '$name' berhasil ditambahkan!";
                    } catch (Exception $e) {
                        $error_msg = "Error: " . $e->getMessage();
                    }
                } else {
                    if (!isset($_SESSION['products_katalog'])) {
                        $_SESSION['products_katalog'] = [];
                    }
                    
                    $new_product = [
                        'id' => time(),
                        'name' => $name,
                        'price' => $price,
                        'stock' => $stock,
                        'category' => $category,
                        'description' => $description,
                        'image' => $image_url,
                        'is_flash_sale' => false,
                        'status' => 'active'
                    ];
                    
                    array_unshift($_SESSION['products_katalog'], $new_product);
                    $success_msg = "Produk '$name' berhasil ditambahkan (Mode Offline)!";
                }
            }
        }
    } 
    elseif (isset($_POST['action']) && $_POST['action'] === 'update_flash') {
        // UPDATE FLASH SALE
        $_SESSION['fs_active'] = ($_POST['fs_status'] == '1');
        $_SESSION['fs_end'] = $_POST['fs_end_time'] ?? date('Y-m-d H:i', strtotime('+1 day'));
        $_SESSION['fs_discount'] = intval($_POST['fs_discount'] ?? 0);
        $success_msg = "Pengaturan Flash Sale berhasil diperbarui!";
    }
    elseif (isset($_POST['action']) && $_POST['action'] === 'update_order_status') {
        $order_id = intval($_POST['order_id'] ?? 0);
        $new_status = $_POST['status'] ?? 'pending';
        
        if ($order_id > 0 && $db_available) {
            try {
                executeQuery("UPDATE orders SET status = ? WHERE id = ?", [$new_status, $order_id]);
                $success_msg = "Status pesanan diperbarui!";
            } catch (Exception $e) {
                $error_msg = "Error: " . $e->getMessage();
            }
        }
    }
    elseif (isset($_POST['action']) && $_POST['action'] === 'reply_contact') {
        $contact_id = intval($_POST['contact_id'] ?? 0);
        
        if ($contact_id > 0 && $db_available) {
            try {
                executeQuery("UPDATE contacts SET is_read = 1 WHERE id = ?", [$contact_id]);
                $success_msg = "Pesan ditandai sudah dibaca!";
            } catch (Exception $e) {
                $error_msg = "Error: " . $e->getMessage();
            }
        }
    }
    elseif (isset($_POST['action']) && $_POST['action'] === 'update_settings') {
        if ($db_available && $_SESSION['admin_role'] === 'super_admin') {
            $settings_keys = ['site_name', 'site_description', 'contact_phone', 'contact_wa', 'contact_address', 'shipping_cost', 'free_shipping_min'];
            
            foreach ($settings_keys as $key) {
                if (isset($_POST[$key])) {
                    try {
                        executeQuery("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = ?", 
                            [$key, $_POST[$key], $_POST[$key]]);
                    } catch (Exception $e) {
                        // Ignore errors
                    }
                }
            }
            $success_msg = "Pengaturan berhasil disimpan!";
        }
    }
}

// Handle GET requests (delete, toggle)
if (isset($_GET['delete_id'])) {
    $product_id = intval($_GET['delete_id']);
    
    if ($product_id > 0) {
        if ($db_available) {
            try {
                executeQuery("DELETE FROM products WHERE id = ?", [$product_id]);
                $success_msg = "Produk berhasil dihapus!";
            } catch (Exception $e) {
                $error_msg = "Error: " . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['toggle_fs'])) {
    $product_id = intval($_GET['toggle_fs']);
    
    if ($product_id > 0) {
        if ($db_available) {
            try {
                $product = getQueryRow("SELECT is_flash_sale FROM products WHERE id = ?", [$product_id]);
                if ($product) {
                    $new_status = $product['is_flash_sale'] ? 0 : 1;
                    executeQuery("UPDATE products SET is_flash_sale = ? WHERE id = ?", [$new_status, $product_id]);
                    $success_msg = "Status flash sale diperbarui!";
                }
            } catch (Exception $e) {
                $error_msg = "Error: " . $e->getMessage();
            }
        }
    }
}

if (isset($_GET['delete_contact'])) {
    $contact_id = intval($_GET['delete_contact']);
    
    if ($contact_id > 0 && $db_available) {
        try {
            executeQuery("DELETE FROM contacts WHERE id = ?", [$contact_id]);
            $success_msg = "Pesan berhasil dihapus!";
        } catch (Exception $e) {
            $error_msg = "Error: " . $e->getMessage();
        }
    }
}

if (isset($_GET['delete_order'])) {
    $order_id = intval($_GET['delete_order']);
    
    if ($order_id > 0 && $db_available) {
        try {
            executeQuery("DELETE FROM order_items WHERE order_id = ?", [$order_id]);
            executeQuery("DELETE FROM orders WHERE id = ?", [$order_id]);
            $success_msg = "Pesanan berhasil dihapus!";
        } catch (Exception $e) {
            $error_msg = "Error: " . $e->getMessage();
        }
    }
}

if (isset($_GET['delete_user'])) {
    $user_id = intval($_GET['delete_user']);
    
    if ($user_id > 0 && $db_available && $_SESSION['admin_role'] === 'super_admin') {
        try {
            executeQuery("DELETE FROM users WHERE id = ?", [$user_id]);
            $success_msg = "User berhasil dihapus!";
        } catch (Exception $e) {
            $error_msg = "Error: " . $e->getMessage();
        }
    }
}

// Ambil data untuk tabs
$katalog = [];
$orders = [];
$users = [];
$contacts = [];
$settings = [];

if ($db_available) {
    $katalog = getQueryResult("SELECT * FROM products WHERE status = 'active' ORDER BY id DESC");
    $orders = getQueryResult("SELECT o.*, u.name as customer_name, u.phone as customer_phone FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 50");
    $users = getQueryResult("SELECT * FROM users ORDER BY created_at DESC LIMIT 50");
    $contacts = getQueryResult("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 20");
    
    $settings_result = getQueryResult("SELECT * FROM settings");
    foreach ($settings_result as $s) {
        $settings[$s['setting_key']] = $s['setting_value'];
    }
    
    if (empty($katalog)) {
        $katalog = [
            ['id' => 1, 'name' => 'Kursi Makan Jati', 'price' => 450000, 'category' => 'Kursi', 'image' => 'https://images.unsplash.com/photo-1503602642458-232111445657?q=80&w=300&h=300&fit=crop', 'is_flash_sale' => false, 'stock' => 10],
        ];
    }
} else {
    if (!isset($_SESSION['products_katalog'])) {
        $_SESSION['products_katalog'] = [
            ['id' => 1, 'name' => 'Kursi Makan Jati', 'price' => 450000, 'category' => 'Kursi', 'image' => 'https://images.unsplash.com/photo-1503602642458-232111445657?q=80&w=300&h=300&fit=crop', 'is_flash_sale' => false, 'stock' => 10],
        ];
    }
    $katalog = $_SESSION['products_katalog'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Berkah Mebel Ayu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .tab-active { border-bottom: 3px solid #b45309; color: #b45309; }
    </style>
</head>
<body class="bg-stone-50 flex">

    <!-- SIDEBAR -->
    <aside class="w-64 min-h-screen bg-[#2D1B14] text-stone-300 p-6 fixed shadow-2xl flex flex-col">
        <div class="mb-8 text-white flex-shrink-0">
            <div class="font-bold text-xl uppercase tracking-widest">Admin Panel</div>
            <div class="text-xs text-amber-400 mt-2">
                <i class="fas fa-user-circle mr-1"></i> <?php echo $_SESSION['admin_name'] ?? 'Admin'; ?>
            </div>
            <div class="text-xs text-stone-500 mt-1">
                <?php echo $db_available ? '<span class="text-green-400"><i class="fas fa-database"></i> Database Online</span>' : '<span class="text-orange-400"><i class="fas fa-warning"></i> Mode Offline</span>'; ?>
            </div>
        </div>
        
        <nav class="space-y-2 flex-1">
            <a href="?tab=dashboard" class="block px-4 py-3 rounded-lg transition <?php echo $current_tab == 'dashboard' ? 'bg-amber-700 text-white' : 'hover:bg-stone-700'; ?>">
                <i class="fas fa-tachometer-alt mr-2 w-6"></i> Dashboard
            </a>
            <a href="?tab=products" class="block px-4 py-3 rounded-lg transition <?php echo $current_tab == 'products' ? 'bg-amber-700 text-white' : 'hover:bg-stone-700'; ?>">
                <i class="fas fa-box mr-2 w-6"></i> Produk
            </a>
            <a href="?tab=orders" class="block px-4 py-3 rounded-lg transition <?php echo $current_tab == 'orders' ? 'bg-amber-700 text-white' : 'hover:bg-stone-700'; ?>">
                <i class="fas fa-shopping-cart mr-2 w-6"></i> Pesanan
                <?php if($stats['orders'] > 0): ?>
                    <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $stats['orders']; ?></span>
                <?php endif; ?>
            </a>
            <a href="?tab=users" class="block px-4 py-3 rounded-lg transition <?php echo $current_tab == 'users' ? 'bg-amber-700 text-white' : 'hover:bg-stone-700'; ?>">
                <i class="fas fa-users mr-2 w-6"></i> Customer
            </a>
            <a href="?tab=contacts" class="block px-4 py-3 rounded-lg transition <?php echo $current_tab == 'contacts' ? 'bg-amber-700 text-white' : 'hover:bg-stone-700'; ?>">
                <i class="fas fa-envelope mr-2 w-6"></i> Pesan Masuk
                <?php if($stats['contacts'] > 0): ?>
                    <span class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"><?php echo $stats['contacts']; ?></span>
                <?php endif; ?>
            </a>
            <?php if($_SESSION['admin_role'] === 'super_admin'): ?>
            <a href="?tab=settings" class="block px-4 py-3 rounded-lg transition <?php echo $current_tab == 'settings' ? 'bg-amber-700 text-white' : 'hover:bg-stone-700'; ?>">
                <i class="fas fa-cog mr-2 w-6"></i> Pengaturan
            </a>
            <?php endif; ?>
        </nav>
        
        <div class="border-t border-stone-600 pt-4 mt-4">
            <a href="../index.php" target="_blank" class="block px-4 py-2 rounded-lg hover:bg-stone-700 transition">
                <i class="fas fa-eye mr-2"></i> Lihat Website
            </a>
            <a href="?logout=1" class="block px-4 py-2 rounded-lg text-red-400 hover:bg-red-900/30 transition mt-2">
                <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 p-8 ml-64">
        
        <!-- MESSAGES -->
        <?php if($success_msg): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-xl border border-green-300 flex items-center">
                <i class="fas fa-check-circle mr-3"></i> 
                <span><?php echo htmlspecialchars($success_msg); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if($error_msg): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-xl border border-red-300 flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i> 
                <span><?php echo htmlspecialchars($error_msg); ?></span>
            </div>
        <?php endif; ?>

        <!-- ========== DASHBOARD TAB ========== -->
        <?php if($current_tab == 'dashboard'): ?>
        <div class="space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Produk</p>
                            <p class="text-3xl font-bold text-gray-800"><?php echo $stats['products']; ?></p>
                        </div>
                        <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-box text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Pesanan</p>
                            <p class="text-3xl font-bold text-gray-800"><?php echo $stats['orders']; ?></p>
                        </div>
                        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Customer</p>
                            <p class="text-3xl font-bold text-gray-800"><?php echo $stats['users']; ?></p>
                        </div>
                        <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-amber-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-gray-500 text-sm">Total Penjualan</p>
                            <p class="text-2xl font-bold text-gray-800">Rp <?php echo number_format($stats['total_sales'], 0, ',', '.'); ?></p>
                        </div>
                        <div class="w-14 h-14 bg-amber-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-money-bill-wave text-amber-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-bolt text-amber-700 mr-2"></i>Quick Actions
                </h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="?tab=products" class="p-4 bg-blue-50 rounded-xl text-center hover:bg-blue-100 transition">
                        <i class="fas fa-plus-circle text-2xl text-blue-600 mb-2"></i>
                        <p class="text-sm font-semibold text-blue-800">Tambah Produk</p>
                    </a>
                    <a href="?tab=orders" class="p-4 bg-green-50 rounded-xl text-center hover:bg-green-100 transition">
                        <i class="fas fa-list text-2xl text-green-600 mb-2"></i>
                        <p class="text-sm font-semibold text-green-800">Lihat Pesanan</p>
                    </a>
                    <a href="?tab=contacts" class="p-4 bg-purple-50 rounded-xl text-center hover:bg-purple-100 transition">
                        <i class="fas fa-envelope text-2xl text-purple-600 mb-2"></i>
                        <p class="text-sm font-semibold text-purple-800">Pesan Masuk</p>
                    </a>
                    <a href="?tab=settings" class="p-4 bg-amber-50 rounded-xl text-center hover:bg-amber-100 transition">
                        <i class="fas fa-cog text-2xl text-amber-600 mb-2"></i>
                        <p class="text-sm font-semibold text-amber-800">Pengaturan</p>
                    </a>
                </div>
            </div>
            
            <!-- Recent Orders -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-stone-700 to-stone-800 p-4">
                    <h3 class="text-lg font-bold text-white">
                        <i class="fas fa-shopping-cart mr-2"></i>Pesanan Terbaru
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-stone-100 text-xs font-bold uppercase">
                            <tr>
                                <th class="px-4 py-3">No. Pesanan</th>
                                <th class="px-4 py-3">Customer</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(!empty($orders)): ?>
                                <?php foreach(array_slice($orders, 0, 5) as $order): ?>
                                <tr class="border-b hover:bg-stone-50">
                                    <td class="px-4 py-3 font-mono text-xs"><?php echo htmlspecialchars($order['order_number']); ?></td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($order['customer_name'] ?? 'Guest'); ?></td>
                                    <td class="px-4 py-3 text-right font-semibold">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                                    <td class="px-4 py-3">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                            <?php echo $order['status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                                ($order['status'] == 'processing' ? 'bg-blue-100 text-blue-700' : 
                                                ($order['status'] == 'shipped' ? 'bg-purple-100 text-purple-700' : 
                                                ($order['status'] == 'delivered' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'))); ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-500"><?php echo date('d/m/Y', strtotime($order['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-3xl mb-2"></i>
                                        <p>Belum ada pesanan</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t">
                    <a href="?tab=orders" class="text-amber-700 hover:text-amber-800 font-semibold text-sm">
                        <i class="fas fa-arrow-right mr-1"></i> Lihat Semua Pesanan
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ========== PRODUCTS TAB ========== -->
        <?php if($current_tab == 'products'): ?>
        <div class="space-y-6">
            <!-- Form Tambah Produk -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h2 class="text-xl font-bold text-stone-800 mb-4">
                    <i class="fas fa-plus-circle text-amber-700 mr-2"></i>Tambah Produk Baru
                </h2>
                
                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="action" value="add_product">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Produk *</label>
                            <input type="text" name="p_name" placeholder="Kursi Makan Jati" required 
                                   class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Harga (Rp) *</label>
                            <input type="number" name="p_price" placeholder="450000" required min="0"
                                   class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Stok *</label>
                            <input type="number" name="p_stock" placeholder="10" required min="0"
                                   class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none" value="0">
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Kategori</label>
                            <select name="p_category" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none bg-white">
                                <option>Kursi</option>
                                <option>Meja</option>
                                <option>Lemari</option>
                                <option>Tempat Tidur</option>
                                <option>Rak</option>
                                <option>Sofa</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Gambar Produk</label>
                            <input type="file" name="p_image" accept="image/*"
                                   class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl text-sm focus:border-amber-600 focus:outline-none">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                        <textarea name="p_description" placeholder="Deskripsi produk..." rows="2"
                                  class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none resize-none"></textarea>
                    </div>
                    
                    <button type="submit" class="bg-gradient-to-r from-stone-700 to-stone-800 hover:from-stone-800 hover:to-stone-900 text-white font-bold py-2 px-6 rounded-xl transition shadow-lg">
                        <i class="fas fa-save mr-2"></i>Simpan Produk
                    </button>
                </form>
            </div>

            <!-- Flash Sale Settings -->
            <div class="bg-white p-6 rounded-2xl shadow-lg border-l-4 border-red-600">
                <h2 class="text-xl font-bold text-stone-800 mb-4">
                    <i class="fas fa-bolt text-red-600 mr-2"></i>Pengaturan Flash Sale
                </h2>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="update_flash">
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                            <select name="fs_status" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-red-600 focus:outline-none bg-white">
                                <option value="1" <?php echo $_SESSION['fs_active'] ? 'selected' : ''; ?>>Aktif</option>
                                <option value="0" <?php echo !$_SESSION['fs_active'] ? 'selected' : ''; ?>>Nonaktif</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Diskon (%)</label>
                            <input type="number" name="fs_discount" value="<?php echo $_SESSION['fs_discount']; ?>" 
                                   min="0" max="100" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-red-600 focus:outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Waktu Berakhir</label>
                            <input type="datetime-local" name="fs_end_time" 
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($_SESSION['fs_end'] ?? '+1 day')); ?>"
                                   class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-red-600 focus:outline-none">
                        </div>
                    </div>
                    
                    <button type="submit" class="bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white font-bold py-2 px-6 rounded-xl transition">
                        <i class="fas fa-sync-alt mr-2"></i>Update
                    </button>
                </form>
            </div>

            <!-- Daftar Produk -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-stone-700 to-stone-800 p-4">
                    <h2 class="text-xl font-bold text-white">
                        <i class="fas fa-list mr-2"></i>Daftar Produk (<?php echo count($katalog); ?>)
                    </h2>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-stone-100 text-xs font-bold uppercase">
                            <tr>
                                <th class="px-4 py-3">Produk</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3 text-right">Harga</th>
                                <th class="px-4 py-3 text-center">Stok</th>
                                <th class="px-4 py-3 text-center">FS</th>
                                <th class="px-4 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($katalog)): ?>
                                <?php foreach ($katalog as $p): 
                                    $is_fs = ($_SESSION['fs_active'] && ($p['is_flash_sale'] ?? false));
                                ?>
                                <tr class="border-b hover:bg-stone-50 <?php echo $is_fs ? 'bg-red-50' : ''; ?>">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <img src="<?php echo htmlspecialchars($p['image'] ?? 'https://placehold.co/50x50'); ?>" 
                                                 class="w-10 h-10 rounded-lg object-cover">
                                            <div class="font-semibold"><?php echo htmlspecialchars($p['name']); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3"><?php echo htmlspecialchars($p['category'] ?? 'Umum'); ?></td>
                                    <td class="px-4 py-3 text-right font-semibold">Rp <?php echo number_format($p['price'], 0, ',', '.'); ?></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold
                                            <?php echo ($p['stock'] ?? 0) > 5 ? 'bg-green-100 text-green-700' : (($p['stock'] ?? 0) > 0 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700'); ?>">
                                            <?php echo $p['stock'] ?? 0; ?>
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="?tab=products&toggle_fs=<?php echo $p['id']; ?>" 
                                           class="inline-flex w-8 h-8 rounded-full items-center justify-center transition
                                           <?php echo ($p['is_flash_sale'] ?? false) ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-stone-200 hover:bg-stone-300 text-stone-600'; ?>">
                                            <i class="fas fa-bolt"></i>
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <a href="?tab=products&delete_id=<?php echo $p['id']; ?>" 
                                           onclick="return confirm('Hapus produk ini?')"
                                           class="inline-flex w-8 h-8 rounded-full bg-red-100 hover:bg-red-600 text-red-600 hover:text-white transition">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                        <i class="fas fa-inbox text-3xl mb-2"></i>
                                        <p>Tidak ada produk</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- ========== ORDERS TAB ========== -->
        <?php if($current_tab == 'orders'): ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-stone-700 to-stone-800 p-4">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-shopping-cart mr-2"></i>Kelola Pesanan
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-stone-100 text-xs font-bold uppercase">
                        <tr>
                            <th class="px-4 py-3">No. Pesanan</th>
                            <th class="px-4 py-3">Customer</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Pembayaran</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($orders)): ?>
                            <?php foreach($orders as $order): ?>
                            <tr class="border-b hover:bg-stone-50">
                                <td class="px-4 py-3 font-mono text-xs"><?php echo htmlspecialchars($order['order_number']); ?></td>
                                <td class="px-4 py-3">
                                    <div class="font-semibold"><?php echo htmlspecialchars($order['customer_name'] ?? 'Guest'); ?></div>
                                    <div class="text-xs text-gray-500"><?php echo htmlspecialchars($order['customer_phone'] ?? ''); ?></div>
                                </td>
                                <td class="px-4 py-3 text-right font-bold">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></td>
                                <td class="px-4 py-3">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="update_order_status">
                                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                        <select name="status" onchange="this.form.submit()" 
                                                class="text-xs px-2 py-1 rounded-full font-semibold border-0 cursor-pointer
                                                <?php echo $order['status'] == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                                    ($order['status'] == 'processing' ? 'bg-blue-100 text-blue-700' : 
                                                    ($order['status'] == 'shipped' ? 'bg-purple-100 text-purple-700' : 
                                                    ($order['status'] == 'delivered' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'))); ?>">
                                            <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="processing" <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing</option>
                                            <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>Shipped</option>
                                            <option value="delivered" <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                            <option value="cancelled" <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-gray-600"><?php echo htmlspecialchars($order['payment_method'] ?? '-'); ?></td>
                                <td class="px-4 py-3 text-gray-500"><?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?></td>
                                <td class="px-4 py-3 text-center">
                                    <a href="?tab=orders&delete_order=<?php echo $order['id']; ?>" 
                                       onclick="return confirm('Hapus pesanan ini?')"
                                       class="inline-flex w-8 h-8 rounded-full bg-red-100 hover:bg-red-600 text-red-600 hover:text-white transition">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-shopping-cart text-3xl mb-2"></i>
                                    <p>Belum ada pesanan</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ========== USERS TAB ========== -->
        <?php if($current_tab == 'users'): ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-stone-700 to-stone-800 p-4">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-users mr-2"></i>Kelola Customer
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-stone-100 text-xs font-bold uppercase">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Telepon</th>
                            <th class="px-4 py-3">Kota</th>
                            <th class="px-4 py-3">Bergabung</th>
                            <?php if($_SESSION['admin_role'] === 'super_admin'): ?>
                            <th class="px-4 py-3 text-center">Aksi</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($users)): ?>
                            <?php foreach($users as $user): ?>
                            <tr class="border-b hover:bg-stone-50">
                                <td class="px-4 py-3 font-semibold"><?php echo htmlspecialchars($user['name']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($user['email']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($user['city'] ?? '-'); ?></td>
                                <td class="px-4 py-3 text-gray-500"><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                                <?php if($_SESSION['admin_role'] === 'super_admin'): ?>
                                <td class="px-4 py-3 text-center">
                                    <a href="?tab=users&delete_user=<?php echo $user['id']; ?>" 
                                       onclick="return confirm('Hapus user ini?')"
                                       class="inline-flex w-8 h-8 rounded-full bg-red-100 hover:bg-red-600 text-red-600 hover:text-white transition">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
<td colspan="<?php echo $_SESSION['admin_role'] === 'super_admin' ? 6 : 5; ?>" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-users text-3xl mb-2"></i>
                                    <p>Belum ada customer</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ========== CONTACTS TAB ========== -->
        <?php if($current_tab == 'contacts'): ?>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-stone-700 to-stone-800 p-4">
                <h2 class="text-xl font-bold text-white">
                    <i class="fas fa-envelope mr-2"></i>Pesan Masuk (<?php echo $stats['contacts']; ?> belum dibaca)
                </h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-stone-100 text-xs font-bold uppercase">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Subjek</th>
                            <th class="px-4 py-3">Pesan</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($contacts)): ?>
                            <?php foreach($contacts as $contact): ?>
                            <tr class="border-b hover:bg-stone-50 <?php echo !$contact['is_read'] ? 'bg-blue-50' : ''; ?>">
                                <td class="px-4 py-3 font-semibold"><?php echo htmlspecialchars($contact['name']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($contact['email']); ?></td>
                                <td class="px-4 py-3"><?php echo htmlspecialchars($contact['subject'] ?? '-'); ?></td>
                                <td class="px-4 py-3 max-w-xs truncate"><?php echo htmlspecialchars($contact['message']); ?></td>
                                <td class="px-4 py-3 text-gray-500"><?php echo date('d/m/Y H:i', strtotime($contact['created_at'])); ?></td>
                                <td class="px-4 py-3 text-center">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="reply_contact">
                                        <input type="hidden" name="contact_id" value="<?php echo $contact['id']; ?>">
                                        <button type="submit" class="inline-flex w-8 h-8 rounded-full bg-green-100 hover:bg-green-600 text-green-600 hover:text-white transition">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <a href="?tab=contacts&delete_contact=<?php echo $contact['id']; ?>" 
                                       onclick="return confirm('Hapus pesan ini?')"
                                       class="inline-flex w-8 h-8 rounded-full bg-red-100 hover:bg-red-600 text-red-600 hover:text-white transition ml-1">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                    <i class="fas fa-envelope-open text-3xl mb-2"></i>
                                    <p>Belum ada pesan</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- ========== SETTINGS TAB ========== -->
        <?php if($current_tab == 'settings' && $_SESSION['admin_role'] === 'super_admin'): ?>
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h2 class="text-xl font-bold text-stone-800 mb-4">
                <i class="fas fa-cog text-amber-700 mr-2"></i>Pengaturan Website
            </h2>
            
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="update_settings">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nama Website</label>
                        <input type="text" name="site_name" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'Berkah Mebel Ayu'); ?>"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Deskripsi</label>
                        <input type="text" name="site_description" value="<?php echo htmlspecialchars($settings['site_description'] ?? ''); ?>"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="contact_phone" value="<?php echo htmlspecialchars($settings['contact_phone'] ?? ''); ?>"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">WhatsApp</label>
                        <input type="text" name="contact_wa" value="<?php echo htmlspecialchars($settings['contact_wa'] ?? ''); ?>"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Alamat</label>
                        <textarea name="contact_address" rows="2"
                                  class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none"><?php echo htmlspecialchars($settings['contact_address'] ?? ''); ?></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Biaya Ongkir (Rp)</label>
                        <input type="number" name="shipping_cost" value="<?php echo htmlspecialchars($settings['shipping_cost'] ?? '25000'); ?>"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Gratis Ongkir Min. Belanja (Rp)</label>
                        <input type="number" name="free_shipping_min" value="<?php echo htmlspecialchars($settings['free_shipping_min'] ?? '500000'); ?>"
                               class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none">
                    </div>
                </div>
                
                <button type="submit" class="bg-gradient-to-r from-amber-600 to-amber-700 hover:from-amber-700 hover:to-amber-800 text-white font-bold py-2 px-6 rounded-xl transition shadow-lg">
                    <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                </button>
            </form>
        </div>
        <?php endif; ?>

    </main>
</body>
</html>

