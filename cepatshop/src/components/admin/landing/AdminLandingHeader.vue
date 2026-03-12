<template>
   <div class="admin-landing-header">
      <div class="admin-landing-header__brand">
         <AdminBrandLockup loading="lazy" />
      </div>
      <div class="admin-landing-header__nav">
         <q-btn
            v-for="item in links"
            :key="item.target"
            flat
            no-caps
            dense
            class="admin-landing-header__link"
            @click="$emit('navigate', item.target)"
         >
            {{ item.label }}
         </q-btn>
      </div>
      <div class="admin-landing-header__action">
         <q-btn
            v-if="cta"
            :to="cta.to"
            color="primary"
            unelevated
            rounded
            no-caps
            :icon="cta.icon"
            :label="cta.label"
         />
      </div>
   </div>
</template>

<script>
import AdminBrandLockup from 'components/AdminBrandLockup.vue'

export default {
   name: 'AdminLandingHeader',
   components: { AdminBrandLockup },
   props: {
      links: {
         type: Array,
         default: () => [],
      },
      cta: {
         type: Object,
         default: null,
      },
   },
   emits: ['navigate'],
}
</script>

<style scoped>
.admin-landing-header {
   position: sticky;
   top: 12px;
   z-index: 20;
   display: grid;
   grid-template-columns: auto 1fr auto;
   align-items: center;
   gap: 16px;
   padding: 12px 16px;
   border-radius: 24px;
   border: 1px solid rgba(148, 163, 184, 0.18);
   background: rgba(255, 255, 255, 0.88);
   backdrop-filter: blur(16px);
   box-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
}

.admin-landing-header__nav {
   display: flex;
   align-items: center;
   justify-content: center;
   gap: 6px;
   overflow-x: auto;
   scrollbar-width: none;
}

.admin-landing-header__nav::-webkit-scrollbar {
   display: none;
}

.admin-landing-header__link {
   padding: 6px 10px;
   border-radius: 999px;
   color: var(--cs-text-secondary);
   font-weight: 600;
}

.admin-landing-header__link:hover {
   background: rgba(13, 158, 79, 0.1);
   color: var(--cs-primary);
}

@media (max-width: 1023px) {
   .admin-landing-header {
      grid-template-columns: 1fr;
      justify-items: start;
      gap: 12px;
   }

   .admin-landing-header__nav {
      justify-content: flex-start;
      width: 100%;
   }
}
</style>
