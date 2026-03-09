<template>
   <div class="page-heading flex justify-between items-center">
      <div class="row items-center">
         <q-btn v-if="can_back" icon="arrow_back" class="q-mr-sm" padding="4px 6px" flat
            @click=handleBackButton></q-btn>
         <div :style="titleStyle">{{ title }}</div>
      </div>
      <slot></slot>
   </div>
</template>

<script>
import { createMetaMixin } from 'quasar'
export default {
   props: {
      size: {
         type: String,
         default: '23px'
      },
      title: String,
      goBack: Boolean,
      backUrl: {
         default: null
      },
      routeName: {
         default: null
      },
   },
   computed: {
      can_back() {
         return this.goBack || this.backUrl || this.routeName ? true : false
      },
      titleStyle() {
         return `font-size: ${this.size}`
      }
   },
   methods: {
      handleBackButton() {
         if (this.goBack) {
            this.$router.back()
         } else if (this.backUrl) {
            this.$router.push({ name: this.bachUrl })
         } else if (this.routeName) {
            this.$router.push({ name: this.routeName })
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