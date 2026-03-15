<template>
   <footer id="footer" class="admin-landing-footer">
      <div class="admin-landing-footer__brand">
         <AdminBrandLockup loading="lazy" />
         <p class="admin-landing-footer__description">{{ description }}</p>
      </div>

      <div class="admin-landing-footer__column">
         <div class="admin-landing-footer__title">Kontak</div>
         <div v-for="item in contacts" :key="item.label" class="admin-landing-footer__contact">
            <q-icon :name="item.icon" size="18px" color="primary" />
            <span>{{ item.value }}</span>
         </div>
      </div>

      <div class="admin-landing-footer__column">
         <div class="admin-landing-footer__title">Link Penting</div>
         <q-btn
            v-for="item in links"
            :key="item.label"
            flat
            no-caps
            align="left"
            class="admin-landing-footer__link"
            @click="handleClick(item)"
         >
            {{ item.label }}
         </q-btn>
      </div>
   </footer>
</template>

<script>
import AdminBrandLockup from 'components/AdminBrandLockup.vue'

export default {
   name: 'AdminLandingFooter',
   components: { AdminBrandLockup },
   props: {
      description: {
         type: String,
         default: '',
      },
      contacts: {
         type: Array,
         default: () => [],
      },
      links: {
         type: Array,
         default: () => [],
      },
   },
   emits: ['navigate'],
   methods: {
      handleClick(item) {
         if (item.to) {
            this.$router.push(item.to)
            return
         }

         if (item.target) {
            this.$emit('navigate', item.target)
         }
      },
   },
}
</script>

<style scoped>
.admin-landing-footer {
   display: grid;
   grid-template-columns: minmax(0, 1.1fr) repeat(2, minmax(220px, 1fr));
   gap: 24px;
   padding: 28px;
   border-radius: 32px;
   background: linear-gradient(145deg, #0f172a 0%, #1f2937 100%);
   color: #e2e8f0;
   box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
}

.admin-landing-footer__description {
   margin-top: 18px;
   margin-bottom: 0;
   color: rgba(226, 232, 240, 0.8);
   line-height: 1.8;
   max-width: 56ch;
}

.admin-landing-footer__title {
   font-size: 0.82rem;
   letter-spacing: 0.08em;
   text-transform: uppercase;
   font-weight: 700;
   opacity: 0.7;
   margin-bottom: 14px;
}

.admin-landing-footer__contact {
   display: flex;
   align-items: flex-start;
   gap: 10px;
   padding: 8px 0;
}

.admin-landing-footer__link {
   justify-content: flex-start;
   width: 100%;
   min-height: 40px;
   color: rgba(226, 232, 240, 0.92);
   border-radius: 12px;
}

.admin-landing-footer__link:hover {
   background: rgba(255, 255, 255, 0.08);
}

@media (max-width: 1023px) {
   .admin-landing-footer {
      grid-template-columns: 1fr;
      padding: 22px;
      border-radius: 24px;
   }
}
</style>
