<template>
   <div>
      <q-list v-if="render_payments.length" separator bordered>
         <q-item-label header class="text-weight-bold text-sm">Virtual Account</q-item-label>
         <q-item class="q-py-md" v-for="(item, index) in render_payments" :key="index" clickable
            @click="selectPayment(item)" :class="{ 'bg-green-1': is_selected_payment(item.id) }"
            style="min-height: 65px">
            <q-item-section side>
               <q-icon size="sm" :name="is_selected_payment(item.id)
                  ? 'radio_button_checked'
                  : 'radio_button_unchecked'
                  " :color="is_selected_payment(item.id) ? 'green' : 'grey-8'"></q-icon>
            </q-item-section>
            <q-item-section>
               <q-item-label class="payment-name">
                  {{ item.payment_name }}</q-item-label>
               <q-item-label class="text-grey-8" style="font-size: 12.7px" v-if="item.payment_fee">
                  Biaya Layanan {{ moneyFormat(item.payment_fee) }}
               </q-item-label>
            </q-item-section>
            <q-item-section side>
               <img v-if="item.icon_url" :src="item.icon_url" class="thumbnail payment-icon" />
            </q-item-section>
         </q-item>
      </q-list>
   </div>
</template>

<script>
import { moneyFormat } from 'src/utils';
export default {
   props: {
      payment_chanels: Array,
      source: {
         type: String,
         default: 'cart'
      }
   },
   computed: {
      config() {
         return this.$store.state.config;
      },
      cart_order_form() {
         return this.$store.getters[`${this.source}/getChartOrderForm`];
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
      render_payments() {
         if (this.cart_order_form) {

            if (this.payment_chanels.length) {
               return this.payment_chanels.map((item) => {
                  return {
                     id: item.paymentMethod,
                     payment_name: item.paymentName,
                     payment_code: "",
                     payment_method: item.paymentMethod,
                     payment_type: "PAYMENT_GATEWAY",
                     payment_fee: Number(item.totalFee),
                     icon_url: item.paymentImage,
                  }
               });
            }
         }
         return [];
      },
   },
   methods: {
      is_selected_payment(id) {
         if (this.cart_order_form.payment?.id == id) {
            return true;
         }
         return false;
      },
      selectPayment(item) {
         this.$store.commit(`${this.source}/SET_PAYMENT`, item);
         this.$store.commit("CLEAR_ERRORS");
         this.$emit('onClose', 1)
      },
   },
};
</script>
