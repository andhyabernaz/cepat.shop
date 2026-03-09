<template>
   <q-layout view="hHh lpR fFf" class="bg-light overflow-x-hidden main-layout"
      :class="[{ 'mobile-view': !is_mode_desktop, 'desktop-view': is_mode_desktop }, isActiveTheme]">
      <notify v-if="config && config.is_notifypro" />
      <q-header :reveal="isReveal"
         :class="{ 'bg-white text-grey-8 box-shadow q-pt-sm': isActiveTheme != 'elegant', 'bg-brand text-white': isActiveTheme == 'elegant' }">
         <q-toolbar>
            <div class="flex q-gutter-x-sm cursor-pointer items-center" @click="$router.push('/')"
               :class="{ 'col-auto': is_mode_desktop, 'col': !is_mode_desktop }">
               <img v-if="shop" class="logo" :src="shop.logo ? shop.logo : '/icon/icon-192x192.png'" alt="Logo" />
               <div v-if="shop && shop.name && config.display_sitename" class="text-weight-bold text-no-wrap text-lg">
                  {{ shop.name }}</div>
            </div>
            <div class="col q-mx-lg q-pa-sm" v-if="is_mode_desktop">
               <q-input ref="input" borderless dense class="text-sm q-px-lg bg-grey-1 rounded20" v-model="search"
                  @keyup.enter="searchNow" placeholder="Cari produk...">
                  <template v-slot:append>
                     <q-icon name="search" class="cursor-pointer" @click="searchNow" />
                  </template>
               </q-input>
            </div>
            <div class="col-auto">
               <MenuRight no-search />
            </div>
         </q-toolbar>
         <q-tabs v-if="is_mode_desktop" inline-label indicator-color="primary">
            <q-route-tab label="Beranda" icon="home" to="/" exact />
            <q-route-tab icon="favorite" label="Favorite" :to="{ name: 'ProductFavorite' }" exact />
            <q-route-tab icon="source" label="Katalog" :to="{ name: 'ProductIndex' }" exact />
            <q-route-tab icon="shopping_cart" label="Keranjang" :to="{ name: 'Cart' }" exact>
               <q-badge v-if="cartCount > 0" color="green" floating>{{
                  cartCount
                  }}</q-badge>
            </q-route-tab>
            <q-route-tab icon="contact_page" label="Artikel" :to="{ name: 'FrontPostIndex' }" exact />
         </q-tabs>
      </q-header>
      <q-page-container>
         <router-view />
         <FooterBock></FooterBock>
      </q-page-container>
      <q-footer class="bg-white text-primary footer-tab box-shadow-top" v-if="showFooter">
         <q-tabs active-color="primary" class="text-grey-8 text-xs" no-caps dense switch-indicator
            indicator-color="primary">
            <q-route-tab icon="eva-home-outline" label="Beranda" :to="{ name: 'Home' }" exact />

            <q-route-tab icon="eva-search" :to="{ name: 'ProductSearch' }" label="Cari" exact />

            <q-route-tab v-if="config && config.theme == 'default'" icon="eva-shopping-bag"
               :to="{ name: 'ProductIndex' }" label="Katalog" exact />
            <q-route-tab v-if="config && config.theme == 'romance'" icon="eva-shopping-bag"
               :to="{ name: 'ProductIndex' }" class="bg-primary text-white" label="Katalog" exact />
            <q-btn v-if="config && config.theme == 'elegant'" :to="{ name: 'ProductIndex' }" icon="eva-shopping-bag"
               class="text-md" color="primary" round></q-btn>

            <q-route-tab icon="eva-book-open-outline" :to="{ name: 'FrontPostIndex' }" label="Artikel" exact />

            <q-tab icon="eva-person-outline" @click="toDashboard" exact label="Akun" />
         </q-tabs>
      </q-footer>
      <BackToTop />
   </q-layout>
</template>

<script>
import Notify from 'components/Notify.vue'
import { mapGetters } from 'vuex'
import BackToTop from 'components/BackToTop.vue'
import FooterBock from 'components/FooterBlock.vue'
import { createMetaMixin } from 'quasar'
import MenuRight from 'components/MenuRight.vue'
export default {
   components: { Notify, BackToTop, FooterBock, MenuRight },
   name: 'FrontLayout',
   data() {
      return {
         tab: 'images',
         onsearch: false,
         search: '',
      }
   },
   computed: {
      ...mapGetters('cart', ['cartCount']),
      ...mapGetters('product', ['favoriteCount']),
      logoWidth() {
         if (this.shop && this.shop.name) {
            return 'width:35px;height:35px;object:cover'
         } else {
            return 'width:auto;height:35px;object:contain'
         }
      },
      isReveal() {
         if (this.$route.name == 'Cart') {
            return false
         }
         return true
      },
       isMobileWidth() {
         return this.$store.getters['isMobileWidth']
      },
      showFooter() {
         if(this.isMobileWidth) {

            if(this.$route.name == 'ProductShow') {
               return false
            }
            return true
         }
         return false
      },
      isActiveTheme() {
         return this.$store.getters["getTheme"];
      },
      shop() {
         return this.$store.state.shop
      },
      config() {
         return this.$store.state.config
      },
      user() {
         return this.$store.state.user.user
      },
      is_mode_desktop() {
         return this.$store.getters['isModeDesktop']
      },
   },
   mounted() {
      if (!this.shop) {
         this.$store.dispatch('getShop')
      }

      this.$store.dispatch('getConfig')

      if (this.$store.state.user.token && !this.$store.state.has_validation_token) {
         this.$store.dispatch('user/validationToken')
      }
      this.$store.dispatch("cart/getCarts");

      // setTimeout(() => {
      //    window.addEventListener("resize", this.pageResize);
      //    this.pageResize()
      // }, 100)
   },
   beforeUnmount() {
      // window.removeEventListener("resize", this.pageResize);
   },
   methods: {
      pageResize() {
         const main = document.querySelector('.main-content')
         this.$store.commit("SET_PAGE_WIDTH", main.clientWidth);
      },
      toDashboard() {
         if (this.user) {
            if (this.user.is_admin) {
               this.$router.push({ name: 'AdminDashboard' })
            } else {
               this.$router.push({ name: 'CustomerDashboard' })
            }
         } else {
            this.$router.push({ name: 'Login' })

         }
      },
      searchNow() {
         if (!this.search || this.search == '') return
         this.$router.push({ name: 'ProductSearch', query: { q: this.search } })
      },
   },
   mixins: [
      createMetaMixin(function () {
         return {
            ogUrl: { property: 'og:url', content: location.href },
            ogImage1: { property: 'og:image', content: this.shop?.logo }
         }
      })
   ]

}
</script>