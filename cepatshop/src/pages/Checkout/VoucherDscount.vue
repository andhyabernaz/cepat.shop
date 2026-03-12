<template>
   <div class="q-mt-sm">
      <q-card flat bordered square classs="shadow" id="voucher_discount">
         <q-card-section>
            <q-list>

               <q-item-label class="card-subtitle flex justify-between items-center">
                  Voucher Diskon
               </q-item-label>
               <q-item clickable @click="modal = true" class="cursor-pointer q-py-md bg-grey-1">
                  <q-item-section avatar top>
                     <q-avatar icon="local_offer" size="lg" color="grey-3"></q-avatar>
                  </q-item-section>
                  <q-item-section v-if="cart_order_form.voucher">

                     <q-item-label class="text-md">
                        {{ cart_order_form.voucher.type_label }}
                     </q-item-label>
                     <q-item-label caption>{{ cart_order_form.voucher.detail_label }}</q-item-label>

                  </q-item-section>
                  <q-item-section v-else>
                     <q-item-label class="text-md">Gunakan Voucher Diskon</q-item-label>
                  </q-item-section>
                  <q-item-section side>
                     <q-icon name="arrow_forward_ios" size="sm" flat dense></q-icon>
                  </q-item-section>
               </q-item>
            </q-list>
         </q-card-section>
      </q-card>
      <q-dialog v-model="modal">
         <q-card class="max-width-mobile q-pb-lg">
            <div>
               <div class="card-header sticky-top">
                  <div>Voucher Diskon</div>
                  <q-btn flat icon="close" v-close-popup color="white"></q-btn>
               </div>
               <div>
                  <div v-if="rendered_vouchers.length" class="q-pa-md">
                     <q-list>
                        <q-item clickable :disable="selisih_minimum_tarnsaksi(item) > 0"
                           :class="{ 'bg-green-1 border-green': is_selected_voucher(item.id) }"
                           class="q-mb-sm border q-pa-xs" v-for="(item, index) in rendered_vouchers" :key="index"
                           @click="selectVoucher(item)">
                           <q-item-section avatar>
                              <div class="text-white text-weight-bold text-center voucher-avatar"
                                 v-if="item.is_type_shipping"
                                 :class="selisih_minimum_tarnsaksi(item) == 0 ? 'bg-teal' : 'bg-grey-6'">
                                 <q-item-label class="text-md">Gratis</q-item-label>
                                 <q-item-label>Ongkir</q-item-label>
                              </div>
                              <div class="text-white text-weight-bold text-center voucher-avatar" v-else
                                 :class="selisih_minimum_tarnsaksi(item) == 0 ? 'bg-amber-9' : 'bg-grey-6'">
                                 <q-item-label class="text-md">Voucher</q-item-label>
                                 <q-item-label>Belanja</q-item-label>
                              </div>

                           </q-item-section>
                           <q-item-section>
                              <q-item-label class="text-weight-medium text-15">{{ item.detail_label
                              }}</q-item-label>
                              <q-item-label caption>{{ item.end_date_label }}</q-item-label>
                              <q-item-label v-if="selisih_minimum_tarnsaksi(item) > 0"
                                 class="text-amber-10 text-xs text-weight-bold">
                                 Tambahkan produk senilai {{
                                    moneyFormat(selisih_minimum_tarnsaksi(item)) }} untuk menggunakan voucher
                              </q-item-label>
                           </q-item-section>
                        </q-item>
                     </q-list>
                  </div>

                  <div class="q-pa-xl text-center" v-if="!rendered_vouchers.length">Belum ada voucher tersedia</div>

               </div>
            </div>
         </q-card>
      </q-dialog>
   </div>
</template>

<script>
import { Api } from 'boot/axios'
export default {
   props: {
      source: {
         type: String,
         default: 'cart'
      }
   },
   data() {
      return {
         modal: false,
         all_vouchers: []
      }
   },
   computed: {
      config() {
         return this.$store.state.config
      },
      cart_order_form() {
         return this.$store.getters[`${this.source}/getChartOrderForm`]
      },
      rendered_vouchers() {
         if (this.all_vouchers.length && this.cart_order_form) {
            if (this.cart_order_form.is_digital) {
               return this.all_vouchers.filter(el => !el.is_type_shipping)
            } else if (this.cart_order_form.is_default) {
               return this.all_vouchers
            }
         }
         return []
      }
   },
   mounted() {
      this.getVouchers()
   },
   methods: {
      selectVoucher(item) {

         this.$store.commit(`${this.source}/TOGGLE_VOUCHER`, item)
         this.modal = false

      },
      selisih_minimum_tarnsaksi(voucher) {
         if (voucher.min_transaction > this.cart_order_form.subtotal) {
            return voucher.min_transaction - this.cart_order_form.subtotal
         }
         return 0
      },
      is_selected_voucher(id) {
         if (this.cart_order_form.voucher && this.cart_order_form.voucher.id == id) {
            return true
         }
         return false
      },
      getVouchers() {
         Api.get('getVoucherActive').then(res => {
            this.all_vouchers = res.data.data
         })
      }
   }
}
</script>
