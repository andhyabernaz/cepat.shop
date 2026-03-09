<template>
   <q-page padding>
      <AppHeader title="Page Visited"></AppHeader>
      <AffiliateMenu />
      <div class="box-column flat">
         <div>
            <div class="card-subtitle flex justify-between items-center">
               <div>Page Visited</div>
               <q-select filled square dense label="Periode" v-model="queryParams.period" :options="periodeOptions"
                  map-options emit-value @update:model-value="getData()"></q-select>
            </div>
            <div class="table-responsive">
               <table class="table bordered aligned">
                  <thead>
                     <tr>
                        <th>#</th>
                        <th colspan="2">Produk</th>
                        <th>Dikunjungi</th>
                        <th>Potensi Pendapatan</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr v-for="(product, idx) in leads.data" :key="product.id">
                        <td>{{ leads.from + idx }}</td>
                        <td>
                           <q-img :src="product.assets[0].src" class="bg-white img-product-admin img-thumbnail"
                              ratio="1" width="55px" />
                        </td>
                        <td>
                           <q-item-label>{{ product.title }}</q-item-label>
                           <q-item-label class="text-grey-8 text-nowrap">Harga Jual : {{ moneyIdr(getPrice(product))
                           }}</q-item-label>
                           <q-item-label class="text-grey-8">
                              <span>Komisi : {{ product.affiliate_detail }}</span>
                           </q-item-label>
                        </td>
                        <td>
                           <q-item-label>{{ product.leads_count }}</q-item-label>
                        </td>
                        <td>
                           <q-item-label>
                              {{ moneyIdr(getKomisi(product)) }}
                           </q-item-label>
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
export default {
   components: { AffiliateMenu },
   data() {
      return {
         queryParams: {
            period: 'monthly'
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
   computed: {
      leads() {
         return this.$store.state.affiliate.page_visited
      },
   },
   mounted() {
      this.getData()
   },
   methods: {
      getData(url = null) {

         setTimeout(() => {
            if (!url) {
               url = `leads/visited?${new URLSearchParams(this.queryParams).toString()}`
            }
            this.$store.dispatch('affiliate/pageVisited', url)
         }, 300)
      },
      getPrice(product) {
         if (product.maxPrice) {
            return parseInt(product.maxPrice)
         }
         return parseInt(product.price)
      },
      getKomisi(product) {
         let komisi = 0;
         if (product.aff_is_percentage == true) {
            komisi = parseInt(this.getPrice(product)) * parseInt(product.aff_amount) / 100
         } else {
            komisi = product.aff_amount
         }
         return parseInt(komisi) * parseInt(product.leads_count)
      }
   }
}
</script>