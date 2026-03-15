<template>
   <div>
      <q-card flat bordered square class="q-mt-md">
         <q-card-section>

            <div class="card-subtitle">Rincian Pembayaran</div>

            <div class="bg-grey-1 q-py-sm text-grey-10">
               <q-list>
                  <q-item dense>
                     <q-item-section>Subtotal Produk</q-item-section>
                     <q-item-section side>{{ moneyFormat(cart_order_form.subtotal) }} IDR</q-item-section>
                  </q-item>
                  <q-item dense v-if="cart_order_form.courier">
                     <q-item-section>Biaya Pengiriman</q-item-section>
                     <q-item-section side>
                        <q-item-label v-if="cart_order_form.courier">
                           {{ moneyFormat(cart_order_form.courier.price) }} IDR

                        </q-item-label>
                        <q-item-label v-else>0 IDR</q-item-label>
                     </q-item-section>
                  </q-item>
                  <q-item dense v-if="cart_order_form.shipping_discount">
                     <q-item-section>
                        <div clas="text-13 text-green">Diskon Pengiriman</div>
                     </q-item-section>
                     <q-item-section side>- {{ moneyFormat(cart_order_form.shipping_discount) }} IDR</q-item-section>
                  </q-item>

                  <q-item dense v-if="cart_order_form.voucher_discount">
                     <q-item-section>
                        <div>Voucher Belanja</div>
                     </q-item-section>
                     <q-item-section side>- {{ moneyFormat(cart_order_form.voucher_discount) }} IDR</q-item-section>
                  </q-item>

                  <q-item dense v-if="cart_order_form.payment && cart_order_form.payment.payment_fee">
                     <q-item-section>
                        <div>Layanan Pembayaran</div>
                        <div class="text-xs text-grey-6">{{ cart_order_form.payment.payment_name }}</div>
                     </q-item-section>
                     <q-item-section side>{{ moneyFormat(cart_order_form.payment.payment_fee) }} IDR</q-item-section>
                  </q-item>
                  <q-item dense v-if="cart_order_form.service_fee > 0">
                     <q-item-section>{{ config.service_fee_label }}</q-item-section>
                     <q-item-section side>{{ cart_order_form.service_fee ? moneyFormat(cart_order_form.service_fee) : 0
                        }}
                        IDR</q-item-section>
                  </q-item>
                  <q-item dense v-if="cart_order_form.unique_code">
                     <q-item-section>Kode Unik</q-item-section>
                     <q-item-section side>{{ cart_order_form.unique_code }} IDR</q-item-section>
                  </q-item>
                  <q-item class="q-py-sm q-mt-sm bg-grey-3 text-16">
                     <q-item-section>Total Pembayaran</q-item-section>
                     <q-item-section side class="text-black text-weight-bold">{{
                        moneyFormat(cart_order_form.billing_total)
                     }}
                        IDR</q-item-section>
                  </q-item>
               </q-list>
            </div>
         </q-card-section>
      </q-card>

   </div>
</template>

<script>
export default {
   name: 'ReviewOrder',
   props: {
      source: {
         type: String,
         default: 'cart'
      },
      payment: Object,
      noPayment: Boolean
   },
   computed: {
      cart_order_form() {
         return this.$store.getters[`${this.source}/getChartOrderForm`]
      },
      config() {
         return this.$store.state.config
      },
   },
}
</script>
