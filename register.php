<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berkah Mebel Ayu - Daftar Furniture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            overflow-x: hidden;
            background: #2d1b14; /* Cokelat Kayu Gelap */
        }

        /* Background Bertema Kayu */
        .register-container {
            background: linear-gradient(135deg, #4b2c20 0%, #8b4513 50%, #d2b48c 100%);
            position: relative;
            overflow: hidden;
        }

        /* Partikel Serbuk Kayu Berkilau */
        .wood-dust {
            position: absolute;
            bottom: -50px;
            background: radial-gradient(circle, #fcd34d 0%, transparent 70%);
            border-radius: 50%;
            animation: rise linear infinite;
            pointer-events: none;
            filter: blur(1px);
        }

        @keyframes rise {
            0% { transform: translateY(0) scale(1); opacity: 0; }
            20% { opacity: 0.6; }
            80% { opacity: 0.6; }
            100% { transform: translateY(-110vh) scale(0.5); opacity: 0; }
        }

        /* Gelombang Tekstur Kayu */
        .wave-container {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            line-height: 0;
            z-index: 1;
        }

        .wave-container svg {
            position: relative;
            display: block;
            width: calc(150% + 1.3px);
            height: 150px;
            animation: waveMove 15s linear infinite;
        }

        @keyframes waveMove {
            0% { transform: translateX(0); }
            100% { transform: translateX(-25%); }
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(210, 180, 140, 0.5);
            z-index: 10;
            position: relative;
        }

        /* Input Fokus Warna Kayu Jati */
        .form-input:focus {
            box-shadow: 0 0 15px rgba(139, 69, 19, 0.3);
            border-color: #8b4513;
        }
    </style>
</head>
<body class="opacity-0 transition-opacity duration-700">

    <div class="register-container min-h-screen flex items-center justify-center p-4">
        
        <div class="wave-container">
            <svg viewBox="0 0 1200 120" preserveAspectRatio="none">
                <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V120H0V95.8C58.47,87.31,127.33,103.18,187.33,103.18c57.56,0,105.32-24.18,134.06-46.74Z" fill="rgba(60, 30, 10, 0.2)"></path>
            </svg>
        </div>

        <div class="w-full max-w-lg">
            <div class="text-center mb-6 relative z-10">
                <div class="inline-block bg-white p-4 rounded-3xl shadow-2xl mb-4">
                    <img src="img/Screenshot 2025-10-22 083935.png" alt="Berkah Mebel Ayu" class="h-14 w-auto">
                </div>
                <h1 class="text-3xl font-bold text-white tracking-wide drop-shadow-md">Katalog Eksklusif Mebel</h1>
                <p class="text-amber-100 italic">Daftar untuk melihat koleksi jati terbaik</p>
            </div>

            <!-- Pesan Sukses -->
            <?php if (isset($_SESSION['register_success']) && $_SESSION['register_success']): ?>
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg text-center">
                    <i class="fas fa-check-circle mr-2"></i>Akun berhasil dibuat! Silakan <a href="login.php" class="font-bold underline">login di sini</a>
                </div>
                <?php unset($_SESSION['register_success']); ?>
            <?php endif; ?>

            <!-- Pesan Error -->
            <?php if (isset($_SESSION['register_error'])): ?>
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg text-center">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($_SESSION['register_error']); ?>
                </div>
                <?php unset($_SESSION['register_error']); ?>
            <?php endif; ?>

            <div class="glass-card rounded-[2.5rem] shadow-2xl p-8 md:p-10">
                <form method="POST" action="register_process.php" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-amber-900 text-sm font-bold mb-2 ml-1">
                            <i class="fas fa-user-tie mr-2"></i>Nama Lengkap
                        </label>
                        <input type="text" name="name" class="form-input w-full px-4 py-3 bg-amber-50/50 border border-amber-200 rounded-2xl focus:outline-none transition-all" placeholder="Nama Anda" required>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-amber-900 text-sm font-bold mb-2 ml-1">
                            <i class="fas fa-envelope mr-2"></i>Email
                        </label>
                        <input type="email" name="email" class="form-input w-full px-4 py-3 bg-amber-50/50 border border-amber-200 rounded-2xl focus:outline-none transition-all" placeholder="email@anda.com" required>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-amber-900 text-sm font-bold mb-2 ml-1">
                            <i class="fas fa-phone mr-2"></i>No. Telepon
                        </label>
                        <input type="tel" name="phone" class="form-input w-full px-4 py-3 bg-amber-50/50 border border-amber-200 rounded-2xl focus:outline-none transition-all" placeholder="08xxxxxxxxxx" required>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-amber-900 text-sm font-bold mb-2 ml-1">
                            <i class="fas fa-lock mr-2"></i>Password
                        </label>
                        <input type="password" name="password" class="form-input w-full px-4 py-3 bg-amber-50/50 border border-amber-200 rounded-2xl focus:outline-none transition-all" placeholder="••••••" required>
                    </div>

                    <div class="md:col-span-1">
                        <label class="block text-amber-900 text-sm font-bold mb-2 ml-1">
                            <i class="fas fa-check-double mr-2"></i>Konfirmasi Password
                        </label>
                        <input type="password" name="password_confirm" class="form-input w-full px-4 py-3 bg-amber-50/50 border border-amber-200 rounded-2xl focus:outline-none transition-all" placeholder="••••••" required>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="terms" class="w-4 h-4 text-amber-700 rounded border-2 border-gray-300 focus:ring-2 focus:ring-amber-500 cursor-pointer" required>
                            <span class="ml-2 text-sm text-gray-600">Saya setuju dengan <a href="#" class="text-amber-800 hover:underline">Syarat & Ketentuan</a></span>
                        </label>
                    </div>

                    <div class="md:col-span-2 mt-4">
                        <button type="submit" class="w-full bg-gradient-to-r from-amber-800 to-amber-600 hover:from-amber-900 hover:to-amber-700 text-white font-bold py-4 rounded-2xl shadow-xl transform transition hover:-translate-y-1 active:scale-95">
                            BUAT AKUN MEBEL <i class="fas fa-hammer ml-2"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center mt-6">
                    <p class="text-gray-600 text-sm">Sudah memiliki akun? 
                        <a href="login.php" class="text-amber-800 font-bold hover:underline">Masuk di sini</a>
                    </p>
                    <a href="index.php" class="inline-block mt-3 text-sm text-gray-500 hover:text-amber-700 transition">
                        <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('load', () => {
            document.body.classList.remove('opacity-0');
        });

        // Membuat Serbuk Kayu (Wood Dust)
        function createWoodDust() {
            const container = document.querySelector('.register-container');
            const dust = document.createElement('div');
            dust.classList.add('wood-dust');
            
            // Ukuran partikel kecil seperti debu
            const size = Math.random() * 6 + 2 + 'px';
            dust.style.width = size;
            dust.style.height = size;
            
            dust.style.left = Math.random() * 100 + 'vw';
            
            const duration = Math.random() * 5 + 3 + 's';
            dust.style.animationDuration = duration;
            
            container.appendChild(dust);

            setTimeout(() => {
                dust.remove();
            }, parseFloat(duration) * 1000);
        }

        // Interval serbuk kayu yang lebih halus
        setInterval(createWoodDust, 200);
    </script>
</body>
</html>