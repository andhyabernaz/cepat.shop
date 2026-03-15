# 📦 Panduan Deployment Cepatshop ke cPanel

> **Dokumen ini** berisi panduan lengkap untuk men-deploy aplikasi **Cepatshop** (Laravel 10 / PHP 8.1) ke hosting cPanel secara live. Ikuti setiap langkah secara berurutan untuk hasil deployment yang sukses.

---

## 📋 Daftar Isi

1. [Prasyarat & Persiapan](#1--prasyarat--persiapan)
2. [Persiapan File Lokal](#2--persiapan-file-lokal)
3. [Upload File ke Hosting](#3--upload-file-ke-hosting)
4. [Membuat Database di cPanel](#4--membuat-database-di-cpanel)
5. [Import Database via phpMyAdmin](#5--import-database-via-phpmyadmin)
6. [Konfigurasi Environment (.env)](#6--konfigurasi-environment-env)
7. [Konfigurasi .htaccess](#7--konfigurasi-htaccess)
8. [Pengaturan Domain & Subdomain](#8--pengaturan-domain--subdomain)
9. [Konfigurasi Permission & Storage](#9--konfigurasi-permission--storage)
10. [Verifikasi Deployment](#10--verifikasi-deployment)
11. [Troubleshooting Error Umum](#11--troubleshooting-error-umum)
12. [Checklist Validasi Production](#12--checklist-validasi-production)

---

## 1. 🔧 Prasyarat & Persiapan

### Persyaratan Hosting

| Komponen        | Minimum        | Direkomendasikan |
|-----------------|----------------|------------------|
| PHP             | 8.1            | 8.1 – 8.2       |
| MySQL           | 5.7            | 8.0+             |
| Disk Space      | 500 MB         | 1 GB+            |
| RAM             | 256 MB         | 512 MB+          |
| mod_rewrite     | ✅ Aktif        | ✅ Aktif          |
| SSL Certificate | Opsional       | ✅ Aktif (HTTPS)  |

### Ekstensi PHP yang Diperlukan

Pastikan ekstensi berikut **aktif** di cPanel → **Select PHP Version**:

```
✅ BCMath         ✅ Ctype          ✅ cURL
✅ DOM            ✅ Fileinfo       ✅ JSON
✅ Mbstring       ✅ OpenSSL        ✅ PDO
✅ PDO_MySQL      ✅ Tokenizer      ✅ XML
✅ GD / Imagick   ✅ Zip            ✅ Intl
```

> [!IMPORTANT]
> **Cepatshop membutuhkan PHP 8.1 atau lebih baru.** Pastikan versi PHP di hosting sudah benar sebelum mulai deployment.

### Apa yang Perlu Disiapkan

- [x] Akses ke **cPanel Hosting** (URL, username, password)
- [x] **Domain** sudah terhubung/pointed ke hosting
- [x] Kode sumber **Cepatshop** sudah final dan teruji di lokal
- [x] File database: `cepatshop_starter.sql` atau `cepatshop_starter_with_demo.sql`
- [x] Software FTP (opsional): FileZilla, WinSCP, atau sejenisnya

---

## 2. 📁 Persiapan File Lokal

### 2.1 Struktur Proyek Cepatshop

Berikut adalah struktur folder proyek yang perlu dipahami:

```
cepat.shop/
├── app/                    ← Logic aplikasi (Controllers, Models, dll)
├── bootstrap/              ← File bootstrap Laravel
├── cepatshop/              ← Modul custom Cepatshop
├── config/                 ← File konfigurasi
│   ├── app.php
│   ├── database.php
│   ├── midtrans.php        ← Konfigurasi payment gateway
│   ├── duitku.php          ← Konfigurasi payment gateway
│   └── rajaongkir.php      ← Konfigurasi ongkos kirim
├── database/
│   ├── migrations/         ← 151 file migration
│   ├── cepatshop_starter.sql
│   └── cepatshop_starter_with_demo.sql
├── lang/                   ← File bahasa
├── public/                 ← ⚡ DOCUMENT ROOT (index.php ada di sini)
│   ├── index.php
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   ├── fonts/
│   ├── favicon.ico
│   └── upload/
├── resources/              ← Views dan assets mentah
├── routes/                 ← Routing aplikasi
│   ├── web.php
│   ├── api.php
│   └── cepatshop/          ← Routes modul Cepatshop
├── storage/                ← File cache, logs, uploads
├── vendor/                 ← Dependencies Composer
├── .env                    ← ⚠️ Konfigurasi environment
├── .htaccess               ← Redirect root → public/
├── artisan                 ← CLI Laravel
├── composer.json           ← Dependencies list
└── composer.lock
```

### 2.2 Optimasi Sebelum Upload

Jalankan perintah berikut di **command prompt lokal** sebelum upload:

```bash
# 1. Install dependencies production only
composer install --optimize-autoloader --no-dev

# Catatan: aplikasi tetap bisa berjalan tanpa paket dev. Debugbar hanya aktif jika APP_DEBUG=true dan paketnya terpasang.

# 2. Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 3. Optimasi untuk production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2.3 Buat Arsip ZIP

Buat file ZIP dari seluruh folder proyek. **Pastikan folder `vendor/` ikut di-ZIP** karena kita tidak bisa menjalankan `composer install` dengan mudah di shared hosting.

> [!WARNING]
> **Jangan ZIP folder `.git/`** — folder ini tidak diperlukan di production dan ukurannya bisa sangat besar.

```
Cara membuat ZIP:
1. Buka folder cepat.shop di File Explorer
2. Pilih semua file dan folder KECUALI .git/
3. Klik kanan → "Compress to ZIP file"
4. Beri nama: cepatshop-production.zip
```

---

## 3. 📤 Upload File ke Hosting

Anda bisa upload file menggunakan **File Manager cPanel** atau **FTP Client**.

### Metode A: Via File Manager cPanel (Direkomendasikan)

**Langkah-langkah:**

1. **Login ke cPanel** hosting Anda melalui browser
   - URL biasanya: `https://namadomainanda.com:2083` atau `https://namadomainanda.com/cpanel`

2. **Buka File Manager** pada bagian "Files"

3. **Navigasi ke `public_html`**
   - Klik folder `public_html` di sidebar kiri

4. **Upload file ZIP**
   - Klik tombol **"Upload"** di toolbar atas
   - Pilih file `cepatshop-production.zip` dari komputer Anda
   - Tunggu proses upload selesai (tergantung ukuran file & kecepatan internet)

5. **Extract file ZIP**
   - Klik kanan pada file ZIP yang sudah terupload
   - Pilih **"Extract"**
   - Pastikan extract ke lokasi yang benar (lihat Langkah 6)

6. **Atur struktur folder** ← ⚡ **INI LANGKAH KRITIS**

   Ada **2 metode** untuk mengatur struktur folder:

---

#### 📌 Metode 1: Semua File di `public_html` (Mudah, tapi Kurang Aman)

Pindahkan **semua isi** folder `public/` ke `public_html/`, dan letakkan folder lainnya di **satu level di atas** `public_html/`:

```
/home/username/
├── cepatshop/                    ← Folder baru di luar public_html
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── lang/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   ├── artisan
│   ├── composer.json
│   └── composer.lock
│
└── public_html/                  ← Document Root
    ├── index.php                 ← ⚠️ PERLU DIEDIT (lihat Langkah 7)
    ├── .htaccess
    ├── css/
    ├── js/
    ├── fonts/
    ├── favicon.ico
    ├── upload/
    └── storage → ../cepatshop/storage/app/public (symlink)
```

> [!CAUTION]
> Jika menggunakan metode ini, Anda **HARUS** mengedit file `public_html/index.php` agar menunjuk ke lokasi baru. Lihat [Bagian 7.2](#72-edit-indexphp-metode-1).

---

#### 📌 Metode 2: Seluruh Proyek di `public_html` (Paling Mudah)

Letakkan **seluruh proyek** di dalam `public_html/` dan gunakan `.htaccess` root untuk redirect ke folder `public/`:

```
/home/username/public_html/
├── app/
├── bootstrap/
├── cepatshop/
├── config/
├── database/
├── lang/
├── public/                       ← Document root sebenarnya
│   ├── index.php
│   ├── .htaccess
│   ├── css/
│   ├── js/
│   └── ...
├── resources/
├── routes/
├── storage/
├── vendor/
├── .env                          ← ⚠️ Harus dikonfigurasi
├── .htaccess                     ← Redirect ke public/
├── artisan
├── composer.json
└── composer.lock
```

> [!TIP]
> **Metode 2 lebih mudah** karena tidak perlu mengedit `index.php`. File `.htaccess` di root sudah menghandle redirect ke folder `public/`.

---

### Metode B: Via FTP Client (FileZilla)

1. **Buka FileZilla** dan isi koneksi:

   | Field    | Nilai                            |
   |----------|----------------------------------|
   | Host     | `ftp.namadomainanda.com` atau IP |
   | Username | Username FTP dari cPanel         |
   | Password | Password FTP                     |
   | Port     | `21` (FTP) atau `990` (FTPS)     |

2. **Klik "Quickconnect"**

3. **Navigasi** ke folder `public_html/` di panel kanan (Remote site)

4. **Upload** semua file dari panel kiri (Local site) ke panel kanan

5. Tunggu proses upload selesai

> [!NOTE]
> Upload via FTP biasanya **lebih lambat** dibanding upload ZIP + Extract via File Manager. Gunakan FTP hanya jika perlu upload file individual.

---

### Metode C: Deploy Otomatis via cPanel Git (Opsional)

Jika hosting Anda menyediakan fitur **Git Version Control** (dengan deployment), repository ini sudah menyiapkan file [.cpanel.yml](file:///c:/xampp/htdocs/cepat/.cpanel.yml) sebagai contoh task deploy.

Catatan:
- Path deploy default diarahkan ke `$HOME/public_html/` dan perlu Anda sesuaikan jika document root berbeda.
- File `.env` sengaja tidak ikut dicopy agar konfigurasi production tidak tertimpa saat deploy.

## 4. 🗄️ Membuat Database di cPanel

### 4.1 Buat Database Baru

1. Login ke **cPanel**
2. Cari dan klik **"MySQL® Database Wizard"** di bagian "Databases"
3. **Step 1 — Create A Database:**
   - Masukkan nama database: `cepatshop`
   - Klik **"Next Step"**
   - Nama lengkap akan menjadi: `username_cepatshop`

4. **Step 2 — Create Database Users:**
   - Username: `cepatshop` (atau nama pilihan Anda)
   - Password: Gunakan **Password Generator** untuk password yang kuat
   - **⚠️ CATAT PASSWORD INI** — akan digunakan di file `.env`
   - Klik **"Create User"**

5. **Step 3 — Add User to Database:**
   - Centang **"ALL PRIVILEGES"**
   - Klik **"Next Step"**

> [!IMPORTANT]
> **Catat informasi berikut** — akan digunakan untuk konfigurasi `.env`:
> - Nama Database: `username_cepatshop`
> - Username Database: `username_cepatshop`
> - Password Database: `(password yang Anda buat)`
> - Host Database: `localhost` (biasanya default di shared hosting)

---

## 5. 📥 Import Database via phpMyAdmin

### 5.1 Persiapkan File SQL

Proyek Cepatshop sudah menyertakan file SQL yang siap diimport:

| File                              | Keterangan           |
|-----------------------------------|----------------------|
| `cepatshop_starter.sql`           | Database kosong      |
| `cepatshop_starter_with_demo.sql` | Database + data demo |

> [!TIP]
> Untuk deployment pertama kali, gunakan `cepatshop_starter_with_demo.sql` agar bisa langsung melihat contoh data.

### 5.2 Langkah Import

1. Di cPanel, klik **"phpMyAdmin"** pada bagian "Databases"

2. **Pilih database** yang baru dibuat di sidebar kiri (misal: `username_cepatshop`)

3. Klik tab **"Import"** di bagian atas

4. Klik **"Choose File"** / **"Pilih File"**
   - Pilih file `cepatshop_starter.sql` atau `cepatshop_starter_with_demo.sql`

5. Pastikan pengaturan berikut:
   ```
   Format:              SQL
   Character set:       utf-8
   SQL compatibility:   NONE
   ```

6. Klik tombol **"Import"** / **"Go"**

7. Tunggu proses selesai — akan muncul pesan sukses berwarna **hijau**

> [!WARNING]
> Jika file SQL terlalu besar (> 50MB), Anda mungkin perlu:
> - Split file SQL menggunakan tool seperti **SQLDumpSplitter**
> - Import via **SSH** dengan perintah: `mysql -u username -p database_name < file.sql`
> - Hubungi provider hosting untuk meningkatkan limit upload

---

## 6. ⚙️ Konfigurasi Environment (.env)

### 6.1 Edit File .env

Buka file `.env` melalui **File Manager cPanel** (klik kanan → Edit), lalu sesuaikan konfigurasi berikut:

```env
# ================================
# KONFIGURASI APLIKASI
# ================================
APP_NAME="Cepatshop App"
APP_ENV=production
APP_DEBUG=false                    # ⚠️ HARUS false di production!
APP_KEY=base64:88FFNy5Sr5sfeZX6a3lR9Z9pGN03MOvG2ZA+z/zJ5As=
APP_URL=https://namadomainanda.com
ASSET_URL=https://namadomainanda.com
APP_INSTALLED=true

SECRET_KEY=c98b2511b5a3c6ee692cc848cf

# ================================
# KONFIGURASI DATABASE
# ================================
DB_CONNECTION=mysql
DB_HOST=localhost                   # Biasanya localhost di shared hosting
DB_PORT=3306
DB_DATABASE=username_cepatshop     # ← Ganti dengan nama database lengkap
DB_USERNAME=username_cepatshop     # ← Ganti dengan username database
DB_PASSWORD=PASSWORD_ANDA          # ← Ganti dengan password database

# ================================
# KONFIGURASI CACHE & SESSION
# ================================
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
BROADCAST_DRIVER=log
FILESYSTEM_DISK=local
SESSION_LIFETIME=120

# ================================
# KONFIGURASI LOG
# ================================
LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error                    # Di production, gunakan 'error' bukan 'debug'

# ================================
# KONFIGURASI MAIL (Sesuaikan)
# ================================
MAIL_MAILER=smtp
MAIL_HOST=mail.namadomainanda.com  # Sesuaikan dengan hosting
MAIL_PORT=465
MAIL_USERNAME=info@namadomainanda.com
MAIL_PASSWORD=PASSWORD_EMAIL_ANDA
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="info@namadomainanda.com"
MAIL_FROM_NAME="${APP_NAME}"

# ================================
# FORCE DELETE (Keamanan)
# ================================
FORCE_USER_DELETE=false
```

### 6.2 Hal yang WAJIB Diubah

| Variable        | Dari (Lokal)                           | Ke (Production)                |
|-----------------|----------------------------------------|--------------------------------|
| `APP_ENV`       | `local`                               | `production`                   |
| `APP_DEBUG`     | `true`                                 | `false`                        |
| `APP_URL`       | `http://localhost/cepat`               | `https://namadomainanda.com`   |
| `ASSET_URL`     | `http://localhost/cepat`               | `https://namadomainanda.com`   |
| `DB_HOST`       | `127.0.0.1`                            | `localhost`                    |
| `DB_DATABASE`   | `cepatshop`                            | `username_cepatshop`           |
| `DB_USERNAME`   | `root`                                 | `username_cepatshop`           |
| `DB_PASSWORD`   | *(kosong)*                             | `password_database_anda`       |
| `LOG_LEVEL`     | `debug`                                | `error`                        |

> [!TIP]
> Jika aplikasi dipasang di subfolder (contoh `https://namadomainanda.com/cepat`), set `APP_URL` dan `ASSET_URL` ke URL lengkap tersebut (termasuk path `/cepat`).

> [!CAUTION]
> **`APP_DEBUG=true` di production adalah RISIKO KEAMANAN BESAR!** Informasi sensitif seperti password database, API keys, dan stack traces bisa terekspos ke pengunjung.

---

## 7. 🔀 Konfigurasi .htaccess

### 7.1 File .htaccess Root (Redirect ke Public)

Jika menggunakan **Metode 2** (seluruh proyek di `public_html`), file `.htaccess` di root sudah benar:

```apache
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

> File ini berfungsi mengarahkan **semua request** dari root ke folder `public/`.

### 7.2 Edit `index.php` (Metode 1)

Jika menggunakan **Metode 1** (file public di `public_html`, sisanya di folder terpisah), edit file `public_html/index.php`:

**Cari baris-baris berikut:**
```php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
```

**Ubah menjadi** (sesuaikan path ke lokasi folder proyek):
```php
require __DIR__.'/../cepatshop/vendor/autoload.php';
$app = require_once __DIR__.'/../cepatshop/bootstrap/app.php';
```

### 7.3 File .htaccess di `public/` (Rewrite Rules)

File ini **sudah benar** dan tidak perlu diubah:

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

> [!NOTE]
> Di shared hosting, umumnya tidak bisa mematikan WAF/mod_security via `.htaccess`. Jika ada request yang diblokir, minta provider untuk whitelist/disable rule yang terkait.

### 7.4 Tambahan: Paksa HTTPS (Opsional tapi Direkomendasikan)

Tambahkan baris berikut di **awal** file `.htaccess` di folder `public/` (sebelum `RewriteEngine On`):

```apache
# Force HTTPS
RewriteCond %{HTTPS} !=on
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

**File lengkap setelah ditambahkan:**

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Force HTTPS
    RewriteCond %{HTTPS} !=on
    RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

---

## 8. 🌐 Pengaturan Domain & Subdomain

### 8.1 Domain Utama

Domain utama biasanya sudah otomatis mengarah ke `public_html/`. Pastikan:

1. **DNS A Record** mengarah ke IP server hosting
2. **Nameservers** sudah diatur ke nameserver hosting Anda

Cek di cPanel → **"Zone Editor"** / **"DNS Zone Editor"**:

```
Tipe    Nama                    Nilai
A       namadomainanda.com      IP_SERVER_ANDA
A       www.namadomainanda.com  IP_SERVER_ANDA
```

### 8.2 Membuat Subdomain (Opsional)

Jika ingin deploy di subdomain (misal: `shop.namadomainanda.com`):

1. Di cPanel, buka **"Subdomains"** atau **"Domains"**
2. Klik **"Create A New Domain"** (cPanel terbaru) atau **"Create"** (cPanel lama)
3. Isi form:

   | Field             | Nilai                                     |
   |-------------------|-------------------------------------------|
   | Domain            | `shop.namadomainanda.com`                 |
   | Document Root     | `/public_html/shop` atau path custom      |
   | Share doc root    | ❌ Jangan dicentang                        |

4. Klik **"Submit"**
5. Upload file Cepatshop ke document root subdomain tersebut

### 8.3 Addon Domain (Domain Terpisah)

Jika menggunakan domain yang berbeda (misal: `cepat.shop`):

1. Di cPanel, buka **"Domains"** → **"Create A New Domain"**
2. Masukkan nama domain: `cepat.shop`
3. **Uncheck** "Share document root"
4. Atur document root ke: `/public_html/cepatshop` (atau custom)
5. Arahkan **DNS/Nameserver** domain `cepat.shop` ke hosting yang sama

---

## 9. 🔐 Konfigurasi Permission & Storage

### 9.1 Set Permission Folder

Jalankan perintah berikut via **cPanel Terminal** atau **SSH**:

```bash
# Set permission untuk folder storage
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/

# Set permission untuk folder upload
chmod -R 775 public/upload/
```

Atau jika tidak ada akses SSH, gunakan **File Manager**:
1. Klik kanan folder `storage/` → **"Change Permissions"**
2. Set permission ke `0775` (rwxrwxr-x)
3. Centang **"Recurse into subdirectories"**
4. Ulangi untuk folder `bootstrap/cache/` dan `public/upload/`

### 9.2 Buat Storage Link

Laravel membutuhkan symbolic link dari `public/storage` ke `storage/app/public`.

**Via SSH/Terminal:**
```bash
php artisan storage:link
```

**Tanpa SSH** (manual via File Manager):
1. Buat file PHP sementara di `public_html/` atau `public/`:

```php
<?php
// File: create-storage-link.php
// HAPUS FILE INI SETELAH DIJALANKAN!

$target = __DIR__ . '/../storage/app/public';
$link = __DIR__ . '/storage';

// Untuk Metode 1 (terpisah):
// $target = __DIR__ . '/../cepatshop/storage/app/public';

if (file_exists($link)) {
    echo "Symlink sudah ada!";
} else {
    if (symlink($target, $link)) {
        echo "✅ Symlink berhasil dibuat!";
    } else {
        echo "❌ Gagal membuat symlink. Coba via SSH.";
    }
}
```

2. Akses via browser: `https://namadomainanda.com/create-storage-link.php`
3. **⚠️ HAPUS file ini setelah selesai!**

### 9.3 Clear Cache di Production

Jika ada akses SSH/Terminal, jalankan:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**Tanpa SSH** (buat file PHP sementara):

```php
<?php
// File: clear-cache.php
// HAPUS FILE INI SETELAH DIJALANKAN!

require __DIR__ . '/../vendor/autoload.php';
// Untuk Metode 1: require __DIR__ . '/../cepatshop/vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
// Untuk Metode 1: $app = require_once __DIR__ . '/../cepatshop/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->call('config:cache');
$kernel->call('route:cache');
$kernel->call('view:cache');

echo "✅ Cache berhasil di-generate!";
```

> [!CAUTION]
> **SELALU HAPUS file PHP utility** (`create-storage-link.php`, `clear-cache.php`) **setelah digunakan!** File-file ini bisa menjadi celah keamanan jika dibiarkan.

---

## 10. ✅ Verifikasi Deployment

### 10.1 Tes Halaman Utama

1. Buka browser dan akses: `https://namadomainanda.com`
2. Pastikan halaman utama Cepatshop tampil dengan benar
3. Periksa:
   - ✅ Layout dan CSS ter-load dengan benar
   - ✅ Gambar/logo tampil
   - ✅ JavaScript berfungsi (menu, slider, dll.)
   - ✅ Tidak ada error di Console (F12 → Console)

### 10.2 Tes Panel Admin

1. Akses: `https://namadomainanda.com/admin`
2. Login dengan akun admin
3. Periksa:
   - ✅ Halaman dashboard tampil
   - ✅ Menu navigasi berfungsi
   - ✅ Data di database ter-load

### 10.3 Tes Fungsionalitas

| Fitur                  | URL/Cara Tes                  | Expected Result   |
|------------------------|-------------------------------|-------------------|
| Homepage               | `/`                           | Tampil dengan baik|
| Login Admin            | `/admin`                      | Form login muncul |
| Registrasi             | `/register`                   | Form register muncul|
| Katalog Produk         | `/products` atau sejenisnya   | List produk tampil|
| Upload Gambar          | Upload via admin panel        | File tersimpan    |
| API Endpoint           | `/api/...`                    | Response JSON     |

### 10.4 Cek Error Log

Periksa file log di `storage/logs/laravel.log`:
- Via File Manager: navigasi ke `storage/logs/`
- Jika banyak error, cek dan perbaiki satu per satu

---

## 11. 🚨 Troubleshooting Error Umum

### ❌ Error 500 — Internal Server Error

**Penyebab & Solusi:**

| # | Penyebab | Solusi |
|---|----------|--------|
| 1 | File `.env` tidak ada atau salah | Pastikan file `.env` ada di root proyek dan konfigurasi benar |
| 2 | `APP_KEY` kosong | Jalankan `php artisan key:generate` atau salin dari `.env.example` |
| 3 | Permission folder salah | Jalankan `chmod -R 775 storage/ bootstrap/cache/` |
| 4 | PHP version terlalu rendah | Ubah ke PHP 8.1+ via cPanel → Select PHP Version |
| 5 | Ekstensi PHP tidak aktif | Aktifkan ekstensi yang diperlukan (lihat Bagian 1) |
| 6 | `vendor/` tidak ada | Upload folder `vendor/` atau jalankan `composer install` |
| 7 | `.htaccess` bermasalah | Pastikan `mod_rewrite` aktif di server |
| 8 | WAF / mod_security memblokir request | Minta provider hosting whitelist/disable rule yang memblokir (umumnya tidak bisa dimatikan via `.htaccess` di shared hosting) |

**Cara Debug:**
```bash
# Aktifkan sementara debug mode untuk melihat error detail:
# Di .env, ubah:
APP_DEBUG=true

# Setelah debug selesai, KEMBALIKAN ke:
APP_DEBUG=false
```

---

### ❌ Connection Timeout / Database Error

**Penyebab & Solusi:**

| # | Penyebab | Solusi |
|---|----------|--------|
| 1 | Nama database salah | Pastikan format: `username_namadb` |
| 2 | Username database salah | Pastikan format: `username_userdb` |
| 3 | Password salah | Cek ulang password di MySQL Databases cPanel |
| 4 | DB_HOST salah | Biasanya `localhost`, bukan `127.0.0.1` |
| 5 | User belum di-assign ke database | Cek di cPanel → MySQL Databases → Add User to Database |
| 6 | Database belum diimport | Import SQL via phpMyAdmin |
| 7 | Port MySQL salah | Pastikan `DB_PORT=3306` |

**Cara Tes Koneksi Database:**

Buat file temporary `test-db.php` di `public/` atau `public_html/`:

```php
<?php
// HAPUS FILE INI SETELAH DIGUNAKAN!
$host = 'localhost';
$db   = 'username_cepatshop';
$user = 'username_cepatshop';
$pass = 'PASSWORD_ANDA';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    echo "✅ Koneksi database BERHASIL!<br>";
    echo "Server: " . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "<br>";
    echo "Jumlah tabel: " . $pdo->query("SHOW TABLES")->rowCount();
} catch (PDOException $e) {
    echo "❌ GAGAL: " . $e->getMessage();
}
```

---

### ❌ Permission Issues (403 Forbidden)

**Penyebab & Solusi:**

| # | Penyebab | Solusi |
|---|----------|--------|
| 1 | Permission file terlalu ketat | File: `644`, Folder: `755` |
| 2 | Ownership salah | Pastikan file dimiliki oleh user cPanel |
| 3 | `Options -Indexes` aktif | Ini normal — mencegah directory listing |
| 4 | `storage/` tidak writable | `chmod -R 775 storage/` |
| 5 | `.htaccess` salah sintaks | Cek error log Apache |

**Permission yang benar:**

```bash
# File biasa
find /home/username/public_html -type f -exec chmod 644 {} \;

# Folder biasa
find /home/username/public_html -type d -exec chmod 755 {} \;

# Folder yang perlu writable
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
chmod -R 775 public/upload/
```

---

### ❌ Asset Tidak Ter-load (CSS/JS/Gambar 404)

**Penyebab & Solusi:**

| # | Penyebab | Solusi |
|---|----------|--------|
| 1 | `APP_URL` salah | Sesuaikan di `.env` |
| 2 | `ASSET_URL` salah | Sesuaikan di `.env` |
| 3 | Path asset case-sensitive | Linux case-sensitive, pastikan huruf besar/kecil benar |
| 4 | Folder `public/css`, `public/js` tidak ada | Upload ulang folder assets |
| 5 | Cache konfigurasi lama | Jalankan `php artisan config:cache` |

---

### ❌ Route Tidak Ditemukan (404 Not Found)

| # | Penyebab | Solusi |
|---|----------|--------|
| 1 | `mod_rewrite` tidak aktif | Hubungi hosting untuk mengaktifkan |
| 2 | `.htaccess` tidak di-proses | Cek `AllowOverride All` di konfigurasi Apache |
| 3 | Route cache outdated | Jalankan `php artisan route:cache` |
| 4 | Symlink storage belum dibuat | Buat via `php artisan storage:link` |

---

### ❌ Upload File Gagal

| # | Penyebab | Solusi |
|---|----------|--------|
| 1 | `upload_max_filesize` terlalu kecil | Tambah di `.htaccess`: `php_value upload_max_filesize 64M` |
| 2 | `post_max_size` terlalu kecil | Tambah: `php_value post_max_size 64M` |
| 3 | Permission folder upload | `chmod -R 775 public/upload/` |
| 4 | Storage link belum dibuat | Buat symlink storage |

Tambahkan di `.htaccess` (folder public):
```apache
# Increase upload limits
php_value upload_max_filesize 64M
php_value post_max_size 64M
php_value max_execution_time 300
php_value max_input_time 300
```

---

## 12. 📝 Checklist Validasi Production

Gunakan checklist berikut untuk memastikan deployment berhasil:

### 🔧 Konfigurasi Server

- [ ] PHP versi 8.1+ aktif
- [ ] Ekstensi PHP yang diperlukan sudah aktif
- [ ] `mod_rewrite` aktif
- [ ] SSL certificate terpasang

### 📁 File & Folder

- [ ] Semua file terupload lengkap
- [ ] Folder `vendor/` ada dan lengkap
- [ ] File `.env` sudah dikonfigurasi untuk production
- [ ] File `.htaccess` berfungsi (redirect ke public/)
- [ ] Permission folder `storage/` = 775
- [ ] Permission folder `bootstrap/cache/` = 775
- [ ] Permission folder `public/upload/` = 775
- [ ] Symlink `public/storage` sudah dibuat

### 🗄️ Database

- [ ] Database sudah dibuat di cPanel
- [ ] User database sudah di-assign dengan ALL PRIVILEGES
- [ ] SQL file berhasil diimport
- [ ] Koneksi database berfungsi (tes via `test-db.php`)

### ⚙️ Environment

- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` sesuai domain
- [ ] `ASSET_URL` sesuai domain
- [ ] `DB_DATABASE` sesuai nama di cPanel
- [ ] `DB_USERNAME` sesuai user di cPanel
- [ ] `DB_PASSWORD` benar
- [ ] `LOG_LEVEL=error`

### 🌐 Fungsionalitas

- [ ] Halaman utama tampil dengan benar
- [ ] CSS dan JavaScript ter-load
- [ ] Gambar/logo tampil
- [ ] Login admin berfungsi
- [ ] Dashboard admin ter-load
- [ ] CRUD produk berfungsi
- [ ] Upload gambar berfungsi
- [ ] API endpoint merespons
- [ ] Email terkirim (jika dikonfigurasi)

### 🔐 Keamanan

- [ ] `APP_DEBUG=false` ✅
- [ ] HTTPS aktif ✅
- [ ] File `.env` tidak bisa diakses via browser ✅
- [ ] File utility sementara sudah dihapus ✅
- [ ] Folder `.git/` tidak ada di production ✅
- [ ] `FORCE_USER_DELETE=false` ✅
- [ ] `mod_security` rules sudah disesuaikan ✅

### 📊 Performance

- [ ] Config cache: `php artisan config:cache`
- [ ] Route cache: `php artisan route:cache`
- [ ] View cache: `php artisan view:cache`
- [ ] Error log bersih dari critical errors

---

## 📌 Ringkasan Cepat — Quick Reference

```
┌─────────────────────────────────────────────────────────┐
│                ALUR DEPLOYMENT CEPATSHOP                 │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  1. Persiapan Lokal                                     │
│     └─ composer install --no-dev → clear cache → ZIP    │
│                                                         │
│  2. Upload ke Hosting                                   │
│     └─ File Manager / FTP → Extract di public_html      │
│                                                         │
│  3. Setup Database                                      │
│     └─ Create DB → Create User → Assign → Import SQL    │
│                                                         │
│  4. Konfigurasi .env                                    │
│     └─ APP_ENV=production, APP_DEBUG=false, DB creds    │
│                                                         │
│  5. Konfigurasi .htaccess                               │
│     └─ Pastikan rewrite rules benar + Force HTTPS       │
│                                                         │
│  6. Permission & Storage                                │
│     └─ chmod 775 storage/ → php artisan storage:link    │
│                                                         │
│  7. Verifikasi & Go Live! 🚀                            │
│     └─ Tes semua fitur → Cek logs → Checklist ✅        │
│                                                         │
└─────────────────────────────────────────────────────────┘
```

---

> **Dokumen ini dibuat pada:** 10 Maret 2026  
> **Aplikasi:** Cepatshop (Laravel 10, PHP 8.1)  
> **Versi Dokumen:** 1.0
