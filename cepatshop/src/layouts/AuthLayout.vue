<template>
   <q-layout view="hHh lpR fFf" class="auth__layout bg-grey-2">
       <q-header class="text-dark bg-grey-2" >
       <q-toolbar>
         <q-btn :to="{name: 'Home'}"
            unelevated round dense
            icon="eva-arrow-back" color="grey-3" text-color="dark"/>
       </q-toolbar>
    </q-header>
      <q-page-container>
           <div class="main-content full-width">
              <router-view />
           </div>
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
      is_mode_desktop() {
         return this.$store.getters['isModeDesktop']
      },
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