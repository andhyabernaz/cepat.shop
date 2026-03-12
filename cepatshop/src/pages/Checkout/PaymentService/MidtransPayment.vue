<template>
   <div>
      <q-list v-if="render_payments.length" separator>
         <q-item-label header class="text-weight-bold text-sm">Via Payment Gateway</q-item-label>
         <q-item class="q-py-md" v-for="(item, index) in render_payments" :key="index" clickable
            @click="selectPayment(item)" :class="{ 'bg-green-1': is_selected_payment(item.id) }"
            style="min-height: 65px">
            <q-item-section side top>
               <q-icon size="sm" :name="is_selected_payment(item.id)
                  ? 'radio_button_checked'
                  : 'radio_button_unchecked'
                  " :color="is_selected_payment(item.id) ? 'green' : 'grey-8'"></q-icon>
            </q-item-section>
            <q-item-section>
               <q-item-label class="payment-name">
                  {{ item.payment_name }}
               </q-item-label>
               <q-item-label class="payment-info">
                  Pembayaran menggunakan virtual accounts, QRIS, ewalet, alfamart atau indomaret by Midtrans
               </q-item-label>
               <div>
                  <PaymentIcons></PaymentIcons>
               </div>
            </q-item-section>
         </q-item>
      </q-list>
   </div>
</template>

<script setup>
import PaymentIcons from 'src/components/PaymentIcons.vue';
import { computed } from 'vue';
import { useStore } from 'vuex';

const emits = defineEmits(['onClose'])
const props = defineProps({
   source: {
      type: String,
      default: 'cart',
   },
})

const store = useStore()

const cart_order_form = computed(() => {
   return store.getters[`${props.source}/getChartOrderForm`];
})
const render_payments = computed(() => {
   return [
      {
         id: "midtrans",
         payment_name: "Midtrans Payment",
         payment_code: "",
         payment_fee: 0,
         payment_method: "Midtrans",
         payment_type: "PAYMENT_GATEWAY",
         icon_url: "static/payment_all.webp",
         is_enable: true,
      }
   ];
})

const is_selected_payment = (id) => {
   if (cart_order_form.value.payment?.id == id) {
      return true;
   }
   return false;
}
const selectPayment = (item) => {
   store.commit(`${props.source}/SET_PAYMENT`, item);
   store.commit("CLEAR_ERRORS");
   emits('onClose', 1)
}
</script>
