<script setup>
import { Loading } from 'quasar';
import { moneyIdr } from 'src/utils'
import { computed, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';

const store = useStore()
const router = useRouter()

const page_width = computed(() => store.state.page_width)

const is_mini = computed(() => {
   return page_width.value <= 440
})

const user = computed(() => store.state.user.user)

const modal = ref(false)
const deposit_amount = ref(null)

const handleDeposit = () => {
   deposit_amount.value = 0
   modal.value = true
}

const addToCart = () => {
   modal.value = false
   Loading.show()

   let cartItem = {
      product_id: 0,
      product_stock: 1,
      sku: 'DEPO',
      name: `Deposit Saldo ${moneyIdr(deposit_amount.value)}`,
      price: deposit_amount.value,
      quantity: 1,
      weight: 1,
      product_type: 'Deposit'
   }

   store.dispatch('cart/addToCartDeposit', cartItem).then((res) => {
      store.commit('cart/SET_CARTS', [res.data.data])
      setTimeout(() => {
         router.replace({ name: 'Checkout' })
      }, 100)
   }).finally(() => {
      Loading.hide()
   })

}

const formatAmount = (val) => {
   setTimeout(() => {
      deposit_amount.value = parseInt(val)
   }, 10)
}

</script>

<template>
   <div v-if="user">
      <q-card class="bg-brand text-white">
         <q-cad-section>
            <div class="q-py-md" :class="{ 'q-px-md': page_width > 700 }">

               <div class="flex justify-around no-wrap">
                  <div class="flex no-wrap justify-center">
                     <div class="q-pa-sm">
                        <div class="items-center" :class="{ 'row': !is_mini }">
                           <q-icon name="account_balance_wallet" size="28px"></q-icon>
                           <div class="text-md text-weight-bold text-nowrap" style="min-width:93px">{{
                              moneyIdr(user.saldo_balance) }}</div>
                        </div>
                        <q-btn icon-right="arrow_right" label="Isi Saldo" flat padding="2px" no-caps
                           @click="handleDeposit" class="q-mt-xs btn-mini"></q-btn>
                     </div>
                  </div>
                  <!-- <q-separator color="white" vertical></q-separator>
                  <div class="flex no-wrap justify-center">

                     <div class="q-pa-sm">
                        <div class="items-center" :class="{ 'row': !is_mini }">
                           <q-icon name="eva-shopping-bag" size="28px"></q-icon>
                           <div class="text-sm text-weight-bold text-nowrap" style="min-width:93px">Pesanan</div>
                        </div>
                        <q-btn icon-right="arrow_right" label="Lihat Detil" flat padding="2px" no-caps
                           class="q-mt-xs btn-mini" :to="{ name: 'CustomerOrder' }"></q-btn>
                     </div>
                  </div> -->

                  <q-separator color="white" vertical></q-separator>
                  <div class="flex no-wrap justify-center">

                     <div class="q-pa-sm">
                        <div class="items-center" :class="{ 'row': !is_mini }">
                           <q-icon name="assignment" size="28px"></q-icon>
                           <div class="text-sm text-weight-bold text-nowrap" style="min-width:93px">Mutasi Saldo</div>
                        </div>
                        <q-btn icon-right="arrow_right" label="Lihat Detil" flat padding="2px" no-caps
                           class="q-mt-xs btn-mini" :to="{ name: 'CustomerMutasiSaldo' }"></q-btn>
                     </div>
                  </div>

               </div>
            </div>
         </q-cad-section>
      </q-card>
      <q-dialog v-model="modal">
         <div class="box-column card-md">
            <div class="card-title">Isi Saldo</div>
            <q-form @submit="addToCart">
               <q-input label="Nominal" requred min="5000" type="number" v-model="deposit_amount"
                  @update:model-value="formatAmount" :hint="moneyIdr(deposit_amount, true)"></q-input>
               <div class="card-action q-mt-md">
                  <q-btn type="submit" color="primary" label="Order Sekarang"></q-btn>
                  <q-btn color="primary" label="Batal" outline v-close-popup></q-btn>
               </div>
            </q-form>
         </div>
      </q-dialog>
   </div>
</template>