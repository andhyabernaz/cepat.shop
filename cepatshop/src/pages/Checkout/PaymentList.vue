<template>
   <div class="q-mt-sm">
      <q-card flat bordered square classs="shadow" id="payment_section">
         <q-card-section>
            <q-list padding>
               <q-item-label class="card-subtitle flex justify-between items-center">
                  Metode Pembayaran
                  <!-- <q-btn no-caps label="Pilih Pembayaran" color="primary" flat icon-right="east" padding="4px 12px"
                     @click="modal = true"></q-btn> -->
               </q-item-label>
               <q-item clickable @click="modal = true" class="cursor-pointer q-py-lg bg-grey-1">
                  <q-item-section avatar top class="q-pt-sm">
                     <q-avatar icon="payments" size="lg" color="grey-3"></q-avatar>
                  </q-item-section>
                  <q-item-section v-if="cart_order_form.payment">
                     <q-item-label class="text-md">
                        {{ cart_order_form.payment.payment_name }}
                     </q-item-label>
                     <q-item-label caption>
                        {{ cart_order_form.payment.payment_type.split('_').join(' ') }}
                     </q-item-label>
                     <q-item-label caption v-if="cart_order_form.payment.payment_info">
                        {{ cart_order_form.payment.payment_info }}
                     </q-item-label>
                     <q-item-label caption v-if="cart_order_form.payment.payment_fee">
                        Payment Fee {{ moneyIdr(cart_order_form.payment.payment_fee) }}
                     </q-item-label>
                     <q-item-label v-if="['SHOPEEPAY', 'OVO', 'DANA'].includes(cart_order_form.payment.payment_method)"
                        class="text-orange-8 tex-xs">
                        Pastikan nomor telpon pada pesanan sesuai dengan nomor yang
                        terdaftar di aplikasi {{ cart_order_form.payment.payment_name }} anda
                     </q-item-label>
                  </q-item-section>
                  <q-item-section v-else>
                     <q-item-label class="text-md">Pilih Metode Pembayaran</q-item-label>
                     <q-item-label caption>Pilih salah satu metode pembayaran</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                     <q-icon name="arrow_forward_ios" size="sm" flat dense></q-icon>
                  </q-item-section>
               </q-item>
            </q-list>
            <div class="text-red text-xs q-pa-sm" v-if="errors.payment">
               {{ errors.payment }}
            </div>
         </q-card-section>
      </q-card>
      <q-dialog v-model="modal" position="bottom">
         <q-card class="max-width-mobile q-pb-xl" style="min-height: 300px">
            <div>
               <div class="card-header sticky-top">
                  <div>Metode Pembayaran</div>
                  <q-btn flat icon="close" v-close-popup color="white"></q-btn>
               </div>
               <div class="text-center q-py-xl margin-auto" v-if="!can_select_payment">
                  <div class="text-md text-weight-bold">Alamat pengiriman belum dipilih</div>
                  <div class="text-grey-8">Silahkan pilih alamat pengiriman terlebih dahulu</div>
               </div>

               <div v-else>
                  <q-list separator>
                     <DirectPayment :payment_chanels="direct_payments" @onClose="closeModal"></DirectPayment>
                     <TripayPayment :payment_chanels="payment_chanels" v-if="is_tripay_payment" @onClose="closeModal"></TripayPayment>
                     <XenditPayment v-if="is_xendit_payment" @onClose="closeModal"></XenditPayment>
                     <MidtransPayment v-if="is_midtrans_payment" @onClose="closeModal"></MidtransPayment>
                     <DuitkuPayment :payment_chanels="payment_chanels" v-if="is_duitku_payment" @onClose="closeModal"></DuitkuPayment>

                  </q-list>
               </div>
            </div>
         </q-card>
      </q-dialog>
   </div>
</template>

<script>
import { Api } from 'src/boot/axios';
import { moneyIdr } from 'src/utils';
import DirectPayment from './PaymentService/DirectPayment.vue';
import TripayPayment from './PaymentService/TripayPayment.vue';
import XenditPayment from './PaymentService/XenditPayment.vue';
import MidtransPayment from './PaymentService/MidtransPayment.vue';
import DuitkuPayment from './PaymentService/DuitkuPayment.vue';
export default {
   components: { DirectPayment, TripayPayment, XenditPayment, MidtransPayment, DuitkuPayment },
   data() {
      return {
         payment_chanels: [],
         direct_payments: [],
         modal: false,
      };
   },
   computed: {
      config() {
         return this.$store.state.config;
      },
      is_tripay_payment() {
         if (this.config && this.config.is_pg_ready && this.config.payment_default == 'Tripay') {
            return true
         }
         return false
      },
      is_xendit_payment() {
         if (this.config && this.config.is_pg_ready && this.config.payment_default == 'Xendit') {
            return true
         }
         return false
      },
      is_midtrans_payment() {
         if (this.config && this.config.is_pg_ready && this.config.payment_default == 'Midtrans') {
            return true
         }
         return false
      },
      is_duitku_payment() {
         if (this.config && this.config.is_pg_ready && this.config.payment_default == 'Duitku') {
            return true
         }
         return false
      },
      cart_order_form() {
         return this.$store.getters["cart/getChartOrderForm"];
      },
      user() {
         return this.$store.state.user.user
      },
      can_select_payment() {
         if (this.cart_order_form.is_digital) {
            return true
         }
         if (this.cart_order_form.customer) {
            return true
         }

         return false
      },

      errors() {
         return this.$store.state.errors;
      },


   },
   methods: {
      closeModal() {
         this.modal = false
      },
      getPaymentChanel() {
         Api.get(`payment-chanels?amount=${this.cart_order_form.grand_total}`).then(response => {
            if (response.status == 200) {
               if (response.data.success) {
                  this.payment_chanels = response.data.data
               }
            }
         })
      },
       getDirectPayments() {
         Api.get('getBanks').then(res => {
            this.direct_payments = res.data.data
         })
      },
   },
   mounted() {
      this.getDirectPayments()
      this.getPaymentChanel()
   }
};
</script>
