<template>
   <div class="text-dark">
      <div>

         <q-card flat square class="q-mt-md section">

            <q-card-section>

               <div class="text-18 q-mb-sm text-weight-bold">
                  <div> Pembayaran</div>
                  <!-- <div> {{ moneyIdr(invoice.billing_total) }}</div> -->
               </div>

               <div class="text-center bg-grey-1 q-pa-md text-weight-bold">
                  <div class="text-16 q-mb-sm">
                     <div>Bayar via {{ invoice.transaction.payment_name }} </div>
                     <div>Total {{ moneyIdr(invoice.billing_total) }}
                     </div>
                  </div>

                  <div class="q-py-sm text-15">
                     <div v-if="invoice.transaction.payment_code">
                        <div class="text-weight-bold">
                           <div>
                              {{ invoice.transaction.payment_type == 'DIRECT_TRANSFER'
                                 ? 'Nomor Rekening / Pembayaran'
                                 : 'Nomor Pembayaran / VA'
                              }}
                           </div>
                           <q-btn color="dark" @click="copyString(invoice.transaction.payment_code)"
                              :label="invoice.transaction.payment_code" icon-right="content_copy" size="lg"
                              class="btn-icon-copy q-my-sm">
                           </q-btn>
                           <div>{{ invoice.transaction.payment_method }}</div>
                        </div>
                     </div>
                     <div v-else-if="qrUrl" class="column justify-center items-center q-gutter-y-sm">
                        <!-- <div>QR CODE {{ invoice.transaction.payment_name }}</div> -->
                        <img @click="viewQrModal = true" :src="qrUrl"
                           style="width:180px;height:auto;cursor: zoom-in" />
                        <q-btn label="Unduh" class="btn-action" color="teal"
                           @click=downloadFile(qrUrl)></q-btn>
                     </div>

                     <div v-else>
                        <q-btn color="green" icon="payments" label="Lanjutkan Pembayaran" v-if="showSnap"
                           @click="openSnap(invoice.transaction.snap_token)"></q-btn>
                        <q-btn color="green" icon="payments" label="Lanjutkan Pembayaran"
                           v-else-if="invoice.transaction.pay_url"
                           @click="openUrl(invoice.transaction.pay_url)"></q-btn>
                        <q-btn color="green" icon="payments" label="Lanjutkan Pembayaran"
                           v-else-if="invoice.transaction.checkout_url"
                           @click="openUrl(invoice.transaction.checkout_url)"></q-btn>
                     </div>
                  </div>
                  <div class="q-mt-sm">

                     <CountdownTimer title="Batas Pembayaran" :date="invoice.expired_at" background="transparent"
                        color="dark" padding="6px 0px">
                     </CountdownTimer>
                  </div>

                  <div class="text-grey-8 text-sm text-weight-regular bg-yellow-2 q-pa-md q-mt-sm">
                     Segera lakukan pembayaran sebelum melewati batas pembayaran dan pastikan nominal pembayaran
                     sesuai
                     dengan
                     tagihan, pembayaran
                     akan
                     di
                     <span v-if="invoice.transaction.payment_type == 'DIRECT_TRANSFER'">
                        <b>verifikasi manual oleh tim kami.</b>
                     </span>
                     <span v-else>
                        <b>verifikasi otomatis oleh sistem.</b>
                     </span>
                  </div>
               </div>
               <div class="q-mt-md" v-if="render_instructions && render_instructions.length">
                  <div class="card-subtitle">
                     <div>Panduan Pembayaran</div>

                  </div>
                  <q-list separator class="text-grey-9 q-mt-sm">
                     <q-expansion-item v-for="(item, index) in render_instructions" :key="index" group="somegroup"
                        :label="item.title" :default-opened="index == 0" header-class="bg-grey-2">
                        <q-card class="q-py-md">
                           <q-list bordered>
                              <q-item v-for="(step, index) in item.steps" :key="index">
                                 <q-item-section avatar>
                                    <q-avatar color="grey-7" text-color="white" size="sm">
                                       {{ index + 1 }}
                                    </q-avatar>
                                 </q-item-section>

                                 <q-item-section>
                                    <div v-html="step"></div>
                                 </q-item-section>
                              </q-item>
                           </q-list>
                        </q-card>
                     </q-expansion-item>
                  </q-list>
               </div>
            </q-card-section>


         </q-card>

      </div>
      <q-dialog v-model="viewQrModal">
         <img :src="qrUrl" style="max-width:380px;width:100%;height:auto;" />
      </q-dialog>
   </div>
</template>

<script setup>
import QRCode from "qrcode";
import CountdownTimer from "components/CountdownTimer.vue";
import { useStore } from 'vuex';
const store = useStore()

import { saveAs } from 'file-saver';
import { computed, ref } from 'vue';
import { copyString, dateFormat, moneyIdr } from 'src/utils';
import { Notify } from "quasar";
const props = defineProps(['invoice'])
const viewQrModal = ref(false)

const isOVO = computed(() => {
   return props.invoice.transaction.payment_method == 'OVO' ? true : false
})

const render_instructions = computed(() => {
   let instructions = JSON.parse(props.invoice.transaction.instructions)
   if (props.invoice.transaction.payment_type == 'DIRECT_TRANSFER') {
      instructions = [
         {
            title: 'Bank Transfer',
            steps: [
               `Lakukan pembayaran via <b>${props.invoice.transaction.payment_name}</b> ke nomor <b>${props.invoice.transaction.payment_code}.</b> sebelum ${dateFormat(props.invoice.expired_at, { weekday: 'long', hour: 'numeric', minute: 'numeric' })}`,
               `Transfer sejumlah  <b>${moneyIdr(props.invoice.billing_total)}</b> (tanpa pembulatan) `,
               "Simpan dan <b>upload bukti pembayaran.</b>"]
         }
      ]
   }

   return instructions
})

const showSnap = computed(() => {
   if (window.snap && props.invoice && props.invoice.transaction.snap_token) {
      return true
   }
   return false
})

const qrUrl = computed(() => {
   let qrUrl = null
   if (props.invoice) {

      if (props.invoice.transaction.qr_string) {

          QRCode.toDataURL(props.invoice.transaction.qr_string, function (err, url) {
            // console.log(url); // This URL can be set as the src of an <img> tag
            qrUrl = url
         });

      } else if (props.invoice.transaction.qr_url) {
         qrUrl = props.invoice.transaction.qr_url
      }
   }


   return qrUrl
})


const downloadFile = (url) => {
   saveAs(url)
   // window.open(url, 'new')
}
const getData = () => {
   store.commit("SET_LOADING", true);
   setTimeout(() => {
      store
         .dispatch("order/getInvoice", props.invoice.order_ref)
         .then((response) => {
            store.commit("order/SET_INVOICE", response.data.data);
         }).finally(() => {
            store.commit("SET_LOADING", false);
         })
   }, 2000)
}
const openSnap = (token) => {

   window.snap.pay(token, {
      onSuccess: (result) => {
         getData()
         Notify.create({
            type: 'positive',
            message: 'Payment Success!'
         })

      },
      onPending: (result) => {
         Notify.create({
            type: 'positive',
            message: 'wating your payment!'
         })
      },
      onError: (result) => {
         getData()
         Notify.create({
            type: 'negative',
            message: 'payment failed!'
         })
      },
      onClose: () => {
         Notify.create({
            type: 'positive',
            message: 'you closed the popup without finishing the payment'
         })
      }
   })
}

const openUrl = (url) => {
   window.open(url, 'new')
}



</script>
