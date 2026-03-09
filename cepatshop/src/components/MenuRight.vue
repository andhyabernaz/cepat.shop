<template>
   <div class="flex items-center no-wrap" :class="isDesktop ? 'q-gutter-x-sm' : 'q-gutter-x-xs'">

      <q-btn aria-label="Shopping Cart" :to="{ name: 'Cart' }" unelevated round :size="isDesktop ? '16px' : '15px'"
         padding="5px" dense color="grey-1" text-color="grey-9" icon="eva-shopping-cart-outline">
         <q-badge v-if="cartCount > 0" color="red" floating>{{ cartCount }}</q-badge>
      </q-btn>
      <q-btn aria-label="Produk Favorite" v-if="showFavorite" :to="{ name: 'ProductFavorite' }" color="grey-1"
         padding="5px" unelevated round :size="isDesktop ? '16px' : '15px'" dense icon="eva-heart-outline"
         text-color="grey-9">
         <q-badge v-if="favoriteCount > 0" color="red" floating>{{ favoriteCount }}</q-badge>
      </q-btn>
      <ShareButton></ShareButton>
      <q-btn v-if="is_mode_desktop" aria-label="Akun" @click="toDashboard" unelevated round
         :size="isDesktop ? '16px' : '15px'" padding="5px" dense color="grey-1" text-color="grey-9"
         icon="eva-person-outline">
      </q-btn>
   </div>
</template>

<script>
import { mapGetters } from 'vuex'
import ShareButton from 'components/ShareButton.vue'
export default {
   components: { ShareButton },
   props: {
      noFavorite: {
         type: Boolean,
         default: false
      },
      noSearch: {
         type: Boolean,
         default: false
      },
   },
   data() {
      return {
         search: '',
      }
   },
   computed: {
      ...mapGetters('product', ['favoriteCount']),
      ...mapGetters('cart', ['cartCount']),
      user() {
         return this.$store.state.user.user
      },
      page_width() {
         return this.$store.state.page_width
      },
      is_mode_desktop() {
         return this.$store.getters['isModeDesktop']
      },
      isDesktop() {
         return this.page_width > 800
      },
      showFavorite() {
         if (window.innerWidth < 400 && this.noFavorite) {
            return false
         }
         return true
      },

   },
   methods: {
      searchNow() {
         if (!this.search || this.search == '') return
         this.$router.push({ name: 'ProductSearch', query: { q: this.search } })
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
   }
}
</script>
