<?php
ob_start(); 

// Pengecekan sesi agar tidak muncul 'Notice' karena double start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Pengaturan Error Reporting (Nonaktifkan di production)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Mencegah script mati jika proses database lama
set_time_limit(0);

// Load Konfigurasi Database
if (file_exists('db_config.php')) {
    require_once 'db_config.php';
} else {
    // Jika config tidak ada, lanjutkan tanpa database untuk demo
    $conn = null;
}

// --- LANJUTKAN LOGIKA DASHBOARD/KODE KAMU DI SINI ---
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berkah Mebel Ayu - Furniture Kayu Premium</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        * { box-sizing: border-box; }
        .font-serif { font-family: 'Playfair Display', serif; }
    </style>
</head>
<body class="bg-wood-light text-gray-800">

    <nav id="navbar" class="fixed top-0 w-full z-50 bg-[#2D1B14]/90 backdrop-blur-md border-b border-white/5 transition-all duration-300 ease-in-out">
        <div id="nav-container" class="container mx-auto px-4 py-3 md:py-4 transition-all duration-300">
            <div class="flex items-center justify-between">
                <a href="index.php" class="flex items-center gap-3 group transition-transform hover:scale-105">
                    <div class="relative">
                        <div class="absolute inset-0 bg-[#D4A373] rounded-xl blur-lg opacity-20 group-hover:opacity-40 transition-opacity"></div>
                        <img src="img/Screenshot 2025-10-22 083935.png" alt="Logo" class="h-10 md:h-12 w-auto relative z-10 rounded-lg shadow-lg border border-white/10">
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="font-serif text-xl md:text-2xl font-bold text-white tracking-tight leading-none">Berkah Mebel Ayu</h1>
                        <p class="text-[#D4A373] text-[9px] uppercase tracking-[0.2em] font-bold mt-1">Artisan Furniture</p>
                    </div>
                </a>

                <div class="hidden lg:flex items-center gap-10">
                    <a href="#home" class="relative text-gray-300 hover:text-white transition-all text-sm font-medium group py-2">Beranda<span class="absolute bottom-0 left-1/2 w-0 h-0.5 bg-[#D4A373] transition-all duration-300 group-hover:w-full group-hover:left-0"></span></a>
                    <a href="#produk" class="relative text-gray-300 hover:text-white transition-all text-sm font-medium group py-2">Produk<span class="absolute bottom-0 left-1/2 w-0 h-0.5 bg-[#D4A373] transition-all duration-300 group-hover:w-full group-hover:left-0"></span></a>
                    <a href="#tentang" class="relative text-gray-300 hover:text-white transition-all text-sm font-medium group py-2">Tentang<span class="absolute bottom-0 left-1/2 w-0 h-0.5 bg-[#D4A373] transition-all duration-300 group-hover:w-full group-hover:left-0"></span></a>
                    <a href="#kontak" class="relative text-gray-300 hover:text-white transition-all text-sm font-medium group py-2">Kontak<span class="absolute bottom-0 left-1/2 w-0 h-0.5 bg-[#D4A373] transition-all duration-300 group-hover:w-full group-hover:left-0"></span></a>
                </div>

                <div class="flex items-center gap-3 md:gap-6">
    <a href="login.php" class="hidden sm:flex items-center gap-2 text-gray-300 hover:text-[#D4A373] text-sm font-semibold transition-all group">
        <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center group-hover:bg-[#D4A373]/10 transition-colors">
            <i class="fas fa-sign-in-alt"></i>
        </div>
        <span>Masuk</span>
    </a>

    <a href="register.php" class="group relative bg-[#D4A373] text-[#2D1B14] px-5 py-2.5 rounded-xl text-sm font-bold overflow-hidden transition-all hover:shadow-[0_0_20px_rgba(212,163,115,0.4)] active:scale-95 flex items-center gap-2">
        <div class="relative z-10 flex items-center gap-2">
            <i class="fas fa-user-plus text-xs"></i>
            <span>Daftar</span>
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
        <div class="absolute inset-0 bg-white opacity-0 group-hover:opacity-10 transition-opacity"></div>
    </a>

    <button id="mobile-menu-btn" class="lg:hidden text-white p-2 hover:bg-white/5 rounded-lg transition-colors focus:outline-none">
        <i id="menu-icon" class="fas fa-bars text-2xl"></i>
    </button>
</div>
            </div>
        </div>
        <div id="mobile-menu" class="hidden lg:hidden bg-[#2D1B14] border-t border-white/10 w-full p-6 space-y-5 shadow-2xl">
            <a href="#home" class="flex items-center justify-between text-gray-300 hover:text-[#D4A373] text-lg font-medium group">Beranda <i class="fas fa-chevron-right text-xs opacity-0 group-hover:opacity-100 transition-all"></i></a>
            <a href="#produk" class="flex items-center justify-between text-gray-300 hover:text-[#D4A373] text-lg font-medium group">Produk <i class="fas fa-chevron-right text-xs opacity-0 group-hover:opacity-100 transition-all"></i></a>
            <a href="#tentang" class="flex items-center justify-between text-gray-300 hover:text-[#D4A373] text-lg font-medium group">Tentang <i class="fas fa-chevron-right text-xs opacity-0 group-hover:opacity-100 transition-all"></i></a>
            <a href="#kontak" class="flex items-center justify-between text-gray-300 hover:text-[#D4A373] text-lg font-medium group">Kontak <i class="fas fa-chevron-right text-xs opacity-0 group-hover:opacity-100 transition-all"></i></a>
            <div class="pt-4 border-t border-white/5">
                <a href="login.php" class="block w-full text-center py-3 rounded-xl border border-[#D4A373] text-[#D4A373] font-bold hover:bg-[#D4A373] hover:text-[#2D1B14] transition-all">Masuk ke Akun</a>
            </div>
        </div>
    </nav>

    <section id="home" class="relative min-h-[90vh] flex items-center overflow-hidden bg-[#2D1B14]">
        <div class="absolute inset-0 opacity-20">
            <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?auto=format&fit=crop&q=80" alt="Wood Texture" class="w-full h-full object-cover">
        </div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#2D1B14] via-[#2D1B14]/80 to-transparent"></div>
        <div class="container mx-auto px-4 py-20 relative z-20">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16 items-center">
                <div class="space-y-8">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-md">
                        <span class="flex h-2 w-2 rounded-full bg-green-400 animate-pulse"></span>
                        <span class="text-xs font-medium tracking-widest uppercase text-gray-300">Sustainable Craftsmanship</span>
                    </div>
                    <h1 class="font-serif text-5xl md:text-7xl font-bold leading-[1.1] text-white">
                        <span class="text-[#D4A373]">Keindahan</span> Alam <br>
                        Dalam Setiap <span class="relative">
                            <span class="relative z-10 text-[#E9EDC9]">Serat Kayu</span>
                            <svg class="absolute -bottom-2 left-0 w-full h-3 text-[#D4A373]/30 -z-0" viewBox="0 0 100 10" preserveAspectRatio="none"><path d="M0 5 Q 25 0 50 5 T 100 5" stroke="currentColor" stroke-width="2" fill="none"/></svg>
                        </span>
                    </h1>
                    <p class="max-w-lg text-lg md:text-xl text-gray-400 leading-relaxed font-light">
                        Membawa kehangatan hutan tropis langsung ke ruang tamu Anda. Furniture artisan kami dibuat dari kayu berkelanjutan dengan detail yang presisi.
                    </p>
                    <div class="flex flex-wrap gap-5 pt-4">
                        <a href="login.php" class="group relative px-8 py-4 bg-[#D4A373] text-[#2D1B14] font-bold rounded-xl overflow-hidden transition-all hover:bg-[#B18659] active:scale-95 shadow-xl shadow-[#D4A373]/20">
                            <span class="relative z-10 flex items-center gap-2">Mulai Belanja <i class="fas fa-arrow-right transition-transform group-hover:translate-x-1"></i></span>
                        </a>
                        <a href="#produk" class="px-8 py-4 rounded-xl border border-white/20 text-white font-medium hover:bg-white/10 transition-all backdrop-blur-sm flex items-center gap-2">
                            <i class="fas fa-play-circle text-[#D4A373]"></i> Lihat Koleksi
                        </a>
                    </div>
                </div>
                <div class="relative flex justify-center items-center">
                    <div class="absolute w-[350px] md:w-[400px] h-[437px] md:h-[400px] bg-[#D4A373]/10 rounded-full blur-[80px]"></div>
                    <div class="relative z-10 w-full max-w-[400px] md:max-w-[450px] aspect-[4/5] md:aspect-square rounded-[2rem] overflow-hidden shadow-2xl rotate-3 hover:rotate-0 transition-transform duration-700 group border-8 border-white/5">
                        <img src="img/2025-09-09.jpg" alt="Furniture Showcase" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-8">
                            <p class="text-white font-serif italic text-lg text-center w-full">"Kualitas yang bertahan lintas generasi"</p>
                        </div>
                    </div>
                    <div class="absolute -bottom-4 -right-4 z-30 w-20 h-20 md:w-24 md:h-24 bg-white rounded-full shadow-xl flex flex-col items-center justify-center text-center p-2">
                        <div class="w-5 h-5 md:w-7 md:h-7 bg-orange-100 rounded-full flex items-center justify-center text-orange-600 mb-1"><i class="fas fa-award text-xs"></i></div>
                        <p class="text-[6px] md:text-[7px] uppercase tracking-tighter text-gray-400 font-bold leading-none mb-0.5">Material</p>
                        <p class="text-[8px] md:text-[9px] font-extrabold text-gray-800 leading-tight">100% Solid <br> Teak Wood</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<style>
    :root {
        --luxury-gold: #C5A358;
        --deep-wood: #2D1B14;
        --soft-cream: #FDFBF7;
    }

    /* Background mewah dengan tekstur halus */
    .luxury-section {
        background: radial-gradient(circle at top right, #FDFBF7 0%, #F4F1EA 100%);
        position: relative;
        overflow: hidden;
    }

    /* Efek garis emas yang elegan */
    .gold-divider {
        height: 1px;
        background: linear-gradient(90deg, transparent, var(--luxury-gold), transparent);
        width: 150px;
        margin: 2rem auto;
    }

    /* Card Luxury: Lebih bersih, lebih tajam */
    .luxury-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(197, 163, 88, 0.1);
        border-radius: 1.5rem;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .luxury-card:hover {
        transform: translateY(-12px);
        border-color: var(--luxury-gold);
        box-shadow: 0 30px 60px -15px rgba(45, 27, 20, 0.1);
        background: white;
    }

    /* Animasi Daun yang Sangat Halus */
    #leaf-scene {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 5;
    }

    .falling-leaf {
        position: absolute;
        will-change: transform;
        animation: leaf-sway linear infinite;
        filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));
    }

    @keyframes leaf-sway {
        0% { transform: translate3d(0, -20px, 0) rotate(0deg); opacity: 0; }
        20% { opacity: 0.3; }
        80% { opacity: 0.3; }
        100% { transform: translate3d(80px, 90vh, 0) rotate(180deg); opacity: 0; }
    }

    /* Tipografi Mewah */
    .font-serif-luxury {
        font-family: 'Playfair Display', serif;
        letter-spacing: -0.02em;
    }
</style>

<section class="luxury-section py-32 px-4">
    <div id="leaf-scene"></div>

    <div class="container mx-auto relative z-10">
        <div class="text-center mb-24" data-aos="fade-up" data-aos-duration="1200">
            <span class="text-[var(--luxury-gold)] text-xs font-bold uppercase tracking-[0.6em] mb-4 block">Heriage & Excellence</span>
            <h2 class="font-serif-luxury text-5xl md:text-7xl font-bold text-[var(--deep-wood)] mb-6">
                Filosofi <span class="italic text-[var(--luxury-gold)]">Kualitas</span> Kami
            </h2>
            <div class="gold-divider"></div>
            <p class="text-[#5D4037]/60 text-xl max-w-2xl mx-auto font-light leading-relaxed italic">
                "Keindahan yang melampaui waktu, keahlian yang diwariskan melalui generasi."
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
            <div class="luxury-card p-12 group" data-aos="fade-up" data-aos-delay="200">
                <div class="mb-10 inline-block p-5 rounded-full bg-[var(--soft-cream)] border border-[var(--luxury-gold)]/20 transition-all duration-500 group-hover:bg-[var(--luxury-gold)] group-hover:text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </div>
                <h3 class="text-2xl font-serif-luxury font-bold text-[var(--deep-wood)] mb-4">Material Selektif</h3>
                <p class="text-[#5D4037]/70 font-light leading-relaxed">Hanya menggunakan inti kayu Jati Tua pilihan yang telah melalui proses pematangan alami.</p>
            </div>

            <div class="luxury-card p-12 group" data-aos="fade-up" data-aos-delay="400">
                <div class="mb-10 inline-block p-5 rounded-full bg-[var(--soft-cream)] border border-[var(--luxury-gold)]/20 transition-all duration-500 group-hover:bg-[var(--luxury-gold)] group-hover:text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758L5 19m0-14l4.121 4.121"></path></svg>
                </div>
                <h3 class="text-2xl font-serif-luxury font-bold text-[var(--deep-wood)] mb-4">Mahakarya Tangan</h3>
                <p class="text-[#5D4037]/70 font-light leading-relaxed">Setiap detail ukiran adalah manifestasi dedikasi seniman Jepara untuk kesempurnaan hunian Anda.</p>
            </div>

            <div class="luxury-card p-12 group" data-aos="fade-up" data-aos-delay="600">
                <div class="mb-10 inline-block p-5 rounded-full bg-[var(--soft-cream)] border border-[var(--luxury-gold)]/20 transition-all duration-500 group-hover:bg-[var(--luxury-gold)] group-hover:text-white">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <h3 class="text-2xl font-serif-luxury font-bold text-[var(--deep-wood)] mb-4">Proteksi Prima</h3>
                <p class="text-[#5D4037]/70 font-light leading-relaxed">Komitmen kami dibuktikan dengan garansi struktural komprehensif selama lima tahun penuh.</p>
            </div>
        </div>
    </div>
</section>

<script>
    AOS.init({ duration: 1000, once: true });

    function createLeaf() {
        const container = document.getElementById('leaf-scene');
        if (!container || container.childElementCount > 8) return; // Daun sangat sedikit agar tetap premium

        const leaf = document.createElement('div');
        leaf.className = 'falling-leaf';
        
        // Warna emas redup dan coklat muda
        const colors = ['#C5A358', '#E5D5B0', '#D4A373'];
        const color = colors[Math.floor(Math.random() * colors.length)];
        
        // Ukuran kecil agar elegan
        const size = Math.random() * 8 + 6;
        
        leaf.innerHTML = `<svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="${color}" style="opacity: 0.4;"><path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C19,20 22,3 22,3C21,5 14,5.25 9,6.25C4,7.25 2,11.5 2,13.5C2,15.5 3.75,17.25 3.75,17.25C7,8 17,8 17,8Z"/></svg>`;

        const startX = Math.random() * 100;
        const duration = Math.random() * 6 + 10;

        leaf.style.left = startX + '%';
        leaf.style.animationDuration = duration + 's';

        container.appendChild(leaf);
        setTimeout(() => leaf.remove(), duration * 1000);
    }

    // Interval lama agar daun jarang-jarang
    setInterval(createLeaf, 2500);
</script>

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Plus+Jakarta+Sans:wght@300;400;600&display=swap');

    :root {
        --dark-teak: #2D1B14;
        --golden-oak: #D4A373;
        --warm-ash: #F2EFE9;
        --luxury-gold: #C5A358;
    }

    .furniture-boutique {
        background-color: var(--warm-ash);
        /* Tekstur halus serat kayu pada background */
        background-image: url("https://www.transparenttextures.com/patterns/wood-pattern.png");
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .luxury-title {
        font-family: 'Playfair Display', serif;
    }

    /* Kartu Produk: Efek Papan Kayu Halus */
    .wood-card {
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid rgba(45, 27, 20, 0.05);
        border-radius: 4px; /* Sudut lebih tajam/kotak memberi kesan furniture solid */
        transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }

    .wood-card:hover {
        transform: translateY(-10px);
        background: #ffffff;
        box-shadow: 0 30px 60px rgba(45, 27, 20, 0.12);
        border-color: var(--golden-oak);
    }

    /* Frame Gambar Produk */
    .img-frame {
        position: relative;
        padding: 15px;
        background: #fff;
    }

    /* Animasi Daun yang Lebih Elegan */
    #leaf-scene {
        position: absolute;
        inset: 0;
        pointer-events: none;
        z-index: 2;
    }

    .leaf-particle {
        position: absolute;
        will-change: transform;
        animation: leaf-float linear infinite;
        filter: blur(0.3px);
    }

    @keyframes leaf-float {
        0% { transform: translate3d(0, -50px, 0) rotate(0deg); opacity: 0; }
        20% { opacity: 0.5; }
        80% { opacity: 0.5; }
        100% { transform: translate3d(150px, 100vh, 0) rotate(720deg); opacity: 0; }
    }
</style>

<?php
// 2. Simulasi cek login (Ganti sesuai sistem login kamu)
$is_logged_in = isset($_SESSION['user_id']);
?>

<div class="furniture-boutique relative overflow-hidden">
    <div id="leaf-scene"></div>

    <section id="produk" class="py-24 px-4 relative z-10">
        <div class="container mx-auto">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                <div class="max-w-xl" data-aos="fade-right">
                    <span class="text-[var(--golden-oak)] font-bold uppercase tracking-[0.4em] text-[11px] mb-4 block">The Masterpiece</span>
                    <h2 class="luxury-title text-5xl md:text-6xl text-[var(--dark-teak)] leading-tight">
                        Koleksi <span class="italic">Mahakarya</span> <br> Kayu Jati
                    </h2>
                </div>
                <div class="hidden md:block w-32 h-[1px] bg-[var(--golden-oak)] mb-6"></div>
                <p class="text-[var(--dark-teak)]/60 max-w-xs text-sm leading-relaxed" data-aos="fade-left">
                    Dibuat satu per satu dengan ketelitian tinggi, memastikan setiap serat kayu menceritakan kualitasnya.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-12">
                <?php
                $products = [
                    ['name' => 'Kursi Makan Jati', 'price' => '450.000', 'img' => 'img/unnamed.jpg', 'cat' => 'Dining Room'],
                    ['name' => 'Meja Minimalis', 'price' => '750.000', 'img' => 'img/unnamed (1).jpg', 'cat' => 'Workspace'],
                    ['name' => 'Lemari Pakaian', 'price' => '850.000', 'img' => 'img/unnamed (2).jpg', 'cat' => 'Bedroom'],
                    ['name' => 'Tempat Tidur King', 'price' => '1.200.000', 'img' => 'img/2024-01-02.jpg', 'cat' => 'Bedroom']
                ];

                foreach ($products as $index => $p) {
                ?>
                <div class="wood-card group flex flex-col" data-aos="fade-up" data-aos-delay="<?php echo $index * 100; ?>">
                    <div class="img-frame overflow-hidden">
                        <div class="relative h-[400px] overflow-hidden">
                            <img src="<?php echo $p['img']; ?>" alt="<?php echo $p['name']; ?>" class="w-full h-full object-cover transition-transform duration-[2s] group-hover:scale-105">
                            <div class="absolute inset-0 bg-[var(--dark-teak)]/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                    </div>
                    <div class="p-8">
                        <span class="text-[10px] uppercase tracking-[0.2em] text-[var(--golden-oak)] font-bold mb-2 block"><?php echo $p['cat']; ?></span>
                        <h3 class="luxury-title text-2xl font-bold text-[var(--dark-teak)] mb-4"><?php echo $p['name']; ?></h3>
                        <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-100">
                            <span class="text-lg font-bold text-[var(--dark-teak)]">Rp <?php echo $p['price']; ?></span>
                            
                            <button 
                                onclick="handleAction('<?php echo $p['name']; ?>')" 
                                class="text-[var(--golden-oak)] hover:text-[var(--dark-teak)] transition-colors">
                                <i class="fas fa-plus-circle text-2xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <section id="tentang" class="py-32 px-4 relative z-10 bg-white/40 backdrop-blur-md">
        <div class="container mx-auto">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="text-[#D4A373] font-bold uppercase tracking-[0.4em] text-xs mb-4 block">Testimonials</span>
                <h2 class="font-serif text-4xl md:text-6xl text-gray-800 mb-6">Apa Kata Pelanggan Kami</h2>
                <div class="w-24 h-1 bg-[#D4A373] mx-auto"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-xl border border-white/20" data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-[#D4A373] rounded-full flex items-center justify-center text-white font-bold text-xl">A</div>
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-800">Ahmad S.</h4>
                            <p class="text-gray-600 text-sm">Jakarta</p>
                        </div>
                    </div>
                    <div class="flex text-[#D4A373] mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-700 italic">"Furniture dari Berkah Mebel Ayu sangat berkualitas. Kayu jatinya solid dan finishingnya sangat halus. Sudah 5 tahun pakai, masih terlihat baru."</p>
                </div>

                <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-xl border border-white/20" data-aos="fade-up" data-aos-delay="400">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-[#D4A373] rounded-full flex items-center justify-center text-white font-bold text-xl">S</div>
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-800">Siti R.</h4>
                            <p class="text-gray-600 text-sm">Surabaya</p>
                        </div>
                    </div>
                    <div class="flex text-[#D4A373] mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-700 italic">"Pelayanan sangat ramah dan profesional. Meja makan yang saya pesan sesuai dengan gambar dan deskripsi. Harga juga reasonable."</p>
                </div>

                <div class="bg-white/80 backdrop-blur-sm p-8 rounded-2xl shadow-xl border border-white/20" data-aos="fade-up" data-aos-delay="600">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-[#D4A373] rounded-full flex items-center justify-center text-white font-bold text-xl">B</div>
                        <div class="ml-4">
                            <h4 class="font-bold text-gray-800">Budi W.</h4>
                            <p class="text-gray-600 text-sm">Bandung</p>
                        </div>
                    </div>
                    <div class="flex text-[#D4A373] mb-4">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="text-gray-700 italic">"Lemari pakaian dari sini sangat kokoh. Sudah 3 tahun, tidak ada masalah sama sekali. Rekomendasi untuk yang mau furniture berkualitas."</p>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
function handleAction(productName) {
    // Ambil status login dari PHP
    const isLoggedIn = <?php echo json_encode($is_logged_in); ?>;

    if (isLoggedIn) {
        // Jika sudah login, bisa arahkan ke keranjang atau munculkan notif sukses
        alert("Berhasil menambahkan " + productName + " ke keranjang.");
    } else {
        // Jika belum login, beri peringatan dan arahkan ke login.php
        alert("Maaf, Anda harus login terlebih dahulu untuk memesan koleksi mahakarya kami.");
        window.location.href = "login.php"; 
    }
}
</script>

<script>
    AOS.init({ duration: 1500, once: true });

    function createBoutiqueLeaf() {
        const container = document.getElementById('leaf-scene');
        if (!container || container.childElementCount > 10) return;

        const leaf = document.createElement('div');
        leaf.className = 'leaf-particle';
        
        // Warna: Emas Kayu, Coklat Kayu Tua, atau Gliter Emas
        const colors = ['#D4A373', '#A67C52', '#C5A358'];
        const color = colors[Math.floor(Math.random() * colors.length)];
        const size = Math.random() * 8 + 6;
        
        leaf.innerHTML = `
            <svg width="${size}" height="${size}" viewBox="0 0 24 24" fill="${color}" style="opacity: 0.4;">
                <path d="M17,8C8,10 5.9,16.17 3.82,21.34L5.71,22L6.66,19.7C7.14,19.87 7.64,20 8,20C19,20 22,3 22,3C21,5 14,5.25 9,6.25C4,7.25 2,11.5 2,13.5C2,15.5 3.75,17.25 3.75,17.25C7,8 17,8 17,8Z"/>
            </svg>`;

        const startX = Math.random() * 100;
        const duration = Math.random() * 8 + 10;

        leaf.style.left = startX + '%';
        leaf.style.animationDuration = duration + 's';
        
        container.appendChild(leaf);
        setTimeout(() => leaf.remove(), duration * 1000);
    }
    
    setInterval(createBoutiqueLeaf, 1500);
</script>


<style>
    :root {
        --vvip-gold: #D4A373;
        --royal-dark: #1A110D;
    }

    /* Animasi Debu Emas (Glow Particles) */
    .glow-particle {
        position: absolute;
        background: radial-gradient(circle, var(--vvip-gold) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
        opacity: 0;
        animation: fly-dust 8s infinite ease-in-out;
    }

    @keyframes fly-dust {
        0% { transform: translate(0, 0) scale(0); opacity: 0; }
        50% { opacity: 0.6; }
        100% { transform: translate(100px, -100px) scale(1.5); opacity: 0; }
    }

    /* Frame Peta yang Melayang (Floating Map) */
    .vvip-map-wrapper {
        position: relative;
        padding: 20px;
        background: linear-gradient(145deg, #2D1B14, #1A110D);
        border-radius: 2rem;
        box-shadow: 0 40px 100px rgba(0,0,0,0.4);
        transition: all 0.8s cubic-bezier(0.2, 0.8, 0.2, 1);
        overflow: hidden;
    }

    .vvip-map-wrapper:hover {
        transform: perspective(1000px) rotateY(-5deg) rotateX(5deg) translateY(-10px);
        box-shadow: -20px 40px 80px rgba(212, 163, 115, 0.15);
    }

    /* Garis Berjalan Emas (Animated Border) */
    .animated-border {
        position: absolute;
        inset: 0;
        border: 2px solid transparent;
        border-radius: 2rem;
        background: linear-gradient(90deg, var(--vvip-gold), transparent, var(--vvip-gold)) border-box;
        -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
        mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
        mask-composite: exclude;
        background-size: 200% auto;
        animation: border-flow 4s linear infinite;
    }

    @keyframes border-flow {
        0% { background-position: 0% center; }
        100% { background-position: 200% center; }
    }

    .vvip-glass {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(212, 163, 115, 0.1);
        padding: 2rem;
        border-radius: 1.5rem;
    }
</style>

<section id="lokasi" class="py-32 bg-[#0F0A08] relative overflow-hidden">
    <div id="gold-dust-container" class="absolute inset-0"></div>

    <div class="container mx-auto px-4 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-center">
            
            <div class="lg:col-span-5" data-aos="fade-right">
                <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-[var(--vvip-gold)]/10 border border-[var(--vvip-gold)]/20 mb-8">
                    <span class="w-2 h-2 rounded-full bg-[var(--vvip-gold)] animate-ping"></span>
                    <span class="text-[var(--vvip-gold)] text-[10px] font-bold uppercase tracking-[0.4em]">Official Showroom</span>
                </div>
                
                <h2 class="font-serif text-5xl md:text-6xl text-white mb-8 leading-tight">
                    Kehadiran <span class="italic text-[var(--vvip-gold)]">Nyata</span> <br> di Tengah Anda
                </h2>

                <div class="space-y-6">
                    <div class="vvip-glass group hover:bg-[var(--vvip-gold)]/5 transition-all">
                        <div class="flex gap-6">
                            <div class="w-12 h-12 flex-shrink-0 bg-[var(--vvip-gold)]/10 rounded-xl flex items-center justify-center text-[var(--vvip-gold)]">
                                <i class="fas fa-map-marker-alt text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-bold tracking-widest text-xs uppercase mb-2">Pusat Produksi & Showroom</h4>
                                <p class="text-gray-400 text-sm leading-relaxed">
                                    Balapulang Wetan, Balapulang, Tegal Regency,<br> Jawa Tengah 52464
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="vvip-glass group hover:bg-[var(--vvip-gold)]/5 transition-all">
                        <div class="flex gap-6">
                            <div class="w-12 h-12 flex-shrink-0 bg-[var(--vvip-gold)]/10 rounded-xl flex items-center justify-center text-[var(--vvip-gold)]">
                                <i class="fas fa-crown text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-white font-bold tracking-widest text-xs uppercase mb-2">Layanan Royal</h4>
                                <p class="text-gray-400 text-sm">Silahkan Di Kunjungi Ke Lokasi Kami Ya</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 flex flex-wrap gap-4">
                    <a href="https://wa.me/6282327294909" class="px-10 py-5 bg-[var(--vvip-gold)] text-[var(--royal-dark)] font-black rounded-full hover:scale-105 hover:shadow-[0_0_30px_rgba(212,163,115,0.4)] transition-all flex items-center gap-3">
                        <i class="fab fa-whatsapp text-xl"></i> Hubungi Kami
                    </a>
                </div>
            </div>

            <div class="lg:col-span-7" data-aos="zoom-in-left">
                <div class="vvip-map-wrapper">
                    <div class="animated-border"></div>
                    <div class="absolute top-8 left-8 z-20 bg-[var(--royal-dark)]/90 backdrop-blur-md px-6 py-3 rounded-2xl border border-[var(--vvip-gold)]/30">
                        <p class="text-[var(--vvip-gold)] text-[9px] font-black uppercase tracking-[0.3em] mb-1">Status Operasional</p>
                        <p class="text-white text-xs font-bold flex items-center gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Buka: 08.00 - 16.00
                        </p>
                    </div>

                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.324245362947!2d109.1384065!3d-7.0883838!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6f96e5ba2915ab%3A0xa14a7d037a464a1a!2sToko%20Ayu%20Mebel!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid" 
                        width="100%" 
                        height="550" 
                        style="border:0; filter: contrast(1.1) brightness(0.9) grayscale(0.2);" 
                        allowfullscreen="" 
                        loading="lazy" 
                        class="rounded-xl relative z-10">
                    </iframe>

                    <div class="absolute bottom-8 right-8 z-20">
                        <a href="https://maps.google.com/?cid=11622239242272786970&g_mp=CiVnb29nbGUubWFwcy5wbGFjZXMudjEuUGxhY2VzLkdldFBsYWNl" target="_blank" class="w-16 h-16 bg-[var(--vvip-gold)] text-[var(--royal-dark)] rounded-full flex items-center justify-center shadow-2xl hover:scale-110 transition-transform">
                            <i class="fas fa-directions text-2xl"></i>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
    // Membuat Efek Debu Emas Berterbangan
    function initGoldDust() {
        const container = document.getElementById('gold-dust-container');
        if(!container) return;

        for (let i = 0; i < 15; i++) {
            const dust = document.createElement('div');
            dust.className = 'glow-particle';
            
            const size = Math.random() * 4 + 2;
            const startX = Math.random() * 100;
            const startY = Math.random() * 100;
            const duration = Math.random() * 5 + 5;
            const delay = Math.random() * 5;

            dust.style.width = `${size}px`;
            dust.style.height = `${size}px`;
            dust.style.left = `${startX}%`;
            dust.style.top = `${startY}%`;
            dust.style.animationDuration = `${duration}s`;
            dust.style.animationDelay = `${delay}s`;

            container.appendChild(dust);
        }
    }

    document.addEventListener('DOMContentLoaded', initGoldDust);
</script>

   <footer class="bg-[#1A110D] pt-20 pb-10 text-gray-400">
    <div class="container mx-auto px-4 grid grid-cols-1 md:grid-cols-3 gap-16 mb-20">
        <div>
            <h3 class="luxury-title text-2xl text-white mb-6">Berkah Mebel Ayu</h3>
            <p class="leading-relaxed mb-6">Pusat furniture kayu jati berkualitas tinggi dari Balapulang. Kami mengedepankan kualitas konstruksi dan keindahan seni ukir.</p>
            <div class="flex gap-4">
                <a href="#" class="hover:text-[var(--luxury-gold)]"><i class="fab fa-instagram text-xl"></i></a>
                <a href="#" class="hover:text-[var(--luxury-gold)]"><i class="fab fa-facebook text-xl"></i></a>
                <a href="#" class="hover:text-[var(--luxury-gold)]"><i class="fab fa-tiktok text-xl"></i></a>
            </div>
        </div>
        <div>
            <h4 class="text-white font-bold mb-6 tracking-widest uppercase text-xs">Menu Cepat</h4>
            <ul class="space-y-4 text-sm">
                <li><a href="#" class="hover:text-white transition-colors">Koleksi Terbaru</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Promo Terbatas</a></li>
                <li><a href="#" class="hover:text-white transition-colors">Cara Pemesanan</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-bold mb-6 tracking-widest uppercase text-xs">Alamat Workshop</h4>
            <p class="text-sm leading-relaxed mb-4">
                Balapulang Wetan, Balapulang, Tegal Regency,<br> Central Java 52464
            </p>
            <p class="text-sm"><i class="fas fa-phone mr-2"></i> +62 823-2729-4909</p>
            <p class="text-sm mt-2"><i class="fas fa-envelope mr-2"></i> admin@berkahmebelayu.com</p>
        </div>
    </div>
    
    <div class="border-t border-white/5 pt-10 text-center">
        <p class="text-[10px] tracking-[0.4em] uppercase text-gray-600">
            EST. 2008 — COPYRIGHT © 2026 BERKAH MEBEL AYU ROYALTY
        </p>
    </div>
</footer>


    <div id="detailModal" class="fixed inset-0 bg-[#0F0A08]/95 backdrop-blur-xl z-[9999] hidden items-center justify-center p-4">
        <div class="bg-[#1A0F0A] border border-[#D4A373]/30 rounded-3xl w-full max-w-6xl relative overflow-hidden">
            <button id="closeDetailModalBtn" class="absolute top-6 right-6 text-[#D4A373] w-12 h-12 flex items-center justify-center border border-[#D4A373]/20 rounded-full">
                <i class="fas fa-times text-xl"></i>
            </button>
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <div class="p-10"><img id="detailProductImage" src="" class="w-full h-[400px] object-cover rounded-2xl"></div>
                <div class="p-12">
                    <h1 id="detailProductName" class="text-4xl font-serif font-bold text-white mb-6"></h1>
                    <h2 id="detailProductPrice" class="text-3xl font-serif text-[#D4A373] mb-6"></h2>
                    <p id="detailProductDescription" class="text-gray-400 mb-8 italic"></p>
                    <button id="addToCartBtn" class="w-full bg-[#D4A373] text-[#1A0F0A] py-4 rounded-full font-bold">RESERVE NOW</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('nav-container');
            if (window.scrollY > 50) nav.classList.replace('py-3', 'py-2');
            else nav.classList.replace('py-2', 'py-3');
        });

        // Mobile Menu Toggle
        const menuBtn = document.getElementById('mobile-menu-btn');
        const mobileMenu = document.getElementById('mobile-menu');
        menuBtn.addEventListener('click', () => mobileMenu.classList.toggle('hidden'));

        // Counter Animation
        const startCounter = () => {
            document.querySelectorAll('.counter').forEach(c => {
                const target = +c.getAttribute('data-target');
                const update = () => {
                    const count = +c.innerText;
                    if (count < target) {
                        c.innerText = Math.ceil(count + (target / 100));
                        setTimeout(update, 30);
                    } else c.innerText = target;
                };
                update();
            });
        };

        // Intersection Observer for Counter
        const observer = new IntersectionObserver((entries) => {
            if(entries[0].isIntersecting) startCounter();
        }, { threshold: 0.5 });
        observer.observe(document.querySelector('#tentang'));

        // Modal Logic
        function openDetailModal(name, price, img, badge, stock, description, qty) {
            document.getElementById('detailProductName').innerText = name;
            document.getElementById('detailProductPrice').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(price);
            document.getElementById('detailProductImage').src = img;
            document.getElementById('detailProductDescription').innerText = description;
            document.getElementById('detailModal').classList.remove('hidden');
            document.getElementById('detailModal').classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        document.getElementById('closeDetailModalBtn').onclick = () => {
            document.getElementById('detailModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        };
    </script>
</body>
</html>