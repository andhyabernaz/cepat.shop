<template>
   <div class="bg-grey-1">
      <q-card flat>
         <q-card-section>
            <div class="q-pa-xs">
               <div class="card-subtitle flex justify-between items-center">
                  <div>Payment Gateway Aktif</div>
                  <q-badge :color="config.is_pg_ready ? 'green' : 'grey-8'">{{ config.is_pg_ready ? 'Active' : 'Not Active' }}</q-badge>
               </div>

               <q-select filled emit-value map-options :options="payment_service_options" v-model:model-value="form.payment_default" @update:model-value="updateData"></q-select>
            </div>
         </q-card-section>
      </q-card>
      <XenditConfig @onUpdate="getConfig"></XenditConfig>
      <TripayConfig @onUpdate="getConfig"></TripayConfig>
      <MidtransConfig @onUpdate="getConfig"></MidtransConfig>
      <DuitkuConfig @onUpdate="getConfig"></DuitkuConfig>

   </div>
</template>

<script setup>
import { computed, onMounted, reactive } from 'vue';
import TripayConfig from './Components/TripayConfig.vue'
import XenditConfig from './Components/XenditConfig.vue'
import MidtransConfig from './Components/MidtransConfig.vue';
import DuitkuConfig from './Components/DuitkuConfig.vue';

import { useStore } from 'vuex';
import { BaseApi } from 'src/boot/axios';

const store = useStore()

const config = computed(() => store.state.config)

const payment_service_options = computed(() => {
   let opts = [
      {label: 'Tidak Ada', value: ''}
   ]
   if(config.value) {
      config.value.payment_services.forEach(el => {
         opts.push({label: el, value: el })
      });
   }

   return opts
})

const form = reactive({
   payment_default: ''
})

const getConfig = () => {
   store.dispatch('getAdminConfig')
}

onMounted(() => {
   form.payment_default = config.value.payment_default ?? ''
})

const updateData = () => {
   BaseApi.post('config', form).then((res) => {

      store.commit('SET_CONFIG', res.data.data)
   })
}

</script>