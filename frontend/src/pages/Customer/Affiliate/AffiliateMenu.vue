<template>
   <div class="bg-white q-mb-md">
      <q-tabs v-model="tab" align="left">
         <q-route-tab :to="{ name: 'CustomerAffiliate' }" name="Link" label="Affiliasi"></q-route-tab>
         <q-route-tab :to="{ name: 'CustomerAffiliateProduct' }" name="Product" label="Produk Affiliasi"></q-route-tab>
         <q-route-tab :to="{ name: 'CustomerPageVisited' }" name="Visited" label="Page Visited"></q-route-tab>
         <q-route-tab :to="{ name: 'CustomerLeads' }" name="Leads" label="Leads"></q-route-tab>
         <q-route-tab :to="{ name: 'CustomerLeaderboard' }" name="Leaderboard" label="Leaderboard"></q-route-tab>
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