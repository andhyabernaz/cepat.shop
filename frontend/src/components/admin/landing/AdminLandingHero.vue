<template>
   <section id="hero" class="admin-landing-hero">
      <div class="admin-landing-hero__copy">
         <div class="admin-landing-hero__eyebrow">{{ eyebrow }}</div>
         <h1 class="admin-landing-hero__title">{{ title }}</h1>
         <p class="admin-landing-hero__description">{{ description }}</p>
         <div class="admin-landing-hero__badges">
            <span v-for="badge in badges" :key="badge" class="admin-landing-hero__badge">{{ badge }}</span>
         </div>
         <div class="admin-landing-hero__actions">
            <q-btn color="primary" unelevated rounded no-caps icon="auto_awesome" :label="primaryLabel" @click="$emit('primary')" />
            <q-btn outline rounded no-caps color="primary" icon="inventory_2" :label="secondaryLabel" @click="$emit('secondary')" />
         </div>
      </div>

      <div class="admin-landing-hero__visual">
         <div class="admin-landing-hero__card admin-landing-hero__card--brand">
            <div class="admin-landing-hero__card-label">{{ brandName }}</div>
            <div class="admin-landing-hero__card-title">Komposisi komponen siap jualan</div>
            <div class="admin-landing-hero__chip-group">
               <span v-for="chip in componentTags" :key="chip" class="admin-landing-hero__chip">{{ chip }}</span>
            </div>
         </div>
         <div
            v-for="(item, idx) in insights"
            :key="item.label"
            class="admin-landing-hero__card admin-landing-hero__card--metric"
            :style="{ animationDelay: `${idx * 90}ms` }"
         >
            <div class="admin-landing-hero__metric-head">
               <q-icon :name="item.icon || 'insights'" size="20px" :color="item.accent || 'primary'" />
               <span>{{ item.label }}</span>
            </div>
            <div class="admin-landing-hero__metric-value">{{ item.value }}</div>
            <div class="admin-landing-hero__metric-caption">{{ item.description }}</div>
         </div>
      </div>
   </section>
</template>

<script>
export default {
   name: 'AdminLandingHero',
   props: {
      eyebrow: {
         type: String,
         default: '',
      },
      title: {
         type: String,
         default: '',
      },
      description: {
         type: String,
         default: '',
      },
      brandName: {
         type: String,
         default: 'Cepatshop',
      },
      badges: {
         type: Array,
         default: () => [],
      },
      componentTags: {
         type: Array,
         default: () => [],
      },
      insights: {
         type: Array,
         default: () => [],
      },
      primaryLabel: {
         type: String,
         default: 'Lihat Fitur',
      },
      secondaryLabel: {
         type: String,
         default: 'Kelola Produk',
      },
   },
   emits: ['primary', 'secondary'],
}
</script>

<style scoped>
.admin-landing-hero {
   display: grid;
   grid-template-columns: minmax(0, 1.1fr) minmax(320px, 0.9fr);
   gap: 22px;
   align-items: stretch;
}

.admin-landing-hero__copy,
.admin-landing-hero__visual {
   min-width: 0;
}

.admin-landing-hero__copy {
   padding: 34px;
   border-radius: 34px;
   background:
      radial-gradient(circle at top left, rgba(13, 158, 79, 0.16), transparent 42%),
      linear-gradient(145deg, #f8fffb 0%, #edf7ff 100%);
   border: 1px solid rgba(148, 163, 184, 0.16);
   box-shadow: 0 24px 60px rgba(15, 23, 42, 0.09);
}

.admin-landing-hero__eyebrow {
   display: inline-flex;
   padding: 7px 14px;
   border-radius: 999px;
   background: rgba(15, 23, 42, 0.9);
   color: #f8fafc;
   font-size: 0.78rem;
   font-weight: 700;
   letter-spacing: 0.08em;
   text-transform: uppercase;
}

.admin-landing-hero__title {
   margin-top: 18px;
   margin-bottom: 16px;
   font-size: clamp(2rem, 4vw, 3.55rem);
   line-height: 0.96;
   max-width: 12ch;
}

.admin-landing-hero__description {
   max-width: 62ch;
   margin: 0;
   color: var(--cs-text-secondary);
   font-size: 1rem;
   line-height: 1.85;
}

.admin-landing-hero__badges,
.admin-landing-hero__chip-group {
   display: flex;
   flex-wrap: wrap;
   gap: 10px;
}

.admin-landing-hero__badges {
   margin-top: 22px;
}

.admin-landing-hero__badge,
.admin-landing-hero__chip {
   display: inline-flex;
   align-items: center;
   padding: 8px 12px;
   border-radius: 999px;
   font-size: 0.86rem;
   font-weight: 600;
}

.admin-landing-hero__badge {
   background: rgba(13, 158, 79, 0.1);
   color: var(--cs-primary);
}

.admin-landing-hero__actions {
   display: flex;
   flex-wrap: wrap;
   gap: 12px;
   margin-top: 26px;
}

.admin-landing-hero__visual {
   display: grid;
   grid-template-columns: repeat(2, minmax(0, 1fr));
   gap: 14px;
}

.admin-landing-hero__card {
   position: relative;
   overflow: hidden;
   min-height: 170px;
   padding: 20px;
   border-radius: 28px;
   border: 1px solid rgba(148, 163, 184, 0.18);
   background: var(--cs-surface);
   box-shadow: var(--cs-shadow-card);
   animation: adminLandingHeroIn 0.45s ease-out both;
}

.admin-landing-hero__card--brand {
   grid-column: 1 / -1;
   min-height: 210px;
   background: linear-gradient(145deg, #0f172a 0%, #134e4a 100%);
   color: #f8fafc;
}

.admin-landing-hero__card--brand::after {
   content: '';
   position: absolute;
   inset: auto -60px -80px auto;
   width: 180px;
   height: 180px;
   border-radius: 50%;
   background: rgba(255, 255, 255, 0.08);
}

.admin-landing-hero__card-label {
   position: relative;
   z-index: 1;
   font-size: 0.78rem;
   letter-spacing: 0.08em;
   text-transform: uppercase;
   opacity: 0.72;
}

.admin-landing-hero__card-title {
   position: relative;
   z-index: 1;
   margin-top: 14px;
   margin-bottom: 18px;
   font-size: 1.55rem;
   font-weight: 700;
   line-height: 1.1;
   max-width: 11ch;
}

.admin-landing-hero__chip-group {
   position: relative;
   z-index: 1;
}

.admin-landing-hero__chip {
   background: rgba(255, 255, 255, 0.1);
   color: #f8fafc;
}

.admin-landing-hero__metric-head {
   display: flex;
   align-items: center;
   gap: 8px;
   color: var(--cs-text-secondary);
   font-size: 0.82rem;
   font-weight: 700;
   letter-spacing: 0.04em;
   text-transform: uppercase;
}

.admin-landing-hero__metric-value {
   margin-top: 18px;
   font-size: clamp(1.4rem, 2vw, 2rem);
   font-weight: 800;
   line-height: 1;
}

.admin-landing-hero__metric-caption {
   margin-top: 10px;
   color: var(--cs-text-secondary);
   font-size: 0.93rem;
   line-height: 1.6;
}

@keyframes adminLandingHeroIn {
   from {
      opacity: 0;
      transform: translateY(16px);
   }

   to {
      opacity: 1;
      transform: translateY(0);
   }
}

@media (max-width: 1100px) {
   .admin-landing-hero {
      grid-template-columns: 1fr;
   }
}

@media (max-width: 767px) {
   .admin-landing-hero__copy {
      padding: 24px;
      border-radius: 26px;
   }

   .admin-landing-hero__visual {
      grid-template-columns: 1fr;
   }

   .admin-landing-hero__card {
      min-height: auto;
      border-radius: 22px;
   }
}
</style>
