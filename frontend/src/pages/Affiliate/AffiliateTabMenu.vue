<template>
   <div class="bg-white q-mb-md">
      <q-tabs v-model="tab" align="left">
         <q-route-tab :to="{ name: 'AffiliatesIndex' }" name="transaksi" label="Transaksi"></q-route-tab>
         <q-route-tab :to="{ name: 'AffiliateUser' }" name="Users" label="Users"></q-route-tab>
      </q-tabs>
   </div>
</template>

<script>
export default {
   data() {
      return {
         tab: ''
      }
   },
   mounted() {

   }
}
</script>