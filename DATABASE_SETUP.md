# 📊 Setup Database - Berkah Mebel Ayu

## Panduan Membuat Database

Ikuti langkah-langkah berikut untuk membuat database MySQL:

### **Metode 1: Menggunakan phpMyAdmin (Paling Mudah)**

1. **Buka phpMyAdmin**
   - Akses: http://localhost/phpmyadmin
   - Login dengan username: `root` (password kosong)

2. **Buat Database Baru**
   - Klik tab "Databases"
   - Masukkan nama database: `berkah_mebel_ayu`
   - Pilih charset: `utf8mb4_unicode_ci`
   - Klik "Create"

3. **Import File SQL**
   - Pilih database yang baru dibuat: `berkah_mebel_ayu`
   - Klik tab "Import"
   - Klik "Choose File" dan pilih file `database.sql`
   - Klik "Go" untuk menjalankan script

### **Metode 2: Menggunakan MySQL Command Line**

```bash
# 1. Buka MySQL CLI
mysql -u root

# 2. Buat database dan import file SQL
mysql -u root < database.sql

# Atau jalankan command-command berikut:
CREATE DATABASE berkah_mebel_ayu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE berkah_mebel_ayu;
SOURCE /path/to/database.sql;
```

### **Metode 3: Menggunakan Script Setup PHP**

Buka file ini di browser:
```
http://localhost/e-commery/setup.php
```

---

## ✅ Verifikasi Database Berhasil Dibuat

Setelah import, periksa apakah database sudah berhasil:

```sql
-- Lihat semua tabel
USE berkah_mebel_ayu;
SHOW TABLES;

-- Lihat data users
SELECT * FROM users;

-- Lihat data products
SELECT * FROM products;
```

---

## 🔑 Credentials Database

- **Host:** localhost
- **Username:** root
- **Password:** (kosong)
- **Database:** berkah_mebel_ayu

---

## 📝 Tabel yang Dibuat

✅ users - Tabel pengguna  
✅ products - Tabel produk  
✅ categories - Tabel kategori  
✅ cart - Tabel keranjang belanja  
✅ wishlist - Tabel wishlist  
✅ orders - Tabel pesanan  
✅ order_items - Tabel detail pesanan  
✅ reviews - Tabel review dan rating  

---

## 🔐 Catatan Keamanan

- ⚠️ Password demo masih menggunakan plain text, gunakan `password_hash()` untuk production
- Ganti username/password MySQL jika sudah di-production
- Jangan gunakan password kosong di server production

---

## 📌 Next Steps

Setelah database berhasil dibuat:

1. Update file `auth.php` untuk menggunakan MySQL
2. Update file `register_process.php` untuk menyimpan ke MySQL
3. Update file `profile_process.php` untuk update data MySQL
4. Update `cart.php`, `orders.php`, dll untuk read/write dari MySQL

