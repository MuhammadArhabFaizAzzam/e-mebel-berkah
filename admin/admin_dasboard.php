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
                
                header("Location: admin_dasboard.php");
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
        } else {
            $error_login = "Password salah!";
        }
    }
}

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin_dasboard.php");
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

// --- 2. INISIALISASI DATA ---
if (!isset($_SESSION['fs_active'])) {
    $_SESSION['fs_active'] = false;
    $_SESSION['fs_end'] = date('Y-m-d H:i', strtotime('+1 day'));
    $_SESSION['fs_discount'] = 0;
}

// Ambil produk dari database jika tersedia, kalau tidak dari session
$products_db = [];

if ($db_available) {
    $products_db = getQueryResult("SELECT * FROM products WHERE status = 'active' ORDER BY id DESC");
    if (empty($products_db)) {
        $products_db = [
            ['id' => 1, 'name' => 'Kursi Makan Jati', 'price' => 450000, 'category' => 'Kursi', 'image' => 'https://images.unsplash.com/photo-1503602642458-232111445657?q=80&w=300&h=300&fit=crop', 'is_flash_sale' => false, 'stock' => 10],
        ];
    }
} else {
    // Fallback ke session
    if (!isset($_SESSION['products_katalog'])) {
        $_SESSION['products_katalog'] = [
            ['id' => 1, 'name' => 'Kursi Makan Jati', 'price' => 450000, 'category' => 'Kursi', 'image' => 'https://images.unsplash.com/photo-1503602642458-232111445657?q=80&w=300&h=300&fit=crop', 'is_flash_sale' => false, 'stock' => 10],
        ];
    }
    $products_db = $_SESSION['products_katalog'];
}

// --- 3. LOGIKA TAMBAH PRODUK ---
$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $target_file = "";
    if (isset($_FILES['p_image']) && $_FILES['p_image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $file_type = $_FILES['p_image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $target_dir = "../uploads/";
            if (!file_exists($target_dir)) { mkdir($target_dir, 0777, true); }
            $file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["p_image"]["name"])); 
            $target_file = $target_dir . $file_name;
            move_uploaded_file($_FILES["p_image"]["tmp_name"], $target_file);
        } else {
            $error_msg = "Format file tidak didukung! Gunakan JPG/PNG.";
        }
    }
    
    $new_product = [
        'name' => htmlspecialchars($_POST['p_name'] ?? 'Produk Baru'),
        'price' => intval($_POST['p_price'] ?? 0),
        'category' => $_POST['p_category'] ?? 'Kursi',
        'stock' => intval($_POST['p_stock'] ?? 0),
        'description' => htmlspecialchars($_POST['p_description'] ?? ''),
        'image' => !empty($target_file) ? $target_file : 'https://placehold.co/300x300?text=No+Image',
        'is_flash_sale' => false,
        'status' => 'active'
    ];
    
    if ($db_available) {
        // Simpan ke database
        $stmt = executeQuery(
            "INSERT INTO products (name, price, category, stock, description, image, is_flash_sale, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
            [$new_product['name'], $new_product['price'], $new_product['category'], $new_product['stock'], $new_product['description'], $new_product['image'], 0, 'active']
        );
        $success_msg = "Produk berhasil ditambahkan ke database!";
    } else {
        // Simpan ke session
        $new_product['id'] = time();
        array_unshift($_SESSION['products_katalog'], $new_product);
        $success_msg = "Produk berhasil ditambahkan (Mode Offline)!";
    }
}

// --- 4. LOGIKA FLASH SALE & HAPUS ---
if (isset($_GET['toggle_fs'])) {
    $product_id = $_GET['toggle_fs'];
    
    if ($db_available) {
        $product = getQueryRow("SELECT * FROM products WHERE id = ?", [$product_id]);
        if ($product) {
            $new_status = $product['is_flash_sale'] ? 0 : 1;
            executeQuery("UPDATE products SET is_flash_sale = ? WHERE id = ?", [$new_status, $product_id]);
        }
    } else {
        foreach ($_SESSION['products_katalog'] as &$p) {
            if ($p['id'] == $product_id) { $p['is_flash_sale'] = !($p['is_flash_sale'] ?? false); break; }
        }
    }
    header("Location: admin_dasboard.php"); exit();
}

if (isset($_GET['delete_id'])) {
    $product_id = $_GET['delete_id'];
    
    if ($db_available) {
        executeQuery("DELETE FROM products WHERE id = ?", [$product_id]);
    } else {
        foreach ($_SESSION['products_katalog'] as $key => $p) {
            if ($p['id'] == $product_id) { unset($_SESSION['products_katalog'][$key]); break; }
        }
        $_SESSION['products_katalog'] = array_values($_SESSION['products_katalog']);
    }
    header("Location: admin_dasboard.php"); exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_flash'])) {
    $_SESSION['fs_active'] = ($_POST['status'] == '1');
    $_SESSION['fs_end'] = $_POST['end_time'];
    $_SESSION['fs_discount'] = intval($_POST['discount']);
    $success_msg = "Flash Sale diperbarui!";
    
    // Update semua produk flash sale di database
    if ($db_available && $_SESSION['fs_active']) {
        executeQuery("UPDATE products SET is_flash_sale = 0", []);
    }
}

$katalog = $db_available ? $products_db : $_SESSION['products_katalog'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Admin - Berkah Mebel Ayu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-stone-50 flex">

    <aside class="w-64 min-h-screen bg-[#2D1B14] text-stone-300 p-6 fixed shadow-2xl">
        <div class="mb-10 text-white">
            <div class="font-bold text-xl uppercase tracking-widest">Admin Panel</div>
            <div class="text-xs text-amber-400 mt-1">
                <i class="fas fa-user-circle mr-1"></i> <?php echo $_SESSION['admin_name'] ?? 'Admin'; ?>
            </div>
            <div class="text-xs text-stone-500">
                <?php echo $db_available ? '<span class="text-green-400"><i class="fas fa-database"></i> Online</span>' : '<span class="text-orange-400"><i class="fas fa-exclamation-triangle"></i> Offline</span>'; ?>
            </div>
        </div>
        <nav class="space-y-4">
            <a href="../dashboard.php" target="_blank" class="block hover:text-amber-400"><i class="fas fa-eye mr-2"></i> Lihat Web</a>
            <a href="?logout=1" class="block text-red-400 hover:text-red-200 mt-10"><i class="fas fa-sign-out-alt mr-2"></i> Logout</a>
        </nav>
    </aside>

    <main class="flex-1 p-10 ml-64">
        <?php if(isset($success_msg)): ?>
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-xl border border-green-200"><i class="fas fa-check-circle mr-2"></i> <?php echo $success_msg; ?></div>
        <?php endif; ?>
        <?php if(isset($error_msg)): ?>
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-xl border border-red-200"><i class="fas fa-exclamation-circle mr-2"></i> <?php echo $error_msg; ?></div>
        <?php endif; ?>

        <div class="bg-white p-8 rounded-3xl shadow-sm mb-8">
            <h3 class="text-xl font-bold mb-4">Tambah Produk</h3>
            <form method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-6 gap-4">
                <input type="text" name="p_name" placeholder="Nama Produk" required class="bg-stone-100 p-3 rounded-xl col-span-2">
                <input type="number" name="p_price" placeholder="Harga" required class="bg-stone-100 p-3 rounded-xl">
                <input type="number" name="p_stock" placeholder="Stok" required class="bg-stone-100 p-3 rounded-xl" value="0">
                <select name="p_category" class="bg-stone-100 p-3 rounded-xl">
                    <option>Kursi</option><option>Meja</option><option>Lemari</option><option>Tempat Tidur</option><option>Rak</option><option>Sofa</option>
                </select>
                <button type="submit" name="add_product" class="bg-stone-800 text-white p-3 rounded-xl font-bold">Simpan</button>
                <div class="col-span-1 md:col-span-6">
                    <input type="file" name="p_image" class="text-sm">
                    <textarea name="p_description" placeholder="Deskripsi produk..." class="bg-stone-100 p-3 rounded-xl w-full mt-2" rows="2"></textarea>
                </div>
            </form>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-sm mb-8 border-l-4 border-red-600">
            <h3 class="text-xl font-bold mb-4">Pengaturan Flash Sale</h3>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <select name="status" class="bg-stone-100 p-3 rounded-xl">
                    <option value="1" <?php echo $_SESSION['fs_active'] ? 'selected' : ''; ?>>Aktif</option>
                    <option value="0" <?php echo !$_SESSION['fs_active'] ? 'selected' : ''; ?>>Nonaktif</option>
                </select>
                <input type="number" name="discount" value="<?php echo $_SESSION['fs_discount']; ?>" placeholder="Diskon %" class="bg-stone-100 p-3 rounded-xl">
                <input type="datetime-local" name="end_time" value="<?php echo date('Y-m-d\TH:i', strtotime($_SESSION['fs_end'])); ?>" class="bg-stone-100 p-3 rounded-xl">
                <button type="submit" name="update_flash" class="bg-red-600 text-white p-3 rounded-xl font-bold">Update Promo</button>
            </form>
        </div>

        <div class="bg-white rounded-3xl shadow-sm overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-stone-100 text-xs font-bold uppercase">
                    <tr>
                        <th class="p-4">Produk</th>
                        <th class="p-4">Harga</th>
                        <th class="p-4">Stok</th>
                        <th class="p-4 text-center">Flash Sale</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($katalog as $p): 
                        $is_fs = ($_SESSION['fs_active'] && ($p['is_flash_sale'] ?? false));
                    ?>
                    <tr class="border-b <?php echo $is_fs ? 'bg-red-50' : ''; ?>">
                        <td class="p-4 flex items-center gap-3">
                            <img src="<?php echo $p['image'] ?? 'https://placehold.co/50x50?text=No+Image'; ?>" class="w-10 h-10 rounded-lg object-cover">
                            <span class="font-bold"><?php echo htmlspecialchars($p['name']); ?></span>
                        </td>
                        <td class="p-4 font-bold">Rp <?php echo number_format($p['price'], 0, ',', '.'); ?></td>
                        <td class="p-4"><?php echo $p['stock'] ?? 0; ?></td>
                        <td class="p-4 text-center">
                            <a href="?toggle_fs=<?php echo $p['id']; ?>" class="p-2 rounded-full <?php echo ($p['is_flash_sale'] ?? false) ? 'bg-red-600 text-white' : 'bg-stone-200 text-stone-400'; ?>">
                                <i class="fas fa-bolt"></i>
                            </a>
                        </td>
                        <td class="p-4 text-center text-red-500">
                            <a href="?delete_id=<?php echo $p['id']; ?>" onclick="return confirm('Hapus produk ini?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>
