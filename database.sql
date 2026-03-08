-- Berkah Mebel Ayu - Database Schema
-- Database untuk aplikasi e-commerce furniture

-- Buat database
CREATE DATABASE IF NOT EXISTS `berkah_mebel_ayu`;
USE `berkah_mebel_ayu`;

-- ===========================
-- Tabel Users (Pengguna)
-- ===========================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20),
  `address` TEXT,
  `city` VARCHAR(50),
  `province` VARCHAR(50),
  `postal_code` VARCHAR(10),
  `profile_image` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_active` BOOLEAN DEFAULT TRUE
);

-- ===========================
-- Tabel Produk (Products)
-- ===========================
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `seller_id` INT,
  `name` VARCHAR(150) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `description` TEXT,
  `price` DECIMAL(10, 2) NOT NULL,
  `discount_price` DECIMAL(10, 2),
  `stock` INT NOT NULL DEFAULT 0,
  `image` VARCHAR(255),
  `rating` DECIMAL(3, 2) DEFAULT 0,
  `reviews_count` INT DEFAULT 0,
  `is_featured` BOOLEAN DEFAULT FALSE,
  `status` ENUM('active', 'inactive', 'pending') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- ===========================
-- Tabel Keranjang Belanja (Cart)
-- ===========================
CREATE TABLE IF NOT EXISTS `cart` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_cart_item` (`user_id`, `product_id`)
);

-- ===========================
-- Tabel Wishlist (Daftar Keinginan)
-- ===========================
CREATE TABLE IF NOT EXISTS `wishlist` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `added_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_wishlist_item` (`user_id`, `product_id`)
);

-- ===========================
-- Tabel Pesanan (Orders)
-- ===========================
CREATE TABLE IF NOT EXISTS `orders` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `order_number` VARCHAR(50) NOT NULL UNIQUE,
  `total_amount` DECIMAL(10, 2) NOT NULL,
  `status` ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
  `payment_method` VARCHAR(50),
  `shipping_address` TEXT,
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);

-- ===========================
-- Tabel Detail Pesanan (Order Items)
-- ===========================
CREATE TABLE IF NOT EXISTS `order_items` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(150) NOT NULL,
  `product_price` DECIMAL(10, 2) NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` DECIMAL(10, 2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
);

-- ===========================
-- Tabel Review & Rating
-- ===========================
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `rating` INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
  `comment` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_review` (`user_id`, `product_id`)
);

-- ===========================
-- Tabel Kategori Produk (Categories)
-- ===========================
CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL UNIQUE,
  `description` TEXT,
  `image` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===========================
-- Insert Data Demo
-- ===========================

-- Insert Kategori
INSERT INTO `categories` (`name`, `description`) VALUES
('Kursi', 'Berbagai pilihan kursi berkualitas'),
('Meja', 'Meja makan, kerja, dan dekorasi'),
('Lemari', 'Lemari penyimpanan dan pajangan'),
('Tempat Tidur', 'Tempat tidur dengan desain premium'),
('Rak', 'Rak buku dan rak dinding'),
('Sofa', 'Sofa dan kursi santai');

-- Insert User Demo
INSERT INTO `users` (`name`, `email`, `password`, `phone`, `address`, `city`, `province`, `postal_code`) VALUES
('Demo User', 'demo@mebel.com', '$2y$10$YourHashedPasswordHere', '08123456789', 'Jl. Demo No. 123', 'Jakarta', 'DKI Jakarta', '12345'),
('John Doe', 'john@example.com', '$2y$10$YourHashedPasswordHere', '08234567890', 'Jl. Contoh No. 456', 'Bandung', 'Jawa Barat', '40123');

-- Insert Produk Demo
INSERT INTO `products` (`name`, `category`, `description`, `price`, `discount_price`, `stock`, `image`, `is_featured`) VALUES
('Kursi Makan Kayu Jati', 'Kursi', 'Kursi makan berkualitas premium dari kayu jati pilihan dengan finishing natural', 450000, 315000, 15, 'kursi-jati.jpg', TRUE),
('Meja Makan Minimalis', 'Meja', 'Meja makan keluarga dengan desain modern dan kapasitas 6 orang', 750000, 562500, 8, 'meja-makan.jpg', TRUE),
('Lemari Pakaian Besar', 'Lemari', 'Lemari pakaian dengan sistem penyimpanan cerdas dan desain minimalis', 850000, NULL, 5, 'lemari.jpg', FALSE),
('Tempat Tidur Kling Size', 'Tempat Tidur', 'Tempat tidur dengan frame kayu solid dan desain minimalis elegan', 1200000, 720000, 3, 'tempat-tidur.jpg', TRUE),
('Rak Buku 5 Tingkat', 'Rak', 'Rak buku gantung dengan sistem floating untuk dekorasi modern', 320000, 224000, 12, 'rak-buku.jpg', FALSE),
('Sofa Premium Leather', 'Sofa', 'Sofa premium dengan kulit asli dan desain ergonomis', 950000, 665000, 4, 'sofa.jpg', TRUE),
('Sofa Kayu Minimalis', 'Sofa', 'Sofa dengan frame kayu solid dan bantal berkualitas premium', 680000, NULL, 6, 'sofa-kayu.jpg', FALSE),
('Kursi Teras Minimalis', 'Kursi', 'Kursi teras dengan desain minimalis dan bahan tahan cuaca', 280000, 224000, 20, 'kursi-teras.jpg', FALSE);

-- ===========================
-- Tabel Admin Users (Untuk Multiple Admin)
-- ===========================
CREATE TABLE IF NOT EXISTS `admin_users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `role` ENUM('super_admin', 'admin', 'editor') DEFAULT 'admin',
  `is_active` BOOLEAN DEFAULT TRUE,
  `last_login` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin (password: admin123)
-- Password dihash dengan bcrypt
INSERT IGNORE INTO `admin_users` (`username`, `email`, `password`, `name`, `role`) VALUES
('admin', 'admin@berkahmebelayu.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin Utama', 'super_admin');

-- ===========================
-- Tabel Kontak/Message dari User
-- ===========================
CREATE TABLE IF NOT EXISTS `contacts` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `subject` VARCHAR(200),
  `message` TEXT NOT NULL,
  `is_read` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===========================
-- Tabel Konfigurasi Website
-- ===========================
CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `setting_key` VARCHAR(50) NOT NULL UNIQUE,
  `setting_value` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default settings
INSERT IGNORE INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'Berkah Mebel Ayu'),
('site_description', 'Toko Furniture Berkualitas'),
('contact_phone', '081234567890'),
('contact_wa', '081234567890'),
('contact_address', 'Jl. Contoh No. 123, Jakarta'),
('shipping_cost', '25000'),
('free_shipping_min', '500000');

-- ===========================
-- Tambahkan kolom is_flash_sale ke products jika belum ada
-- ===========================
ALTER TABLE `products` ADD COLUMN `is_flash_sale` BOOLEAN DEFAULT FALSE AFTER `status`;
ALTER TABLE `products` ADD COLUMN `flash_sale_price` DECIMAL(10, 2) NULL AFTER `is_flash_sale`;
ALTER TABLE `products` ADD COLUMN `flash_sale_end` DATETIME NULL AFTER `flash_sale_price`;

-- ===========================
-- Index untuk performa
-- ===========================
CREATE INDEX `idx_user_email` ON `users`(`email`);
CREATE INDEX `idx_product_category` ON `products`(`category`);
CREATE INDEX `idx_cart_user` ON `cart`(`user_id`);
CREATE INDEX `idx_order_user` ON `orders`(`user_id`);
CREATE INDEX `idx_review_user` ON `reviews`(`user_id`);
CREATE INDEX `idx_review_product` ON `reviews`(`product_id`);
CREATE INDEX `idx_admin_email` ON `admin_users`(`email`);
CREATE INDEX `idx_admin_username` ON `admin_users`(`username`);
