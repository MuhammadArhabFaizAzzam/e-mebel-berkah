<?php
session_start();
require_once 'db_config.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Handle stock update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'update_stock') {
        $product_id = intval($_POST['product_id']);
        $new_stock = intval($_POST['stock']);
        
        $query = "UPDATE products SET stock = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $new_stock, $product_id);
        
        if ($stmt->execute()) {
            $_SESSION['message'] = "Stock berhasil diperbarui!";
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = "Error mengupdate stock";
            $_SESSION['message_type'] = 'error';
        }
        header('Location: manage_stock.php');
        exit;
    }
}

// Get all products
$query = "SELECT id, name, price, stock, image FROM products ORDER BY name";
$result = $conn->query($query);
$products = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Stock - Berkah Mebel Ayu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body class="bg-[#F9F7F2]">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-[#2D1B14]/90 backdrop-blur-md border-b border-white/5">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between">
            <a href="dashboard.php" class="flex items-center gap-2 text-white font-bold">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
            <h1 class="text-2xl font-serif font-bold text-white">Kelola Stock Produk</h1>
            <a href="logout.php" class="text-gray-300 hover:text-white transition">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <!-- Alert Messages -->
        <?php if (isset($_SESSION['message'])): ?>
        <div class="mb-6 p-4 rounded-lg bg-<?php echo $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>-50 border border-<?php echo $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>-200">
            <p class="text-<?php echo $_SESSION['message_type'] === 'success' ? 'green' : 'red'; ?>-800">
                <i class="fas fa-<?php echo $_SESSION['message_type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?> mr-2"></i>
                <?php echo $_SESSION['message']; ?>
            </p>
        </div>
        <?php 
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        endif; 
        ?>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($products as $product): ?>
            <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all">
                <!-- Image -->
                <div class="relative h-48 bg-gray-200 overflow-hidden">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="w-full h-full object-cover">
                    <!-- Stock Badge -->
                    <div class="absolute top-4 right-4">
                        <?php if ($product['stock'] > 0): ?>
                            <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-bold flex items-center gap-1">
                                <i class="fas fa-check-circle"></i> <?php echo $product['stock']; ?> Unit
                            </span>
                        <?php else: ?>
                            <span class="bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold flex items-center gap-1">
                                <i class="fas fa-times-circle"></i> Habis
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Info -->
                <div class="p-6">
                    <h3 class="font-bold text-lg text-[#2D1B14] mb-2">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h3>
                    <p class="text-[#A67C52] text-lg font-bold mb-6">
                        Rp <?php echo number_format($product['price'], 0, ',', '.'); ?>
                    </p>

                    <!-- Stock Form -->
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="update_stock">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Stock Baru:</label>
                            <input type="number" name="stock" value="<?php echo $product['stock']; ?>" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#D4A373]" required>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-[#2D1B14] text-white px-4 py-2 rounded-lg font-bold hover:bg-[#A67C52] transition flex items-center justify-center gap-2">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                            <button type="button" onclick="quickAdd(this, <?php echo $product['id']; ?>)" class="px-4 py-2 bg-blue-500 text-white rounded-lg font-bold hover:bg-blue-600 transition" title="Tambah 5 unit">
                                <i class="fas fa-plus"></i> +5
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- If No Products -->
        <?php if (empty($products)): ?>
        <div class="text-center py-12">
            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
            <p class="text-gray-600 text-lg">Tidak ada produk untuk dikelola</p>
        </div>
        <?php endif; ?>
    </div>

    <script>
        function quickAdd(button, productId) {
            const form = button.closest('form');
            const stockInput = form.querySelector('input[name="stock"]');
            const currentStock = parseInt(stockInput.value);
            stockInput.value = currentStock + 5;
            form.submit();
        }
    </script>
</body>
</html>
