<template>
   <q-page padding>
     <AppHeader title="Affiliate"></AppHeader>
     <AffiliateTabMenu></AffiliateTabMenu>
      <div class="q-mt-md box-column flat">
         <div>
            <div class="card-subtitle row justify-between">
               <div>List Transaksi</div>
               <div class="row q-gutter-x-sm">

                  <q-select style="min-width:150px" filled square dense label="Status" v-model="queryParam.status" :options="[
                     'ALL', 'UNPAID', 'PROCESSED' ,'COMPLETED', 'FAILED'
                  ]"  @update:model-value="loadData()"></q-select>
                  <q-select style="min-width:150px" filled square dense label="Periode" v-model="queryParam.periode" :options="periodeOptions"
                     map-options emit-value @update:model-value="loadData()"></q-select>
               </div>
            </div>
            <div class="table-responsive">
               <table class="table bordered middle dense">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th>Created At</th>
                        <th>Produk</th>
                        <th>User Ref</th>
                        <th>Invoice</th>
                        <th>Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(item, index) in leads.data" :key="index">
                        <td>{{ leads.from + index }}</td>
                        <td class="text-nowrap">{{ dateFormat(item.created_at) }}</td>
                        <td>
                           <div class="row q-gutter-x-sm items-center">
                              <q-img :src="item.asset" v-if="item.asset" class="img-icon"></q-img>
                              <q-item-label>{{ item.product_name }}</q-item-label>
                           </div>
                        </td>
                        <td>
                           <q-item-label>{{ item.user_name }}</q-item-label>
                           <q-item-label caption>{{ item.user_phone }}</q-item-label>
                           <!-- <q-item-label caption>{{ item.user_email }}</q-item-label> -->
                        </td>
                        <td>
                           <q-item-label>{{ item.invoice_ref }}</q-item-label>
                        </td>
                        <td>
                           <div>
                              <q-badge :color="getLeadColor(item.order_status)">  {{ item.order_status }}</q-badge>
                           </div>

                        </td>
                     </tr>
                  </tbody>
               </table>
               <div class="text-center q-py-md" v-if="!leads.total">Tidak ada data</div>
            </div>
            <SimplePagination v-bind="leads" @loadUrl="getData"></SimplePagination>
         </div>
      </div>
   </q-page>
</template>
  
<script>
import { BaseApi } from 'boot/axios'
import AffiliateTabMenu from './AffiliateTabMenu'
import { dateFormat } from 'src/utils';
export default {
   components: { AffiliateTabMenu },
   data() {
      return {
         leads: {
            data: [],
            from: 1,
            total: 1,
         },
         lead_total: 0,
         queryParam: {
            periode: 'alltime',
            status: 'ALL',
            skip: 0
         },
         periodeOptions: [
            { label: 'Last 7 Day', value: 'weekly' },
            { label: 'Last 30 Day', value: 'monthly' },
            { label: 'Last 1 year', value: 'yearly' },
            { label: 'Alltime', value: 'alltime' },
         ]
      }
   },
   created() {
      this.getData()
   },
   methods: {
      getLeadColor(status) {
         if (status == 'COMPLETED') {
            return 'green'
         }
         if (status == 'PROCESSED') {
            return 'teal'
         }
         return 'grey-8'
      },

      getData(url = null) {
         if(!url) {
            url =`affiliates?${new URLSearchParams(this.queryParam).toString()}`
         }
         this.$store.commit('SET_LOADING', true)
         BaseApi.get(url).then(res => {
            if (res.status == 200) {
               this.leads = {...res.data.data}
            }
         }).finally(() => this.$store.commit('SET_LOADING', false))
      },
      loadData(url = null) {
        setTimeout(() => {
          this.getData(url)
        }, 100)
      }
   }
}
</script>
  