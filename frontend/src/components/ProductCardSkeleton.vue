<template>
   <div class="column full-height relative bg-white">
      <q-skeleton :height="imgHeight" square />
      <div class="relative col column justify-between q-pa-sm">
         <div class="flex justify-between items-center">
            <q-skeleton type="text" width="50%" />
            <q-skeleton type="circle" size="20px" />
         </div>
         <q-skeleton type="text" width="90%" />
         <q-skeleton class="q-mt-sm" height="20px" width="50%" />
      </div>
   </div>
</template>
<script>

export default {
   props: ['width'],
   computed: {
      imgHeight() {
         if (this.width) {
            return `${this.width}px`
         }
         return '170px'
      },
      page_width() {
         return this.$store.state.page_width
      }
   }
}
</script>
