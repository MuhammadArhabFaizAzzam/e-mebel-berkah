# ⚡ Quick Setup Guide - Multi-Seller System

## 🔧 Langkah Setup

### Step 1: Update Database Schema
Jalankan query SQL ini di phpMyAdmin atau MySQL:

```sql
-- Update products table untuk multi-seller
ALTER TABLE products ADD COLUMN seller_id INT;
ALTER TABLE products ADD COLUMN status ENUM('active', 'inactive', 'pending') DEFAULT 'active';
ALTER TABLE products ADD FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE;

-- Jika sudah ada products lama, set seller_id ke admin (user_id = 1)
UPDATE products SET seller_id = 1 WHERE seller_id IS NULL;
```

### Step 2: Check Folder Permissions
Pastikan folder ini exist dan writable:

```bash
# Windows (di terminal atau file explorer)
mkdir img\products
# atau check properties → Security → Edit

# Linux/Mac
mkdir -p img/products
chmod 755 img/products
```

### Step 3: Verify Database Connection
Check di `db_config.php` bahwa koneksi database OK:

```php
// db_config.php harus punya:
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

### Step 4: Test Functionality

#### Test 1: Login ke Dashboard
```
1. Login ke dashboard.php
2. Cek apakah menu "Mulai Berjualan" muncul (kanan atas, dropdown profil)
```

#### Test 2: Akses Seller Dashboard
```
1. Klik "Mulai Berjualan"
2. Harus redirect ke seller_dashboard.php
3. Lihat statistik toko kosong (karena produk baru)
```

#### Test 3: Upload Produk
```
1. Klik "Tambah Produk Baru"
2. Isi form:
   - Nama: "Meja Kerja Test"
   - Kategori: "Meja"
   - Harga: 500000
   - Stok: 5
   - Foto: upload test image
3. Klik "Upload Produk"
4. Harus kembali ke dashboard dan muncul di tabel
```

#### Test 4: Edit Produk
```
1. Di seller dashboard, klik icon edit (pensil)
2. Ubah beberapa field (nama, harga, dll)
3. Klik "Simpan Perubahan"
4. Harus update di tabel
```

#### Test 5: Lihat di Dashboard Pembeli
```
1. Klik "Kembali Belanja"
2. Produk yang diupload harusnya muncul di dashboard
3. Bisa dicari by category atau ditambah ke cart
```

#### Test 6: Toggle Status Produk
```
1. Di seller dashboard, klik tombol status (Aktif/Nonaktif)
2. Jika inactive, produk tidak tampil di dashboard pembeli
3. Klik lagi untuk aktifkan kembali
```

#### Test 7: Delete Produk
```
1. Di seller dashboard, klik icon trash (sampah)
2. Confirm delete
3. Produk dan gambarnya harus terhapus
```

---

## 📋 Checklist Implementasi

- [ ] Database updated dengan seller_id dan status
- [ ] Folder `img/products/` created dengan permission 755
- [ ] Menu "Mulai Berjualan" muncul di dashboard
- [ ] seller_dashboard.php accessible
- [ ] seller_process.php handle uploads
- [ ] seller_edit_product.php accessible
- [ ] Produk upload berhasil disimpan
- [ ] Gambar upload disimpan ke `img/products/`
- [ ] Produk muncul di dashboard pembeli (saat status='active')
- [ ] Edit produk working
- [ ] Delete produk working
- [ ] Status toggle working
- [ ] Pagination untuk multiple products

---

## 🎨 Customization Options

### 1. Ubah Warna Theme Seller
File: `seller_dashboard.php`, `seller_edit_product.php`

Current: `from-emerald-700 to-teal-700`

Options:
```
- Blue: from-blue-700 to-cyan-700
- Purple: from-purple-700 to-pink-700
- Orange: from-orange-700 to-red-700
- Green: from-green-700 to-lime-700
```

### 2. Ubah Ukuran Max Upload
File: `seller_process.php` (line ~95 dan ~210)

```php
// Current: 5MB
if ($_FILES['image']['size'] > 5 * 1024 * 1024) {
    // Change 5 to desired MB
}
```

### 3. Tambah Kategori Produk
File: `seller_dashboard.php` (line ~365) dan `seller_edit_product.php` (line ~100)

```html
<!-- Add new option -->
<option value="Nama Kategori Baru">Nama Kategori Baru</option>
```

---

## 🚨 Common Issues & Fixes

### Issue 1: "Cannot write to directory"
```
Error: Failed to upload image
Cause: img/products folder tidak writable
Fix: 
1. Right-click folder → Properties → Security
2. Edit → add write permission untuk SYSTEM
3. Atau via command: icacls "img\products" /grant:r %username%:F
```

### Issue 2: "Produk tidak muncul setelah upload"
```
Cause: Status produk = 'inactive'
Fix: 
1. Di seller dashboard, cek status produk
2. Klik tombol status untuk aktifkan
3. Atau langsung query: UPDATE products SET status='active' WHERE id=X
```

### Issue 3: "File type not supported"
```
Cause: Upload file bukan gambar (harus JPG/PNG/GIF)
Fix:
1. Gunakan format: JPG, JPEG, PNG, GIF
2. Ukuran max 5MB
3. Pastikan file extension benar
```

### Issue 4: "Database connection error"
```
Cause: MySQL tidak running atau config salah
Fix:
1. Start XAMPP MySQL
2. Check db_config.php settings
3. Verify username/password/database name
4. System akan fallback ke array default jika error
```

### Issue 5: "Permission Denied when creating folder"
```
Windows: mkdir img\products
Linux: mkdir -p img/products && chmod 755 img/products
macOS: mkdir -p img/products && chmod 755 img/products
```

---

## 📊 Database Query Reference

### Lihat semua produk aktif:
```sql
SELECT * FROM products WHERE status = 'active';
```

### Lihat produk dari seller tertentu:
```sql
SELECT * FROM products WHERE seller_id = 5;
```

### Lihat statistik per seller:
```sql
SELECT 
    seller_id, 
    COUNT(*) as total_products,
    SUM(stock) as total_stock,
    COUNT(CASE WHEN status='active' THEN 1 END) as active_products
FROM products 
GROUP BY seller_id;
```

### Reset seller_id jika NULL:
```sql
UPDATE products SET seller_id = 1 WHERE seller_id IS NULL;
```

### Set semua produk jadi active:
```sql
UPDATE products SET status = 'active' WHERE status != 'active';
```

---

## 🔐 Security Notes

1. **File Upload Validation**
   - Only JPG/PNG/GIF allowed
   - Max 5MB per file
   - Filename sanitized (timestamp + random)

2. **Ownership Check**
   - Hanya pemilik produk bisa edit/delete
   - Query dengan seller_id validation

3. **Database Injection Prevention**
   - Semua query pakai prepared statement
   - Input validation & sanitization

4. **Status Control**
   - Inactive produk tidak tampil ke pembeli
   - Owner tetap bisa manage semua produk mereka

---

## 📚 File Structure

```
e-commery/
├── seller_dashboard.php          ✨ Main seller interface
├── seller_process.php            🔧 Backend logic
├── seller_edit_product.php       ✏️ Edit form
├── dashboard.php                 📝 Buyer dashboard (updated)
├── index.php                     🏠 Landing page (updated)
├── database.sql                  🗄️ Schema (updated)
├── SELLER_SYSTEM_GUIDE.md        📖 Full documentation
├── SETUP_GUIDE.md                ⚡ This file
└── img/
    └── products/                 📸 User product images (new folder)
```

---

## ✅ Verification Checklist

After setup, verify:

- [ ] Database has `seller_id` column in products table
- [ ] Database has `status` column in products table
- [ ] `img/products/` folder exists and is writable
- [ ] Can login to dashboard.php
- [ ] "Mulai Berjualan" menu shows in profile dropdown
- [ ] Can access seller_dashboard.php
- [ ] Can upload product with image
- [ ] Image file saved to `img/products/`
- [ ] Product appears in database
- [ ] Product visible in buyer dashboard (if status='active')
- [ ] Can edit product details
- [ ] Can delete product (removes image too)
- [ ] Can toggle product status
- [ ] Multiple sellers can upload products
- [ ] All products from all sellers show in buyer dashboard

---

**Ready to go live!** 🚀

For more details, see: `SELLER_SYSTEM_GUIDE.md`
