<script setup>
import QRCode from "qrcode";
import PaymentInstruction from "./PaymentInstruction.vue";
import { copyString, dateParse } from "src/utils";
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { useStore } from "vuex";
import { useRouter } from "vue-router";
import { Notify } from "quasar";
import { Api } from "src/boot/axios";

const store = useStore()
const props = defineProps(['invoice'])
const router = useRouter()

const qrData = ref(null)
const is_waybill_loading = ref(false)
const trackingModal = ref(false)
const order_waybill = ref(null)
const buktiModal = ref(false)

const shop = computed(() => store.state.shop)
const config = computed(() => store.state.config)
const isPaid = computed(() => props.invoice.transaction.status == "PAID" ? true : false)
const isPaymentType = computed(() => {
   return props.invoice.transaction.payment_type == "DIRECT"
      ? "DirectPayment"
      : "PaymentGateway";
})
const getRoutePath = () => {
   let props = router.resolve({
      name: "UserInvoice",
      params: { order_ref: props.invoice.order_ref },
   });

   return location.origin + props.href;
}
const generateQr = () => {
   let opts = {
      errorCorrectionLevel: "H",
      type: "image/jpeg",
      quality: 0.3,
      margin: 1,
   };
   QRCode.toDataURL(getRoutePath(), opts, (err, url) => {
      if (err) throw err;
      qrData.value = url;
   });
}
const goToUrl = (url) => {
   window.location.href = url;
}
const lacakPengiriman = () => {
   if (order_waybill.value) {
      trackingModal.value = true;
   } else {
      is_waybill_loading.value = true;

      store.dispatch("order/shippingWaybill", props.invoice.id)
         .then((res) => {
            let data = res.data;
            if (!data.data) {
               Notify.create(res.data.message);
            } else {
               order_waybill.value = res.data.data;
               trackingModal.value = true;
            }
         })
         .finally(() => {
            is_waybill_loading.value = false;
         });
   }
}
const copyAddress = () => {
   let addr = `${props.invoice.customer_name}\n${props.invoice.customer_whatsapp}\n${props.invoice.shipping_address}`;
   if (props.invoice.shipping_coordinate) {
      addr += `\nKoordinat (Lat/Lng): ${props.invoice.shipping_coordinate}`
   }
   copyString(addr);
}

const getData = () => {
   closeSSE()
   closePolling()
   store
      .dispatch("order/getInvoice", props.invoice.order_ref)
      .then((response) => {
         store.commit("order/SET_INVOICE", response.data.data);
      })
}

const pollingCount = ref(1)
const pollingInterval = ref(null)
const SSEInstance = ref(null)
const usingSSE = ref(false)

const closeSSE = () => {
   usingSSE.value = false;
   if (SSEInstance.value) {
      SSEInstance.value.close()
   }
}
const closePolling = () => {
   pollingCount.value = 0
   clearInterval(pollingInterval.value)
}

const listenPayment = () => {
   closePolling()
   if (props.invoice.order_status != "PENDING") {
      closeSSE()
      return
   }

   let trxId = props.invoice.transaction.id

   try {

      const API_PUBLIC_URL = process.env.PUBLIC_API;
      SSEInstance.value = new EventSource(`${API_PUBLIC_URL}sse/payment-status/${trxId}`);

      usingSSE.value = true;

      SSEInstance.value.addEventListener('payment.updated', (e) => {
         let status = JSON.parse(e.data);
         if (status != 'UNPAID') {
            getData()
         }
      });

      SSEInstance.value.addEventListener('heartbeat', (e) => {
         console.log('Koneksi SSE aktif...');
      });

      SSEInstance.value.onerror = () => {
         console.log("❌ SSE error → fallback");
         startPolling(trxId);
      };

      SSEInstance.value.addEventListener("close", () => {
         console.log("🔌 SSE closed → fallback");
         startPolling(trxId);
      });

   } catch (err) {
      console.log("❌ Browser tidak support SSE → fallback");
      startPolling(trxId);
   }
}

const startPolling = (trxId) => {
   closeSSE()
   if (props.invoice.order_status != "PENDING") {
      closePolling()
      return
   }

   if (usingSSE.value) return
   console.log("🔄 Fallback ke polling...");

   pollingInterval.value = setInterval(() => {
      console.log('Koneksi Polling Aktif...')
      if (pollingCount.value > 20) {
         closePolling()
         // listenPayment()
         return
      }
      Api.get(`poll/payment-status/${trxId}`)
         .then(res => {
            let status = res.data.data

            if (status != 'UNPAID') {
               closePolling()
               getData()
            }
         }).finally(() => {
            pollingCount.value++
         })
   }, 10000);
}

onMounted(() => {
   closeSSE()
   closePolling()

   let trxId = props.invoice.transaction.id

   if (['UNPAID', 'PAY'].includes(props.invoice.transaction.status)) {
      console.log('started to polling');

      setTimeout(() => {
         startPolling(trxId)
         // listenPayment()
         // startPolling(props.invoice.transaction.id)
      }, 10000)
   }
})

onBeforeUnmount(() => {
   closeSSE()
   closePolling()
})

</script>


<template>
   <div class="no-print text-grey-8">
      <div v-if="invoice">

         <q-card class="section shadow" square>
            <q-card-section>
               <q-list>
                  <q-item dense class="q-px-xs">
                     <q-item-section style="max-width: 180px" top>
                        <q-item-label class="text-weight-medium">No. Pesanan</q-item-label>
                     </q-item-section>
                     <q-item-section>
                        <q-item-label @click="copyString(invoice.order_ref)">
                           {{ invoice.order_ref }}
                        </q-item-label>
                     </q-item-section>
                  </q-item>
                  <q-item dense class="q-px-xs" v-if="invoice.created_at">
                     <q-item-section style="max-width: 180px" top>Tanggal Pembelian</q-item-section>
                     <q-item-section>{{ dateParse(invoice.created_at) }}</q-item-section>
                  </q-item>

                  <q-item dense v-if="invoice.shipping_at">
                     <q-item-section style="max-width: 180px" top>Tanggal Pengiriman</q-item-section>
                     <q-item-section>{{
                        dateParse(invoice.shipping_at)
                     }}</q-item-section>
                  </q-item>
                  <q-item dense v-if="invoice.received_at">
                     <q-item-section style="max-width: 180px" top>Tanggal Terkirim</q-item-section>
                     <q-item-section>{{
                        dateParse(invoice.received_at)
                     }}</q-item-section>
                  </q-item>
                  <q-item dense class="q-px-xs">
                     <q-item-section style="max-width: 180px" top>Total Pembayaran</q-item-section>
                     <q-item-section>
                        <q-item-label>
                           {{ moneyIdr(invoice.billing_total) }}
                        </q-item-label>
                     </q-item-section>
                  </q-item>
                  <q-item dense class="q-px-xs">
                     <q-item-section style="max-width: 180px" top>Metode Pembayaran</q-item-section>
                     <q-item-section>
                        <q-item-label>
                           {{ invoice.transaction.payment_name }}
                        </q-item-label>
                     </q-item-section>
                  </q-item>
                  <q-item dense class="q-px-xs">
                     <q-item-section style="max-width: 180px" top>Tipe Pembayaran</q-item-section>
                     <q-item-section>
                        <q-item-label class="text-14">
                           {{ invoice.transaction.payment_type.split('_').join(' ') }}
                        </q-item-label>
                     </q-item-section>
                  </q-item>
                  <!-- <q-item dense class="q-px-xs">
                     <q-item-section style="max-width: 180px" top>Total Tagihan</q-item-section>
                     <q-item-section>
                        <q-item-label>
                           {{ moneyIdr(invoice.billing_total) }}
                        </q-item-label>
                     </q-item-section>
                  </q-item> -->
                  <q-item dense class="q-px-xs">
                     <q-item-section style="max-width: 180px">Status Pembayaran</q-item-section>
                     <q-item-section>
                        <q-item-label>
                           <span class="q-px-md rounded-borders text-white q-py-xs" :class="`bg-${getOrderStatusColor(
                              invoice.transaction.status
                           )}`">
                              {{ invoice.transaction.status_label }}
                           </span>
                        </q-item-label>
                     </q-item-section>
                  </q-item>
               </q-list>
            </q-card-section>
         </q-card>

         <q-card class="section shadow q-mt-md" square>
            <q-card-section>
               <div class="card-subtitle">Catatan Pembeli</div>
               <div class="q-mt-sm q-pa-sm bg-grey-3"> {{ invoice.note ? invoice.note : '-' }}</div>
            </q-card-section>
         </q-card>

         <div
            v-if="invoice.transaction.status == 'UNPAID' && $route.name == 'UserInvoice' && !['COD', 'CASH'].includes(invoice.transaction.payment_type)">
            <PaymentInstruction :invoice="invoice"></PaymentInstruction>
         </div>

         <q-card class="section shadow q-mt-md" square v-if="invoice.product_type == 'Default'">
            <q-card-section>
               <div class="card-subtitle flex justify-between items-center">
                  <div>Informasi Pengiriman</div>
                  <q-btn v-if="invoice.can_check_waybill" label="HISTORY" flat @click="lacakPengiriman" color="teal"
                     padding="3px 12px" :loading="is_waybill_loading"></q-btn>
               </div>
               <div>{{ invoice.shipping_courier_name }}</div>
               <div class="text-grey-7 text-13">
                  {{ invoice.shipping_courier_service }}
               </div>
               <div v-if="invoice.shipping_courier_code" class="flex items-center">
                  <div style="line-height: normal">
                     {{ invoice.shipping_courier_code }}
                  </div>
                  <q-btn label="salin" padding="2px 12px" flat color="teal"
                     @click="copyString(invoice.shipping_courier_code)"></q-btn>
               </div>
            </q-card-section>
         </q-card>
         <q-card class="section shadow q-mt-md" square>
            <q-card-section>
               <div class="card-subtitle flex justify-between items-center">
                  <div>Detail Customer</div>

                  <q-btn flat label="salin" @click="copyAddress" color="teal" padding="3px 12px"></q-btn>
               </div>
               <div>
                  <div class="flex justify-between">
                     <div class="text-weight-medium">{{ invoice.customer_name }}</div>
                  </div>
                  <div>{{ invoice.customer_whatsapp }}</div>
                  <div v-html="invoice.shipping_address"></div>
                  <div class="q-mt-xs" v-if="invoice.shipping_coordinate">
                     <div>Koordinat (Lat/Lng) : {{ invoice.shipping_coordinate ? invoice.shipping_coordinate : '-' }}
                        <q-btn v-if="invoice.shipping_coordinate" unelevated padding="0px 6px" color="green-1"
                           text-color="teal" class="q-ml-sm" no-caps
                           @click="copyString(invoice.shipping_coordinate)">salin</q-btn>
                     </div>
                  </div>
               </div>
            </q-card-section>
         </q-card>
         <q-card class="section shadow q-mt-md" square>
            <q-card-section>
               <div class="card-subtitle">Detil Pesanan</div>
               <q-separator color="teal q-pt-xs" />
               <q-list>
                  <q-item class="bg-green-1" dense>
                     <q-item-section>Produk</q-item-section>
                     <q-item-section side>Harga</q-item-section>
                  </q-item>
                  <q-item v-for="(item, index) in invoice.items" :key="index" class="bg-grey-1">
                     <q-item-section>
                        <div class="row">
                           <q-img @click="goToUrl(item.product_url)" v-if="item.image_url" :src="item.image_url"
                              class="cursor-pointer img-thumbnail q-mr-sm" width="60px">
                              <q-tooltip>Lihat Produk</q-tooltip>
                           </q-img>
                           <div>
                              <div>{{ item.name }}</div>
                              <div class="text-caption tet-grey-6">{{ item.note }}</div>
                              <q-item-label class="text-grey-7 q-mt-xs">{{ item.quantity }}X
                                 {{ moneyIdr(item.price) }}</q-item-label>
                           </div>
                        </div>
                     </q-item-section>
                     <q-item-section side>{{ moneyIdr(item.price * item.quantity) }}</q-item-section>
                  </q-item>
               </q-list>
            </q-card-section>
         </q-card>
         <q-card class="section shadow q-mt-md" square>
            <q-card-section>
               <div class="card-subtitle">Rincian Pembayaran</div>
               <q-list>
                  <q-item dense class="q-px-none">
                     <q-item-section>No. Referensi</q-item-section>
                     <q-item-section side>{{
                        invoice.transaction.payment_ref
                        }}</q-item-section>
                  </q-item>
                  <q-item dense class="q-px-none">
                     <q-item-section>Subtotal Produk</q-item-section>
                     <q-item-section side>{{ moneyFormat(invoice.order_subtotal) }} IDR</q-item-section>
                  </q-item>
                  <q-item dense class="q-px-none" v-if="invoice.voucher_discount">
                     <q-item-section>Voucher Belanja</q-item-section>
                     <q-item-section side>-{{ moneyFormat(invoice.voucher_discount) }} IDR</q-item-section>
                  </q-item>
                  <q-item dense class="q-px-none" v-if="invoice.product_type == 'Default'">
                     <q-item-section>
                        Biaya Pengiriman ({{ invoice.order_weight }}gram)
                     </q-item-section>
                     <q-item-section side>{{ moneyFormat(invoice.shipping_cost) }} IDR</q-item-section>
                  </q-item>
                  <q-item dense class="q-px-none" v-if="invoice.shipping_discount">
                     <q-item-section>Diskon Pengiriman</q-item-section>
                     <q-item-section side>-{{ moneyFormat(invoice.shipping_discount) }} IDR</q-item-section>
                  </q-item>

                  <q-item dense class="q-px-none" v-if="invoice.service_fee">
                     <q-item-section>{{ config.service_fee_label }}</q-item-section>
                     <q-item-section side>{{ moneyFormat(invoice.service_fee) }} IDR</q-item-section>
                  </q-item>
                  <q-item dense class="q-px-none" v-if="invoice.order_unique_code">
                     <q-item-section>Kode Unik</q-item-section>
                     <q-item-section side>{{ invoice.order_unique_code }} IDR</q-item-section>
                  </q-item>
                  <q-item dense class="q-px-none" v-if="invoice.discount">
                     <q-item-section>Diskon</q-item-section>
                     <q-item-section side>
                        {{ moneyFormat(invoice.discount) }} IDR</q-item-section>
                  </q-item>
                  <q-item dense class="q-px-none" v-if="invoice.payment_fee">
                     <q-item-section>
                        <q-item-label>
                           Payment Fee
                        </q-item-label>
                        <q-item-label caption> {{ invoice.transaction.payment_name }}</q-item-label>
                     </q-item-section>
                     <q-item-section side>{{ moneyFormat(invoice.payment_fee) }} IDR</q-item-section>
                  </q-item>

                  <q-item dense class="q-px-none">
                     <q-item-section>
                        <q-item-label class="text-weight-bold q-py-sm text-md">Total Pembayaran</q-item-label>
                     </q-item-section>
                     <q-item-section side>
                        <q-item-label class="text-weight-bold q-py-sm text-md">{{ moneyFormat(invoice.billing_total) }}
                           IDR</q-item-label>
                     </q-item-section>
                  </q-item>
               </q-list>
            </q-card-section>
         </q-card>

         <q-card class="section shadow q-mt-md" square v-if="invoice.transaction.payment_proof">
            <q-card-section>
               <div class="card-subtitle">Bukti Pembayaran</div>
               <img :src="invoice.transaction.payment_proof_url"
                  style="width:110px;height:110px;object-fit:cover;object-position: top"
                  class="cursor-pointer thumbnail" @click="buktiModal = true" />
            </q-card-section>
         </q-card>

      </div>

      <q-dialog v-model="buktiModal">
         <img :src="invoice.transaction.payment_proof_url" class="payment-proof" />
      </q-dialog>

      <q-dialog v-model="trackingModal" position="bottom">
         <q-card class="max-width-mobile" style="min-height: 60vh">
            <div class="sticky-top box-shadow">
               <div class="flex justify-between items-center q-px-md q-pb-sm q-pt-md bg-grey-2">
                  <div>
                     <div class="text-md2 text-weight-medium">Histori Pesanan</div>
                     <!-- <div class="text-weight-medium text-md" v-if="order_waybill && order_waybill.shipping_status">
                        <span class="bg-blue-1 text-blue q-px-xs">
                           {{ order_waybill.shipping_status }}
                        </span>
                     </div> -->
                  </div>
                  <q-btn flat icon="close" padding="sm" round v-close-popup></q-btn>
               </div>
               <!-- <q-separator></q-separator> -->
            </div>
            <q-card-section v-if="order_waybill">
               <q-timeline color="grey-8">
                  <q-timeline-entry v-for="(item, idx) in order_waybill.histories" :key="idx"
                     :subtitle="dateParse(item.date + ' ' + item.time)" :color="idx == 0 ? 'blue' : 'grey-5'">
                     <div>
                        <div>{{ item.description }}</div>
                        <div class="text-grey-7" v-if="item.city_name">
                           {{ item.city_name }}
                        </div>
                     </div>
                  </q-timeline-entry>
               </q-timeline>

               <div v-if="!order_waybill.histories.length" class="absolute-center">
                  Tidak ada data
               </div>
            </q-card-section>
         </q-card>
      </q-dialog>
   </div>
</template>
