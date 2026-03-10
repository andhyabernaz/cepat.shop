<template>
   <q-layout view="lHh LpR fFf" class="cs-admin-content q-pb-lg">
      <q-header bordered class="cs-admin-header">
         <q-toolbar class="cs-toolbar">
            <q-btn dense flat round icon="menu" @click="toggleLeftDrawer"
               class="bg-grey-2 q-pa-xs q-mr-sm" />
            <img style="height:26px" :src="shop.logo" />
            <q-toolbar-title v-if="config && config.display_sitename">
               {{ shop.name }}
            </q-toolbar-title>
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
export default {
   components: { MainMenu },
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
   },
   created() {
      this.$store.dispatch('getAffiliateConfig')
      if (!this.shop) {
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
      }
   },
   mounted() {
      setTimeout(() => {
         this.$store.dispatch("getAdminConfig");
      }, 500);
   },
};
</script>
