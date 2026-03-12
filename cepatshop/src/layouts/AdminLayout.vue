<template>
   <q-layout view="lHh LpR fFf" class="cs-admin-content q-pb-lg">
      <q-header bordered class="cs-admin-header">
         <q-toolbar class="cs-toolbar">
            <q-btn dense flat round icon="menu" @click="toggleLeftDrawer"
               class="bg-grey-2 q-pa-xs q-mr-sm" />
            <AdminBrandLockup
               class="admin-toolbar-brand"
               :compact="is_mobile"
               loading="eager"
            />
            <q-toolbar-title></q-toolbar-title>
            <q-space></q-space>
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
      <q-drawer show-if-above v-model="leftDrawerOpen" side="left" bordered class="cs-admin-drawer" :mini="is_mini" :mini-width="62">
         <MainMenu></MainMenu>
      </q-drawer>
      <q-page-container>
         <div class="main-content">
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

@media (max-width: 767px) {
   .admin-toolbar-brand {
      margin-right: 4px;
   }
}
</style>
