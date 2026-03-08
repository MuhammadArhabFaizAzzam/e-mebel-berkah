# 📝 Dokumentasi Sistem Autentikasi User

## Overview
Sistem login dan register user Berkah Mebel Ayu telah diperbaiki untuk menggunakan **database MySQL** sebagai sistem utama, dengan fallback ke file JSON jika database tidak tersedia.

---

## 🔄 Flow Autentikasi

### 1. **REGISTER (Daftar Akun Baru)**

**File: `register.php` → `register_process.php`**

```
User mengisi form daftar
    ↓
Form dikirim ke register_process.php
    ↓
Validasi (nama, email, password, terms)
    ↓
Database tersedia? 
    ├─ YA → Cek email di DB, hash password, INSERT ke users table
    │       ↓
    │       Auto-login user
    │       ↓
    │       Redirect ke dashboard.php
    │
    └─ TIDAK → Fallback ke users.json
              Cek email di JSON, hash password, SAVE ke file
              ↓
              Auto-login user
              ↓
              Redirect ke dashboard.php
```

**Validasi Register:**
- ✅ Nama min 3 karakter
- ✅ Email valid dan unik (tidak boleh duplikat)
- ✅ Password min 6 karakter
- ✅ Password harus sama dengan konfirmasi
- ✅ Must accept terms & conditions

**Auto-login:** Setelah register berhasil, user langsung login otomatis tanpa perlu mengisi form login lagi.

---

### 2. **LOGIN (Masuk ke Akun)**

**File: `login.php` → `auth.php`**

```
User mengisi email dan password
    ↓
Form dikirim ke auth.php
    ↓
Database tersedia?
    ├─ YA → Query users table WHERE email = ?
    │       Password cocok? (password_verify)
    │       ├─ YA → Set session, update last_login, redirect dashboard
    │       └─ TIDAK → Error message, redirect login
    │
    └─ TIDAK → Fallback ke users.json
              Cari email di array
              Password cocok?
              ├─ YA → Set session, redirect dashboard
              └─ TIDAK → Error message, redirect login
```

**Session Variables yang di-set:**
```php
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_phone'] = $user['phone']; // (database only)
$_SESSION['user_address'] = $user['address']; // (database only)
```

---

### 3. **LOGOUT (Keluar Akun)**

**File: `logout.php`**

```
User klik logout
    ↓
Clear semua $_SESSION variables
    ↓
session_destroy()
    ↓
Delete session cookie
    ↓
Redirect ke index.php
```

---

## 💾 Database Schema

### Tabel: `users`

```sql
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  phone VARCHAR(20),
  address TEXT,
  city VARCHAR(50),
  province VARCHAR(50),
  postal_code VARCHAR(10),
  profile_image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  is_active BOOLEAN DEFAULT TRUE
);
```

---

## 🚀 Setup Awal

### 1. **Jalankan Setup Database**
```bash
# Akses phpMyAdmin dan jalankan database_tables.sql
# atau gunakan command line
mysql -u root < database_tables.sql
```

### 2. **Setup User Demo**
Buka di browser:
```
http://localhost/e-commery/setup_users.php
```

Atau jalankan manual di database:
```sql
INSERT INTO users (name, email, phone, password, is_active, created_at, updated_at) 
VALUES 
('Admin Demo', 'demo@mebel.com', '08123456789', '$2y$10$...hashed_password...', 1, NOW(), NOW()),
('User Biasa', 'user@mebel.com', '08987654321', '$2y$10$...hashed_password...', 1, NOW(), NOW());
```

### 3. **Akun Demo yang Tersedia**

| Email | Password | Nama |
|-------|----------|------|
| demo@mebel.com | demo123 | Admin Demo |
| user@mebel.com | user123 | User Biasa |

---

## 🔒 Security Features

### ✅ Password Hashing
- Menggunakan `PASSWORD_DEFAULT` (bcrypt)
- Password di-hash saat register & simpan ke DB
- Verifikasi menggunakan `password_verify()`

### ✅ Session Management
- Session regenerate ID setelah login
- Session destroy saat logout
- Cookie session dihapus dengan benar

### ✅ Input Validation
- Email validation dengan `filter_var()`
- Password confirmation check
- HTML escape pada tampilan (`htmlspecialchars()`)

### ✅ SQL Injection Prevention
- Menggunakan prepared statements dengan `?` placeholders
- Parameter binding di `executeQuery()` dan `getQueryRow()`

---

## 📱 User Interface

### Register Page
- Form fields: Nama, Email, No. Telepon, Password, Konfirmasi Password
- Checkbox: Terms & Conditions
- Error messages yang jelas
- Link untuk login jika sudah punya akun

### Login Page
- Form fields: Email, Password
- Checkbox: Remember me (untuk implementasi future)
- Social login buttons (Google, Facebook) - simulator
- Link untuk register jika belum punya akun

### Protected Pages
- `dashboard.php` - User dashboard
- `settings.php` - User settings
- `cart.php` - Shopping cart
- `etc.` - Pages yang require login

---

## ⚠️ Fallback System (Offline Mode)

Jika database **tidak tersedia**, sistem akan fallback ke `users.json`:

```json
[
  {
    "id": 3,
    "name": "User Offline",
    "email": "offline@mebel.com",
    "phone": "089xxxx",
    "password": "$2y$10$...hashed...",
    "created_at": "2025-02-24 10:30:00"
  }
]
```

**Catatan:** 
- Data dari JSON hanya temporary (session-based)
- Saat database online lagi, otomatis prioritas ke database
- File JSON tidak diperlukan jika database selalu tersedia

---

## 🧪 Testing Checklist

- [ ] Login dengan akun demo berhasil
- [ ] Register akun baru berhasil
- [ ] Auto-login setelah register berfungsi
- [ ] Error message muncul untuk email duplikat
- [ ] Error message untuk password tidak cocok
- [ ] Logout menghapus session dengan benar
- [ ] Akses protected page tanpa login redirect ke login
- [ ] Remember me functionality (TODO)
- [ ] Forgot password (TODO)
- [ ] Social auth (TODO)

---

## 📋 File-file yang Diperbaiki

1. ✅ **auth.php** - Login processor (DB + Fallback)
2. ✅ **register.php** - Register UI (Added session check)
3. ✅ **register_process.php** - Register processor (DB + Fallback)
4. ✅ **logout.php** - Logout handler (Improved)
5. ✅ **login.php** - Login UI (Added session check)
6. ✨ **setup_users.php** - Setup demo users (NEW)

---

## 🔗 Related Files

- **db_config.php** - Database configuration & functions
- **dashboard.php** - User dashboard
- **settings.php** - User settings
- **cart.php** - Shopping cart

---

## 📞 Support

Jika ada masalah dengan autentikasi:
1. Cek apakah database tersambung
2. Cek file `db_config.php` untuk konfigurasi database
3. Cek table `users` sudah exist di database
4. Cek error log di browser console dan PHP error log

---

**Last Updated:** 24 Feb 2025
**Version:** 1.0
