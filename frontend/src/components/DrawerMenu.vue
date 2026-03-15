<template>
   <q-drawer show-if-above v-model="leftDrawerOpen" side="left" bordered :mini="is_mini" :mini-width="62">
      <MainMenu :menus="menus"></MainMenu>
   </q-drawer>
</template>
<script>
import MainMenu from "components/MainMenu.vue";
export default {
   components: { MainMenu },
   props: { 
      menus: {
         default: []
      }
   },
   computed: {
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
   },
};
</script>
