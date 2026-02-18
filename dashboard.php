<?php
session_start();

// --- 1. SINKRONISASI DATA DARI ADMIN ---
// Mengambil status Flash Sale dari Session
$fs_active = isset($_SESSION['fs_active']) ? $_SESSION['fs_active'] : false;
$fs_end = isset($_SESSION['fs_end']) ? $_SESSION['fs_end'] : date('Y-m-d H:i:s');
$fs_discount_pct = isset($_SESSION['fs_discount']) ? $_SESSION['fs_discount'] : 0;

// Cek otomatis apakah waktu promo sudah habis
if (time() > strtotime($fs_end)) {
    $fs_active = false;
}

// MENGAMBIL DATA PRODUK DARI INPUTAN ADMIN
// Jika admin belum input apa pun, kita sediakan data kosong atau default
if (isset($_SESSION['products_katalog']) && !empty($_SESSION['products_katalog'])) {
    $products_db = $_SESSION['products_katalog'];
} else {
    // Data dummy awal jika katalog masih kosong
    $products_db = [
        ['id' => 1, 'name' => 'Kursi Makan Jati', 'price' => 450000, 'category' => 'Kursi', 'image' => 'https://images.unsplash.com/photo-1503602642458-232111445657?q=80&w=300&h=300&fit=crop'],
        ['id' => 2, 'name' => 'Meja Minimalis', 'price' => 1500000, 'category' => 'Meja', 'image' => 'https://images.unsplash.com/photo-1530018607912-eff2df114f11?q=80&w=300&h=300&fit=crop']
    ];
}
// --- 2. LOGIKA PROSES HARGA (VERSI UPDATE) ---
$products_display = [];
foreach ($products_db as $p) {
    // Produk ikut diskon CUMA kalau Flash Sale aktif DAN produk itu ditandai Admin
    $is_product_promo = ($fs_active && isset($p['is_flash_sale']) && $p['is_flash_sale']);
    
    if ($is_product_promo && $fs_discount_pct > 0) {
        $discount_amount = $p['price'] * ($fs_discount_pct / 100);
        $final_price = $p['price'] - $discount_amount;
    } else {
        $final_price = $p['price'];
    }

    $products_display[] = [
        'id' => $p['id'],
        'name' => $p['name'],
        'image' => $p['image'],
        'category' => $p['category'],
        'price_original' => $p['price'],
        'price_final' => $final_price,
        'is_flash_sale' => $is_product_promo,
        'discount_pct' => $fs_discount_pct
    ];
}
// Filter Kategori
$categories = ['Semua', 'Kursi', 'Meja', 'Lemari', 'Tempat Tidur'];
$selected_category = $_GET['category'] ?? 'Semua';

// Nomor WhatsApp Admin
$wa_number = "6282325274909"; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - Berkah Mebel Ayu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Pro:wght@700&family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Quicksand', sans-serif; background: #F9F7F4; }
        .font-artisan { font-family: 'Crimson Pro', serif; }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        
        .flash-sale-container {
            background: linear-gradient(rgba(45, 27, 20, 0.9), rgba(45, 27, 20, 0.9)), url('https://www.transparenttextures.com/patterns/carbon-fibre.png');
            border: 2px solid #ef4444;
            animation: glow 3s infinite alternate;
        }
        @keyframes glow { from { box-shadow: 0 0 10px rgba(239, 68, 68, 0.2); } to { box-shadow: 0 0 20px rgba(239, 68, 68, 0.6); } }
    </style>
</head>
<body>

<nav class="bg-[#2D1B14] text-white p-4 sticky top-0 z-50 shadow-lg">
    <div class="container mx-auto flex justify-between items-center">
        <a href="dashboard.php" class="flex items-center gap-3">
            <i class="fas fa-tree text-amber-500 text-2xl"></i>
            <div>
                <h1 class="font-artisan text-xl font-bold tracking-wider">BERKAH MEBEL AYU</h1>
                <p class="text-[10px] text-amber-500/80 uppercase tracking-widest">Kualitas Jati Asli</p>
            </div>
        </a>
        <div class="hidden md:flex items-center gap-2 opacity-80">
            <i class="fab fa-whatsapp text-green-400 text-lg"></i>
            <span class="text-sm">0823-2527-4909</span>
        </div>
    </div>
</nav>

<div class="container mx-auto px-4 py-8">

    <div class="mb-10 text-center md:text-left">
        <h2 class="font-artisan text-4xl text-[#2D1B14] font-bold">Koleksi Produk Baru</h2>
        <p class="text-stone-500 mt-2">Daftar furniture terbaru yang baru saja ditambahkan oleh Admin.</p>
    </div>

    <?php if ($fs_active): ?>
    <div class="flash-sale-container rounded-[2rem] p-6 md:p-10 mb-12 flex flex-col md:flex-row items-center justify-between text-white">
        <div class="flex items-center gap-6">
            <div class="bg-red-600 p-4 rounded-2xl animate-pulse">
                <i class="fas fa-bolt text-4xl text-yellow-300"></i>
            </div>
            <div>
                <h3 class="font-artisan text-3xl font-bold">Flash Sale <span class="text-yellow-400">Aktif!</span></h3>
                <p class="text-stone-300 text-sm uppercase tracking-widest mt-1">
                    Diskon Masal: <span class="bg-white text-red-600 px-2 rounded font-bold"><?php echo $fs_discount_pct; ?>%</span>
                </p>
            </div>
        </div>
        <div id="countdown" class="flex gap-2 mt-6 md:mt-0"></div>
    </div>

    <script>
        const countDownDate = new Date("<?php echo $fs_end; ?>").getTime();
        const x = setInterval(function() {
            const now = new Date().getTime();
            const distance = countDownDate - now;
            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            document.getElementById("countdown").innerHTML = `
                <div class="bg-white/10 p-3 rounded-lg w-16 text-center"><div class="text-xl font-bold">${hours}</div><div class="text-[8px]">JAM</div></div>
                <div class="bg-white/10 p-3 rounded-lg w-16 text-center"><div class="text-xl font-bold">${minutes}</div><div class="text-[8px]">MENIT</div></div>
                <div class="bg-red-600 p-3 rounded-lg w-16 text-center animate-bounce"><div class="text-xl font-bold">${seconds}</div><div class="text-[8px]">DETIK</div></div>
            `;
            if (distance < 0) { clearInterval(x); window.location.reload(); }
        }, 1000);
    </script>
    <?php endif; ?>

    <div class="flex gap-3 overflow-x-auto pb-6 mb-8 no-scrollbar">
        <?php foreach ($categories as $cat): ?>
            <a href="?category=<?php echo $cat; ?>" class="px-6 py-2 rounded-full border border-[#2D1B14] font-bold text-sm whitespace-nowrap transition-all <?php echo $selected_category == $cat ? 'bg-[#2D1B14] text-white shadow-lg' : 'text-[#2D1B14] hover:bg-[#2D1B14]/10'; ?>">
                <?php echo $cat; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
        <?php foreach ($products_display as $p): 
            if ($selected_category !== 'Semua' && $p['category'] !== $selected_category) continue;

            $harga_format = number_format($p['price_final'], 0, ',', '.');
            $teks_wa = "Halo Berkah Mebel Ayu, saya tertarik dengan produk *{$p['name']}*. \n\nHarga: Rp {$harga_format} \n\nApakah stok masih tersedia?";
            $link_wa = "https://wa.me/{$wa_number}?text=" . urlencode($teks_wa);
        ?>
        <div class="group bg-white rounded-3xl overflow-hidden shadow-sm hover:shadow-2xl transition-all border border-stone-100 <?php echo $p['is_flash_sale'] ? 'ring-2 ring-red-500' : ''; ?>">
            <div class="relative h-64 bg-stone-100 overflow-hidden">
                <img src="<?php echo $p['image']; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                <?php if ($p['is_flash_sale']): ?>
                    <div class="absolute top-0 right-0 bg-red-600 text-white px-3 py-1 rounded-bl-xl font-bold text-xs">
                        <i class="fas fa-bolt mr-1 text-yellow-300"></i> -<?php echo $p['discount_pct']; ?>%
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="p-6">
                <span class="text-[10px] uppercase tracking-widest text-stone-400 font-bold"><?php echo $p['category']; ?></span>
                <h3 class="font-bold text-stone-800 text-lg mb-2 leading-tight min-h-[50px]"><?php echo $p['name']; ?></h3>
                
                <div class="flex items-center gap-2 mb-6">
                    <span class="text-xl font-black text-[#2D1B14]">Rp <?php echo $harga_format; ?></span>
                    <?php if ($p['is_flash_sale']): ?>
                        <span class="text-xs text-stone-400 line-through">Rp <?php echo number_format($p['price_original'], 0, ',', '.'); ?></span>
                    <?php endif; ?>
                </div>

                <a href="<?php echo $link_wa; ?>" target="_blank" class="block w-full py-3 rounded-xl font-bold text-sm text-center transition-all <?php echo $p['is_flash_sale'] ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'; ?> text-white shadow-lg shadow-stone-100">
                    <i class="fab fa-whatsapp text-lg mr-2"></i> Pesan Sekarang
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-20 border-t border-stone-200 pt-8 text-center text-stone-400 text-sm">
        <p>&copy; <?php echo date('Y'); ?> Berkah Mebel Ayu. Data disinkronkan dari Admin Panel.</p>
    </div>

</div>

</body>
</html>