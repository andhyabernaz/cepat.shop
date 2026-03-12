<script>
export default {
   props: {
      source: {
         type: String,
         default: 'cart'
      },
      resetNoteOnMount: {
         type: Boolean,
         default: true
      },
      editable: {
         type: Boolean,
         default: false
      }
   },

   computed: {
      cart_order_form() {
         return this.$store.getters[`${this.source}/getChartOrderForm`]
      },
      customer_note: {
         get() {
            return this.$store.state[this.source].customer_note
         },
         set(val) {
            this.$store.commit(`${this.source}/SET_CUSTOMER_NOTE`, val)
         }
      }
   },
   mounted() {
      if (this.resetNoteOnMount) {
         this.customer_note = ''
      }
   },
   methods: {
      incrementQty(item) {
         const nextQty = parseInt(item.quantity || 0) + 1
         const maxStock = parseInt(item.product_stock || 0)

         if (maxStock > 0 && nextQty > maxStock) {
            this.$q.notify({
               type: 'warning',
               message: `Stok ${item.name} tersisa ${maxStock}`,
            })
            return
         }

         this.$store.commit(`${this.source}/UPDATE_ITEM_QTY`, {
            sku: item.sku,
            quantity: nextQty,
         })
      },
      decrementQty(item) {
         const nextQty = parseInt(item.quantity || 0) - 1
         if (nextQty < 1) {
            return
         }

         this.$store.commit(`${this.source}/UPDATE_ITEM_QTY`, {
            sku: item.sku,
            quantity: nextQty,
         })
      },
      removeItem(item) {
         this.$store.commit(`${this.source}/REMOVE_ITEM`, item.sku)
      }
   }

}
</script>

<template>
   <q-card class="cart-detail bg-white shadow q-mt-sm" square bordered>
      <q-separator color="teal q-pt-xs" />
      <q-card-section>
         <div class="card-subtitle">Detail Pesanan</div>
         <q-list separator class="bg-grey-1">
            <q-item v-for="item in cart_order_form.items" :key="item.sku || item.id">
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
                     <div class="row items-center q-gutter-x-xs q-mt-xs" v-if="editable">
                        <q-btn dense flat round icon="remove" @click="decrementQty(item)"></q-btn>
                        <div class="text-weight-medium">{{ item.quantity }}</div>
                        <q-btn dense flat round icon="add" @click="incrementQty(item)"></q-btn>
                        <q-btn dense flat color="negative" icon="delete_outline" @click="removeItem(item)"></q-btn>
                     </div>
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
