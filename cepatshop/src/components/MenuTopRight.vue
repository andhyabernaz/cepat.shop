<template>
  <div class="row items-center" :class="isDesktop ? 'q-gutter-x-sm' : 'q-gutter-x-xs'">
    <q-btn class="lt-md" :to="{ name: 'Cart' }" unelevated round :size="isDesktop ? '16px' : '14px'"
      :padding="isDesktop ? '6px' : '4px'" dense color="white" text-color="grey-9" icon="shopping_cart">
      <q-badge v-if="cartCount > 0" color="pink" floating>{{ cartCount }}</q-badge>
    </q-btn>
    <q-btn class="lt-md" :to="{ name: 'ProductFavorite' }" color="white" :padding="isDesktop ? '6px' : '4px'" unelevated
      round :size="isDesktop ? '16px' : '14px'" dense icon="favorite" text-color="grey-9">
      <q-badge v-if="favoriteCount > 0" color="pink" floating>{{ favoriteCount }}</q-badge>
    </q-btn>
    <q-btn v-if="webShareApiSupported" :padding="isDesktop ? '6px' : '4px'" @click="shareTheWeb" color="white" unelevated
      dense round :size="isDesktop ? '16px' : '14px'" icon="share" text-color="grey-9">
    </q-btn>
    <q-btn v-if="!user" class="gt-sm" style="min-width:120px;" :to="{ name: 'Login' }" rounded label="login"
      color="primary" unelevated></q-btn>
    <q-btn v-if="!user" class="gt-sm" style="min-width:120px;" :to="{ name: 'Register' }" outline rounded
      label="Register" color="primary" unelevated></q-btn>
    <q-btn v-if="user" class="gt-sm" style="min-width:120px;" @click="goDashboard" rounded label="Dashboard"
      color="primary" unelevated></q-btn>
    <q-btn v-if="user" class="gt-sm" style="min-width:120px;" @click="LogOut" outline rounded label="Logout"
      color="primary" unelevated></q-btn>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
export default {
  props: ['imagename'],
  computed: {
    ...mapGetters('product', ['favoriteCount']),
    ...mapGetters('cart', ['cartCount']),
    isDesktop() {
      return this.$q.platform.is.desktop ? true : false
    },
    webShareApiSupported() {
      return navigator.share
    },
    user() {
      return this.$store.state.user.user
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
    },
    LogOut() {
      this.$store.dispatch('user/logout')
    },
    goDashboard() {
      if (this.user) {
        if (this.user.is_admin) {
          this.$router.push({ name: 'AdminDashboard' })
        } else {
          this.$router.push({ name: 'CustomerDashboard' })
        }
      } else {
        this.$router.push({ name: 'Login' })

      }
    }
  }
}
</script>
