# Panduan Optimasi Performa - Cepatshop

## Ringkasan Perubahan

### 1. HTML Template (`cepatshop/src/prod.template.html`)
- **DNS Prefetch & Preconnect** untuk Google Fonts → mengurangi latency DNS lookup
- **Font preload async** → font dimuat non-blocking, tidak menghambat render
- **Critical CSS inline** → first paint lebih cepat
- **Favicon dikurangi** → hanya 32x32 dan 16x16 (mengurangi request)
- **`lang="id"`** dan **`theme-color`** meta tag ditambahkan

### 2. Server Caching & Compression (`public/.htaccess`)
- **GZIP Compression** → semua text-based assets (HTML, CSS, JS, JSON, SVG, fonts) dikompresi ~70% lebih kecil
- **Browser Caching** → static assets (CSS/JS/images/fonts) di-cache 1 tahun (karena filename sudah di-hash)
- **Cache-Control immutable** → browser tidak perlu revalidate untuk hashed assets
- **Security headers** → X-Content-Type-Options, X-Frame-Options, X-XSS-Protection
- **ETag removal** untuk static assets → mengurangi header size
- **Keep-Alive** → koneksi persistent

### 3. Webpack/Quasar Build (`cepatshop/quasar.config.js`)
- **GZIP build** diaktifkan → file .gz siap disajikan
- **Chunk splitting dioptimasi**:
  - `vendor` chunk terpisah (node_modules)
  - `quasar` chunk terpisah (framework)
  - `swiper` chunk terpisah (carousel library)
  - `common` chunk untuk shared modules
  - Max chunk size 244KB → parallel loading lebih efisien
- **Icon fonts dikurangi**: `ionicons-v4` dan `roboto-font` dihapus → ~200KB lebih ringan
- **CSS minimization** diaktifkan

### 4. Lazy Loading Images (`cepatshop/src/boot/lazy-img.js`)
- **Vue directive `v-lazy-img`** → gambar dimuat hanya saat mendekati viewport
- **IntersectionObserver** dengan rootMargin 200px → preload sebelum terlihat
- **Native `loading="lazy"`** sebagai baseline
- **`decoding="async"`** → decode gambar di background thread
- **Fade-in animation** → UX smooth saat gambar muncul
- **SVG placeholder** → layout tidak bergeser (CLS = 0)

### 5. CSS Font Optimization (`cepatshop/src/css/_tokens.scss`)
- **Render-blocking `@import url()` dihapus** → font dimuat async via HTML preload
- **Font weights dikurangi** dari 6 (300-800) menjadi 4 (400,500,600,700) → ~40% lebih kecil
- **System font fallback** → teks langsung terlihat sebelum Inter dimuat (FOUT > FOIT)

---

## Cara Menggunakan Lazy Loading

```html
<!-- Cara 1: Dengan binding -->
<img v-lazy-img="product.image_url" alt="Product" />

<!-- Cara 2: Dengan src biasa (directive akan intercept) -->
<img v-lazy-img src="/images/banner.jpg" alt="Banner" />
```

---

## Panduan Maintenance Performa

### Checklist Rutin
- [ ] Jalankan Lighthouse audit setiap deploy baru
- [ ] Pastikan semua gambar baru menggunakan `v-lazy-img`
- [ ] Gunakan format WebP untuk gambar produk (via backend image processing)
- [ ] Jangan tambah icon font baru tanpa menghapus yang tidak dipakai
- [ ] Review bundle size dengan `quasar build --analyze`

### Menambah Gambar Baru
1. Kompres gambar sebelum upload (tinypng.com atau squoosh.app)
2. Gunakan resolusi maksimal yang dibutuhkan (jangan upload 4000px untuk thumbnail 200px)
3. Pertimbangkan format WebP (90% browser support)
4. Selalu gunakan `v-lazy-img` untuk gambar di bawah fold

### Menambah Library Baru
1. Cek bundle size di bundlephobia.com sebelum install
2. Gunakan dynamic import: `const lib = () => import('library')`
3. Jika library besar, tambahkan cacheGroup baru di quasar.config.js

### Monitoring
- **Google PageSpeed Insights**: https://pagespeed.web.dev/
- **GTmetrix**: https://gtmetrix.com/
- **WebPageTest**: https://www.webpagetest.org/
- **Chrome DevTools** → Lighthouse tab → Performance audit

### Target Skor
| Metrik | Target |
|--------|--------|
| Lighthouse Performance (Mobile) | ≥ 90 |
| Lighthouse Performance (Desktop) | ≥ 95 |
| First Contentful Paint | < 1.5s |
| Largest Contentful Paint | < 2.5s |
| Cumulative Layout Shift | < 0.1 |
| Total Blocking Time | < 200ms |

### Build & Deploy
```bash
cd cepatshop
npx quasar build    # Build production dengan semua optimasi
```
Build otomatis menyalin file ke `public/` dan `resources/views/` via afterBuild hook.

---

## Estimasi Dampak Performa

| Optimasi | Estimasi Pengurangan |
|----------|---------------------|
| GZIP Compression | ~70% ukuran transfer |
| Font async loading | ~300ms FCP lebih cepat |
| Hapus ionicons + roboto | ~200KB bundle |
| Chunk splitting | Parallel loading, cache hit lebih tinggi |
| Lazy loading images | ~50-80% pengurangan initial page weight |
| Browser caching | 0ms untuk repeat visits |
| Hapus render-blocking CSS @import | ~200-400ms render lebih cepat |
