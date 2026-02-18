<?php
session_start();

// Setup Dummy Data jika belum ada (agar tidak error saat testing)
if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

// HANDLE REQUEST POST (Tambah/Kurang/Hapus)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;

    if ($action === 'add_to_cart' && $id > 0) {
        if (!isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] = 0;
        $_SESSION['cart'][$id]++;
        header('Location: cart.php'); // Refresh halaman
        exit;
    }
    if ($action === 'remove_item' && $id > 0) {
        unset($_SESSION['cart'][$id]);
    }
    if ($action === 'update_qty' && $id > 0) {
        $qty = intval($_POST['qty']);
        if ($qty <= 0) unset($_SESSION['cart'][$id]);
        else $_SESSION['cart'][$id] = $qty;
    }
}

// --- LOGIKA HARGA DINAMIS DARI SESSION ADMIN ---
$fs_active = isset($_SESSION['fs_active']) ? $_SESSION['fs_active'] : false;
$fs_end = isset($_SESSION['fs_end']) ? $_SESSION['fs_end'] : date('Y-m-d H:i:s');
$fs_discount_pct = isset($_SESSION['fs_discount']) ? $_SESSION['fs_discount'] : 0;

// Cek Waktu
if (time() > strtotime($fs_end)) {
    $fs_active = false;
}

// Data Produk Master (Sama dengan Dashboard)
$products_lookup = [
    1 => ['id' => 1, 'name' => 'Kursi Makan Kayu Jati', 'price' => 450000, 'image' => 'img/unnamed.jpg'],
    2 => ['id' => 2, 'name' => 'Meja Makan Minimalis', 'price' => 750000, 'image' => 'img/unnamed (1).jpg'],
    3 => ['id' => 3, 'name' => 'Lemari Pakaian Besar', 'price' => 850000, 'image' => 'img/unnamed (2).jpg'],
    4 => ['id' => 4, 'name' => 'Tempat Tidur King Size', 'price' => 1200000, 'image' => 'img/2024-01-02.jpg'],
    5 => ['id' => 5, 'name' => 'Rak Buku 5 Tingkat', 'price' => 320000, 'image' => 'img/2025-05-15.jpg'],
    6 => ['id' => 6, 'name' => 'Sofa Kulit Premium', 'price' => 950000, 'image' => 'img/2025-09-09.jpg'],
];

$cart_items = [];
$total_bayar = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
    if (isset($products_lookup[$id])) {
        $p = $products_lookup[$id];
        
        // HITUNG HARGA DISKON SECARA REALTIME
        if ($fs_active && $fs_discount_pct > 0) {
            $final_price = $p['price'] - ($p['price'] * ($fs_discount_pct / 100));
            $is_promo = true;
        } else {
            $final_price = $p['price'];
            $is_promo = false;
        }

        $subtotal = $final_price * $qty;
        $total_bayar += $subtotal;

        $cart_items[] = [
            'id' => $id,
            'name' => $p['name'],
            'image' => $p['image'],
            'original_price' => $p['price'],
            'final_price' => $final_price,
            'qty' => $qty,
            'subtotal' => $subtotal,
            'is_promo' => $is_promo,
            'discount_pct' => $fs_discount_pct
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Keranjang Belanja</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@700&family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Quicksand', sans-serif; background: #FDFCFB; } .font-artisan { font-family: 'Crimson Pro', serif; } </style>
</head>
<body>

<nav class="bg-[#3E2723] p-4 text-white sticky top-0 z-50">
    <div class="container mx-auto flex justify-between items-center">
        <a href="dashboard.php" class="font-artisan text-xl">Berkah Mebel Ayu</a>
        <a href="dashboard.php" class="text-sm font-bold opacity-70 hover:opacity-100"><i class="fas fa-arrow-left mr-2"></i>Kembali Belanja</a>
    </div>
</nav>

<div class="container mx-auto px-4 py-10">
    <h1 class="font-artisan text-3xl font-bold mb-8">Keranjang <span class="text-amber-700">Belanja</span></h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-4">
            <?php if(empty($cart_items)): ?>
                <div class="p-10 text-center border-2 border-dashed rounded-3xl text-stone-400 font-bold">
                    Keranjang Kosong
                </div>
            <?php else: ?>
                <?php foreach ($cart_items as $item): ?>
                <div class="bg-white rounded-3xl p-5 flex flex-col md:flex-row items-center gap-6 border border-stone-100 shadow-sm <?php echo $item['is_promo'] ? 'ring-1 ring-red-200' : ''; ?>">
                    <div class="w-24 h-24 rounded-2xl overflow-hidden bg-stone-100 relative shrink-0">
                        <img src="<?php echo $item['image']; ?>" class="w-full h-full object-cover">
                        <?php if($item['is_promo']): ?>
                            <div class="absolute bottom-0 w-full bg-red-600 text-white text-[8px] text-center font-bold py-0.5">DISKON <?php echo $item['discount_pct']; ?>%</div>
                        <?php endif; ?>
                    </div>

                    <div class="flex-1 text-center md:text-left">
                        <h3 class="font-bold text-stone-800 text-lg"><?php echo $item['name']; ?></h3>
                        <div class="flex flex-col md:flex-row items-center gap-2 mt-1">
                            <span class="text-[#3E2723] font-black">Rp <?php echo number_format($item['final_price'], 0, ',', '.'); ?></span>
                            <?php if($item['is_promo']): ?>
                                <span class="text-stone-300 line-through text-xs">Rp <?php echo number_format($item['original_price'], 0, ',', '.'); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        <form method="POST">
                            <input type="hidden" name="action" value="update_qty">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <input type="hidden" name="qty" value="<?php echo $item['qty'] - 1; ?>">
                            <button class="w-8 h-8 bg-stone-100 rounded hover:bg-stone-200">-</button>
                        </form>
                        <span class="font-bold w-8 text-center"><?php echo $item['qty']; ?></span>
                        <form method="POST">
                            <input type="hidden" name="action" value="update_qty">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <input type="hidden" name="qty" value="<?php echo $item['qty'] + 1; ?>">
                            <button class="w-8 h-8 bg-stone-100 rounded hover:bg-stone-200">+</button>
                        </form>
                    </div>

                    <div class="text-right min-w-[100px]">
                        <p class="text-[10px] text-stone-400 font-bold uppercase">Subtotal</p>
                        <p class="font-black text-stone-800">Rp <?php echo number_format($item['subtotal'], 0, ',', '.'); ?></p>
                        <form method="POST" class="mt-1">
                            <input type="hidden" name="action" value="remove_item">
                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                            <button class="text-[10px] text-red-500 font-bold hover:underline">HAPUS</button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-[2.5rem] p-8 border border-stone-100 shadow-xl sticky top-24">
                <h2 class="font-artisan text-2xl mb-6">Ringkasan</h2>
                <div class="flex justify-between items-center mb-6 pb-6 border-b border-stone-100">
                    <span class="text-stone-500 font-bold">Total Bayar</span>
                    <span class="text-3xl font-black text-[#3E2723]">Rp <?php echo number_format($total_bayar, 0, ',', '.'); ?></span>
                </div>
                <button class="w-full bg-[#3E2723] text-white py-4 rounded-2xl font-bold shadow-lg hover:bg-black transition-all">
                    CHECKOUT SEKARANG
                </button>
            </div>
        </div>
    </div>
</div>

</body>
</html>