<?php 
session_start();

// Jika user sudah login, redirect ke dashboard
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berkah Mebel Ayu - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow-x: hidden;
            background: #f0f4f8;
        }

        .login-container {
            background: linear-gradient(135deg, #5d4037 0%, #a67c52 100%);
            position: relative;
            overflow: hidden;
            min-height: 100vh;
        }

        /* Animasi Awan */
        .cloud {
            position: absolute;
            color: rgba(255, 255, 255, 0.3);
            animation: moveClouds linear infinite;
        }

        @keyframes moveClouds {
            from { transform: translateX(-150px); }
            to { transform: translateX(110vw); }
        }

        /* Animasi Daun Jatuh */
        .leaf {
            position: absolute;
            top: -20px;
            opacity: 0.8;
            pointer-events: none;
            animation: fall linear forwards;
            z-index: 5;
        }

        @keyframes fall {
            0% {
                transform: translateY(0) rotate(0deg) translateX(0);
                opacity: 0.8;
            }
            100% {
                transform: translateY(105vh) rotate(720deg) translateX(100px);
                opacity: 0;
            }
        }

        /* Pohon Dekorasi */
        .tree-decoration {
            position: absolute;
            bottom: -20px;
            right: -30px;
            font-size: 250px;
            color: rgba(45, 27, 20, 0.2);
            pointer-events: none;
            z-index: 1;
        }

        .form-input {
            transition: all 0.3s ease;
        }

        .form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(139, 69, 19, 0.2);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 10;
            position: relative;
            width: 100%;
            max-width: 450px;
        }
    </style>
</head>
<body class="opacity-0 transition-opacity duration-500">

    <div class="login-container flex flex-col items-center justify-center p-4">
        
        <i class="fas fa-cloud cloud text-6xl" style="top: 10%; animation-duration: 25s;"></i>
        <i class="fas fa-cloud cloud text-8xl" style="top: 25%; animation-duration: 35s; animation-delay: -10s;"></i>
        <i class="fas fa-cloud cloud text-7xl" style="top: 5%; animation-duration: 45s; animation-delay: -5s;"></i>

        <div class="tree-decoration">
            <i class="fas fa-tree"></i>
        </div>

        <div class="text-center mb-8 relative z-10">
            <a href="index.php" class="group inline-block transition-all duration-300">
                <div class="inline-flex items-center justify-center bg-white rounded-2xl p-4 mb-4 shadow-[0_20px_50px_rgba(0,0,0,0.1)] border border-amber-100 group-hover:shadow-amber-900/20 transition-all">
                    <img src="img/Screenshot 2025-10-22 083935.png" 
                         alt="Logo Berkah Mebel Ayu" 
                         class="h-20 w-auto object-contain mix-blend-multiply"> 
                </div>
                
                <h1 class="text-4xl font-bold text-white mb-1 drop-shadow-[0_2px_4px_rgba(0,0,0,0.3)] tracking-tight font-serif">
                    Berkah Mebel Ayu
                </h1>
                <div class="flex items-center justify-center gap-2">
                    <span class="h-[1px] w-8 bg-amber-200/50"></span>
                    <p class="text-amber-100 text-sm md:text-base opacity-90 italic tracking-widest uppercase">
                        Crafting Nature into Comfort
                    </p>
                    <span class="h-[1px] w-8 bg-amber-200/50"></span>
                </div>
            </a>
        </div>

        <div class="glass-card rounded-3xl shadow-2xl p-8 md:p-10 border border-white/20">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Selamat Datang</h2>
            
            <?php if (isset($_SESSION['login_error'])): ?>
                <div class="mb-5 p-4 bg-red-100 border-2 border-red-300 rounded-lg text-red-700 text-sm flex items-center">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span><?php echo htmlspecialchars($_SESSION['login_error']); ?></span>
                </div>
                <?php unset($_SESSION['login_error']); ?>
            <?php endif; ?>
            
            <form method="POST" action="auth.php" class="space-y-5">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-envelope text-amber-700 mr-2"></i>Email
                    </label>
                    <input type="email" name="email" class="form-input w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none" placeholder="email@contoh.com" required>
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">
                        <i class="fas fa-lock text-amber-700 mr-2"></i>Password
                    </label>
                    <input type="password" name="password" class="form-input w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-amber-600 focus:outline-none" placeholder="••••••••" required>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-amber-700 rounded border-2 border-gray-300 focus:ring-2 focus:ring-amber-500 cursor-pointer">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                    <a href="#" class="text-sm text-amber-700 hover:underline font-medium">Lupa Password?</a>
                </div>

                <button type="submit" class="w-full bg-[#8b4513] hover:bg-[#6d360f] text-white font-bold py-3 rounded-xl transition duration-300 shadow-lg flex items-center justify-center active:scale-95">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk Sekarang
                </button>
            </form>

            <div class="relative my-8">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="px-3 bg-white text-gray-400 uppercase tracking-widest">Atau masuk dengan</span>
                </div>
            </div>

           <div class="grid grid-cols-2 gap-4">
    <a href="social_auth_simulator.php?provider=google" class="flex items-center justify-center gap-2 py-2.5 border-2 border-gray-100 rounded-xl hover:bg-gray-50 transition active:scale-95 shadow-sm">
        <img src="https://www.gstatic.com/images/branding/product/1x/googleg_48dp.png" class="w-5 h-5" alt="Google">
        <span class="text-sm font-bold text-gray-700">Google</span>
    </a>
    
    <a href="social_auth_simulator.php?provider=facebook" class="flex items-center justify-center gap-2 py-2.5 border-2 border-gray-100 rounded-xl hover:bg-gray-50 transition active:scale-95 shadow-sm">
        <i class="fab fa-facebook text-blue-600 text-xl"></i>
        <span class="text-sm font-bold text-gray-700">Facebook</span>
    </a>
</div>

            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-gray-600">Belum punya akun? 
                    <a href="register.php" class="text-amber-700 font-bold hover:text-amber-900 underline decoration-amber-200">Daftar</a>
                </p>
                <a href="index.php" class="inline-block mt-4 text-sm text-gray-400 hover:text-amber-700 transition">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>

        <div class="mt-8 text-white/50 text-center text-xs relative z-10">
            &copy; 2024 Berkah Mebel Ayu - Kualitas Kayu Terbaik
        </div>
    </div>

    <script>
        // Animasi muncul saat load
        window.addEventListener('load', () => {
            document.body.classList.remove('opacity-0');
        });

        // Fungsi membuat daun jatuh secara dinamis
        function createLeaf() {
            const leaf = document.createElement('i');
            leaf.classList.add('fas', 'fa-leaf', 'leaf');
            
            // Variasi posisi dan kecepatan
            leaf.style.left = Math.random() * 100 + 'vw';
            leaf.style.fontSize = (Math.random() * 10 + 10) + 'px';
            const duration = (Math.random() * 5 + 5) + 's';
            leaf.style.animationDuration = duration;
            
            // Warna daun acak (hijau ke cokelat)
            const colors = ['#d4a373', '#8b4513', '#a67c52', '#2d4f1e'];
            leaf.style.color = colors[Math.floor(Math.random() * colors.length)];

            document.querySelector('.login-container').appendChild(leaf);

            // Hapus daun setelah sampai bawah
            setTimeout(() => {
                leaf.remove();
            }, parseFloat(duration) * 1000);
        }

        // Jalankan interval daun
        setInterval(createLeaf, 600);
    </script>
</body>
</html>