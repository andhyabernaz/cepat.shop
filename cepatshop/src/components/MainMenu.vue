<template>
   <q-scroll-area class="fit sidebar cs-sidebar" :class="{ 'cs-sidebar--dark': is_dark, 'cs-sidebar--light': !is_dark }">
      <q-list :dark="is_dark" class="q-pb-lg">
         <div :class="is_mini ? 'q-py-xs' : 'q-py-lg'" >

            <q-item>
               <q-item-section avatar>
                  <div class="cs-avatar-ring">
                     <q-avatar color="primary" text-color="white" size="48px">{{ initialName }}</q-avatar>
                  </div>
               </q-item-section>
               <q-item-section>
                  <q-item-label class="text-sm" style="opacity: 0.7">Welcome,</q-item-label>
                  <q-item-label class="text-h5 text-weight-bold">{{ user.name }}</q-item-label>
                  <q-item-label class="text-xs" style="opacity: 0.6">Active from {{ $dateParse(user.created_at)
                  }}</q-item-label>
                  <q-item-label>{{ moneyIdr(user.saldo_balance) }}</q-item-label>
               </q-item-section>
            </q-item>
         </div>
         <q-separator :color="is_dark ? 'grey-8' : 'grey-2'"></q-separator>
         <q-item-label header>
            <div class="flex justify-between items-center">
               <div style="font-weight: 600; letter-spacing: 0.03em; text-transform: uppercase; font-size: 11px; opacity: 0.5">Navigation</div>
               <q-toggle v-model="is_dark"  checked-icon="light_mode" color="green" unchecked-icon="dark_mode"></q-toggle>
            </div>
         </q-item-label>
         <template v-for="(item, i) in menus" :key="i" >
         <q-item clickable v-ripple :to="{ name: item.path }" v-if="item.active && $can(item.ability)"
            class="cs-menu-item"
            :class="{
            'cs-menu-item--active': $route.name == item.path,
            'cs-menu-item--dark': is_dark,
            'cs-menu-item--light': !is_dark,
         }">
            <q-item-section avatar>
               <q-avatar :icon="item.icon" :text-color="getIconTextColor(item.path)" rounded font-size="23px"
                  size="38px" :color="getIconColor(item.path)" />
            </q-item-section>
            <q-item-section>
               <q-item-label class="text-weight-medium">{{ item.label }}</q-item-label>
               <q-item-label caption style="opacity: 0.6">{{ item.caption }}</q-item-label>
            </q-item-section>
            <q-tooltip v-if="is_mini" class="bg-purple text-13" anchor="center right" self="center start">
               {{ item.label }}
            </q-tooltip>
         </q-item>
         </template>
      </q-list>
   </q-scroll-area>
</template>

<script>

export default {
   watch: {
      is_dark() {
         this.handleDarkMode()
      }
   },
   computed: {
      menus() {
         return this.$store.state.admin_menus
      },
      shop() {
         return this.$store.state.shop;
      },
      is_mini() {
         return this.$store.state.is_mini
      },
      user() {
         return this.$store.state.user.user
      },
      initialName() {
         if (this.user) {
            let named = this.user.name.split(' ').map(el => el.slice(0, 1)).join('')
            return named.slice(0, 2).toUpperCase()
         }
         return 'SW'
      }
   },
   methods: {
      getYear() {
         let date = new Date();
         return date.getFullYear();
      },
      go() {
         window.open("https://cepatshop.my.id", "_blank");
      },
      getIconColor(path) {
         if (this.is_dark) {
            return path == this.$route.name ? 'primary' : 'dark'
         } else {
            return path == this.$route.name ? 'primary' : 'white'
         }
      },
      getIconTextColor(path) {
         if (this.is_dark) {
            return path == this.$route.name ? 'white' : 'grey-5'
         } else {
            return path == this.$route.name ? 'white' : 'grey-8'
         }
      },
      exitApp() {
         this.$store.dispatch("user/exitApp", navigator);
      },
      handleDarkMode() {
         if (this.is_dark) {
            localStorage.removeItem("nav_is_light_mode");
         } else {
            localStorage.setItem("nav_is_light_mode", 1);
         }
      },

   },
   data() {
      return {
         is_dark: localStorage.getItem("nav_is_light_mode") ? false : true,
         isShowen: false,
         colors: [
            "green",
            "purple",
            "blue",
            "deep-orange",
            "teal",
            "amber-7",
            "green",
            "purple",
            "blue",
            "deep-orange",
            "teal",
            "amber-7",
            "green",
            "purple",
            "blue",
            "deep-orange",
            "teal",
            "amber-7",
         ],

      };
   },
};
</script>