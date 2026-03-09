<template>
   <q-page padding>
      <AppHeader title="Leads"></AppHeader>
      <AffiliateMenu />
       <div class="box-column flat">
         <div>
            <div class="card-subtitle flex justify-between">
               <div>Leads</div>
               <div class="row q-gutter-x-sm">

                  <q-select style="width:120px" filled square dense label="Status" v-model="queryParam.status" :options="[
                     'ALL', 'UNPAID', 'PROCESSED', 'COMPLETED', 'CANCELLED'
                  ]" @update:modelValue="handleInputOption"></q-select>
                  <q-select filled square dense label="Periode" v-model="queryParam.periode" :options="periodeOptions"
                     map-options emit-value @update:modelValue="handleInputOption"></q-select>
               </div>
            </div>
            <div class="table-responsive">
               <table class="table bordered middle">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th colspan="2">Detail</th>
                        <th>No Pesanan</th>
                        <th>Customer</th>
                        <th>Status</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(item, index) in leads.data" :key="index">
                           <td>{{ leads.from + index}}</td>
                        <td>

                           <q-img :src="item.product_image" class="bg-white img-product-admin img-thumbnail" ratio="1"
                              width="55px" />
                        </td>
                        <td>
                           <q-item-label style="min-width:180px">{{ item.product_name }}</q-item-label>
                           <q-item-label class="text-nowrap text-grey-8">Subtotal {{ moneyIdr(item.subtotal)
                              }}</q-item-label>
                           <q-item-label class="text-nowrap text-grey-8">Komisi {{ item.aff_is_percentage ?
                              `${item.aff_amount}%` : moneyIdr(item.aff_amount) }}</q-item-label>
                        </td>
                        <td class="text-nowrap">{{ item.invoice_ref }}</td>
                        <td>
                           <q-item-label>{{ item.user_name }}</q-item-label>
                           <q-item-label class="text-grey-8">{{ item.user_phone }}</q-item-label>
                           <q-item-label class="text-grey-8">{{ item.user_email }}</q-item-label>
                        </td>
                        <td>
                           <q-badge :color="getLeadColor(item.order_status)">{{ item.order_status }}</q-badge>
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
import AffiliateMenu from "./AffiliateMenu.vue";
import { BaseApi } from 'boot/axios'
export default {
   data() {
      return {
         leads: {
            data: [],
            total: 1,
            from: 1
         },
         queryParam: {
            periode: 'monthly',
            status: 'ALL',
            skip: 0
         },
         periodeOptions: [
            { label: 'Today', value: 'today' },
            { label: 'Last 7 Day', value: 'weekly' },
            { label: 'Last 30 Day', value: 'monthly' },
            { label: 'Last 1 year', value: 'yearly' },
            { label: 'Alltime', value: 'alltime' },
         ]
      }
   },
   components: { AffiliateMenu },

   mounted() {
      this.getData()
   },
   computed: {
      loading() {
         return this.$store.state.loading
      }
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
      handleInputOption() {
         this.leads = []
         this.queryParam.skip = 0
         this.getData()
      },
      getData(url = null) {
         this.$store.commit('SET_LOADING', true)
         if (!url) {
            url = `affiliate/getLeads?${new URLSearchParams(this.queryParam).toString()}`
         }
         BaseApi.get(url).then(res => {
            if (res.status == 200) {
               this.leads = { ...res.data.data }
            }
         }).finally(() => this.$store.commit('SET_LOADING', false))
      },
   }
}
</script>