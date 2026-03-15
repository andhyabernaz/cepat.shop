<template>
   <div class="row items-center" :class="isDesktop ? 'q-gutter-x-sm' : 'q-gutter-x-xs'">
      <q-btn aria-label="Shopping Cart" :to="{ name: 'Cart' }" unelevated round :size="isDesktop ? '16px' : '15px'"
         padding="5px" dense color="white" text-color="grey-9" icon="eva-shopping-cart-outline">
         <q-badge v-if="cartCount > 0" color="red" floating>{{ cartCount }}</q-badge>
      </q-btn>
      <q-btn aria-label="Produk Favorite" v-if="showFavorite" :to="{ name: 'ProductFavorite' }" color="white"
         padding="5px" unelevated round :size="isDesktop ? '16px' : '15px'" dense icon="eva-heart-outline"
         text-color="grey-9">
         <q-badge v-if="favoriteCount > 0" color="red" floating>{{ favoriteCount }}</q-badge>
      </q-btn>
      <q-btn aria-label="Share Page" v-if="webShareApiSupported" padding="5px" @click="shareTheWeb" color="white"
         unelevated dense round :size="isDesktop ? '16px' : '15px'" icon="eva-share" text-color="grey-9">
      </q-btn>
   </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
   props: {
      noFavorite: {
         type: Boolean,
         default: false
      }
   },
   computed: {
      ...mapGetters('product', ['favoriteCount']),
      ...mapGetters('cart', ['cartCount']),
      isDesktop() {
         return this.$q.platform.is.desktop ? true : false
      },
      webShareApiSupported() {
         return navigator.share
      },
      showFavorite() {
         if (window.innerWidth < 400 && this.noFavorite) {
            return false
         }
         return true
      }

   },
   methods: {
      shareTheWeb() {
         const title = document.title;

         navigator.share({
            title: title,
            text: title,
            url: location.href,
         })
      }
   }
}
</script>
