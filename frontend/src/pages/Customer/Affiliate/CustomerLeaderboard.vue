<template>
   <q-page padding>
      <AppHeader title="Leaderboard"></AppHeader>
      <AffiliateMenu />
      <div class="box-column flat">
         <div>
            <div class="card-subtitle flex justify-between">
               <div>Leaderboard</div>
               <q-select filled square dense label="Periode" v-model="periode" :options="periodeOptions" map-options
                  emit-value @update:modelValue="handleInputOption"></q-select>
            </div>
            <div class="table-responsive">
               <table class="table bordered dense middle">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Produk</th>
                        <th>User</th>
                        <th>Penjualan</th>
                        <th>Total Transaksi</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(item, index) in leaderboard.data" :key="index">
                        <td>{{ index + 1 }}</td>
                        <td class="flex q-gutter-sm no-wrap items-center">
                             <q-img :src="item.product_image" class="bg-white img-product-admin img-thumbnail"
                              ratio="1" width="40px" />
                              <q-item-label style="min-width: 180px;">{{ item.product_name }}</q-item-label>
                        </td>
                        <td>
                           <q-item-label>{{ item.user_name }}</q-item-label>
                        </td>
                        <td>
                           <q-item-label>{{ item.item_count }}</q-item-label>
                        </td>
                        <td class="text-nowrap">{{ moneyIdr(item.total_transaction) }}</td>
                     </tr>
                  </tbody>
               </table>
               <div class="text-center q-py-md" v-if="!leaderboard.total">Tidak ada data</div>
            </div>
            <SimplePagination v-bind="leaderboard" @loadUrl="getData"></SimplePagination>
         </div>
      </div>
   </q-page>
</template>
  
<script>
import AffiliateMenu from "./AffiliateMenu.vue";
import { BaseApi } from 'boot/axios'
export default {
   data() {
      return {
         leaderboardavailable: true,
         leaderboard: {
            data: [],
            total: 1,
            from: 1
         },
         periode: 'alltime',
         periodeOptions: [
            { label: 'Last 7 Day', value: 'weekly' },
            { label: 'Last 30 Day', value: 'monthly' },
            { label: 'Last 1 year', value: 'yearly' },
            { label: 'Alltime', value: 'alltime' },
         ]
      }
   },
   computed: {
      loading() {
         return this.$store.state.loading
      }
   },
   components: { AffiliateMenu },

   mounted() {
      this.getData()
   },
   methods: {
      handleInputOption(val) {
         this.periode = val
         this.getData()
      },
      getData(url = null) {
         this.$store.commit('SET_LOADING', true)
         if(!url) {
            url = 'affiliate/leaderboard?periode=' + this.periode
         }
         BaseApi.get(url).then(res => {
            if (res.status == 200) {
               this.leaderboard = {...res.data.data}
            }
         }).finally(() => this.$store.commit('SET_LOADING', false))
      }
   }
}
</script>
  