<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berkah Mebel Ayu - Pengaturan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .navbar {
            background: linear-gradient(90deg, #8B4513 0%, #A0522D 100%);
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="navbar sticky top-0 z-50 shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <a href="dashboard.php" class="flex items-center gap-2">
                        <i class="fas fa-chair text-3xl text-white"></i>
                        <h1 class="text-2xl font-bold text-white">Berkah Mebel Ayu</h1>
                    </a>
                </div>
                <div class="flex items-center gap-6">
                    <a href="dashboard.php" class="text-white hover:text-amber-200 transition">
                        <i class="fas fa-home mr-1"></i>Beranda
                    </a>
                    <a href="logout.php" class="text-white hover:text-amber-200 transition">
                        <i class="fas fa-sign-out-alt mr-1"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Breadcrumb -->
    <div class="bg-white border-b">
        <div class="container mx-auto px-4 py-3">
            <a href="dashboard.php" class="text-amber-700 hover:text-amber-800">Beranda</a>
            <span class="text-gray-500 mx-2">/</span>
            <span class="text-gray-700">Pengaturan</span>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Sidebar -->
            <div class="bg-white rounded-lg shadow-md p-6 h-fit">
                <nav class="space-y-2">
                    <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-amber-50 rounded-lg transition">
                        <i class="fas fa-user mr-2"></i>Profil Saya
                    </a>
                    <a href="orders.php" class="block px-4 py-2 text-gray-700 hover:bg-amber-50 rounded-lg transition">
                        <i class="fas fa-box mr-2"></i>Pesanan Saya
                    </a>
                    <a href="settings.php" class="block px-4 py-2 bg-amber-700 text-white rounded-lg">
                        <i class="fas fa-cog mr-2"></i>Pengaturan
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="md:col-span-3 space-y-6">
                <!-- Security Settings -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-lock text-amber-700 mr-2"></i>Keamanan
                    </h2>

                    <div class="space-y-6">
                        <!-- Change Password -->
                        <div class="border-b pb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Ubah Password</h3>
                            <form class="space-y-4">
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Password Saat Ini</label>
                                    <input 
                                        type="password" 
                                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-amber-700 focus:outline-none"
                                        placeholder="Masukkan password saat ini"
                                    >
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Password Baru</label>
                                    <input 
                                        type="password" 
                                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-amber-700 focus:outline-none"
                                        placeholder="Masukkan password baru"
                                    >
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
                                    <input 
                                        type="password" 
                                        class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:border-amber-700 focus:outline-none"
                                        placeholder="Konfirmasi password baru"
                                    >
                                </div>
                                <button type="submit" class="bg-amber-700 hover:bg-amber-800 text-white font-bold py-2 px-6 rounded-lg transition">
                                    <i class="fas fa-save mr-2"></i>Simpan Password
                                </button>
                            </form>
                        </div>

                        <!-- Two Factor Authentication -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 mb-4">Autentikasi Dua Faktor</h3>
                            <p class="text-gray-600 mb-4">Tingkatkan keamanan akun Anda dengan autentikasi dua faktor</p>
                            <button class="bg-amber-700 hover:bg-amber-800 text-white font-bold py-2 px-6 rounded-lg transition">
                                <i class="fas fa-shield-alt mr-2"></i>Aktifkan 2FA
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Notification Settings -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-bell text-amber-700 mr-2"></i>Notifikasi
                    </h2>

                    <div class="space-y-4">
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <input type="checkbox" checked class="w-5 h-5 text-amber-700 rounded">
                            <div class="ml-3">
                                <p class="font-semibold text-gray-800">Notifikasi Email</p>
                                <p class="text-gray-600 text-sm">Terima notifikasi pesanan melalui email</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <input type="checkbox" checked class="w-5 h-5 text-amber-700 rounded">
                            <div class="ml-3">
                                <p class="font-semibold text-gray-800">Notifikasi SMS</p>
                                <p class="text-gray-600 text-sm">Terima notifikasi pesanan melalui SMS</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <input type="checkbox" class="w-5 h-5 text-amber-700 rounded">
                            <div class="ml-3">
                                <p class="font-semibold text-gray-800">Newsletter</p>
                                <p class="text-gray-600 text-sm">Dapatkan tips furniture dan promosi terbaru</p>
                            </div>
                        </label>

                        <div class="pt-4">
                            <button class="bg-amber-700 hover:bg-amber-800 text-white font-bold py-2 px-6 rounded-lg transition">
                                <i class="fas fa-save mr-2"></i>Simpan Preferensi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Privacy Settings -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">
                        <i class="fas fa-eye-slash text-amber-700 mr-2"></i>Privasi
                    </h2>

                    <div class="space-y-4">
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <input type="checkbox" checked class="w-5 h-5 text-amber-700 rounded">
                            <div class="ml-3">
                                <p class="font-semibold text-gray-800">Profil Publik</p>
                                <p class="text-gray-600 text-sm">Izinkan pengguna lain melihat profil Anda</p>
                            </div>
                        </label>

                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition cursor-pointer">
                            <input type="checkbox" class="w-5 h-5 text-amber-700 rounded">
                            <div class="ml-3">
                                <p class="font-semibold text-gray-800">Riwayat Pembelian Pribadi</p>
                                <p class="text-gray-600 text-sm">Sembunyikan riwayat pembelian Anda</p>
                            </div>
                        </label>

                        <div class="pt-4">
                            <button class="bg-amber-700 hover:bg-amber-800 text-white font-bold py-2 px-6 rounded-lg transition">
                                <i class="fas fa-save mr-2"></i>Simpan Privasi
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Danger Zone -->
                <div class="bg-red-50 border-l-4 border-red-500 rounded-lg shadow-md p-8">
                    <h2 class="text-2xl font-bold text-red-700 mb-6">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Zona Berbahaya
                    </h2>

                    <div>
                        <h3 class="text-lg font-semibold text-red-700 mb-2">Hapus Akun</h3>
                        <p class="text-gray-700 mb-4">Jika Anda menghapus akun, semua data Anda akan hilang secara permanen.</p>
                        <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition">
                            <i class="fas fa-trash mr-2"></i>Hapus Akun Saya
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-16">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400">© 2026 Berkah Mebel Ayu. Semua hak dilindungi.</p>
        </div>
    </footer>
</body>
</html>
