# Panduan Deploy ke InfinityFree - Berkah Mebel Ayu

## Prerequisites
- Akun InfinityFree sudah aktif (if0_41076893)
- File proyek sudah ready

---

## Langkah 1: Login ke InfinityFree Control Panel

1. Buka: https://dash.infinityfree.com/accounts/if0_41076893
2. Login dengan email dan password kamu

---

## Langkah 2: Buat Database MySQL

1. Di dashboard InfinityFree, klik **"MySQL Databases"**
2. Buat database baru:
   - **Database Name**: `berkah_mebel_ayu` (nama sesuai keinginan)
   - **Username**: sama dengan username InfinityFree kamu
   - **Password**: buat password yang kuat
3. Catat informasi berikut:
   - Database Host: `sql312.infinityfree.com` (atau sesuai yang diberikan)
   - Database Name: `epiz_XXXXXXX_berkah_mebel_ayu`
   - Username: `epiz_XXXXXXX`
   - Password: (password yang kamu buat)

---

## Langkah 3: Upload File ke File Manager

1. Di InfinityFree Control Panel, klik **"File Manager"**
2. Navigate ke folder `htdocs`
3. Hapus semua file yang ada di dalam `htdocs` (kecuali .htaccess kalau ada)
4. Upload semua file proyek kamu:
   - Bisa upload satu per satu
   - Atau compress jadi ZIP, lalu upload dan extract

**File yang harus diupload:**
```
/ (root folder)
├── index.php
├── login.php
├── register.php
├── dashboard.php
├── cart.php
├── orders.php
├── wishlist.php
├── profile.php
├── settings.php
├── auth.php
├── logout.php
├── config.php
├── db_config.php
├── .htaccess (sudah kami buat)
├── setup_database.php (sudah kami buat)
├── database.sql
├── admin_dasboard.php
├── admin_process.php
├── seller_dashboard.php
├── seller_edit_product.php
├── seller_process.php
├── cart_process.php
├── profile_process.php
├── register_process.php
├── settings_process.php
├── manage_stock.php
├── api/
│   └── get_all_products.php
├── css/
│   └── style.css
├── js/
│   ├── main.js
│   └── banner.js
├── img/
│   └── (semua file gambar)
└── uploads/
    └── (file uploads jika ada)
```

---

## Langkah 4: Setup Database (GUNAKAN SCRIPT KAMI)

1. Buka browser dan akses:
   
```
   https://if0-41076893.epizy.com/setup_database.php
   
```
   
   *(Ganti `if0-41076893` dengan subdomain kamu)*

2. Isi formulir dengan data MySQL dari Langkah 2:
   - **MySQL Host**: `sql312.infinityfree.com` (atau sesuai instruksi)
   - **MySQL Username**: `epiz_XXXXXXX` (username kamu)
   - **MySQL Password**: (password yang kamu buat)
   - **Database Name**: `epiz_XXXXXXX_berkah_mebel_ayu`

3. Klik **"Setup Sekarang"**

4. Jika berhasil, akan muncul pesan sukses!

5. **PENTING**: Hapus file `setup_database.php` setelah setup selesai!
   - Buka File Manager
   - Cari `setup_database.php`
   - Delete

---

## Langkah 5: Verifikasi Deployment

1. Buka halaman utama:
   
```
   https://if0-41076893.epizy.com/
   
```

2. Test login dengan akun demo:
   - Email: `demo@mebel.com`
   - Password: `demo123`

3. Test registrasi akun baru

4. Test lihat produk, keranjang, dll

---

## Troubleshooting

### Error: "Connection failed"
- Cek username dan password MySQL
- Pastikan database sudah dibuat
- Cek host name (harus sesuai dengan di control panel)

### Error: "404 Not Found"
- Pastikan .htaccess sudah ter-upload
- Cek file index.php ada di folder htdocs

### Error: "403 Forbidden"
- Pastikan permissions file sudah benar
- Coba cek File Manager > Permissions

### Error: "500 Internal Server Error"
- Cek syntax PHP di .htaccess
- Cek error logs di File Manager

---

## Konfigurasi Tambahan (Opsional)

### Mengaktifkan HTTPS
1. Di InfinityFree Control Panel, cari **"SSL/TLS"** atau **"HTTPS"**
2. Aktifkan SSL gratis yang disediakan
3. Update .htaccess untuk force HTTPS

### Custom Domain (jika punya)
1. Klik **"Domains"** di control panel
2. Add domain baru
3. Point domain ke hosting ini

---

## Cara Update Website

1. Compress file yang mau diupload jadi ZIP
2. Buka File Manager > htdocs
3. Upload ZIP > Extract
4. Replace file yang perlu diupdate

---

## Dukungan

Jika ada pertanyaan:
- Email: admin@berkahmebelayu.com
- WA: +62 823-2729-4909

---

**Catatan**: 
- InfinityFree adalah hosting gratis dengan keterbatasan
- Jangan lupa backup secara berkala
- Hapus file sensitif seperti setup_database.php setelah digunakan
