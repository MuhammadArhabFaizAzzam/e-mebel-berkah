<?php
session_start();

// Include database connection
require_once '../db_config.php';

// Cek apakah database tersedia
$db_available = isDatabaseConnected();

// Proses Add Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product') {
    
    // Cek login admin
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        $_SESSION['msg'] = "Anda harus login sebagai admin!";
        header("Location: admin_dasboard.php");
        exit;
    }
    
    $name = $_POST['name'] ?? '';
    $price = intval($_POST['price'] ?? 0);
    $stock = intval($_POST['stock'] ?? 0);
    $category = $_POST['category'] ?? 'Kursi';
    $description = $_POST['description'] ?? '';
    
    // Validasi input
    if (empty($name) || $price <= 0) {
        $_SESSION['msg'] = "Nama produk dan harga harus diisi dengan benar!";
        header("Location: admin_dasboard.php");
        exit;
    }
    
    // Handle image upload
    $image_url = 'https://placehold.co/300x300?text=' . urlencode($name);
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $file_type = $_FILES['image']['type'];
        
        if (in_array($file_type, $allowed_types)) {
            $target_dir = "../uploads/";
            if (!file_exists($target_dir)) { 
                mkdir($target_dir, 0777, true); 
            }
            $file_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.]/", "_", basename($_FILES["image"]["name"])); 
            $target_file = $target_dir . $file_name;
            
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = $target_file;
            }
        }
    }
    
    if ($db_available) {
        // Simpan ke database
        try {
            executeQuery(
                "INSERT INTO products (name, price, stock, category, description, image, is_flash_sale, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [$name, $price, $stock, $category, $description, $image_url, 0, 'active']
            );
            $_SESSION['msg'] = "Produk '$name' berhasil ditambahkan ke database!";
        } catch (Exception $e) {
            $_SESSION['msg'] = "Error: " . $e->getMessage();
        }
    } else {
        // Simpan ke session (fallback)
        if (!isset($_SESSION['products_katalog'])) {
            $_SESSION['products_katalog'] = [];
        }
        
        $new_product = [
            'id' => time(),
            'name' => htmlspecialchars($name),
            'price' => $price,
            'stock' => $stock,
            'category' => $category,
            'description' => $description,
            'image' => $image_url,
            'is_flash_sale' => false
        ];
        
        array_unshift($_SESSION['products_katalog'], $new_product);
        $_SESSION['msg'] = "Produk '$name' berhasil ditambahkan (Mode Offline)!";
    }
    
    header("Location: admin_dasboard.php");
    exit;
}

// Proses Delete Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_product') {
    
    // Cek login admin
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        $_SESSION['msg'] = "Anda harus login sebagai admin!";
        header("Location: admin_dasboard.php");
        exit;
    }
    
    $product_id = intval($_POST['product_id'] ?? 0);
    
    if ($product_id > 0) {
        if ($db_available) {
            try {
                executeQuery("DELETE FROM products WHERE id = ?", [$product_id]);
                $_SESSION['msg'] = "Produk berhasil dihapus dari database!";
            } catch (Exception $e) {
                $_SESSION['msg'] = "Error: " . $e->getMessage();
            }
        } else {
            // Fallback ke session
            if (isset($_SESSION['products_katalog'])) {
                foreach ($_SESSION['products_katalog'] as $key => $p) {
                    if ($p['id'] == $product_id) {
                        unset($_SESSION['products_katalog'][$key]);
                        break;
                    }
                }
                $_SESSION['products_katalog'] = array_values($_SESSION['products_katalog']);
                $_SESSION['msg'] = "Produk berhasil dihapus (Mode Offline)!";
            }
        }
    }
    
    header("Location: admin_dasboard.php");
    exit;
}

// Proses Update Stock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_stock') {
    
    // Cek login admin
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        $_SESSION['msg'] = "Anda harus login sebagai admin!";
        header("Location: admin_dasboard.php");
        exit;
    }
    
    $product_id = intval($_POST['product_id'] ?? 0);
    $new_stock = intval($_POST['stock'] ?? 0);
    
    if ($product_id > 0) {
        if ($db_available) {
            try {
                executeQuery("UPDATE products SET stock = ? WHERE id = ?", [$new_stock, $product_id]);
                $_SESSION['msg'] = "Stok berhasil diperbarui di database!";
            } catch (Exception $e) {
                $_SESSION['msg'] = "Error: " . $e->getMessage();
            }
        } else {
            // Fallback ke session
            if (isset($_SESSION['products_katalog'])) {
                foreach ($_SESSION['products_katalog'] as &$p) {
                    if ($p['id'] == $product_id) {
                        $p['stock'] = $new_stock;
                        break;
                    }
                }
                $_SESSION['msg'] = "Stok berhasil diperbarui (Mode Offline)!";
            }
        }
    }
    
    header("Location: admin_dasboard.php");
    exit;
}

// Proses Toggle Flash Sale
if (isset($_GET['toggle_fs'])) {
    
    // Cek login admin
    if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
        $_SESSION['msg'] = "Anda harus login sebagai admin!";
        header("Location: admin_dasboard.php");
        exit;
    }
    
    $product_id = intval($_GET['toggle_fs']);
    
    if ($product_id > 0) {
        if ($db_available) {
            try {
                $product = getQueryRow("SELECT is_flash_sale FROM products WHERE id = ?", [$product_id]);
                if ($product) {
                    $new_status = $product['is_flash_sale'] ? 0 : 1;
                    executeQuery("UPDATE products SET is_flash_sale = ? WHERE id = ?", [$new_status, $product_id]);
                    $_SESSION['msg'] = "Status flash sale diperbarui!";
                }
            } catch (Exception $e) {
                $_SESSION['msg'] = "Error: " . $e->getMessage();
            }
        } else {
            // Fallback ke session
            if (isset($_SESSION['products_katalog'])) {
                foreach ($_SESSION['products_katalog'] as &$p) {
                    if ($p['id'] == $product_id) {
                        $p['is_flash_sale'] = !($p['is_flash_sale'] ?? false);
                        break;
                    }
                }
                $_SESSION['msg'] = "Status flash sale diperbarui (Mode Offline)!";
            }
        }
    }
    
    header("Location: admin_dasboard.php");
    exit;
}

// Jika tidak ada action, redirect ke admin dashboard
header("Location: admin_dasboard.php");
exit;
?>
