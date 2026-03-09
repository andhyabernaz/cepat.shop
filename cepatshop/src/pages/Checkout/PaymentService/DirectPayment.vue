<template>
   <div v-if="render_payments.length">
      <q-list separator bordered v-if="render_payments.length">
         <q-item-label header class="text-weight-bold text-sm">
            Direct Payment
         </q-item-label>

         <q-item class="q-py-md" :clickable="item.is_enable"
            :class="{ 'bg-green-1': is_selected_payment(item.id), 'bg-grey-1': !item.is_enable }"
            v-for="(item, index) in render_payments" :key="index" @click="selectPayment(item)">
            <q-item-section side>
               <q-icon size="sm" :name="is_selected_payment(item.id)
                  ? 'radio_button_checked'
                  : 'radio_button_unchecked'
                  " :color="is_selected_payment(item.id) ? 'green' : 'grey-8'
                                 "></q-icon>
            </q-item-section>
            <q-item-section>
               <q-item-label class="payment-name">
                  {{ item.payment_name }} 
               </q-item-label>
               <q-item-label class="payment-info" :class="{'error' : !can_saldo_payment(item.payment_type)}" v-if="item.payment_info">{{ item.payment_info }}</q-item-label>
            </q-item-section>
            <q-item-section side>
               <img v-if="item.icon_url" :src="item.icon_url" class="thumbnail payment-icon"  />
            </q-item-section>
         </q-item>
      </q-list>
   </div>
</template>

<script>
import { moneyIdr } from 'src/utils';
export default {
   props: { 
      payment_chanels: Array
   },
   computed: {
      config() {
         return this.$store.state.config;
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
      can_cod_payment() {

         if (this.cart_order_form.is_digital) {
            return false
         }

         if (
            this.config &&
            this.config.is_cod_payment &&
            this.cart_order_form.courier &&
            this.cart_order_form.courier.courier_code == "COD"
         ) {
            return true;
         }
         return false;
      },
      can_cash_payment() {
         if (this.cart_order_form.is_digital) {
            return false
         }
         if (
            this.config &&
            this.config.is_cash_payment &&
            this.cart_order_form.courier &&
            this.cart_order_form.courier.courier_code == "PICKUP"
         ) {
            return true;
         }
         return false
      },
      errors() {
         return this.$store.state.errors;
      },
      render_payments() {
         let payments = []

         if (this.user && !this.cart_order_form.is_deposit && this.user.saldo_balance > 0) {

            let payment_info =`Saldo Anda ${moneyIdr(this.user.saldo_balance)}`
            if(!this.can_saldo_payment('SALDO_BALANCE')) {
               payment_info += ` ( Saldo tidak cukup )`
            }
            payments.push({
               id: "saldo_balance",
               payment_name:  "Bayar Pakai Saldo",
               payment_info: payment_info,
               payment_code: "",
               payment_fee: 0,
               payment_method: "SALDO BALANCE",
               payment_type: "SALDO_BALANCE",
               icon_url: "static/wallet_icon.png",
               is_enable: this.can_saldo_payment('SALDO_BALANCE')
            })
         }


         if (this.can_cash_payment) {
            payments.push({
               id: "cash",
               payment_name: "Bayar tunai ditoko",
               payment_code: "",
               payment_fee: 0,
               payment_method: "CASH",
               payment_type: "CASH",
               icon_url: "static/payment-cod.png",
               is_enable: true
            })
         }

         if (this.can_cod_payment) {
            payments.push({
               id: "cod",
               payment_name: "Bayar ditempat (COD)",
               payment_info: "Bayar saat pesanan diantarkan",
               payment_code: "",
               payment_fee: 0,
               payment_method: "COD",
               payment_type: "COD",
               icon_url: "static/payment-cod.png",
               is_enable: true
            })
         }

         if (this.payment_chanels.length) {
            payments = [...payments, ...this.payment_chanels.map(item => {
               return {
                  id: item.id,
                  payment_name: item.bank_name,
                  payment_code: item.account_number,
                  payment_method: item.bank_detail,
                  payment_info: "Transfer manual",
                  payment_type: "DIRECT_TRANSFER",
                  payment_fee: 0,
                  icon_url: "static/credit_card_icon.png",
                  is_enable: true
               }
            })]
         }


         return payments
      },
   },
   methods: {
        can_saldo_payment(type) {
         if(type != 'SALDO_BALANCE') return true
         if (this.user && this.user.saldo_balance >= this.cart_order_form.billing_total) {
            return true;
         }
         return false;
      },
      is_selected_payment(id) {
         if (this.cart_order_form.payment?.id == id) {
            return true;
         }
         return false;
      },
      selectPayment(item) {
         this.$store.commit("cart/SET_PAYMENT", item);
         this.$store.commit("CLEAR_ERRORS");
          this.$emit('onClose', 1)
      },
   },
};
</script>
