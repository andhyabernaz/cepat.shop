<script setup>
import { onMounted, reactive, ref } from "vue";
import { BaseApi } from "src/boot/axios";
import { moneyFormat, dateFormat } from "src/utils";
import { useStore } from "vuex";

const store = useStore()

const mutasiSaldo = ref({
   data: [],
   from: 1,
   per_page: 10
})

const queryParams = reactive({
   per_page: 10,
   from: '',
   to: '',
   category: 'Default'
})

const getData = (url = null) => {
   store.commit('SET_LOADING', true)
   if (!url) {
      url = 'mutasi-saldos'
   }
   BaseApi.get(url).then(res => {
      mutasiSaldo.value = { ...res.data.data }
   })
}

onMounted(() => {
   getData()
})

</script>

<template>
   <q-page padding>

      <AppHeader title="Mutasi Saldo" />
      <div class="box-column rounded-borders">
         <div class="table-responsive">
            <table class="table bordered">
               <thead>
                  <tr>
                     <th>#</th>
                     <th>Tanggal</th>
                     <th>Detail</th>
                     <!-- <th>Status</th> -->
                     <th>Jumlah</th>
                     <th class="gt-xs">User</th>
                  </tr>
               </thead>
               <tbody>
                  <tr v-for="(item, i) in mutasiSaldo.data" :key="i">
                     <td>{{ mutasiSaldo.from + i }}</td>
                     <td>
                        {{ dateFormat(item.created_at, { hour: '2-digit', minute: '2-digit' }) }}
                     </td>

                     <td>
                        <q-item-label> {{ item.description }} </q-item-label>
                        <q-item-label v-if="item.note">
                           <span class="text-red-6 text-xs" style="padding: 1px 3px">
                              {{ item.note }}
                           </span>
                        </q-item-label>
                        <q-item-label caption class="xs">{{ item.user?.name }}</q-item-label>
                     </td>
                     <!-- <td>
                        <span class="text-weight-bold" :class="item.status == 'Success' ? 'text-green' : 'text-red'
                           ">
                           {{ item.status }}
                        </span>
                     </td> -->
                     <td class="text-no-wrap">
                        {{ moneyFormat(item.amount) }}
                     </td>
                     <td class="gt-xs">{{ item.user?.name }}</td>
                  </tr>

                  <tr v-if="!mutasiSaldo.total">
                     <td colspan="7" style="text-align: center !important">
                        Belum ada data
                     </td>
                  </tr>
               </tbody>
            </table>
         </div>
         <SimplePagination v-bind="mutasiSaldo" @loadUrl="getData" />
      </div>
   </q-page>
</template>
