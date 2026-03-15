<template>
   <q-page padding>
      <AppHeader title="Pesanan Saya"></AppHeader>

      <div class="box-shadow bg-white text-dark q-mb-md">

         <q-tabs outside-arrows mobile-arrows v-model="queryParams.status" active-color="primary"
            @update:model-value="changeTab">
            <q-tab v-for="option in statuses" :key="option.value" :name="option.value" :label="option.label"
               no-caps></q-tab>
         </q-tabs>
      </div>
      <div class="box-column flat">
         <OrderTable :data="orders.data"></OrderTable>
         <div v-if="!orders.total" class="text-center q-py-md">Tidak ada data</div>
      </div>

      <SimplePagination autoHide v-bind="orders" @loadUrl="getData"></SimplePagination>
   </q-page>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import OrderTable from './OrderTable.vue';
export default {
   name: 'CustomerOrderIndex',
   components: { OrderTable },
   data() {
      return {
         filter: 'ALL',
         statuses: [
            { value: 'ALL', label: 'Semua' },
            { value: 'PENDING', label: 'Pending' },
            { value: 'TOSHIP', label: 'Sedang Diproses' },
            { value: 'SHIPPING', label: 'Sedang Dikirim' },
            { value: 'AWAITING_PICKUP', label: 'Belum Diambil' },
            { value: 'COMPLETE', label: 'Selesai' },
            { value: 'CANCELED', label: 'Batal' }
         ],
         queryParams: {
            status: this.$route.query.status || 'ALL',
            per_page: 6
         }
      }
   },
   computed: {
      ...mapState({
         orders: state => state.order.customer_order,
      }),
   },
   mounted() {
      if (!this.total) {
         this.getData()
      }
   },
   methods: {
      ...mapActions('order', ['getCustomerOrders', 'getPaginateCustomerOrder']),
      changeTab(newVal) {
         this.$router.replace({ name: 'CustomerOrder', query: { status: newVal } })
         this.getData()
      },
      paginate(url) {
         this.getData(url)
      },
      getData(url = null) {
         this.$store.commit("SET_LOADING", true);
         if (!url) {
            url = `customer/orders?${new URLSearchParams(this.queryParams).toString()}`
         }

         this.getCustomerOrders(url)

      }
   }
}
</script>
