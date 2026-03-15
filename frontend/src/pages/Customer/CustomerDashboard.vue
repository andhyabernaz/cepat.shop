<template>
   <q-page>
      <div class="header_banner">

         <div class="relative flex justify-center">
            <div class="user-point">
               <UserSaldoBlock></UserSaldoBlock>

            </div>
         </div>
      </div>

      <div class="q-pa-sm">

         <div class="box-column q-mt-md">

            <div class="">
               <div class=" flex justify-between q-mb-sm items-center">
                  <div class="text-weight-bold text-md">Histori Pesanan</div>
                  <q-btn color="primary" label="Selengkapnya" flat dense no-caps icon-right="eva-chevron-right"
                     :to="{ name: 'CustomerOrder' }"></q-btn>
               </div>

               <OrderTable :data="currentOrders.data"></OrderTable>

               <div class="text-center q-py-lg text-grey-6" v-if="!currentOrders.total">Tidak ada data</div>
            </div>
         </div>

         <div class="box-column q-mt-lg">

            <div class="">
               <div class=" flex justify-between q-mb-sm items-center">
                  <div class="text-weight-bold text-md">Histori Mutasi Saldo</div>
                  <q-btn color="primary" label="Selengkapnya" flat dense no-caps icon-right="eva-chevron-right"
                     :to="{ name: 'CustomerMutasiSaldo' }"></q-btn>
               </div>

               <MutasisaldoTable :data="mutasiSaldo"></MutasisaldoTable>
               <div class="text-center q-py-lg text-grey-6" v-if="!mutasiSaldo.total">
                  Tidak ada data
               </div>
            </div>
         </div>
      </div>

   </q-page>
</template>

<script>
import { mapState } from 'vuex'
import { BaseApi } from 'boot/axios'
import MutasisaldoTable from 'components/MutasisaldoTable.vue'
import OrderTable from './OrderTable.vue';
import UserSaldoBlock from 'components/UserSaldoBlock.vue';
export default {
   components: { MutasisaldoTable, OrderTable, UserSaldoBlock },
   data() {
      return {
         modal: false,
         search: '',
         isPwd: true,
         isPwd1: true,
         changePassword: false,
         deposit_amount: 0,
         mutasiSaldo: {
            data: [],
            from: 1,
            total: 0
         },
         currentOrders: {
            data: [],
            from: 1,
            total: 1
         },
      }
   },
   computed: {
      ...mapState({
         user: state => state.user.user,
         shop: state => state.shop,
      }),
   },
   created() {
      this.getCurrentOrders()
      this.getMutasiSaldo()
   },
   methods: {
      getCurrentUSer() {
          this.$store.dispatch('user/getUser')
      },
      getMutasiSaldo() {
         BaseApi.get('customer/mutasi-saldos?category=Default?per_page=5').then(res => {
            this.mutasiSaldo = res.data.data
         })
      },
      getCurrentOrders() {
         this.$store.commit('SET_LOADING', true)
         let url = 'customer/orders?per_page=5'
         BaseApi.get(url).then(res => {
            this.currentOrders = res.data.data
         })
      },
      logout() {
         this.$store.dispatch('user/logout')
      }
   }

}
</script>