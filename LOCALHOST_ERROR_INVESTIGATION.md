# Investigasi Error `http://localhost/cepat/`

## Ringkasan
Investigasi dilakukan pada stack XAMPP (Apache + PHP) dan aplikasi Laravel di folder `c:\xampp\htdocs\cepat`.
Setelah perbaikan, endpoint `http://localhost/cepat/` sudah dapat diakses **normal (HTTP 200)** tanpa fatal error.

## Gejala yang Ditemukan
1. Di log Laravel muncul fatal error autoload:
   - `include(.../app/Traits/UseCourier.php): Failed to open stream`
   - berasal dari classmap Composer lama yang masih menunjuk file/class yang sudah tidak ada.
2. Di log Laravel juga muncul error middleware auth:
   - `Route [login] not defined`
   - disebabkan middleware `Authenticate` melakukan redirect ke route bernama `login`, sementara route tersebut tidak tersedia di proyek ini (arsitektur SPA/API kustom).

## Pemeriksaan yang Dilakukan
- Konfigurasi rewrite root: `.htaccess` (root) → meneruskan ke `public/`.
- Konfigurasi rewrite Laravel: `public/.htaccess` valid dan standar front-controller.
- Routing aplikasi:
  - `routes/web.php` ada route `/` dan fallback frontend.
  - route API (`routes/cepatshop/*.php`) terdaftar saat `php artisan route:list`.
- Konfigurasi environment:
  - `.env` ada, `APP_URL=http://localhost/cepat`, `APP_INSTALLED=true`.
- Permission folder penting:
  - `storage` writable
  - `storage/logs` writable
  - `bootstrap/cache` writable
- Log Apache:
  - tidak ada error kritis yang menghalangi akses aplikasi pada path ini.

## Root Cause
1. **Kode middleware auth tidak sesuai routing aktual aplikasi**
   - `app/Http/Middleware/Authenticate.php` mengarahkan ke `route('login')`, tetapi route login web bernama `login` tidak ada.
2. **Autoload Composer stale (classmap lama)**
   - masih mereferensikan class/file yang sudah tidak ada (`App\\Models\\UserAddress`, `App\\Traits\\UseCourier`).

## Perbaikan yang Diimplementasikan

### 1) Perbaikan middleware auth
File: `app/Http/Middleware/Authenticate.php`

Perubahan:
```php
return $request->expectsJson() ? null : url('/');
```
Sebelumnya:
```php
return $request->expectsJson() ? null : route('login');
```

Tujuan: mencegah exception `Route [login] not defined` pada request non-JSON yang tidak terautentikasi.

### 2) Sinkronisasi autoload Composer
Menjalankan:
```bash
composer dump-autoload -o
```

Hasil: referensi class yang sudah tidak ada berhasil dibersihkan dari file autoload (`vendor/composer/autoload_*.php`).

## Hasil Verifikasi
Pengujian yang dijalankan:
1. `curl.exe -i http://localhost/cepat/`
   - hasil: **HTTP/1.1 200 OK**
2. `php artisan route:list --path=api`
   - hasil: route API termuat normal.
3. `php artisan about`
   - aplikasi berjalan normal pada environment lokal.
4. Cek permission direktori runtime
   - writable untuk `storage`, `storage/logs`, `bootstrap/cache`.

## Catatan Tambahan
- Saat `composer dump-autoload -o`, muncul warning PSR-4 untuk namespace yang tidak sesuai path file (`app/Scopes/MetaDescriptionScope.php`, `app/Utilities/PermissionsChecker.php`, `app/Utilities/RequirementsChecker.php`).
- Warning ini **tidak menghentikan aplikasi**, tetapi sebaiknya dibersihkan agar autoload tetap konsisten dan mengurangi risiko error di kemudian hari.
