<template>
   <q-page padding class="admin-dashboard">
      <q-linear-progress v-if="anyLoading" indeterminate color="primary" class="q-mb-md" />

      <q-banner v-if="errorMessage" class="bg-red-1 text-red-9 q-mb-md" rounded>
         {{ errorMessage }}
      </q-banner>

      <div class="admin-dashboard__grid">
         <BlockObserver @onObserve="observeWidgets">
            <q-card ref="salesAnchor" class="admin-dashboard__card admin-dashboard__card--kpi">
               <q-card-section class="admin-dashboard__card-head">
                  <div class="admin-dashboard__card-title">Total Order Hari Ini</div>
                  <div class="admin-dashboard__card-value" data-test="today-orders">
                     <template v-if="widgetsLoaded">{{ formatNumber(todayOrdersTotal) }}</template>
                     <q-skeleton v-else type="text" width="90px" />
                  </div>
               </q-card-section>
            </q-card>
         </BlockObserver>

         <BlockObserver @onObserve="observeWidgets">
            <q-card class="admin-dashboard__card admin-dashboard__card--kpi">
               <q-card-section class="admin-dashboard__card-head">
                  <div class="admin-dashboard__card-title">Total Penjualan Mingguan</div>
                  <div class="admin-dashboard__card-value" data-test="weekly-sales">
                     <template v-if="widgetsLoaded">{{ formatCurrency(weeklySalesTotal) }}</template>
                     <q-skeleton v-else type="text" width="140px" />
                  </div>
                  <div class="admin-dashboard__card-caption">7 hari terakhir (paid).</div>
               </q-card-section>
            </q-card>
         </BlockObserver>

         <BlockObserver @onObserve="observeWidgets">
            <q-card class="admin-dashboard__card admin-dashboard__card--chart">
               <q-card-section class="admin-dashboard__card-head">
                  <div class="admin-dashboard__card-title">Performa Penjualan</div>
                  <div class="admin-dashboard__card-caption">Grafik garis 7 hari terakhir.</div>
               </q-card-section>
               <q-separator />
               <q-card-section>
                  <div v-if="widgetsLoaded" class="admin-dashboard__chart" role="img" aria-label="Performa penjualan mingguan">
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
                  <div v-else class="admin-dashboard__chart">
                     <q-skeleton height="120px" />
                     <div class="admin-dashboard__chart-legend">
                        <q-skeleton v-for="n in 3" :key="n" type="text" />
                     </div>
                  </div>
               </q-card-section>
            </q-card>
         </BlockObserver>

         <BlockObserver @onObserve="observeOrders">
            <q-card class="admin-dashboard__card admin-dashboard__card--orders">
               <q-card-section class="admin-dashboard__orders-head">
                  <div>
                     <div class="admin-dashboard__card-title">Orderan Terbaru</div>
                     <div class="admin-dashboard__card-caption">Tampilkan per halaman {{ ordersPerPage }} orderan.</div>
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
                  <div v-if="ordersLoaded" class="table-responsive">
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
                           <tr
                              v-for="(item, idx) in ordersRows"
                              :key="item.id"
                              class="admin-dashboard__row"
                              @click="$router.push({ name: 'OrderDetail', params: { id: item.id } })"
                           >
                              <td>{{ (ordersPagination.from || 0) + idx }}</td>
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
                           <tr v-if="!ordersRows.length">
                              <td colspan="6" class="text-center q-py-md">Belum ada orderan terbaru.</td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
                  <div v-else class="table-responsive">
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
                           <tr v-for="n in ordersPerPage" :key="n">
                              <td><q-skeleton type="text" width="18px" /></td>
                              <td><q-skeleton type="text" width="120px" /></td>
                              <td><q-skeleton type="text" width="120px" /></td>
                              <td><q-skeleton type="text" width="90px" /></td>
                              <td><q-skeleton type="text" width="110px" /></td>
                              <td><q-skeleton type="text" width="100px" /></td>
                           </tr>
                        </tbody>
                     </table>
                  </div>
               </q-card-section>
               <q-separator />
               <div class="q-px-sm q-pb-sm">
                  <SimplePagination v-if="ordersLoaded" v-bind="ordersPagination" autoHide @loadUrl="loadOrders" />
               </div>
            </q-card>
         </BlockObserver>
      </div>

      <BlockObserver @onObserve="observeReports">
         <div ref="reportsAnchor" class="admin-dashboard__reports q-mt-md">
            <q-card class="admin-dashboard__card">
               <q-card-section class="admin-dashboard__reports-head">
                  <div>
                     <div class="admin-dashboard__card-title">Ringkasan Performa</div>
                     <div class="admin-dashboard__card-caption">Update otomatis saat halaman terbuka.</div>
                  </div>
                  <q-btn-toggle
                     v-model="reportPeriod"
                     no-caps
                     unelevated
                     toggle-color="primary"
                     color="grey-2"
                     text-color="grey-8"
                     :options="reportPeriodOptions"
                     @update:model-value="loadReports"
                  />
               </q-card-section>
               <q-separator />
               <q-card-section>
                  <div class="admin-dashboard__reports-grid">
                     <q-card
                        v-for="card in transactionReports"
                        :key="card.label"
                        class="admin-dashboard__metric"
                        flat
                        bordered
                     >
                        <q-card-section class="admin-dashboard__metric-body">
                           <div class="admin-dashboard__metric-label">{{ card.label }}</div>
                           <div class="admin-dashboard__metric-value">{{ formatCurrency(card.total) }}</div>
                           <div v-if="card.desc" class="admin-dashboard__metric-caption">{{ card.desc }}</div>
                        </q-card-section>
                     </q-card>
                     <q-card
                        v-for="card in orderReports"
                        :key="card.label"
                        class="admin-dashboard__metric"
                        flat
                        bordered
                     >
                        <q-card-section class="admin-dashboard__metric-body">
                           <div class="admin-dashboard__metric-label">{{ card.label }}</div>
                           <div class="admin-dashboard__metric-value">{{ formatNumber(card.total) }}</div>
                        </q-card-section>
                     </q-card>
                  </div>
               </q-card-section>
            </q-card>
         </div>
      </BlockObserver>
   </q-page>
</template>

<script>
import { BaseApi } from 'boot/axios'
import BlockObserver from 'components/BlockObserver.vue'
import SimplePagination from 'components/SimplePagination.vue'

export default {
   name: 'AdminDashboard',
   components: { BlockObserver, SimplePagination },
   props: {
      initialSection: {
         type: String,
         default: '',
      },
   },
   data() {
      return {
         errorMessage: '',
         todayOrdersTotal: 0,
         weeklySalesTotal: 0,
         chartLabels: [],
         chartSeries: [],
         widgetsLoaded: false,
         widgetsLoading: false,
         ordersLoaded: false,
         ordersLoading: false,
         reportsLoaded: false,
         reportsLoading: false,
         pollerId: null,
         ordersPagination: {},
         ordersPerPage: 5,
         reportPeriod: 'today',
         transactionReports: [],
         orderReports: [],
         reportPeriodOptions: [
            { label: 'Hari Ini', value: 'today' },
            { label: 'Minggu Ini', value: 'weekly' },
            { label: 'Bulanan', value: 'monthly' },
         ],
      }
   },
   computed: {
      anyLoading() {
         return this.widgetsLoading || this.ordersLoading || this.reportsLoading
      },
      chartPolyline() {
         return this.buildLinePolyline(this.chartSeries)
      },
      chartAreaPolyline() {
         return this.buildAreaPolyline(this.chartSeries)
      },
      ordersRows() {
         return Array.isArray(this.ordersPagination?.data) ? this.ordersPagination.data : []
      }
   },
   mounted() {
      this.scrollToInitialSection()
   },
   beforeUnmount() {
      this.stopPolling()
   },
   methods: {
      scrollToInitialSection() {
         const target = this.initialSection === 'reports'
            ? this.$refs.reportsAnchor
            : this.initialSection === 'sales'
               ? this.$refs.salesAnchor?.$el
               : null

         if (!target) {
            return
         }

         window.setTimeout(() => {
            const el = target?.$el ? target.$el : target
            if (el && typeof el.scrollIntoView === 'function') {
               el.scrollIntoView({ behavior: 'smooth', block: 'start' })
            }
         }, 50)
      },
      observeWidgets() {
         if (this.widgetsLoaded || this.widgetsLoading) {
            return
         }
         this.loadWeeklyWidgets()
      },
      loadWeeklyWidgets({ silent } = {}) {
         if (this.widgetsLoading) {
            return
         }

         this.widgetsLoading = true
         if (!silent) {
            this.errorMessage = ''
         }

         BaseApi.get('adminReports?period=weekly&mode=widgets', { silent: Boolean(silent) }).then((res) => {
            const data = res.data.data || {}

            this.todayOrdersTotal = Number(data.today_orders_total || 0)
            this.weeklySalesTotal = Number(data.weekly_sales_total || 0)
            this.chartLabels = Array.isArray(data.weekly_sales_labels) ? data.weekly_sales_labels : []
            this.chartSeries = Array.isArray(data.weekly_sales_series) ? data.weekly_sales_series : []
            this.widgetsLoaded = true
            this.startPolling()
         }).catch(() => {
            this.errorMessage = 'Gagal memuat data dashboard. Silakan refresh halaman.'
         }).finally(() => {
            this.widgetsLoading = false
         })
      },
      observeOrders() {
         if (this.ordersLoaded || this.ordersLoading) {
            return
         }
         this.loadOrders()
      },
      loadOrders(url = null) {
         if (this.ordersLoading) {
            return
         }

         this.ordersLoading = true
         const endpoint = url || `orders?per_page=${this.ordersPerPage}`
         BaseApi.get(endpoint, { silent: true }).then((res) => {
            const data = res.data.data || {}
            this.ordersPagination = data
            this.ordersLoaded = true
            this.startPolling()
         }).catch(() => {
            this.errorMessage = 'Gagal memuat orderan terbaru.'
         }).finally(() => {
            this.ordersLoading = false
         })
      },
      observeReports() {
         if (this.reportsLoaded || this.reportsLoading) {
            return
         }
         this.loadReports(this.reportPeriod)
      },
      loadReports(period) {
         if (this.reportsLoading) {
            return
         }

         const selected = period || this.reportPeriod || 'today'
         this.reportPeriod = selected
         this.reportsLoading = true
         BaseApi.get(`adminReports?period=${encodeURIComponent(selected)}`, { silent: true }).then((res) => {
            const data = res.data.data || {}
            this.transactionReports = Array.isArray(data.transaction_reports) ? data.transaction_reports : []
            this.orderReports = Array.isArray(data.order_reports) ? data.order_reports : []
            this.reportsLoaded = true
            this.startPolling()
         }).catch(() => {
            this.errorMessage = 'Gagal memuat ringkasan performa.'
         }).finally(() => {
            this.reportsLoading = false
         })
      },
      startPolling() {
         if (this.pollerId) {
            return
         }

         this.pollerId = window.setInterval(() => {
            if (typeof document !== 'undefined' && document.hidden) {
               return
            }

            if (this.widgetsLoaded) {
               this.loadWeeklyWidgets({ silent: true })
            }

            if (this.ordersLoaded && this.ordersPagination?.current_page) {
               const baseUrl = this.ordersPagination?.path || this.ordersPagination?.first_page_url
               if (baseUrl) {
                  const query = new URLSearchParams()
                  query.set('page', String(this.ordersPagination.current_page || 1))
                  query.set('per_page', String(this.ordersPerPage))
                  this.loadOrders(`${baseUrl}?${query.toString()}`)
               } else {
                  this.loadOrders(`orders?per_page=${this.ordersPerPage}`)
               }
            }

            if (this.reportsLoaded) {
               this.loadReports(this.reportPeriod)
            }
         }, 20000)
      },
      stopPolling() {
         if (this.pollerId) {
            window.clearInterval(this.pollerId)
            this.pollerId = null
         }
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

.admin-dashboard__card--kpi {
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

.admin-dashboard__row {
   cursor: pointer;
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

.admin-dashboard__reports-head {
   display: flex;
   justify-content: space-between;
   align-items: center;
   gap: 14px;
}

.admin-dashboard__reports-grid {
   display: grid;
   gap: 12px;
   grid-template-columns: repeat(12, minmax(0, 1fr));
}

.admin-dashboard__metric {
   grid-column: span 4;
   border-radius: 16px;
}

.admin-dashboard__metric-body {
   display: grid;
   gap: 6px;
}

.admin-dashboard__metric-label {
   font-weight: 700;
   color: var(--cs-text-secondary);
}

.admin-dashboard__metric-value {
   font-size: 1.25rem;
   font-weight: 800;
}

.admin-dashboard__metric-caption {
   font-size: 0.85rem;
   color: var(--cs-text-secondary);
}

@media (max-width: 1200px) {
   .admin-dashboard__card--kpi,
   .admin-dashboard__card--chart,
   .admin-dashboard__card--orders {
      grid-column: span 12;
   }

   .admin-dashboard__orders-head {
      flex-direction: column;
      align-items: stretch;
   }

   .admin-dashboard__reports-head {
      flex-direction: column;
      align-items: stretch;
   }

   .admin-dashboard__metric {
      grid-column: span 12;
   }
}
</style>
