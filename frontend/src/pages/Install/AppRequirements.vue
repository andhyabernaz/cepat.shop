<script>
// eslint-disable-next-line no-unused-vars
import { Api } from "boot/axios";
export default {

   data() {
      return {
         requirements: []
      }
   },
   mounted() {
      this.getData()
   },
   methods: {
      getData() {
         Api.get('installer/server').then(res => {
            this.requirements = res.data.data

            if (!this.requirements.length) {
               this.$emit('onDone')
            }
         })
      }
   }

}
</script>

<template>
   <div class="q-py-sm">
      <div class="block-title q-mb-md">
         <h2>Server Requirements</h2>
      </div>
      <ul>
         <li v-for="(val, i) in requirements" :key="i">{{ val }}</li>
      </ul>
      <div class="q-pa-md bg-green-1" v-if="!requirements.length">
         Selamat Server anda sudah siap!
      </div>
   </div>
</template>