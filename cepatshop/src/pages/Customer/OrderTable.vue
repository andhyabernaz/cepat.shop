<script setup>
import { dateFormat } from 'src/utils';

defineProps(['data'])
</script>

<template>
   <div class="table-responsive">
      <table class="table aligned bordered">
         <thead>
            <tr>
               <th>Invoice</th>
               <th class="gt-xs">Total</th>
               <th>Status</th>
            </tr>
         </thead>
         <tbody>
            <tr v-for="(order, index) in data" :key="index" class="table-action"
               @click="$router.push({ name: 'UserInvoice', params: { order_ref: order.order_ref }, query: { _rdr: $route.fullPath } })">
               <td>
                  <q-item-label>{{ order.order_ref }}</q-item-label>
                  <q-item-label class="xs">{{ moneyIdr(order.billing_total) }}</q-item-label>
                  <q-item-label caption>{{ dateFormat(order.created_at) }}</q-item-label>
               </td>
               <td class="gt-xs">{{ moneyIdr(order.billing_total) }}</td>
               <td>
                  <span>
                     <q-badge class="text-center justify-center" style="min-width:100px;padding:4px;"
                        :color="getOrderStatusColor(order.order_status)">
                        {{ order.customer_status.label }}
                     </q-badge>
                  </span>
               </td>
            </tr>
         </tbody>
      </table>
   </div>

</template>
