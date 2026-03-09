<template>
   <q-layout view="hHh lpR fFf" class="mobile-view">
      <q-page-container>
         <router-view />
      </q-page-container>
   </q-layout>
</template>

<script>
export default {
   name: 'BlankLayout',
   created() {
      if (!this.$store.state.shop) {
         this.$store.dispatch('getShop')
      }
      if (!this.config) {
         this.$store.dispatch('getConfig')
      } else {
         this.$store.commit('SET_THEME_COLOR', this.config.theme_color)
      }
      this.$store.dispatch("cart/getCarts");

   },
   computed: {
      config() {
         return this.$store.state.config
      },
      user() {
         return this.$store.state.user.user
      },
      is_mode_desktop() {
         return this.$store.getters['isModeDesktop']
      }
   },
   mounted() {
      if (this.$store.state.user.token && !this.$store.state.has_validation_token) {
         this.$store.dispatch('user/validationToken')
      }
   },
   meta() {
      return {
         meta: {
            ogUrl: { property: 'og:url', content: location.href },
            ogImage: { property: 'og:image', content: this.shop?.logo },
         }

      }
   }
}
</script>