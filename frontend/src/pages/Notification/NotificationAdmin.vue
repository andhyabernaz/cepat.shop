<script setup>
import { onMounted, ref } from 'vue';
import MenuTabs from './MenuTabs.vue'
import { BaseApi } from "boot/axios";
import { Notify } from 'quasar';
import { useStore } from 'vuex';

const store = useStore()

const data = ref({ data: [], from: 1, total: 0 })

const statuses = [
   { label: 'Pending', value: 'Pending' },
   { label: 'Sent', value: 'Sent' },
   { label: 'Failed', value: 'Failed' },
]

const status = ref('Pending')

const getData = (url = null) => {
   data.value.data = []
   store.commit('SET_LOADING', true)
   if (!url) {
      url = `messages?is_admin=1`
   }
   BaseApi.get(url).then(res => {
      data.value = { ...res.data.data }
   })
}

const sendMessage = (message) => {
   BaseApi.post(`messages/${message.id}`).then((res) => {
      Notify.create({
         type: 'positive',
         message: res.data.message
      })
   })
}

onMounted(() => getData())

const errorLog = ref('')
const errorModal = ref(false)

const viewError = (log) => {
   errorLog.value = log
   errorModal.value = true
}

</script>

<template>
   <q-page padding>
      <AppHeader title="Notifications">
      </AppHeader>
      <MenuTabs></MenuTabs>
      <q-card class="section shadow">
         <q-card-section>
            <q-select dense filled label="Status" class="q-mb-sm" :options="statuses" emit-value map-options
               v-model="status" @update:modelValue="getData()"></q-select>
            <q-list>

               <q-expansion-item v-for="item in data.data" :key="item.id" :label="item.subject" group="msg"
                  expand-separator>
                  <q-card>

                     <q-card-section class="bg-grey-1">
                        {{ item.body }}
                     </q-card-section>
                  </q-card>
               </q-expansion-item>
            </q-list>
            <div class="text-center q-py-md" v-if="!data.total">Tidak ada notifikasi</div>
            <SimplePagination v-bind="data" @loadUrl="getData" class="q-mt-sm"></SimplePagination>
         </q-card-section>
      </q-card>
      <q-dialog v-model="errorModal">
         <q-card class="card-lg">
            <q-card-section>
               <div class="card-subtitle flex">
                  <div>Error Log</div>
                  <q-space></q-space>
                  <q-btn round flat dense icon="close" v-close-popup></q-btn>
               </div>
               <div class="scroll bg-grey-10 text-grey-2" style="height:60vh">
                  <pre>
                     {{ errorLog }}
                  </pre>
               </div>
            </q-card-section>
         </q-card>
      </q-dialog>
   </q-page>
</template>
