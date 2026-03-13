<template>
   <q-page padding class="admin-dashboard">
      <q-linear-progress v-if="loading" indeterminate color="primary" class="q-mb-md" />

      <q-banner v-if="errorMessage" class="bg-red-1 text-red-9 q-mb-md" rounded>
         {{ errorMessage }}
      </q-banner>

      <div class="admin-dashboard__grid">
         <q-card class="admin-dashboard__card">
            <q-card-section class="admin-dashboard__card-head">
               <div class="admin-dashboard__card-title">Total Order Hari Ini</div>
               <div class="admin-dashboard__card-value" data-test="today-orders">
                  {{ formatNumber(todayOrdersTotal) }}
               </div>
            </q-card-section>
         </q-card>

         <q-card class="admin-dashboard__card">
            <q-card-section class="admin-dashboard__card-head">
               <div class="admin-dashboard__card-title">Total Penjualan Mingguan</div>
               <div class="admin-dashboard__card-value" data-test="weekly-sales">
                  {{ formatCurrency(weeklySalesTotal) }}
               </div>
               <div class="admin-dashboard__card-caption">7 hari terakhir (paid).</div>
            </q-card-section>
         </q-card>

         <q-card class="admin-dashboard__card admin-dashboard__card--chart">
            <q-card-section class="admin-dashboard__card-head">
               <div class="admin-dashboard__card-title">Performa Penjualan</div>
               <div class="admin-dashboard__card-caption">Grafik garis 7 hari terakhir.</div>
            </q-card-section>
            <q-separator />
            <q-card-section>
               <div class="admin-dashboard__chart" role="img" aria-label="Performa penjualan mingguan">
                  <svg viewBox="0 0 240 80" preserveAspectRatio="none" class="admin-dashboard__chart-svg">
                     <polyline
                        v-if="chartPolyline"
                        :points="chartPolyline"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="3"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                     />
                     <polyline
                        v-if="chartAreaPolyline"
                        :points="chartAreaPolyline"
                        fill="currentColor"
                        opacity="0.08"
                     />
                  </svg>
                  <div class="admin-dashboard__chart-legend">
                     <div v-for="(label, idx) in chartLabels" :key="label" class="admin-dashboard__chart-label">
                        <span>{{ label }}</span>
                        <span class="admin-dashboard__chart-label-value">{{ formatCurrency(chartSeries[idx] || 0) }}</span>
                     </div>
                  </div>
               </div>
            </q-card-section>
         </q-card>

         <q-card class="admin-dashboard__card admin-dashboard__card--orders">
            <q-card-section class="admin-dashboard__orders-head">
               <div>
                  <div class="admin-dashboard__card-title">Orderan Terbaru</div>
                  <div class="admin-dashboard__card-caption">Maksimal 5 orderan.</div>
               </div>
               <q-btn
                  :to="{ name: 'OrderIndex' }"
                  flat
                  no-caps
                  icon-right="chevron_right"
                  label="Lihat semua"
               />
            </q-card-section>
            <q-separator />
            <q-card-section>
               <div class="table-responsive">
                  <table class="table aligned bordered wides">
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>No Pesanan</th>
                           <th>Customer</th>
                           <th>Status</th>
                           <th>Total</th>
                           <th>Tgl Pembelian</th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr v-for="(item, idx) in latestOrders" :key="item.id">
                           <td>{{ idx + 1 }}</td>
                           <td>{{ item.order_ref }}</td>
                           <td>{{ item.customer_name || '-' }}</td>
                           <td>
                              <q-badge class="inline-block" :color="orderStatusColor(item)">
                                 {{ orderStatusLabel(item) }}
                              </q-badge>
                           </td>
                           <td>{{ formatCurrency(orderBillingTotal(item)) }}</td>
                           <td>{{ formatDate(item.created_at) }}</td>
                        </tr>
                        <tr v-if="!latestOrders.length">
                           <td colspan="6" class="text-center q-py-md">Belum ada orderan terbaru.</td>
                        </tr>
                     </tbody>
                  </table>
               </div>
            </q-card-section>
         </q-card>
      </div>
   </q-page>
</template>

<script>
import { BaseApi } from 'boot/axios'

export default {
   name: 'AdminDashboard',
   data() {
      return {
         loading: false,
         errorMessage: '',
         todayOrdersTotal: 0,
         weeklySalesTotal: 0,
         latestOrders: [],
         chartLabels: [],
         chartSeries: [],
      }
   },
   computed: {
      chartPolyline() {
         return this.buildLinePolyline(this.chartSeries)
      },
      chartAreaPolyline() {
         return this.buildAreaPolyline(this.chartSeries)
      }
   },
   mounted() {
      this.getData()
   },
   methods: {
      getData() {
         this.loading = true
         this.errorMessage = ''

         BaseApi.get('adminReports?period=weekly&mode=widgets').then((res) => {
            const data = res.data.data || {}

            this.todayOrdersTotal = Number(data.today_orders_total || 0)
            this.weeklySalesTotal = Number(data.weekly_sales_total || 0)
            this.chartLabels = Array.isArray(data.weekly_sales_labels) ? data.weekly_sales_labels : []
            this.chartSeries = Array.isArray(data.weekly_sales_series) ? data.weekly_sales_series : []
            const orders = Array.isArray(data.latest_orders) ? data.latest_orders : []
            this.latestOrders = orders.slice(0, 5)
         }).catch(() => {
            this.errorMessage = 'Gagal memuat data dashboard. Silakan refresh halaman.'
         }).finally(() => {
            this.loading = false
         })
      },
      formatNumber(value) {
         const numeric = Number(value || 0)
         return new Intl.NumberFormat('id-ID').format(isFinite(numeric) ? numeric : 0)
      },
      formatCurrency(value) {
         const numeric = Number(value || 0)
         const formatted = new Intl.NumberFormat('id-ID').format(isFinite(numeric) ? numeric : 0)
         return `Rp ${formatted}`
      },
      formatDate(value) {
         if (!value) {
            return '-'
         }

         const date = new Date(value)
         if (Number.isNaN(date.getTime())) {
            return '-'
         }

         return new Intl.DateTimeFormat('id-ID', {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
         }).format(date)
      },
      orderStatusLabel(order) {
         return order?.admin_status?.label || order?.order_status || '-'
      },
      orderStatusColor(order) {
         const status = order?.order_status
         if (status === 'COMPLETE') return 'green'
         if (status === 'TO_PROCESS') return 'orange'
         if (status === 'PENDING') return 'grey'
         if (status === 'CANCELED') return 'red'
         return 'grey-6'
      },
      orderBillingTotal(order) {
         const billing = order?.billing_total
         if (billing !== undefined && billing !== null) {
            return Number(billing || 0)
         }
         const orderTotal = Number(order?.order_total || 0)
         const paymentFee = Number(order?.payment_fee || 0)
         return orderTotal + paymentFee
      },
      buildLinePolyline(series) {
         const values = Array.isArray(series) ? series.map((n) => Number(n || 0)) : []
         if (!values.length) {
            return ''
         }

         const width = 240
         const height = 80
         const padding = 10
         const min = 0
         const max = Math.max(...values, 1)
         const stepX = values.length > 1 ? (width - padding * 2) / (values.length - 1) : 0

         return values.map((val, idx) => {
            const x = padding + idx * stepX
            const y = height - padding - (val - min) / (max - min) * (height - padding * 2)
            return `${x},${y}`
         }).join(' ')
      },
      buildAreaPolyline(series) {
         const line = this.buildLinePolyline(series)
         if (!line) {
            return ''
         }
         const width = 240
         const height = 80
         const padding = 10
         const first = `${padding},${height - padding}`
         const last = `${width - padding},${height - padding}`
         return `${first} ${line} ${last}`
      },
   },
}
</script>

<style scoped>
.admin-dashboard__grid {
   display: grid;
   gap: 16px;
   grid-template-columns: repeat(12, minmax(0, 1fr));
}

.admin-dashboard__card {
   border-radius: 20px;
   border: 1px solid var(--cs-border-light);
   box-shadow: var(--cs-shadow-card);
}

.admin-dashboard__card-head {
   display: grid;
   gap: 6px;
}

.admin-dashboard__card-title {
   font-weight: 700;
}

.admin-dashboard__card-value {
   font-size: 1.7rem;
   font-weight: 800;
}

.admin-dashboard__card-caption {
   color: var(--cs-text-secondary);
   font-size: 0.85rem;
}

.admin-dashboard__card:nth-child(1),
.admin-dashboard__card:nth-child(2) {
   grid-column: span 6;
}

.admin-dashboard__card--chart {
   grid-column: span 7;
}

.admin-dashboard__card--orders {
   grid-column: span 5;
}

.admin-dashboard__orders-head {
   display: flex;
   justify-content: space-between;
   gap: 14px;
   align-items: center;
}

.admin-dashboard__chart {
   display: grid;
   gap: 12px;
}

.admin-dashboard__chart-svg {
   width: 100%;
   height: 120px;
   color: var(--q-primary);
}

.admin-dashboard__chart-legend {
   display: grid;
   gap: 6px;
}

.admin-dashboard__chart-label {
   display: flex;
   justify-content: space-between;
   gap: 10px;
   font-size: 0.85rem;
   color: var(--cs-text-secondary);
}

.admin-dashboard__chart-label-value {
   color: var(--cs-text-primary);
   font-weight: 600;
}

@media (max-width: 1200px) {
   .admin-dashboard__card:nth-child(1),
   .admin-dashboard__card:nth-child(2),
   .admin-dashboard__card--chart,
   .admin-dashboard__card--orders {
      grid-column: span 12;
   }

   .admin-dashboard__orders-head {
      flex-direction: column;
      align-items: stretch;
   }
}
</style>
