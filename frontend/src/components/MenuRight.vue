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

      <template v-if="!user">
         <q-btn v-if="is_mode_desktop" data-test="nav-login" aria-label="Login" flat dense no-caps color="primary"
            label="Login" @click="openAuth('login')" />
         <q-btn v-if="is_mode_desktop" data-test="nav-register" aria-label="Register" outline dense no-caps color="primary"
            label="Register" @click="openAuth('register')" />
         <q-btn v-else aria-label="Akun" unelevated round :size="isDesktop ? '16px' : '15px'" padding="5px" dense
            color="grey-1" text-color="grey-9" icon="eva-person-outline">
            <q-menu anchor="bottom right" self="top right">
               <q-list dense style="min-width: 160px">
                  <q-item clickable v-close-popup @click="openAuth('login')">
                     <q-item-section>Login</q-item-section>
                  </q-item>
                  <q-item clickable v-close-popup @click="openAuth('register')">
                     <q-item-section>Register</q-item-section>
                  </q-item>
               </q-list>
            </q-menu>
         </q-btn>
      </template>

      <q-btn v-else aria-label="Akun" unelevated round :size="isDesktop ? '16px' : '15px'" padding="5px" dense
         color="grey-1" text-color="grey-9" icon="eva-person-outline">
         <q-menu anchor="bottom right" self="top right">
            <q-list dense style="min-width: 160px">
               <q-item clickable v-close-popup @click="toDashboard">
                  <q-item-section>Dashboard</q-item-section>
               </q-item>
               <q-item clickable v-close-popup @click="logout">
                  <q-item-section>Logout</q-item-section>
               </q-item>
            </q-list>
         </q-menu>
      </q-btn>

      <q-dialog data-test="auth-dialog" v-model="authDialog" :maximized="isMobile" transition-show="slide-up"
         transition-hide="slide-down">
         <LoginBlock :initial-form-type="authFormType" :redirect="redirectAfterAuth" @onClose="authDialog = false"
            @onResponse="handleAuthResponse" />
      </q-dialog>
   </div>
</template>

<script>
import { mapGetters } from 'vuex'
import ShareButton from 'components/ShareButton.vue'
import LoginBlock from 'components/LoginBlock.vue'
export default {
   components: { ShareButton, LoginBlock },
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
         authDialog: false,
         authFormType: 'login',
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
      isMobile() {
         return this.page_width < 600
      },
      showFavorite() {
         if (window.innerWidth < 400 && this.noFavorite) {
            return false
         }
         return true
      },
      redirectAfterAuth() {
         return this.$route?.query?.redirect || null
      },

   },
   methods: {
      searchNow() {
         if (!this.search || this.search == '') return
         this.$router.push({ name: 'ProductSearch', query: { q: this.search } })
      },
      openAuth(type) {
         this.authFormType = type === 'register' ? 'register' : 'login'
         this.authDialog = true
      },
      handleAuthResponse() {
         this.authDialog = false
      },
      toDashboard() {
         if (this.user) {
            if (this.user.is_admin) {
               this.$router.push({ name: 'AdminDashboard' })
            } else {
               this.$router.push({ name: 'CustomerDashboard' })
            }
         } else {
            this.openAuth('login')
         }
      },
      logout() {
         this.$store.dispatch('user/logout')
      },
   }
}
</script>
