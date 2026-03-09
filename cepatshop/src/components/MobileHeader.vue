<template>
   <q-header class="text-grey-9 bg-white box-shadow z-max" v-if="showing">
      <q-toolbar>
         <q-btn v-if="can_back" icon="arrow_back" class="q-mr-sm" padding="4px 6px" flat
            @click=handleBackButton></q-btn>
         <q-toolbar-title class="text-weight-bold brand">{{ title }}</q-toolbar-title>
         <MenuRight />
      </q-toolbar>
   </q-header>
</template>

<script>
import { createMetaMixin } from 'quasar'
import MenuRight from 'components/MenuRight.vue'
export default {
   props: {
      size: {
         type: String,
         default: '23px'
      },
      title: {
         type: String,
         default: ''
      },
      goBack: Boolean,
      backUrl: {
         default: null
      }
   },
   components: { MenuRight },
   computed: {
      can_back() {
         return this.goBack || this.backUrl ? true : false
      },
      titleStyle() {
         return `font-size: ${this.size}`
      },
      is_mode_desktop() {
         return this.$store.getters['isModeDesktop']
      },
      showing() {
         return false
      }
   },
   methods: {
      handleBackButton() {
         if (this.goBack) {
            this.$router.back()
         } else if (this.backUrl) {
            this.$router.push({ name: this.bachUrl })
         }
      },
   },
   mixins: [
      createMetaMixin(function () {
         return {
            title: this.title
         }
      })
   ],
}
</script>