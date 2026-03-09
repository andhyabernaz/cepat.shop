<template>
   <q-layout view="lHh LpR fFf" class="bg-grey-2 q-pb-lg customer__layout">
      <q-header bordered class="shadow bg-white text-dark">
         <q-toolbar>
            <q-btn flat round icon="menu" @click="toggleLeftDrawer" size="11px" class="bg-grey-2 q-pa-xs q-mr-sm" />
            <img style="height:26px" :src="shop.logo" />
            <q-toolbar-title v-if="config && config.display_sitename">
               {{ shop.name }}
            </q-toolbar-title>
            <q-space></q-space>
            <q-btn class="q-mr-sm" icon="person" round dense flat>
               <q-menu auto-close>
                  <q-list>
                     <q-item :to="{ name: 'CustomerAccountEdit' }">
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
            <q-btn icon="store" :to="{ name: 'Home' }" round dense flat></q-btn>
         </q-toolbar>
      </q-header>
      <q-drawer show-if-above v-model="leftDrawerOpen" side="left" bordered :mini="is_mini" :mini-width="62">
         <CustomerMenu></CustomerMenu>
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
import CustomerMenu from "components/CustomerMenu.vue";
export default {
   components: { CustomerMenu },
   created() {
      this.$store.dispatch('getAffiliateConfig')
      this.$store.dispatch('affiliate/getAffiliate')
      if (!this.$store.state.shop) {
         this.$store.dispatch('getShop')
      }
      if (!this.config) {
         this.$store.dispatch('getConfig')
      } else {
         this.$store.commit('SET_THEME_COLOR', this.config.theme_color)
      }
      if (this.$store.state.user.token) {
         this.$store.dispatch('user/getUser')
      }

   },
   computed: {
      config() {
         return this.$store.state.config
      },
      user() {
         return this.$store.state.user.user
      },
      shop() {
         return this.$store.state.shop
      },
      cartCount() {
         return this.$store.getters['cart/cartCount']
      },
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
      }
   },
   methods: {
      logout() {
         this.$store.dispatch('user/logout')
      },
      searchNow() {
         if (!this.search || this.search == '') return
         this.$router.push({ name: 'ProductSearch', query: { q: this.search } })
      },
      toggleLeftDrawer() {
         this.leftDrawerOpen = !this.leftDrawerOpen
      },
   },
};
</script>
