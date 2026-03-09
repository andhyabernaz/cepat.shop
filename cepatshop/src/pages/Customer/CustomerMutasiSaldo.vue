<script setup>
import { computed, onMounted, ref } from "vue";
import { BaseApi } from "src/boot/axios";
import MutasisaldoTable from 'components/MutasisaldoTable.vue'
import { useRoute } from 'vue-router'
import { useStore } from "vuex";

const route = useRoute()
const store = useStore()

const mutasiSaldo = ref({
   data: [],
   from: 1,
   per_page: 6
})

const category = ref(route.query.category || 'Default')

const getData = (url = null) => {
   store.commit("SET_LOADING", true);
   if (!url) {
      url = `customer/mutasi-saldos?category=${category.value}`
   }
   BaseApi.get(url).then(res => {
      mutasiSaldo.value = { ...res.data.data }
   })
}

onMounted(() => {
   getData()
})

const title = computed(() => {
   if (category.value == 'Affiliate') {
      return "Riwayat Transaksi Affiliate"
   }
   return "Riwayat Mutasi Saldo"
})

</script>

<template>
   <q-page padding>

      <AppHeader :title="title"></AppHeader>

      <div class="box-column flat">

         <MutasisaldoTable :data="mutasiSaldo"></MutasisaldoTable>
         <div class="text-center q-py-lg" v-if="!mutasiSaldo.total">Tidak ada data</div>
         <SimplePagination v-bind="mutasiSaldo" @loadUrl="getData" />
      </div>
   </q-page>
</template>
