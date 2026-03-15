<script setup>
import { BaseApi } from 'src/boot/axios';
import { copyString } from 'src/utils';
import { computed, onMounted, ref } from 'vue';
import { useStore } from 'vuex';

const store = useStore()

const vendorName = 'Tripay'

const config = computed(() => store.state.config)
const emits = defineEmits(['onUpdate'])
const callback_url = ref('')

const envMode = ['sandbox', 'production']

const form = ref({
   tripay_mode: '',
   tripay_api_key: '',
   tripay_private_key: '',
   tripay_merchant_id: '',
})

const daftarTripay = () => {
   let url = 'https://tripay.co.id/?ref=TP10450'
   window.open(url, '_blank')
}

const paymentConfig = ref(null)

const getData = () => {
   BaseApi.get(`payment-configs?vendor=${vendorName}`).then(res => {
      paymentConfig.value = res.data.data
     form.value = {...res.data.data}
   })
}

const isReady = computed(() => {

   if(!paymentConfig.value) return

   for(const x in paymentConfig.value) {
      if(paymentConfig.value[x] == '' || paymentConfig.value[x] == null || !paymentConfig.value[x]) {
         return false
         break;
      }
   }

   return true
})

const updateData = () => {
   BaseApi.post('payment-configs', form.value).then(() => {
      getData()

      emits('onUpdate', 1)

   })
}
onMounted(() => {
   getData()
   if (config.value) {
      callback_url.value = config.value.tripay_callback
   }
})
</script>

<template>

   <q-card class="q-mt-lg q-pa-sm" flat>
      <q-card-section>
         <div class="flex items-center justify-between q-mb-xs">
            <div class="card-subtitle">{{vendorName}} Payment Gateway</div>
            <q-badge :color="isReady ? 'green' : 'grey-7'">{{ isReady ? 'Ready' : 'Not Ready' }}</q-badge>
         </div>
         <form @submit.prevent="updateData">
            <div class="q-mb-md text-grey-7 text-caption">
               <div>Pengaturan {{vendorName}} payment Gateway, Silahkan daftar di {{vendorName}} untuk mendapatkan Kredensial, Anda
                  dapat
                  mendaftar melalui link berikut <span
                     class="cursor-pointer bg-green-1 text-dark q-px-xs text-weight-medium q-px-xs"
                     @click="daftarTripay">https://tripay.co.id/register</span></div>
            </div>
            <div class="q-pt-sm">
               <div class="q-gutter-y-sm">

                  <q-select label="ENV MODE" filled :options="envMode" v-model="form.tripay_mode"></q-select>
                  <q-input filled v-model="form.tripay_merchant_id" label="Tripay Merchant ID" />
                  <q-input filled v-model="form.tripay_api_key" label="Tripay Api Key" />
                  <q-input filled v-model="form.tripay_private_key" label="Tripay Private Key" />

                  <div>
                     <q-input filled v-model="callback_url" readonly>
                        <template v-slot:append>
                           <q-btn icon="content_copy" @click="copyString(callback_url)" unelevated size="18px"
                              padding="4px" flat></q-btn>
                        </template>
                     </q-input>
                     <div class="text-xs text-grey-8 q-pa-xs">Salin URL callback ke dashboard merchant {{vendorName}}</div>
                  </div>
               </div>
            </div>
            <div class="q-pt-lg">
               <q-btn class="full-width" type="submit" label="Simpan Pengaturan" color="primary"></q-btn>
            </div>
         </form>
      </q-card-section>
   </q-card>
</template>