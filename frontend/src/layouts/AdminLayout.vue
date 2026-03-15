<template>
   <q-layout
      view="lHh LpR fFf"
      class="cs-admin-content q-pb-lg"
      :class="[isActiveTheme]"
   >
      <q-header bordered class="cs-admin-header">
         <q-toolbar class="cs-toolbar">
            <q-btn
               dense
               flat
               round
               :icon="drawerIcon"
               @click="toggleLeftDrawer"
               class="bg-grey-2 q-pa-xs q-mr-sm"
            />
            <AdminBrandLockup
               class="admin-toolbar-brand"
               :compact="is_mobile_nav || is_mini"
               loading="eager"
            />
            <div v-if="!is_mobile_nav" class="cs-admin-topnav">
               <q-btn v-if="$can('all')" flat no-caps label="Dashboard" :to="{ name: 'AdminDashboard' }" />
               <q-btn v-if="$can('all')" flat no-caps label="Penjualan" :to="{ name: 'AdminSales' }" />
               <q-btn v-if="$can('view-order')" flat no-caps label="Orderan" :to="{ name: 'OrderIndex' }" />
               <q-btn-dropdown v-if="can_products_menu" flat no-caps label="Produk">
                  <q-list style="min-width: 220px">
                     <q-item v-if="$can('view-product')" clickable v-close-popup :to="{ name: 'AdminProductIndex' }">
                        <q-item-section>Produk</q-item-section>
                     </q-item>
                     <q-item v-if="$can('view-category')" clickable v-close-popup :to="{ name: 'CategoryIndex' }">
                        <q-item-section>Kategori</q-item-section>
                     </q-item>
                     <q-item v-if="$can('view-promo')" clickable v-close-popup :to="{ name: 'PromoIndex' }">
                        <q-item-section>Promo</q-item-section>
                     </q-item>
                     <q-item v-if="$can('view-voucher')" clickable v-close-popup :to="{ name: 'VoucherIndex' }">
                        <q-item-section>Voucher</q-item-section>
                     </q-item>
                  </q-list>
               </q-btn-dropdown>
               <q-btn v-if="$can('all')" flat no-caps label="Laporan" :to="{ name: 'AdminReport' }" />
               <q-btn v-if="$can('view-config')" flat no-caps label="Pengaturan" :to="{ name: 'Config' }" />
            </div>
            <q-toolbar-title v-else></q-toolbar-title>
            <q-space />
            <q-btn class="q-mr-sm" icon="person" round dense flat>
               <q-menu auto-close>
                  <q-list>
                     <q-item :to="{ name: 'Account' }">
                        <q-item-section side>
                           <q-icon name="account_circle"></q-icon>
                        </q-item-section>
                        <q-item-section>Akun</q-item-section>
                     </q-item>
                     <q-item clickable @click="logout">
                        <q-item-section side>
                           <q-icon name="logout"></q-icon>
                        </q-item-section>
                        <q-item-section>Logout</q-item-section>
                     </q-item>
                  </q-list>
               </q-menu>
            </q-btn>
            <q-btn icon="store" :to="{ name: 'Home', query: { is_update: true } }" round dense flat></q-btn>
         </q-toolbar>
      </q-header>
      <q-drawer
         v-model="leftDrawerOpen"
         side="left"
         bordered
         :overlay="is_mobile_nav"
         :behavior="is_mobile_nav ? 'mobile' : 'desktop'"
         :show-if-above="!is_mobile_nav"
         :mini="!is_mobile_nav && is_mini"
         :width="280"
         :mini-width="92"
         class="cs-admin-drawer"
      >
         <MainMenu />
      </q-drawer>
      <q-page-container>
         <div class="main-content cs-admin-main">
            <router-view />
   
            <q-inner-loading :showing="loading"></q-inner-loading>

         </div>
      </q-page-container>
   </q-layout>
</template>
<script>
import { mapState } from "vuex";
import MainMenu from "components/MainMenu.vue";
import AdminBrandLockup from "components/AdminBrandLockup.vue";
export default {
   components: { MainMenu, AdminBrandLockup },
   name: "AdminLayout",
   computed: {
      ...mapState({
         isCheckLogin: (state) => state.user.isCheckLogin,
         shop: (state) => state.shop,
         user: (state) => state.user.user,
         config: (state) => state.config,
      }),
      leftDrawerOpen: {
         get() {
            return this.$store.state.drawer;
         },
         set(val) {
            return this.$store.commit("SET_DRAWER", val);
         },
      },
      is_mini() {
         return this.$store.state.is_mini
      },
      loading() {
         return this.$store.state.loading
      },
      is_mobile() {
         return this.$q.screen.lt.md
      },
      is_mobile_nav() {
         return this.$q.screen.width <= 767
      },
      isActiveTheme() {
         return this.$store.getters["getTheme"];
      },
      drawerIcon() {
         if (this.is_mobile_nav) {
            return 'menu'
         }
         return this.is_mini ? 'menu' : 'menu_open'
      },
      can_products_menu() {
         return this.$can('view-product')
            || this.$can('view-category')
            || this.$can('view-promo')
            || this.$can('view-voucher')
      },
      brandName() {
         return this.shop?.name || 'Cepatshop'
      },
      brandIconHref() {
         return this.shop?.icon || this.shop?.logo || '/icon/icon-32x32.png'
      }
   },
   created() {
      this.$store.dispatch('getAffiliateConfig')
      if (!this.shop || Object.keys(this.shop).length === 0) {
         this.$store.dispatch("getShop");
      }
      this.$store.dispatch("user/getUser");
   },
   methods: {
      toggleLeftDrawer() {
         this.leftDrawerOpen = !this.leftDrawerOpen
      },
      logout() {
         this.$store.dispatch('user/logout')
      },
      applyFavicon() {
         if (typeof document === 'undefined') {
            return
         }

         document.title = `${this.brandName} Admin`

         const defaultIcons = {
            ico: '/favicon.ico',
            icon16: '/icon/icon-16x16.png',
            icon32: '/icon/icon-32x32.png',
            icon48: '/icon/icon-48x48.png',
         }

         const iconHref = this.brandIconHref

         this.upsertHeadLink('admin-favicon-ico', 'shortcut icon', defaultIcons.ico, null, 'image/x-icon')
         this.upsertHeadLink('admin-favicon-16', 'icon', iconHref || defaultIcons.icon16, '16x16', 'image/png')
         this.upsertHeadLink('admin-favicon-32', 'icon', iconHref || defaultIcons.icon32, '32x32', 'image/png')
         this.upsertHeadLink('admin-favicon-48', 'icon', iconHref || defaultIcons.icon48, '48x48', 'image/png')
      },
      upsertHeadLink(id, rel, href, sizes = null, type = null) {
         let link = document.getElementById(id)
         if (!link) {
            link = document.createElement('link')
            link.id = id
            document.head.appendChild(link)
         }

         link.rel = rel
         link.href = href

         if (sizes) {
            link.sizes = sizes
         } else {
            link.removeAttribute('sizes')
         }

         if (type) {
            link.type = type
         } else {
            link.removeAttribute('type')
         }
      }
   },
   mounted() {
      setTimeout(() => {
         this.$store.dispatch("getAdminConfig");
      }, 500);

      this.applyFavicon()
   },
   watch: {
      brandIconHref() {
         this.applyFavicon()
      },
      brandName() {
         this.applyFavicon()
      }
   }
};
</script>

<style scoped>
.admin-toolbar-brand {
   flex-shrink: 0;
}

.cs-admin-topnav {
   display: flex;
   align-items: center;
   gap: 6px;
   margin-left: 12px;
}

@media (max-width: 767px) {
   .admin-toolbar-brand {
      margin-right: 4px;
   }
}
</style>
