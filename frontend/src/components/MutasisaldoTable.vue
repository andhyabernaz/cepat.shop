<script setup>
import { moneyFormat, dateFormat } from "src/utils";

defineProps(['data'])

</script>

<template>
   <div class="table-responsive">
      <table class="table bordered aligned">
         <thead>
            <tr>
               <th>#</th>
               <th>Tanggal</th>
               <th>Detail</th>
               <th>Jumlah</th>
               <th>Saldo</th>
            </tr>
         </thead>
         <tbody>
            <tr v-for="(item, i) in data.data" :key="i">
               <td>{{ data.from + i }}</td>
               <td>{{ dateFormat(item.created_at) }}</td>
               <td>
                  <q-item-label> {{ item.description }} </q-item-label>
                  <q-item-label v-if="item.note" class="text-red-6 text-xs">
                     {{ item.note }}
                  </q-item-label>
               </td>

               <td class="text-no-wrap text-weight-bold">
                  <span class="text-green" v-if="item.type == 'IN'"
                     :class="{ 'text-green': item.type == 'IN', 'text-red': item.type == 'OUT', }">
                     +{{ moneyFormat(item.amount) }}
                  </span>
                  <span v-else class="text-red">
                     -{{ moneyFormat(item.amount) }}
                  </span>
               </td>
               <td class="text-no-wrap text-weight-bold"> {{ moneyFormat(item.last_saldo) }}</td>
            </tr>

         </tbody>
      </table>
   </div>

</template>
