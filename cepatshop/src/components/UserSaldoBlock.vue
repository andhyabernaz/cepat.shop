<script setup>
import { Loading, Notify } from 'quasar';
import { Api } from 'src/boot/axios';
import { moneyIdr } from 'src/utils'
import { computed, onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { useStore } from 'vuex';

defineProps({
   simple: Boolean,
})

const store = useStore()
const router = useRouter()

const page_width = computed(() => store.state.page_width)

const is_mini = computed(() => {
   return page_width.value <= 440
})

const user = computed(() => store.state.user.user)
const saldoConfig = ref(null)

const modal = ref(false)
const deposit_amount = ref(null)

const handleDeposit = () => {
   deposit_amount.value = 0
   modal.value = true
}

const addToCart = () => {
   let minDeposit = parseInt(saldoConfig.value.min_deposit_amount)
   if(minDeposit > deposit_amount.value) {
      Notify.create({
         type: 'negative',
         message: `Minimun deposit ${moneyIdr(minDeposit)}`
      })

      return
   }
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

const getSaldoConfig = () => {
   Api.get('getSaldoConfig').then(res => {
      saldoConfig.value = res.data.data
   })
}

onMounted(() => {
   getSaldoConfig()
})

</script>

<template>
   <div v-if="user" :class="{ 'box-column': !simple }">
      <div class="q-py-md">

         <q-item>
            <q-item-section avatar>
               <q-avatar icon="account_balance_wallet" color="brand" text-color="white" size="50px">
               </q-avatar>
            </q-item-section>
            <q-item-section>
               <q-item-label class="text-md text-weight-bold">Saldo Balance</q-item-label>
               <q-item-label class="text-grey-7 text-lg q-pt-xs">{{ moneyIdr(user.saldo_balance)
                  }}</q-item-label>
            </q-item-section>
         </q-item>
         <div class="row q-gutter-x-sm q-px-md q-py-sm q-mt-md items-center">

            <q-btn class="col" size="15px" label="Isi Saldo" unelevated color="brand" icon="add_card"
               @click="handleDeposit" no-caps>
            </q-btn>
            <q-btn v-if="!simple" class="col" size="15px" outline color="brand" icon="assignment" no-caps
               label="Riwayat" :to="{ name: 'CustomerMutasiSaldo' }">
            </q-btn>
         </div>
      </div>
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