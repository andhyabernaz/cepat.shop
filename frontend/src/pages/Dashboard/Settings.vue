<template>
   <q-page class="q-pb-xl">
      <q-header :class="getHeaderColorBrand">
         <q-toolbar>
            <q-toolbar-title>
               Dashboard
            </q-toolbar-title>
            <q-btn :to="{ name: 'Home', query: { load: 'true' } }" flat icon-right="eva-arrow-forward"
               label="Lihat Toko" no-caps />
         </q-toolbar>
      </q-header>

      <MainMenu></MainMenu>
   </q-page>
</template>

<script>
import MainMenu from 'components/MainMenu.vue'
export default {
   name: 'SettingMenu',
   components: { MainMenu },
}
</script>