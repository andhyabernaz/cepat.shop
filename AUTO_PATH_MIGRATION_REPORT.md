# Auto Path Migration Report

## Scope
- Audit recursive seluruh codebase untuk referensi path legacy `/auto/`
- Migrasi referensi ke domain utama `https://shop.cepat.digital/`
- Implementasi redirect 301 untuk URL lama
- Verifikasi hasil (scan residual + test suite)

## Findings (pre-migration)
1. Legacy route path `/auto/*` masih aktif di backend routing:
   - `routes/web.php` (beberapa endpoint `'/auto'`, `'/auto/{any}'`, `'/auto/cepat*'`)
   - Jenis: URL routing legacy
2. Frontend build artifact masih embed base path `/auto/`:
   - `cepatshop/build/index.php` (base href `/auto/`)
   - `cepatshop/build/js/app.*.js` (router base/path constant `/auto/`)
   - Jenis: hardcoded compiled asset path
3. Environment canonical URL belum ke domain utama:
   - `.env` (`APP_URL`, `ASSET_URL`)
   - `.env.example` (`APP_URL` belum domain utama, `ASSET_URL` belum tersedia)
   - Jenis: konfigurasi domain/app URL
4. Robots belum deklarasi sitemap domain utama:
   - `public/robots.txt`
   - Jenis: SEO resource metadata

## Changes Implemented
1. Redirect 301 canonical ke domain utama untuk semua legacy `/auto/*`
   - `routes/web.php`
   - `/auto`
   - `/auto/{any}`
   - `/auto/cepat`
   - `/auto/cepat/{any?}`
   - Semua redirect menjaga path dan query string
2. Domain canonical diperbarui ke `https://shop.cepat.digital`
   - `.env`:
     - `APP_URL=https://shop.cepat.digital`
     - `ASSET_URL=https://shop.cepat.digital`
   - `.env.example`:
     - `APP_URL=https://shop.cepat.digital`
     - `ASSET_URL=https://shop.cepat.digital`
3. SEO metadata diperbarui
   - `public/robots.txt`:
     - `Sitemap: https://shop.cepat.digital/sitemap.xml`
4. Frontend artifact diregenerasi
   - Cleaned old generated assets (`cepatshop/build`, `public/js`, `public/css`)
   - Rebuild production bundle: `npm run build` (hasil sukses)
   - Hasil build baru sudah tidak mengandung base `/auto/`

## Verification
1. Residual scan:
   - Referensi `/auto/` tersisa hanya:
     - `routes/web.php` (intended, untuk redirect 301 legacy)
     - `tests/Feature/RouteCanonicalizationTest.php` (intended, untuk verifikasi redirect)
   - Tidak ada residual `/auto/` pada artefak build aktif.
2. Test suite:
   - `php artisan test` => **11 passed**
   - Termasuk test canonicalization redirect ke domain utama.
3. Route check:
   - `php artisan route:list --path=auto` menunjukkan endpoint legacy masih ada sebagai pintu redirect 301.

## Notes
- `sitemap.xml` dihasilkan dinamis dari route/view dan akan mengikuti host canonical (`APP_URL`) saat diakses.
- Update Google Search Console tidak dapat diotomasi dari repository ini; perlu tindakan manual di dashboard Search Console:
  - submit/validasi property `https://shop.cepat.digital`
  - submit ulang sitemap `https://shop.cepat.digital/sitemap.xml`
