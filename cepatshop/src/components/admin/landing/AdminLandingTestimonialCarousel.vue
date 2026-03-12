<template>
   <div class="admin-testimonial">
      <q-carousel
         v-if="normalizedItems.length"
         v-model="slide"
         animated
         swipeable
         infinite
         navigation
         arrows
         control-color="primary"
         class="admin-testimonial__carousel"
         :autoplay="autoPlay"
      >
         <q-carousel-slide
            v-for="item in normalizedItems"
            :key="item.id"
            :name="item.id"
            class="admin-testimonial__slide"
         >
            <div class="admin-testimonial__card">
               <q-rating
                  readonly
                  :model-value="item.rating"
                  color="amber-8"
                  icon="star_border"
                  icon-selected="star"
                  size="20px"
               />
               <p class="admin-testimonial__comment">"{{ item.comment }}"</p>
               <div class="admin-testimonial__author">{{ item.name }}</div>
               <div class="admin-testimonial__meta">{{ item.product_name }} · {{ item.created }}</div>
            </div>
         </q-carousel-slide>
      </q-carousel>

      <div v-else class="admin-testimonial__empty">
         <q-icon name="forum" size="30px" color="grey-6" />
         <div class="admin-testimonial__empty-title">Belum ada testimoni tayang</div>
         <div class="admin-testimonial__empty-caption">Approve ulasan produk lebih dulu agar carousel ini terisi otomatis.</div>
      </div>
   </div>
</template>

<script>
export default {
   name: 'AdminLandingTestimonialCarousel',
   props: {
      items: {
         type: Array,
         default: () => [],
      },
      autoPlay: {
         type: Number,
         default: 6500,
      },
   },
   data() {
      return {
         slide: null,
      }
   },
   computed: {
      normalizedItems() {
         return this.items.map((item, idx) => ({
            id: item.id ?? idx + 1,
            name: item.name || 'Pelanggan',
            comment: item.comment || 'Landing page ini membantu tim kami presentasi produk dengan lebih rapi.',
            rating: Number(item.rating || 5),
            product_name: item.product_name || 'Produk',
            created: item.created || 'Baru saja',
         }))
      },
   },
   watch: {
      normalizedItems: {
         immediate: true,
         handler(items) {
            if (items.length) {
               this.slide = items[0].id
            }
         },
      },
   },
}
</script>

<style scoped>
.admin-testimonial__carousel {
   min-height: 320px;
   border-radius: 28px;
   background:
      radial-gradient(circle at top right, rgba(13, 158, 79, 0.14), transparent 30%),
      linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
   border: 1px solid var(--cs-border-light);
   box-shadow: var(--cs-shadow-card);
}

.admin-testimonial__slide,
.admin-testimonial__card,
.admin-testimonial__empty {
   display: flex;
   flex-direction: column;
   justify-content: center;
   height: 100%;
}

.admin-testimonial__card {
   max-width: 760px;
   margin: 0 auto;
   text-align: center;
   padding: 24px 12px;
}

.admin-testimonial__comment {
   margin-top: 20px;
   margin-bottom: 18px;
   font-size: clamp(1.2rem, 2.8vw, 1.9rem);
   line-height: 1.55;
   color: var(--cs-text-primary);
}

.admin-testimonial__author {
   font-size: 1rem;
   font-weight: 700;
}

.admin-testimonial__meta,
.admin-testimonial__empty-caption {
   margin-top: 8px;
   color: var(--cs-text-secondary);
   line-height: 1.7;
}

.admin-testimonial__empty {
   min-height: 280px;
   align-items: center;
   text-align: center;
   padding: 24px;
   border-radius: 28px;
   border: 1px dashed var(--cs-border);
   background: var(--cs-bg);
}

.admin-testimonial__empty-title {
   margin-top: 14px;
   font-weight: 700;
}
</style>
