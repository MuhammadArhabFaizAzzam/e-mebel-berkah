<?php
session_start();

// Include database connection
require_once '../db_config.php';

// Cek login terlebih dahulu
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    $_SESSION['error_msg'] = "Anda harus login sebagai admin!";
    header("Location: admin_dashboard.php");
    exit;
}

$db_available = isDatabaseConnected();

// --- HANDLE ADD PRODUCT ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product') {
    
    $name = trim($_POST['p_name'] ?? '');
    $price = intval($_POST['p_price'] ?? 0);
    $stock = intval($_POST['p_stock'] ?? 0);
    $category = $_POST['p_category'] ?? 'Kursi';
    $description = htmlspecialchars($_POST['p_description'] ?? '');
    
    // Validasi
    if (empty($name) || $price <= 0) {
        $_SESSION['error_msg'] = "Nama produk dan harga harus diisi dengan benar!";
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
                $_SESSION['error_msg'] = "Format file tidak didukung! Gunakan JPG/PNG/WebP.";
            }
        }
        
        if (!isset($_SESSION['error_msg'])) {
            if ($db_available) {
                try {
                    executeQuery(
                        "INSERT INTO products (name, price, stock, category, description, image, is_flash_sale, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                        [$name, $price, $stock, $category, $description, $image_url, 0, 'active']
                    );
                    $_SESSION['success_msg'] = "Produk '$name' berhasil ditambahkan!";
                } catch (Exception $e) {
                    $_SESSION['error_msg'] = "Error Database: " . $e->getMessage();
                }
            } else {
                // Simpan ke session
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
                $_SESSION['success_msg'] = "Produk '$name' berhasil ditambahkan (Mode Offline)!";
            }
        }
    }
    
    header("Location: admin_dashboard.php");
    exit;
}

// --- HANDLE DELETE PRODUCT ---
if (isset($_GET['delete_id'])) {
    $product_id = intval($_GET['delete_id']);
    
    if ($product_id > 0) {
        if ($db_available) {
            try {
                executeQuery("DELETE FROM products WHERE id = ?", [$product_id]);
                $_SESSION['success_msg'] = "Produk berhasil dihapus!";
            } catch (Exception $e) {
                $_SESSION['error_msg'] = "Error: " . $e->getMessage();
            }
        } else {
            // Hapus dari session
            if (isset($_SESSION['products_katalog'])) {
                foreach ($_SESSION['products_katalog'] as $key => $p) {
                    if ($p['id'] == $product_id) {
                        unset($_SESSION['products_katalog'][$key]);
                        break;
                    }
                }
                $_SESSION['products_katalog'] = array_values($_SESSION['products_katalog']);
                $_SESSION['success_msg'] = "Produk berhasil dihapus (Mode Offline)!";
            }
        }
    }
    
    header("Location: admin_dashboard.php");
    exit;
}

// --- HANDLE TOGGLE FLASH SALE ---
if (isset($_GET['toggle_fs'])) {
    $product_id = intval($_GET['toggle_fs']);
    
    if ($product_id > 0) {
        if ($db_available) {
            try {
                $product = getQueryRow("SELECT is_flash_sale FROM products WHERE id = ?", [$product_id]);
                if ($product) {
                    $new_status = $product['is_flash_sale'] ? 0 : 1;
                    executeQuery("UPDATE products SET is_flash_sale = ? WHERE id = ?", [$new_status, $product_id]);
                    $_SESSION['success_msg'] = "Status flash sale diperbarui!";
                }
            } catch (Exception $e) {
                $_SESSION['error_msg'] = "Error: " . $e->getMessage();
            }
        } else {
            // Toggle di session
            if (isset($_SESSION['products_katalog'])) {
                foreach ($_SESSION['products_katalog'] as &$p) {
                    if ($p['id'] == $product_id) {
                        $p['is_flash_sale'] = !($p['is_flash_sale'] ?? false);
                        break;
                    }
                }
                $_SESSION['success_msg'] = "Status flash sale diperbarui (Mode Offline)!";
            }
        }
    }
    
    header("Location: admin_dashboard.php");
    exit;
}

// --- HANDLE UPDATE STOCK ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_stock') {
    
    $product_id = intval($_POST['product_id'] ?? 0);
    $new_stock = intval($_POST['stock'] ?? 0);
    
    if ($product_id > 0) {
        if ($db_available) {
            try {
                executeQuery("UPDATE products SET stock = ? WHERE id = ?", [$new_stock, $product_id]);
                $_SESSION['success_msg'] = "Stok berhasil diperbarui!";
            } catch (Exception $e) {
                $_SESSION['error_msg'] = "Error: " . $e->getMessage();
            }
        } else {
            // Update di session
            if (isset($_SESSION['products_katalog'])) {
                foreach ($_SESSION['products_katalog'] as &$p) {
                    if ($p['id'] == $product_id) {
                        $p['stock'] = $new_stock;
                        break;
                    }
                }
                $_SESSION['success_msg'] = "Stok berhasil diperbarui (Mode Offline)!";
            }
        }
    }
    
    header("Location: admin_dashboard.php");
    exit;
}

// --- HANDLE UPDATE FLASH SALE SETTINGS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_flash') {
    $_SESSION['fs_active'] = ($_POST['fs_status'] == '1');
    $_SESSION['fs_end'] = $_POST['fs_end_time'] ?? date('Y-m-d H:i', strtotime('+1 day'));
    $_SESSION['fs_discount'] = intval($_POST['fs_discount'] ?? 0);
    $_SESSION['success_msg'] = "Pengaturan Flash Sale berhasil diperbarui!";
    
    header("Location: admin_dashboard.php");
    exit;
}

// Default redirect jika tidak ada action
header("Location: admin_dashboard.php");
exit;
?>
