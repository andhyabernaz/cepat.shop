<script>
import { BaseApi } from "boot/axios";
import { dateFormat } from "src/utils";
export default {
   data() {
      return {
         loading: false,
         periodes: [
            { label: 'Hari Ini', value: 'today' },
            { label: 'Bulan Ini', value: 'monthly' },
            { label: 'Tahun Ini', value: 'yearly' },
            { label: 'Semua Data', value: 'alltime' },
         ],
         latest_orders: [],
         order_reports: [],
         transaction_reports: [],
         filter: {
            period: 'monthly'
         }
      };
   },
   mounted() {
      this.getData();
   },
   methods: {
      getData() {
          this.$store.commit('SET_LOADING', true)
         this.loading = true
         BaseApi.get("adminReports").then((res) => {
            this.latest_orders = res.data.data.latest_orders;
            this.order_reports = res.data.data.order_reports;
            this.transaction_reports = res.data.data.transaction_reports;
         }).finally(() => {
            this.loading = false
         })
      },
      getTransactionReports() {
         this.loading = true
         setTimeout(() => {

            BaseApi.get(`reports/transactions?${new URLSearchParams(this.filter).toString()}`).then(res => {
               this.transaction_reports = res.data.data
            }).finally(() => {
               this.loading = false
            })
         }, 200)
      }
   },
};
</script>

<template>
   <q-page padding>
      <AppHeader title="Dashboard"></AppHeader>
      <div v-if="order_reports.length">
         <div class="grid-2to3">
            <q-card v-for="(item, idx) in order_reports" :key="idx" class="shadow overflow-hidden">
               <q-card-section class="q-pa-lg bg-white">
                  <q-list>
                     <q-item>
                        <q-item-section avatar v-if="item.icon">
                           <q-avatar :icon="item.icon" size="xl" :color="item.color" text-color="white"
                              rounded></q-avatar>
                        </q-item-section>
                        <q-item-section>

                           <q-item-label class="text-lg">{{ item.total }}</q-item-label>
                           <q-item-label class="text-grey-7">{{ item.label }}</q-item-label>
                        </q-item-section>
                     </q-item>
                  </q-list>
               </q-card-section>
            </q-card>
         </div>
      </div>
      <div class="q-mt-xl" v-if="transaction_reports.length">
         <div class="card-title q-mb-md flex justify-between items-center">
            <div>Penjualan</div>
            <q-select filled dense :options="periodes" v-model="filter.period" label="Periode" style="min-width: 140px;"
               @update:model-value="getTransactionReports" emit-value map-options></q-select>
         </div>
         <div class="grid-3-auto">
            <q-card v-for="(item, idx) in transaction_reports" :key="idx" class="shadow overflow-hidden">
               <q-card-section class="q-pa-md bg-white full-height">
                  <q-list class="q-pb-lg">
                     <q-item dense>

                        <q-item-section>

                           <q-item-label>{{ item.label }}</q-item-label>
                           <q-item-label class="text-md2 q-pt-sm" :class="`text-${item.color}`">
                              Rp {{ moneyFormat(item.total) }}
                           </q-item-label>
                           <q-item-label v-if="item.desc" caption class="q-mt-md">{{
                              item.desc
                              }}
                           </q-item-label>
                        </q-item-section>

                        <q-item-section side top v-if="item.icon">
                           <q-icon :name="item.icon" size="md" :color="item.color" text-color="white" rounded></q-icon>
                        </q-item-section>


                     </q-item>
                  </q-list>

               </q-card-section>
            </q-card>
         </div>
      </div>
      <div class="q-mt-lg">
         <div class="card-title flex justify-between items-center q-mb-md">
            <div>Pesanan Terbaru</div>
            <q-btn :to="{ name: 'OrderIndex' }" label="Selengkapnya" flat padding="2px 12px" icon-right="chevron_right"
               no-caps class="btn-action"></q-btn>
         </div>
         <q-card class="section shadow text-grey-8">
            <q-card-section>
               <div class="table-responsive">
                  <table class="table aligned bordered wides">
                     <thead>
                        <tr>
                           <th v-for="h in [
                              '#',
                              'No Pesanan',
                              'Status',
                              'Total',
                              'Tgl Pembelian',
                           ]" :key="h">
                              {{ h }}
                           </th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr v-for="(item, idx) in latest_orders" :key="item.id">
                           <td>{{ idx + 1 }}</td>
                           <td>{{ item.order_ref }}</td>
                           <td>
                              <q-badge class="inline-block" :color="getOrderStatusColor(item.order_status)">
                                 {{ item.admin_status.label }}
                              </q-badge>
                           </td>
                           <td>{{ moneyIdr(item.billing_total) }}</td>
                           <td>{{ dateFormat(item.created_at) }}</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
               <div class="text-center q-py-md" v-if="!latest_orders.length">
                  Tidak ada data
               </div>
            </q-card-section>
         </q-card>
      </div>
   </q-page>
</template>