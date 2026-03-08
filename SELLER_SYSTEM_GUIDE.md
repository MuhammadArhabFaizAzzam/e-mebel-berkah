# 🏪 Panduan Sistem Multi-Seller Berkah Mebel Ayu

## 📋 Ringkasan

Sistem multi-seller memungkinkan pengguna untuk menjadi **pembeli sekaligus penjual** di platform e-commerce Berkah Mebel Ayu. Satu akun bisa digunakan untuk berbelanja dan berjualan produk furniture.

---

## 🎯 Fitur Utama

### 1. **Dua Mode User**
- **Mode Pembeli**: User bisa browsing dan membeli produk dari semua penjual
- **Mode Penjual**: User bisa upload dan kelola produk mereka sendiri

### 2. **Seller Dashboard**
Dashboard khusus untuk penjual dengan fitur:
- 📊 Statistik toko (Total produk, Produk aktif, Total stok)
- ➕ Tambah produk baru dengan form yang user-friendly
- ✏️ Edit dan kelola produk
- 🗑️ Hapus produk dengan konfirmasi
- ⏱️ Toggle status produk (Aktif/Nonaktif)

### 3. **Produk dari Multiple Seller**
- Dashboard pembeli menampilkan produk dari semua seller yang aktif
- Landing page (index.php) menampilkan produk dari berbagai penjual
- Sistem fallback jika database error

---

## 🗄️ Struktur Database

### Perubahan pada Tabel `products`

```sql
CREATE TABLE `products` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `seller_id` INT,                          -- ⭐ BARU: ID penjual
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
  `status` ENUM('active', 'inactive', 'pending') DEFAULT 'active',  -- ⭐ BARU
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`seller_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);
```

**Kolom Baru:**
- `seller_id`: Foreign key ke tabel `users`, menunjukkan siapa pemilik produk
- `status`: Status produk (active, inactive, pending) untuk kontrol visibility

---

## 📁 File & Folder Baru

### File Baru yang Dibuat:

```
seller_dashboard.php          ✨ Halaman utama seller center
seller_process.php            🔧 Backend untuk handle add/update produk
seller_edit_product.php       ✏️ Form edit produk individual
img/products/                 📸 Folder untuk menyimpan gambar produk user
```

### File yang Dimodifikasi:

```
database.sql                  ➕ Tambah kolom seller_id dan status
dashboard.php                 📝 Tambah menu "Mulai Berjualan" + query database aktif
index.php                     📝 Update query untuk show semua seller products
```

---

## 🚀 Cara Penggunaan

### Untuk Pembeli (Mode Normal)

1. Login ke dashboard seperti biasa
2. Lihat produk dari berbagai penjual
3. Tambahkan ke keranjang dan checkout
4. Produk ditampilkan dari semua seller aktif

### Untuk Penjual

#### Step 1: Masuk Seller Center
```
Dashboard → Menu Profil (kanan atas) → "Mulai Berjualan"
```

#### Step 2: Tambah Produk Baru
1. Klik tombol "Tambah Produk Baru"
2. Isi form:
   - **Nama Produk**: Nama barang yang dijual
   - **Kategori**: Pilih dari list kategori
   - **Harga**: Harga normal (required)
   - **Harga Diskon**: Harga sale (optional, jika ada promo)
   - **Stok**: Jumlah barang tersedia
   - **Deskripsi**: Detail produk
   - **Foto**: Upload gambar produk (JPG/PNG, max 5MB)
3. Klik "Upload Produk"

#### Step 3: Kelola Produk
- **Edit**: Klik tombol edit untuk ubah detail produk
- **Hapus**: Klik tombol hapus untuk remove produk (dengan konfirmasi)
- **Toggle Status**: Klik status untuk aktifkan/nonaktifkan produk

#### Step 4: Monitor Statistik
- **Total Produk**: Jumlah semua produk yang sudah diupload
- **Produk Aktif**: Jumlah produk yang currently untuk dijual
- **Total Stok**: Jumlah item keseluruhan
- **Badge Penjual Sejak**: Menunjukkan tahun mulai berjualan

---

## 💾 Logika Penyimpanan Gambar

Gambar produk disimpan di folder `img/products/` dengan struktur:
```
img/
├── products/
│   ├── 1234567890_abc123.jpg    (seller user 1)
│   ├── 1234567891_def456.png    (seller user 2)
│   └── 1234567892_ghi789.jpg    (seller user 1)
└── (folder img lama)
```

**Fitur:**
- Filename otomatis (timestamp + unique ID) untuk menghindari duplikasi
- Validasi file type dan size
- Auto-delete gambar lama saat edit produk
- Fallback jika upload gagal

---

## 🔒 Keamanan & Ownership

Sistem memiliki validasi ownership:

### 1. **Hanya penjual sendiri yang bisa edit**
```php
// Di seller_edit_product.php
if ($product['seller_id'] != $seller_id) {
    // Akses ditolak
}
```

### 2. **Delete hanya bisa dilakukan owner**
```php
// Di seller_dashboard.php
if ($owner['seller_id'] == $seller_id) {
    // Boleh hapus
}
```

### 3. **Status Visibility**
- Produk dengan `status = 'active'` bisa dilihat pembeli
- Produk `status = 'inactive'` tidak muncul di dashboard pembeli
- Owner tetap bisa lihat semua produk mereka di seller dashboard

---

## 📊 Query Database untuk Penjualan

### Ambil produk pembeli (hanya active):
```sql
SELECT * FROM products 
WHERE status = 'active' 
ORDER BY created_at DESC
```

### Ambil produk seller tertentu:
```sql
SELECT * FROM products 
WHERE seller_id = 5 
ORDER BY created_at DESC
```

### Ambil top sellers:
```sql
SELECT seller_id, COUNT(*) as product_count 
FROM products 
WHERE status = 'active'
GROUP BY seller_id 
ORDER BY product_count DESC
```

---

## 🎨 UI/UX Features

### Seller Dashboard
- **Modern Design**: Gradient color scheme (emerald-teal)
- **Statistics Cards**: 4 card dengan info penjualan
- **Responsive Table**: Tabel produk dengan mobile support
- **Modal Form**: Form tambah produk dalam modal yang clean
- **Action Buttons**: Edit, Delete dengan icon yang jelas
- **Status Badge**: Visual indicator untuk status produk

### Form Validasi
- Required fields ditandai dengan asterisk
- Real-time feedback saat input
- File upload dengan preview
- Error handling yang user-friendly

---

## 🔄 Alur Transaksi Multi-Seller

```
┌─────────────────────────────────────┐
│      PEMBELI MEMBELI PRODUK          │
└──────────────┬──────────────────────┘
               │
         ┌─────▼─────┐
         │ Dashboard │ (Lihat semua produk dari seller)
         └─────┬─────┘
               │
         ┌─────▼──────┐
         │ Add Cart   │ (Pilih produk dari seller manapun)
         └─────┬──────┘
               │
         ┌─────▼────────┐
         │ Checkout     │ (Total dari berbagai seller)
         └─────┬────────┘
               │
    ┌──────────┴───────────┐
    │                      │
┌───▼──────┐         ┌────▼───────┐
│ Pembayaran│         │   Pesanan   │
└──────────┘         └─────────────┘

┌──────────────────────────────────┐
│   PENJUAL MENGELOLA TOKO          │
└──────────┬───────────────────────┘
           │
    ┌──────▼──────────────┐
    │ Seller Dashboard    │
    └──────┬───────────────┘
           │
      ┌────┴────────────────────────┐
      │                             │
┌─────▼────┐              ┌────────▼────┐
│ Tambah   │              │ Kelola      │
│ Produk   │              │ Produk      │
└──────────┘              └─────────────┘
      │                             │
      ├─── Upload Foto ────────────┤
      ├─── Set Harga + Diskon ─────┤
      └─── Manage Stock ───────────┘
```

---

## ⚙️ Konfigurasi & Customization

### Menambah Kategori Baru

Edit di `seller_dashboard.php` dan `seller_edit_product.php`:
```html
<option value="Kategori Baru">Kategori Baru</option>
```

### Mengubah Ukuran Max Upload

Edit di `seller_process.php`:
```php
// Current: 5MB
if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
    // Change 5 to desired size in MB
}
```

### Folder Penyimpanan Gambar

Default: `img/products/`

Untuk mengubah:
1. Edit `seller_process.php`:
```php
$upload_dir = 'img/your_new_folder/';  // Change this
```

2. Create folder di root:
```bash
mkdir img/your_new_folder
chmod 755 img/your_new_folder
```

---

## 🐛 Troubleshooting

### 1. Menu "Mulai Berjualan" tidak muncul
- **Cause**: User belum login
- **Fix**: Login dulu ke dashboard

### 2. Produk tidak muncul setelah upload
- **Cause**: Status produk = 'inactive' atau 'pending'
- **Fix**: Di seller dashboard, toggle status ke 'active'

### 3. Gambar tidak terupload
- **Cause**: Folder `img/products/` tidak exist
- **Fix**: Buat folder terlebih dulu:
```bash
mkdir img/products
chmod 755 img/products
```

### 4. Database error di dashboard pembeli
- **Cause**: Query database gagal (koneksi issue)
- **Fix**: System otomatis fallback ke array default. Check error log.

---

## 📈 Future Enhancement Ideas

- [ ] Fitur rating & review per seller
- [ ] Seller profile/toko online
- [ ] Admin approval untuk new sellers
- [ ] Komisi/revenue sharing system
- [ ] Order tracking per seller
- [ ] Seller performance metrics
- [ ] Bulk upload produk (CSV)
- [ ] Inventory management dengan notifikasi
- [ ] Seller chat with buyer
- [ ] Promotional tools (flash sale management)

---

## 📞 Support

Untuk masalah teknis:
1. Check error log di `php_errors.log`
2. Verify database connection di `db_config.php`
3. Check folder permissions untuk `img/products/`

---

**Version**: 1.0  
**Last Updated**: February 2026  
**Status**: ✅ Production Ready
