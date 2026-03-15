<template>
   <router-link
      :to="{ name: targetRoute }"
      class="admin-brand-lockup"
      :class="{
         'admin-brand-lockup--compact': compact,
         'admin-brand-lockup--dark': dark,
      }"
      :aria-label="`Buka ${brandName}`"
   >
      <span class="admin-brand-lockup__mark">
         <img
            class="admin-brand-lockup__logo"
            :src="brandLogoSrc"
            :alt="brandAlt"
            :loading="loading"
            decoding="async"
            width="160"
            height="60"
         />
      </span>
      <span v-if="showLabel" class="admin-brand-lockup__copy">
         <span class="admin-brand-lockup__title">{{ brandName }}</span>
         <span class="admin-brand-lockup__caption">Admin Workspace</span>
      </span>
   </router-link>
</template>

<script>
export default {
   name: 'AdminBrandLockup',
   props: {
      compact: {
         type: Boolean,
         default: false,
      },
      dark: {
         type: Boolean,
         default: false,
      },
      loading: {
         type: String,
         default: 'eager',
      },
      targetRoute: {
         type: String,
         default: 'AdminDashboard',
      }
   },
   computed: {
      shop() {
         return this.$store.state.shop || {}
      },
      brandName() {
         return this.shop.name || 'Cepatshop'
      },
      brandAlt() {
         return `Logo ${this.brandName}`
      },
      brandLogoSrc() {
         return this.shop.logo || this.shop.icon || '/icon/icon-192x192.png'
      },
      showLabel() {
         return !this.compact
      }
   }
}
</script>

<style scoped>
.admin-brand-lockup {
   min-height: 44px;
   display: inline-flex;
   align-items: center;
   gap: 12px;
   padding: 8px 12px;
   border-radius: 18px;
   text-decoration: none;
   transition: transform 0.2s ease, box-shadow 0.2s ease, background-color 0.2s ease;
   background: rgba(255, 255, 255, 0.94);
   border: 1px solid rgba(15, 23, 42, 0.08);
   box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
}

.admin-brand-lockup:hover {
   transform: translateY(-1px);
   box-shadow: 0 14px 32px rgba(15, 23, 42, 0.14);
}

.admin-brand-lockup--dark {
   background: rgba(15, 23, 42, 0.82);
   border-color: rgba(148, 163, 184, 0.22);
   box-shadow: 0 12px 32px rgba(2, 6, 23, 0.35);
}

.admin-brand-lockup__mark {
   display: inline-flex;
   align-items: center;
   justify-content: center;
   width: 160px;
   max-width: 160px;
   min-height: 44px;
}

.admin-brand-lockup--compact .admin-brand-lockup__mark {
   width: 40px;
   max-width: 40px;
}

.admin-brand-lockup__logo {
   width: 100%;
   max-height: 60px;
   object-fit: contain;
   object-position: left center;
   display: block;
}

.admin-brand-lockup--compact .admin-brand-lockup__logo {
   width: 40px;
   height: 40px;
   max-height: 40px;
   object-fit: cover;
   border-radius: 12px;
}

.admin-brand-lockup__copy {
   display: flex;
   flex-direction: column;
   min-width: 0;
}

.admin-brand-lockup__title {
   color: #0f172a;
   font-size: 0.96rem;
   line-height: 1.1;
   font-weight: 700;
   white-space: nowrap;
}

.admin-brand-lockup__caption {
   margin-top: 2px;
   color: #475569;
   font-size: 0.72rem;
   letter-spacing: 0.08em;
   text-transform: uppercase;
   white-space: nowrap;
}

.admin-brand-lockup--dark .admin-brand-lockup__title {
   color: #f8fafc;
}

.admin-brand-lockup--dark .admin-brand-lockup__caption {
   color: rgba(226, 232, 240, 0.72);
}

@media (max-width: 767px) {
   .admin-brand-lockup {
      padding: 6px 10px;
      gap: 10px;
      border-radius: 16px;
   }

   .admin-brand-lockup__mark {
      width: 120px;
      max-width: 120px;
   }

   .admin-brand-lockup__logo {
      max-height: 40px;
   }
}
</style>
