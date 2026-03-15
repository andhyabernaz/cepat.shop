# Admin Landing Page

Landing page admin berada di [AdminDashboard.vue](../cepatshop/src/pages/Dashboard/AdminDashboard.vue) dan dibangun dari komponen reusable berikut:

- [AdminLandingHeader.vue](../cepatshop/src/components/admin/landing/AdminLandingHeader.vue): sticky header internal berisi logo brand, navigasi section, dan CTA utama.
- [AdminLandingHero.vue](../cepatshop/src/components/admin/landing/AdminLandingHero.vue): hero section dengan headline, badge, CTA, dan highlight card.
- [AdminLandingSection.vue](../cepatshop/src/components/admin/landing/AdminLandingSection.vue): wrapper reusable untuk section dengan eyebrow, title, dan description.
- [AdminLandingFeatureCard.vue](../cepatshop/src/components/admin/landing/AdminLandingFeatureCard.vue): kartu fitur unggulan yang merangkum komponen dari lampiran.
- [AdminLandingStatCard.vue](../cepatshop/src/components/admin/landing/AdminLandingStatCard.vue): kartu statistik/operasional yang bisa dipakai lintas section.
- [AdminLandingTestimonialCarousel.vue](../cepatshop/src/components/admin/landing/AdminLandingTestimonialCarousel.vue): carousel testimoni berbasis review approved.
- [AdminLandingFooter.vue](../cepatshop/src/components/admin/landing/AdminLandingFooter.vue): footer kontak dan link penting.

## Data API

Landing page membaca endpoint `GET /api/adminReports?period={today|monthly|yearly|alltime}` dari [DashboardController.php](../app/Http/Controllers/DashboardController.php).

Payload tambahan yang dipakai landing page:

- `landing_stats`: statistik customer, produk aktif, ulasan tayang, dan order selesai.
- `testimonials`: daftar review approved terbaru untuk carousel.

Payload lama tetap dipertahankan:

- `latest_orders`
- `order_reports`
- `transaction_reports`

## Responsive dan interaksi

- Navigasi section memakai smooth scroll dengan offset yang menyesuaikan toolbar admin.
- Grid fitur/statistik turun dari 4 kolom ke 2 kolom lalu 1 kolom untuk tablet dan mobile.
- Hover state ada di header link, feature card, dan footer link.
- Dukungan browser mengikuti `browserslist` project di [package.json](../cepatshop/package.json).

## Verifikasi

- `php artisan test --filter=AdminDashboardLandingDataTest`
- `npm --prefix cepatshop run lint`

Browser matrix manual tetap perlu dijalankan terpisah bila ingin validasi visual di Chrome, Firefox, Safari, Edge, iOS, dan Android secara nyata.
