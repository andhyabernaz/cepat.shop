<script>
export default {

   computed: {
      cart_order_form() {
         return this.$store.getters['cart/getChartOrderForm']
      },
      customer_note: {
         get() {
            return this.$store.state.cart.customer_note
         },
         set(val) {
            this.$store.commit('cart/SET_CUSTOMER_NOTE', val)
         }
      }
   },
   mounted() {
      this.customer_note = ''
   }

}
</script>

<template>
   <q-card class="cart-detail bg-white shadow q-mt-sm" square bordered>
      <q-separator color="teal q-pt-xs" />
      <q-card-section>
         <div class="card-subtitle">Detail Pesanan</div>
         <q-list separator class="bg-grey-1">
            <q-item v-for="item in cart_order_form.items" :key="item.id">
               <q-item-section avatar>
                  <q-avatar icon="account_balance_wallet" rounded text-color="grey-7" color="grey-1" padding="xs"
                     v-if="item.product_type == 'Deposit'" size="50px"></q-avatar>
                  <q-img v-else :src="item.image_url" width="50px" height="50px" class="img-thumbnail"></q-img>
               </q-item-section>
               <q-item-section>
                  <div class="col">
                     <div class="text-weight-medium">{{ item.name }}</div>
                     <div class="text-caption text-grey-7">{{ item.note }}</div>
                     <div class="text-grey-7">{{ item.quantity + 'X ' + moneyIdr(item.price) }}</div>
                  </div>
               </q-item-section>
               <q-item-section side>
                  <q-item-label>{{ moneyIdr(item.price * item.quantity) }}</q-item-label>
               </q-item-section>
            </q-item>
         </q-list>
         <div class="q-pt-md">
            <q-input type="textarea" v-model="customer_note" rows="2" label="Catatan Pembeli" filled></q-input>
         </div>
      </q-card-section>
   </q-card>
</template>