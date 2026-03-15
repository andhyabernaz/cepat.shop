<script setup>
import { BaseApi } from 'src/boot/axios';
import { copyString } from 'src/utils';
import { computed, onMounted, reactive, ref } from 'vue';
import { useStore } from 'vuex';

const store = useStore()

const emits = defineEmits(['onUpdate'])

const vendorName = 'Xendit'

const config = computed(() => store.state.config)

const callback_url = ref('')


const form = ref({
   xendit_api_key: '',
   xendit_callback_token: '',
})


const updateData = () => {
   BaseApi.post('payment-configs', form.value).then(() => {
      getData()
      emits('onUpdate', 1)
   })
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

onMounted(() => {
   if (config.value) {
      callback_url.value = config.value.xendit_callback
   }
   getData()
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
               <div>Pengaturan {{vendorName}} payment Gateway, Silahkan daftar di {{vendorName}} untuk mendapatkan Kredensial</div>
            </div>
            <div class="q-pt-sm">
               <div class="q-gutter-y-sm">

                  <q-input filled v-model="form.xendit_api_key" label="Xendit Api Key" />
                  <q-input filled v-model="form.xendit_callback_token" label="Xendit Callback Token" />

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