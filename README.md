## CepatShop App

Source code aplikasi toko online SPA dan PWA Ready  

### Tech Stack
- Laravel 10
- Vuejs 3

### SYSTEM REQUIREMENTS

- Apache / nginx Server
- PHP 8.1+
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- XML Writer PHP Extension
- GD PHP Extension

### PANDUAN INSTALASI DAN KONFIGURASI CepatShop APP

### INSTALLASI
1. konfigurasi file .env
   - DB_DATABASE
   - DB_USERNAME
   - DB_PASSWORD
   - APP_NAME
   - APP_URL
2. Import database `database-starter.sql`  
   atau menggunakan laravel command `php artisan app:install`  
   Default Kredensial:
   - email = admin@example.com
   - password = password

### CRON JOB ( recommended )
`* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1`

### SITEMAP XML URL
`https://domainanda.com/sitemap.xml`

## Panduan Update
- Backup Database
- Backup folder dan file
  - Folder public/upload
  - Folder public/icon
  - File public/manifest.json
  - File .env

- Replace semua file dan folder pada aplikasi lama dengan yg baru
- Restore file dan forder backup
- Login dan masuk ke pengaturan update dan tekan tombol update  
   
NOTE:  
Jika tidak bisa login setelah mereplace file, silahkan update via url.  
Akses via browser `https://yourdomain.com/force-update?key={app_key}`  
Jika via browser muncul error not found gunakan force refresh browser atau via Http Client seperti postman  
`app_key` terdapat pada file .env APP_KEY

### Penting!!
Jika versi sebelumnya <= 2.7.x, segera atur kembali pengaturan alamat gudang pengiriman dan pengiriman lokal
.



