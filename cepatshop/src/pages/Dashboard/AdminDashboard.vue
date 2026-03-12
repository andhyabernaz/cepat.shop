<template>
   <q-page padding class="admin-landing-page">
      <div class="admin-landing-page__stack">
         <AdminLandingHeader :links="sectionLinks" :cta="headerCta" @navigate="scrollToSection" />

         <AdminLandingHero
            :eyebrow="heroEyebrow"
            :title="heroTitle"
            :description="heroDescription"
            :brand-name="brandName"
            :badges="heroBadges"
            :component-tags="componentTags"
            :insights="heroInsights"
            primary-label="Jelajahi Fitur"
            secondary-label="Kelola Produk"
            @primary="scrollToSection('features')"
            @secondary="$router.push({ name: 'AdminProductIndex' })"
         />

         <AdminLandingSection
            section-id="features"
            eyebrow="Fitur Unggulan"
            title="Komponen landing page yang bisa dirangkai langsung dari admin"
            description="Daftar ini mengikuti komponen pada lampiran, lalu dikemas ulang menjadi blok reusable agar mudah dipakai untuk berbagai campaign."
            soft
         >
            <div class="admin-landing-page__feature-grid">
               <AdminLandingFeatureCard
                  v-for="(item, idx) in featureCards"
                  :key="item.title"
                  :feature="item"
                  :index="idx"
               />
            </div>
         </AdminLandingSection>

         <AdminLandingSection
            section-id="testimonials"
            eyebrow="Testimoni"
            title="Social proof dari ulasan produk yang sudah approved"
            description="Carousel ini menarik data live dari review produk yang dipublish, jadi blok testimoni selalu sinkron dengan aktivitas toko."
            align="center"
         >
            <AdminLandingTestimonialCarousel :items="testimonials" />
         </AdminLandingSection>

         <AdminLandingSection
            section-id="stats"
            eyebrow="Statistik Pengguna"
            title="Ringkasan angka yang relevan untuk presentasi performa"
            description="Statistik ini diambil dari API dashboard admin dan bisa dipakai di hero, section kredibilitas, atau footer CTA."
         >
            <div class="admin-landing-page__stat-grid">
               <AdminLandingStatCard v-for="item in landing_stats" :key="item.label" :item="item" />
            </div>
         </AdminLandingSection>

         <AdminLandingSection
            section-id="activity"
            eyebrow="Aktivitas"
            title="Snapshot operasional tanpa kehilangan dashboard lama"
            description="Section ini mempertahankan data order dan transaksi sebelumnya, hanya dikemas ulang agar konsisten dengan layout landing page."
         >
            <div class="admin-landing-page__toolbar">
               <div class="admin-landing-page__toolbar-copy">
                  <div class="admin-landing-page__toolbar-title">Kinerja Penjualan</div>
                  <div class="admin-landing-page__toolbar-caption">Periode aktif mempengaruhi hero insight dan kartu transaksi.</div>
               </div>
               <q-select
                  filled
                  dense
                  emit-value
                  map-options
                  :options="periodes"
                  v-model="filter.period"
                  label="Periode"
                  style="min-width: 160px"
                  @update:model-value="getData"
               />
            </div>

            <div class="admin-landing-page__stat-grid admin-landing-page__stat-grid--reports">
               <AdminLandingStatCard v-for="item in operationalCards" :key="item.label" :item="item" />
            </div>

            <q-card class="admin-landing-page__orders shadow-sm">
               <q-card-section class="admin-landing-page__orders-head">
                  <div>
                     <div class="admin-landing-page__orders-title">Pesanan Terbaru</div>
                     <div class="admin-landing-page__orders-caption">Referensi cepat untuk follow up admin.</div>
                  </div>
                  <q-btn
                     :to="{ name: 'OrderIndex' }"
                     flat
                     no-caps
                     icon-right="chevron_right"
                     label="Lihat semua"
                  />
               </q-card-section>
               <q-separator />
               <q-card-section>
                  <div class="table-responsive">
                     <table class="table aligned bordered wides">
                        <thead>
                           <tr>
                              <th>#</th>
                              <th>No Pesanan</th>
                              <th>Status</th>
                              <th>Total</th>
                              <th>Tgl Pembelian</th>
                           </tr>
                        </thead>
                        <tbody>
                           <tr v-for="(item, idx) in latest_orders" :key="item.id">
                              <td>{{ idx + 1 }}</td>
                              <td>{{ item.order_ref }}</td>
                              <td>
                                 <q-badge class="inline-block" :color="getOrderStatusColor(item.order_status)">
                                    {{ item.admin_status.label }}
                                 </q-badge>
                              </td>
                              <td>{{ moneyIdr(item.billing_total) }}</td>
                              <td>{{ dateFormat(item.created_at) }}</td>
                           </tr>
                           <tr v-if="!latest_orders.length">
                              <td colspan="5" class="text-center q-py-md">Belum ada pesanan terbaru.</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </q-card-section>
            </q-card>
         </AdminLandingSection>

         <AdminLandingFooter
            :description="footerDescription"
            :contacts="footerContacts"
            :links="footerLinks"
            @navigate="scrollToSection"
         />
      </div>
   </q-page>
</template>

<script>
import { BaseApi } from 'boot/axios'
import AdminLandingFooter from 'components/admin/landing/AdminLandingFooter.vue'
import AdminLandingHeader from 'components/admin/landing/AdminLandingHeader.vue'
import AdminLandingHero from 'components/admin/landing/AdminLandingHero.vue'
import AdminLandingSection from 'components/admin/landing/AdminLandingSection.vue'
import AdminLandingFeatureCard from 'components/admin/landing/AdminLandingFeatureCard.vue'
import AdminLandingStatCard from 'components/admin/landing/AdminLandingStatCard.vue'
import AdminLandingTestimonialCarousel from 'components/admin/landing/AdminLandingTestimonialCarousel.vue'

export default {
   name: 'AdminDashboard',
   components: {
      AdminLandingFooter,
      AdminLandingHeader,
      AdminLandingHero,
      AdminLandingSection,
      AdminLandingFeatureCard,
      AdminLandingStatCard,
      AdminLandingTestimonialCarousel,
   },
   data() {
      return {
         loading: false,
         periodes: [
            { label: 'Hari Ini', value: 'today' },
            { label: 'Bulan Ini', value: 'monthly' },
            { label: 'Tahun Ini', value: 'yearly' },
            { label: 'Semua Data', value: 'alltime' },
         ],
         latest_orders: [],
         order_reports: [],
         transaction_reports: [],
         landing_stats: [],
         testimonials: [],
         filter: {
            period: 'monthly',
         },
         sectionLinks: [
            { label: 'Hero', target: 'hero' },
            { label: 'Fitur', target: 'features' },
            { label: 'Testimoni', target: 'testimonials' },
            { label: 'Statistik', target: 'stats' },
            { label: 'Aktivitas', target: 'activity' },
            { label: 'Kontak', target: 'footer' },
         ],
         componentTags: ['Teks', 'Gambar', 'Form', 'Testimoni', 'FAQ', 'Embed'],
         featureCards: [
            {
               icon: 'text_fields',
               title: 'Teks, Gambar, dan Tombol',
               description: 'Bangun hero section, subheadline, dan CTA tanpa perlu blok kustom baru setiap campaign.',
               items: ['Teks', 'Gambar', 'Tombol'],
            },
            {
               icon: 'assignment',
               title: 'Form Pemesanan dan Daftar',
               description: 'Komponen form dan list memudahkan admin menyusun alur konversi, benefit, dan langkah order.',
               items: ['Form Pemesanan', 'Daftar'],
            },
            {
               icon: 'forum',
               title: 'Testimoni dan FAQ',
               description: 'Gabungkan social proof dan jawaban keberatan utama agar page lebih meyakinkan dan informatif.',
               items: ['Testimoni', 'FAQ'],
            },
            {
               icon: 'video_library',
               title: 'Slider, YouTube, dan Embed',
               description: 'Masukkan media yang lebih kaya untuk demo produk, tutorial, atau konten video penjualan.',
               items: ['Gambar Slider', 'YouTube', 'Embed'],
            },
            {
               icon: 'explore',
               title: 'Scroll Target dan Animation',
               description: 'Bikin navigasi antar section lebih halus dan storytelling page terasa lebih hidup saat di-scroll.',
               items: ['Scroll Target', 'Animation'],
            },
            {
               icon: 'view_agenda',
               title: 'Section, HTML, dan Divider',
               description: 'Kontrol struktur layout dengan blok dasar yang bisa dipakai ulang untuk desktop maupun mobile.',
               items: ['Section', 'HTML', 'Divider'],
            },
         ],
      }
   },
   computed: {
      shop() {
         return this.$store.state.shop || {}
      },
      config() {
         return this.$store.state.config || {}
      },
      brandName() {
         return this.shop.name || 'Cepatshop'
      },
      heroEyebrow() {
         return 'Landing Page Workspace'
      },
      heroTitle() {
         return this.shop.slogan || `Dashboard landing page untuk ${this.brandName}`
      },
      heroDescription() {
         const storeSummary = this.shop.description || 'Susun halaman penjualan dengan blok reusable, social proof, dan CTA yang fokus ke konversi.'

         return `${storeSummary} Semua section di bawah dirancang agar admin bisa mempresentasikan brand, fitur, testimonial, dan statistik dalam satu alur yang rapi.`
      },
      heroBadges() {
         return [
            'Responsive mobile-tablet-desktop',
            'Smooth scroll navigation',
            'Live testimonials & stats',
         ]
      },
      heroInsights() {
         const cards = []

         if (this.transaction_reports[0]) {
            cards.push({
               label: this.transaction_reports[0].label,
               value: `Rp ${this.moneyFormat(this.transaction_reports[0].total)}`,
               description: 'Omzet berdasarkan periode aktif.',
               icon: this.transaction_reports[0].icon,
               accent: this.transaction_reports[0].color,
            })
         }

         if (this.landing_stats[0]) {
            cards.push({
               label: this.landing_stats[0].label,
               value: this.numberFormat(this.landing_stats[0].value),
               description: this.landing_stats[0].description,
               icon: this.landing_stats[0].icon,
               accent: this.landing_stats[0].accent,
            })
         }

         cards.push({
            label: 'Testimoni Live',
            value: this.numberFormat(this.testimonials.length),
            description: 'Diambil dari ulasan produk yang sudah dipublish.',
            icon: 'forum',
            accent: 'amber-8',
         })

         return cards.slice(0, 3)
      },
      operationalCards() {
         const transactionCards = this.transaction_reports.map((item) => ({
            label: item.label,
            value: `Rp ${this.moneyFormat(item.total)}`,
            description: item.desc || 'Ringkasan transaksi periode aktif.',
            icon: item.icon,
            accent: item.color,
         }))

         const orderCards = this.order_reports.map((item) => ({
            label: item.label,
            value: this.numberFormat(item.total),
            description: 'Status order saat ini.',
            icon: item.icon,
            accent: item.color,
         }))

         return [...transactionCards, ...orderCards]
      },
      headerCta() {
         return {
            label: 'Buat Produk',
            icon: 'add_circle',
            to: { name: 'ProductCreate' },
         }
      },
      footerDescription() {
         return `Landing page admin ${this.brandName} dirancang untuk menjaga branding, menonjolkan komponen dari lampiran, dan tetap menyisakan ruang untuk operasional harian tim.`
      },
      footerContacts() {
         return [
            {
               label: 'Brand',
               value: this.brandName,
               icon: 'storefront',
            },
            {
               label: 'Email',
               value: this.shop.email || 'Email toko belum diatur',
               icon: 'mail',
            },
            {
               label: 'WhatsApp',
               value: this.shop.phone || 'Nomor toko belum diatur',
               icon: 'call',
            },
            {
               label: 'Alamat',
               value: this.shop.address || 'Alamat toko belum diatur',
               icon: 'place',
            },
         ]
      },
      footerLinks() {
         return [
            { label: 'Kembali ke Hero', target: 'hero' },
            { label: 'Kelola Produk', to: { name: 'AdminProductIndex' } },
            { label: 'Kelola Pesanan', to: { name: 'OrderIndex' } },
            { label: 'Pengaturan Toko', to: { name: 'Shop' } },
         ]
      },
   },
   mounted() {
      this.getData()
   },
   methods: {
      getData() {
         this.$store.commit('SET_LOADING', true)
         this.loading = true

         const query = new URLSearchParams(this.filter).toString()

         BaseApi.get(`adminReports?${query}`).then((res) => {
            const data = res.data.data || {}

            this.latest_orders = data.latest_orders || []
            this.order_reports = data.order_reports || []
            this.transaction_reports = data.transaction_reports || []
            this.landing_stats = data.landing_stats || []
            this.testimonials = data.testimonials || []
         }).finally(() => {
            this.loading = false
         })
      },
      scrollToSection(target) {
         if (typeof document === 'undefined') {
            return
         }

         const section = document.getElementById(target)
         if (!section) {
            return
         }

         const offset = this.$q.screen.lt.md ? 112 : 128
         const top = section.getBoundingClientRect().top + window.pageYOffset - offset

         window.scrollTo({
            top,
            behavior: 'smooth',
         })
      },
   },
}
</script>

<style scoped>
.admin-landing-page {
   scroll-behavior: smooth;
}

.admin-landing-page__stack {
   display: grid;
   gap: 22px;
}

.admin-landing-page__feature-grid,
.admin-landing-page__stat-grid {
   display: grid;
   gap: 16px;
}

.admin-landing-page__feature-grid {
   grid-template-columns: repeat(3, minmax(0, 1fr));
}

.admin-landing-page__stat-grid {
   grid-template-columns: repeat(4, minmax(0, 1fr));
}

.admin-landing-page__stat-grid--reports {
   grid-template-columns: repeat(4, minmax(0, 1fr));
}

.admin-landing-page__toolbar {
   display: flex;
   justify-content: space-between;
   gap: 16px;
   align-items: center;
   margin-bottom: 18px;
}

.admin-landing-page__toolbar-title {
   font-size: 1.1rem;
   font-weight: 700;
}

.admin-landing-page__toolbar-caption {
   margin-top: 4px;
   color: var(--cs-text-secondary);
}

.admin-landing-page__orders {
   margin-top: 20px;
   border-radius: 24px;
   border: 1px solid var(--cs-border-light);
   box-shadow: var(--cs-shadow-card);
}

.admin-landing-page__orders-head {
   display: flex;
   justify-content: space-between;
   gap: 14px;
   align-items: center;
}

.admin-landing-page__orders-title {
   font-size: 1.05rem;
   font-weight: 700;
}

.admin-landing-page__orders-caption {
   margin-top: 4px;
   color: var(--cs-text-secondary);
}

@media (max-width: 1200px) {
   .admin-landing-page__feature-grid,
   .admin-landing-page__stat-grid,
   .admin-landing-page__stat-grid--reports {
      grid-template-columns: repeat(2, minmax(0, 1fr));
   }
}

@media (max-width: 767px) {
   .admin-landing-page__feature-grid,
   .admin-landing-page__stat-grid,
   .admin-landing-page__stat-grid--reports {
      grid-template-columns: 1fr;
   }

   .admin-landing-page__toolbar,
   .admin-landing-page__orders-head {
      flex-direction: column;
      align-items: stretch;
   }
}
</style>
